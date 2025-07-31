<?php

namespace App\Console\Commands;

use Fanky\Admin\Models\Product;
use Illuminate\Console\Command;
use Rap2hpoutre\FastExcel\FastExcel;

class ExportProductsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'export:products';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily export products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = public_path('export/mmo-price.xlsx');
        if (!is_dir(dirname($path))) {
            mkdir(dirname($path), 0777, true);
        }

        function productGenerator(): \Generator
        {
            $products = Product::query()
                ->with([
                    'catalog',
                    'additional_catalogs',
                    'catalog.parent',
                    'catalog.parent.parent',
                    'catalog.parent.parent.parent',
                    'related'
                ])
                ->where('id', '>', 14076)
                ->get();

            foreach ($products as $product) {
                yield $product;
            }
        }

        return (new FastExcel(productGenerator()))
            ->export($path, function ($product) {
                $_parents = $this->getParents($product, true);
                $parent1 = array_get($_parents, 0);
                $parent2 = array_get($_parents, 1);
                $parent3 = array_get($_parents, 2);

                return [
                    'ID' => $product->id,
                    'Уровень 1' => $parent1 ? $parent1->name : '',
                    'Уровень 2' => $parent2 ? $parent2->name : '',
                    'Уровень 3' => $parent3 ? $parent3->name : '',

                    'Название' => $product->name,
                    'Бренд' => $product->brand ? $product->brand->name : '',
                    'Цена' => $product->price,
                    'Старая цена' => $product->old_price,

                    'Размеры, см' => $product->sizes,
                    'Площадь остекления, м2' => $product->square,
                    'Страна' => $product->manufacturer,
                    'Материал' => $product->material,
                    'Тип' => $product->type,
                    'Ручка' => $product->handle,
                    'Конфигурация' => $product->configuration,

                    'Наличие' => $product->in_stock,
                    'Доп.разделы' => implode(',', $product->additional_catalogs->pluck('id')->toArray()),
                    'Связанные товары' => implode(',', $product->related->pluck('id')->toArray()),
                ];
            });
    }

    private function getParents($product, $reverse = false): array
    {
        $parents = [];
        $parent = $product->catalog;
        while ($parent) {
            $parents[] = $parent;
            $parent = $parent->parent ?? null;
        }
        if ($reverse) {
            $parents = array_reverse($parents);
        }

        return $parents;
    }
}
