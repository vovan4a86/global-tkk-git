<?php namespace Fanky\Admin\Models;

use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Fanky\Admin\Models\SearchIndex
 *
 * @property int $product_id
 * @property array|null $sizes
 * @property array|null $seasons
 * @property string $brand
 * @property array|null $genders
 * @property array|null $types
 * @method static Builder|SearchIndex newModelQuery()
 * @method static Builder|SearchIndex newQuery()
 * @method static Builder|SearchIndex query()
 * @method static Builder|SearchIndex whereCreatedAt($value)
 * @method static Builder|SearchIndex whereName($value)
 * @method static Builder|SearchIndex whereText($value)
 * @method static Builder|SearchIndex whereUpdatedAt($value)
 * @method static Builder|SearchIndex whereUrl($value)
 * @mixin \Eloquent
 */
class SearchIndex extends Model {

	protected $primaryKey = null;
    protected $fillable = ['name', 'text', 'url'];
	public $incrementing = false;

	public function delete() {
		parent::delete();
	}

	public function getAnnounce($search) {
		$text = strip_tags($this->text);
		$text = str_replace(["\n", "\r", "\t"], '', $text);
		$pos = mb_stripos(Str::lower($text), Str::lower($search));
		if($pos === false){
			return $this->announce;
		} else {
			$start = max(0, $pos - 150);
			$length = Str::length($search) + 250;
			$substr = Str::substr($text, $start, $pos-$start) . '<b>';
			$substr .= Str::substr($text, $pos, Str::length($search)) . '</b>';
			$substr .= Str::substr($text, $pos + Str::length($search), 50);

			$substr = trim($substr);

			if($start > 0) $substr = '..' . $substr;
			if($pos + $length < Str::length($text)) $substr .= '..';
			return $substr;
		}
	}

	public function getName($search) {
		$name = strip_tags($this->name);
		$name = str_replace(["\n", "\r", "\t"], '', $name);
		$pos = mb_stripos(Str::lower($name), Str::lower($search));
		if($pos === false){
			return $this->name;
		} else {
			$start = max(0, $pos - 150);
			$length = Str::length($search) + 250;
			$substr = Str::substr($name, $start, $pos-$start) . '<b>';
			$substr .= Str::substr($name, $pos, Str::length($search)) . '</b>';
			$substr .= Str::substr($name, $pos + Str::length($search), 50);

			$substr = trim($substr);

			if($start > 0) $substr = '..' . $substr;
			if($pos + $length < Str::length($name)) $substr .= '..';
			return $substr;
		}
	}

    public static function update_index() {
        //clear_all;
        $item = new self();
        $table = $item->getTable();

        try{
            DB::beginTransaction();

            DB::table($table)->delete();

            $catalogs = Catalog::wherePublished(1)->get();
            foreach ($catalogs as $catalog){
                self::create([
                    'name'	=> $catalog->name,
                    'text' 	=> $catalog->text_after,
                    'url' 	=> $catalog->url
                ]);

                foreach ($catalog->products()->with(['catalog', 'params'])->public()->get() as $product){
                    $params = $product->params->transform(function($param){
                        return $param->name . ': ' . $param->value;
                    })->implode(', ');
                    self::create([
                        'name'	=> $product->name,
                        'text' 	=> implode(' ', [$product->text_small, $product->text, $product->text_description, $params]),
                        'url' 	=> $product->url
                    ]);
                }
            }

            $pages = Page::wherePublished(1)->get();
            foreach ($pages as $page){
                self::create([
                    'name'	=> $page->name,
                    'text' 	=> $page->text,
                    'url' 	=> $page->url
                ]);
            }
            $pages = News::wherePublished(1)->get();
            foreach ($pages as $page){
                self::create([
                    'name'	=> $page->name,
                    'text' 	=> $page->text,
                    'url' 	=> $page->url
                ]);
            }

            DB::commit();

        } catch (\Exception $e){
            \Debugbar::log($e->getMessage());
            DB::rollBack();
        }

    }

	public function getAnnounceAttribute() {
		$text = strip_tags($this->text);

		return Str::words($text, 50);
	}
}
