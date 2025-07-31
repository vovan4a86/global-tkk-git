<?php

namespace App\Imports;

use Artisan;
use Fanky\Admin\Models\Brand;
use Fanky\Admin\Models\Catalog;
use Fanky\Admin\Models\Product;
use Fanky\Admin\Text;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithProgressBar;
use Maatwebsite\Excel\Events\AfterImport;

class ProductsImport implements ToCollection, WithProgressBar,
                                WithHeadingRow, WithEvents
{
    use Importable;

    private $brands = [];
    private $catalogCache = [];

    public function __construct()
    {
        $this->brands = Brand::pluck('id', 'name')->all();
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $i => $row) {
            $row_arr = $row->toArray();
            $catalogPath = array_only($row_arr, ['uroven_1', 'uroven_2', 'uroven_3']);
            $catalog = $this->getCatalog($catalogPath);
            if (!$catalog) {
                continue;
            }

            $brand_name = $row[str_slug('Бренд', '_')];
            if ($brand_name) {
                $brand_id = $this->brands[$brand_name] ?? '';
            }

            $additional_catalogs = (string)$row[str_slug('Доп.разделы', '_')];
            $related = (string)$row[str_slug('Связанные товары', '_')];

            $updateData = [
                'name' => $row[str_slug('Название', '_')],
                'brand' => $brand_id ?? '',
                'price' => $row[str_slug('Цена', '_')],
                'old_price' => $row[str_slug('Старая цена', '_')],

                'sizes' => $row[str_slug('Размеры, см', '_')],
                'square' => $row[str_slug('Площадь остекления, м2', '_')],
                'manufacturer' => $row[str_slug('Страна', '_')],
                'material' => $row[str_slug('Материал', '_')],
                'type' => $row[str_slug('Тип', '_')],
                'handle' => $row[str_slug('Ручка', '_')],
                'configuration' => $row[str_slug('Конфигурация', '_')],

                'in_stock' => $row[str_slug('Наличие', '_')],
            ];

            $product = $row['id'] ? Product::find($row['id']) : null;
            if (!$product) {
                $product = new Product();
                $updateData['order'] = $catalog->products()->max('order') + 1;
                $updateData['alias'] = $this->generateAlias($product);
                $product->fill($updateData);
                $product->save();
                $product->catalog()->attach($catalog->id);
            } else {
                $product->update($updateData);
            }

            //в экселе обязательно вводить через запятую с пробелом, иначе считает, что число и заменяет на точку
            $additional_catalog_ids = explode(',',$additional_catalogs);
            $additional_catalog_ids = array_map('trim', $additional_catalog_ids);
            $product->additional_catalogs()->sync($additional_catalog_ids);

            $related_ids = explode(',',$related);
            $related_ids = array_map('trim', $related_ids);
            $product->related()->sync($related_ids);
        }
    }

    private function getCatalog($path)
    {
        $path = array_values(array_filter($path));
        if(!count($path)) {return null;}
        $key = implode(',', $path);
        if (array_get($this->catalogCache, $key)) {
            return array_get($this->catalogCache, $key);
        }

        $result = null;
        $parent_id = 0;
        foreach ($path as $name) {
            $catalog = Catalog::whereName($name)->whereParentId($parent_id)->first();
            if (!$catalog) {
                $catalog = Catalog::create([
                    'parent_id' => $parent_id,
                    'name' => $name,
                    'h1' => $name,
                    'og_title' => $name,
                    'og_description' => $name,
                    'alias' => Text::translit($name),
                    'title' => $name,
                    'published' => 1,
                    'order' => Catalog::whereParentId($parent_id)->max('order') + 1
                ]);
            } else {
                $catalog->update([
                    'published' => 1,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            $parent_id = $catalog->id;
            $result = $catalog;
        }

        $this->catalogCache[$key] = $result;

        return $result;
    }

    private function generateAlias($product): string
    {
        $result = $alias = Text::translit($product->name);
        $i = 1;
        while (Product::where('catalog_id', $product->catalog_id)->where('alias', $result)->exists()) {
            $result = $alias . '_' . $i++;
        }

        return $result;
    }

    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                Artisan::call('export:products');
            }
        ];
    }

    private function syncPointsWithProduct(Product $product, array $points): void
    {
        if (count($points)) {
            $ids = [];
            foreach ($points as $point) {
                $address = $this->getPointAddressFromString($point);
                $p = Point::where('address', $address)->first();
                if (!$p) {
                    $p = Point::create([
                        'name' => $this->getPointNameFromString($point),
                        'address' => $address,
                        'order' => Point::max('order') + 1
                    ]);
                }
                $ids[] = $p->id;
            }
            $product->points()->sync($ids);
        }
    }

    private function syncGenderWithProduct(Product $product, string $gender): void
    {
        if ($gender) {
            $g = Gender::where('value', $gender)->first();
            if (!$g) {
                $g = Gender::create([
                    'value' => $gender,
                    'order' => Gender::max('order') + 1
                ]);
            }
            $product->genders()->sync($g->id);
        }
    }

    private function syncSeasonsWithProduct(Product $product, array $seasons): void
    {
        if (count($seasons)) {
            $ids = [];
            foreach ($seasons as $season) {
                $s = Season::where('value', $season)->first();
                if (!$s) {
                    $s = Season::create([
                        'value' => $season,
                        'order' => Season::max('order') + 1
                    ]);
                }
                $ids[] = $s->id;
            }
            $product->seasons()->sync($ids);
        }
    }

    private function syncCatalogsWithProduct(array $catalogs, Product $product): void
    {
        foreach ($catalogs as $cat) {
            $catalog_product_ids = $cat->products()->pluck('product_id')->all();
            if (!in_array($product->id, $catalog_product_ids)) {
                $cat->products()->attach($product->id);
            }
        }
    }

    private function getPointNameFromString(string $str): string
    {
        $i_start = stripos($str, '(');
        $i_end = stripos($str, ')');

        if ($i_start && $i_end) {
            return substr($str, $i_start + 1, $i_end - $i_start - 1);
        }

        return '-';
    }

    private function getPointAddressFromString(string $str): string
    {
        $i_start = stripos($str, '(');
        $i_end = stripos($str, ')');

        if ($i_start && $i_end) {
            $cut_name = substr($str, $i_start, $i_end - $i_start + 1);
            if ($cut_name) {
                return (trim(str_replace($cut_name, '', $str)));
            }
        }

        return $str;
    }

    private function getBrandId (string $brand)
    {
        if ($brand) {
            $b = Brand::whereValue($brand)->first();
            if (!$b) {
                $b = Brand::create([
                    'value' => $brand,
                    'order' => Brand::max('order') + 1
                ]);
            }
            return $b->id;
        }
        return 0;
    }

    private function getColorId (string $color_name) {
        if($color_name) {
            $color = Color::whereValue($color_name)->first();
            if (!$color) {
                $color = Color::create([
                    'value' => $color_name,
                    'order' => Color::max('order') + 1
                ]);
            }
            return $color->id;
        }
        return 0;
    }
}
