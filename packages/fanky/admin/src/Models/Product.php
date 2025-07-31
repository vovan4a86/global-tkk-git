<?php namespace Fanky\Admin\Models;

use App\Traits\HasH1;
use App\Traits\HasSeo;
use App\Traits\OgGenerate;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;
use Settings;

/**
 * Fanky\Admin\Models\Product
 *
 * @property int $id
 * @property int $catalog_id
 * @property string $name
 * @property string $alias
 * @property string|null $text
 * @property int $price
 * @property int $raw_price
 * @property int $price_per_item
 * @property int $price_per_metr
 * @property int $price_per_kilo
 * @property int $price_per_m2
 * @property float $k
 * @property string $image
 * @property int $published
 * @property boolean $on_main
 * @property boolean $is_kit
 * @property int $order
 * @property string $title
 * @property string $measure
 * @property string $keywords
 * @property string $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $size
 * @property string|null $h1
 * @property string|null $price_name
 * @property string|null $og_title
 * @property string|null $warehouse
 * @property string|null $wall
 * @property string|null $characteristic
 * @property string|null $characteristic2
 * @property string|null $cutting
 * @property string|null $steel
 * @property string|null $length
 * @property string|null $gost
 * @property string|null $comment
 * @property float|null $weight
 * @property float|null $balance
 * @property string|null $og_description
 * @property-read Catalog $catalog
 * @property-read mixed $image_src
 * @property-read mixed $url
 * @property-read Collection|ProductImage[] $images
 * @method static bool|null forceDelete()
 * @method static Builder|Product onMain()
 * @method static Builder|Product public ()
 * @method static bool|null restore()
 * @method static Builder|Product whereAlias($value)
 * @method static Builder|Product whereCatalogId($value)
 * @method static Builder|Product whereCreatedAt($value)
 * @method static Builder|Product whereDeletedAt($value)
 * @method static Builder|Product whereDescription($value)
 * @method static Builder|Product whereId($value)
 * @method static Builder|Product whereImage($value)
 * @method static Builder|Product whereKeywords($value)
 * @method static Builder|Product whereName($value)
 * @method static Builder|Product whereOnMain($value)
 * @method static Builder|Product whereOrder($value)
 * @method static Builder|Product wherePrice($value)
 * @method static Builder|Product wherePriceUnit($value)
 * @method static Builder|Product wherePublished($value)
 * @method static Builder|Product whereText($value)
 * @method static Builder|Product whereTitle($value)
 * @method static Builder|Product whereUnit($value)
 * @method static Builder|Product whereUpdatedAt($value)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereBalance($value)
 * @method static Builder|Product whereCharacteristic($value)
 * @method static Builder|Product whereCharacteristic2($value)
 * @method static Builder|Product whereComment($value)
 * @method static Builder|Product whereCutting($value)
 * @method static Builder|Product whereGost($value)
 * @method static Builder|Product whereH1($value)
 * @method static Builder|Product whereLength($value)
 * @method static Builder|Product whereOgDescription($value)
 * @method static Builder|Product whereOgTitle($value)
 * @method static Builder|Product wherePriceName($value)
 * @method static Builder|Product whereSize($value)
 * @method static Builder|Product whereSteel($value)
 * @method static Builder|Product whereWall($value)
 * @method static Builder|Product whereWarehouse($value)
 * @method static Builder|Product whereWeight($value)
 * @method static \Illuminate\Database\Query\Builder|Product onlyTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Product withoutTrashed()
 * @mixin \Eloquent
 */
class Product extends Model {
    use HasSeo, HasH1, OgGenerate;

    protected $_parents = [];

    protected $guarded = ['id'];

    const UPLOAD_PATH = '/public/uploads/products/';
    const UPLOAD_URL = '/uploads/products/';
    const NO_IMAGE = "/adminlte/no_image_335.jpg";

    public function catalog(): BelongsTo
    {
        return $this->belongsTo(Catalog::class);
    }

    public function docs(): HasMany {
        return $this->hasMany(Document::class)
            ->orderBy('order');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class)
            ->withDefault(['name' => 'Не указан']);
    }

    public function params(): HasMany
    {
        return $this->hasMany(ProductParam::class)
            ->orderBy('order');
    }

    public function related(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_related', 'product_id', 'related_id');
    }

    public function images(): HasMany {
        return $this->hasMany(ProductImage::class, 'product_id')
            ->orderBy('order');
    }

    public function image(): HasOne {
        return $this->hasOne(ProductImage::class, 'product_id')
            ->orderBy('order');
    }

    public function single_image(): HasOne {
        return $this->hasOne(ProductImage::class, 'product_id')
            ->orderBy('order');
    }

    public function additional_catalogs(): BelongsToMany
    {
        return $this->belongsToMany(Catalog::class);
    }

    public function getCatalogSingleImageAttribute()
    {
        $cat = $this->catalog;

        return $cat->image ? $cat->thumb(2) : '';
    }

    public function chars(): HasMany {
        return $this->hasMany(ProductChar::class)
            ->orderBy('order');
    }

    public function getImage($img) {
        return ProductImage::UPLOAD_URL . $img;
    }

    public function getRootImage() {
        $category = Catalog::find($this->catalog_id);
        $root = $category;
        while ($root->parent_id !== 0) {
            $root = $root->findRootCategory($root->parent_id);
        }
        if ($root->image) {
            return Catalog::UPLOAD_URL . $root->image;
        } else {
            return self::NO_IMAGE;
        }
    }

    public function scopePublic($query) {
        return $query->where('published', 1);
    }

    public function scopeIsAction($query) {
        return $query->where('is_action', 1);
    }

    public function scopeInStock($query) {
        return $query->where('in_stock', 1);
    }

    public function scopeOnMain($query) {
        return $query->where('on_main', 1);
    }

    public function getImageSrcAttribute() {
        return ($this->single_image)
            ? ProductImage::UPLOAD_URL . $this->single_image->image
            : null;
    }

    public function getCardThumbAttribute() {
        return ($this->single_image)
            ? $this->single_image->thumb(2)
            : $this->catalog_single_image;
    }

    public function thumb($thumb) {
        return ($this->image)
            ? $this->image->thumb($thumb)
            : null;
    }

    public function getUrlAttribute($catalog_id = null): string {
        if (!$this->_url) {
            $this->_url = $this->catalog->url . '/' . $this->alias;
        }
        return $this->_url;
    }

    public function getUrl($catalog_id = null): string
    {
        if ($catalog_id) {
            $c = Catalog::find($catalog_id);
            return  $c->url . '/' . $this->alias;
        } else {
            return '';
        }
    }

    public function getParents($with_self = false, $reverse = false) {
        $parents = [];
        if ($with_self) {
            $parents[] = $this;
        }
        if($this->catalog) {
            $parents = array_merge($parents, $this->catalog->getParents(true));
        }
        $parents = array_merge($parents, $this->_parents);
        if ($reverse) {
            $parents = array_reverse($parents);
        }

        return $parents;
    }

    private $_url;

    public function delete() {
        foreach ($this->images as $image) {
            $image->delete();
        }

        parent::delete();
    }

    /**
     * @return Carbon
     */
    public function getLastModify() {
        return $this->updated_at;
    }

    public function getBread(Catalog $catalog): array
    {
        $bread = $catalog->getBread();
        $bread[] = [
            'url' => $this->id,
            'name' => $this->name
        ];

        return $bread;
    }

    public function showCategoryImage($catalog_id) {
        $root = Catalog::find($catalog_id);
        while ($root->parent_id !== 0) {
            $root = $root->findRootCategory($root->parent_id);
        }
        return $root->thumb(2);
    }

    public static function findRootParentName($catalog_id) {
        $root = Catalog::find($catalog_id)->getParents();

        if (isset($root[0])) {
            return Catalog::find($root[0]['id'])->name;
        } else {
            return Catalog::find($catalog_id)->name;
        }
    }

    public static function findRootParentCatalog($catalog_id) {
        $root = Catalog::find($catalog_id)->getParents(false, true);

        if (isset($root[0])) {
            return Catalog::find($root[0]['id']);
        } else {
            return Catalog::find($catalog_id);
        }
    }

    public function multiplyPrice($price) {
        $percent = $price * Settings::get('multiplier') / 100;
        return $price + $percent;
    }

    public static function fullPrice($price) {
        $percent = $price * Settings::get('multiplier') / 100;
        return $price + $percent;
    }

    public function getLength() {
        if ($this->length) {
            return $this->length;
        } elseif ($this->dlina) {
            return preg_replace('/[А-Яа-я]/', '', $this->dlina);
        } else {
            return null;
        }
    }

    public function showAnyImage(): string {
//        $is_item_images = $this->images()->get();
        $cat_image = Catalog::whereId($this->catalog_id)->first();
        $root_image = $this->getRootImage() ?: self::NO_IMAGE;
        return $cat_image->image ? Catalog::UPLOAD_URL . $cat_image->image :
            $root_image;
    }

    private function replaceTemplateVariable($template) {
        $name_parts = explode(' ', $this->name, 2);
        $replace = [
            '{name}' => $this->name,
            '{lower_name}' => Str::lower($this->name),
            '{gost}' => $this->gost,
            '{price}' => $this->price ?? 0,
            '{name_part1}' => array_get($name_parts, 0),
            '{name_part2}' => array_get($name_parts, 1),
            '{size}' => $this->size,
            '{wall}' => $this->wall,
            '{steel}' => $this->steel,
            '{measure}' => $this->measure,
            '{manufacturer}' => $this->manufacturer,
            '{length}' => $this->length,
            '{emails_for_order}' => $this->emails_for_order,
            '{product_article}' => $this->product_article,
        ];

        return str_replace(array_keys($replace), array_values($replace), $template);
    }

    public function getTitleTemplate($catalog_id = null) {
        if (!$catalog_id) $catalog_id = $this->catalog_id;
        $catalog = Catalog::find($catalog_id);
        if (!$catalog) return null;
        if (!empty($catalog->product_title_template)) return $catalog->product_title_template;
        if ($catalog->parent_id) return $this->getTitleTemplate($catalog->parent_id);

        return self::$defaultTitleTemplate;
    }

    public static $defaultTitleTemplate = '{name} купить';

    public function generateTitle() {
        if (!($template = $this->getTitleTemplate())) {
            if ($this->title && $this->title != $this->name) {
                $template = $this->title;
            } else {
                $template = self::$defaultTitleTemplate;
            }
        }

//        if (strpos($template, '{city}') === false) { //если кода city нет - добавляем
//            $template .= '{city}';
//        }
        $this->title = $this->replaceTemplateVariable($template);
    }

    public function getDescriptionTemplate($catalog_id = null) {
        if (!$catalog_id) $catalog_id = $this->catalog_id;
        $catalog = Catalog::find($catalog_id);
        if (!$catalog) return null;
        if (!empty($catalog->product_description_template)) return $catalog->product_description_template;
        if ($catalog->parent_id) return $this->getDescriptionTemplate($catalog->parent_id);

        return self::$defaultDescriptionTemplate;
    }

    public function getTextTemplate($catalog_id = null) {
        if (!$catalog_id) $catalog_id = $this->catalog_id;
        $catalog = Catalog::find($catalog_id);
        if (!$catalog) return null;
        if (!empty($catalog->product_text_template)) return $catalog->product_text_template;
        if ($catalog->parent_id) return $this->getTextTemplate($catalog->parent_id);

        return null;
    }

    public static $defaultDescriptionTemplate = '{name} купить по выгодной цене';

    public function generateDescription() {
        if (!($template = $this->getDescriptionTemplate())) {
            if (!$template && $this->description) {
                $template = $this->description;
            } else {
                $template = self::$defaultDescriptionTemplate;
            }
        }

//        if (strpos($template, '{city}') === false) { //если кода city нет - добавляем
//            $template .= '{city}';
//        }

        $this->description = $this->replaceTemplateVariable($template);;
    }

    public function generateText() {
        $template = $this->getTextTemplate();
        if (!$template) {
            $template = $this->text;
        }

        $this->text = $this->replaceTemplateVariable($template);
    }

    public function generateKeywords() {
        if (!$this->keywords) {
            $this->keywords = mb_strtolower($this->name . ' цена, ' . $this->name . ' купить, ' . $this->name . '');
        }
    }

    public function getProductOrderView(): ?string {
        if ($this->price) {
            return 'catalog.blocks.product_order_t';
        } elseif ($this->price_per_item) {
            return 'catalog.blocks.product_order_item';
//        } elseif($this->price_per_kilo) {
//            return number_format($this->price_per_kilo, '0', '',' ');
//        } elseif($this->price_per_metr) {
//            return number_format($this->price_per_metr, '0', '',' ');
//        } elseif($this->price_per_m2) {
//            return number_format($this->price_per_m2, '0', '',' ');
        } else {
            return 'catalog.blocks.product_order_other';
        }
    }

    public function getRecourseDiscountAmount($id = null) {
        if ($this->discount) return $this->discount;

        if (!$id) $category = Catalog::find($this->catalog_id);
        else $category = Catalog::find($id);

        if ($category->discount) return $category->discount;
        elseif ($category->parent_id == 0) return 0;
        else $this->getRecourseDiscountAmount($category->parent_id);
    }

    public function getRecourseMeasure() {
        if ($this->measure) return $this->measure;

        $catalog = Catalog::find($this->catalog_id);
        while($catalog) {
            if($catalog->catalog_measure) return $catalog->catalog_measure;
            else {
                if($catalog->parent) $catalog = $catalog->parent;
                else return $catalog->catalog_measure;
            }
        }
    }

    public function getPriceWithDiscount() {
        if ($discount = $this->getRecourseDiscountAmount()) {
            $amount = $this->price * $discount / 100;
            return $this->price + $amount;
        }
    }

    public function getStartCount() {
        if (!$this->price) return 0;
        if ($this->min_hours) return $this->min_hours;
        return 1;
    }

    public function makeArticle($id): string {
        return str_pad($id, 6, '0', STR_PAD_LEFT);
    }

    public function getDiscountDelivery($catalog_id) {
        if ($this->discount_delivery) {
            return $this->discount_delivery;
        }
        if($this->catalog()->whereCatalogId($catalog_id)->first()->discount_delivery) {
            return $this->catalog()->whereCatalogId($catalog_id)->first()->discount_delivery;
        }
        return null;
    }

    public function getDiscountPayment($catalog_id) {
        if ($this->discount_payment) {
            return $this->discount_payment;
        } elseif($this->catalog()->whereCatalogId($catalog_id)->first()->discount_payment) {
            return $this->catalog()->whereCatalogId($catalog_id)->first()->discount_payment;
        } else {
            return null;
        }
    }

    public function getSize() {
        if(!count($this->sizes)) {
            return 0;
        }

        $size_id = $this->sizes()->first()->id;
        $size = Size::find($size_id);

        return $size->value;

    }

    public function getPriceFormatAttribute(): string
    {
        return number_format($this->price, 0, ',', ' ');
    }

    public function getOldPriceFormatAttribute(): string
    {
        return number_format($this->old_price, 0, ',', ' ');
    }
}
