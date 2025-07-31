<?php namespace App\Http\Controllers;

use Fanky\Admin\Models\ObjectItem;
use Fanky\Admin\Models\Page;
use Illuminate\Http\Request;
use S;
use Settings;
use View;

class ObjectsController extends Controller {
	public $bread = [];
	protected $object_page;

	public function __construct() {
		$this->object_page = Page::whereAlias('our-objects')
			->get()
			->first();

		$this->bread[] = [
            'url'  => $this->object_page['url'],
            'name' => $this->object_page['name']
		];
	}

	public function index() {
		$page = $this->object_page;
		if (!$page)
			abort(404, 'Страница не найдена');
		$bread = $this->bread;
        $page->ogGenerate();
        $page->setSeo();

        $items = ObjectItem::orderBy('date', 'desc')
            ->public()->paginate(Settings::get('objects_per_page') ?: 6);

        if (count(request()->query())) {
            View::share('canonical', $this->object_page->alias);
        }

        return view('objects.index', [
            'h1'    => $page->getH1(),
            'bread' => $bread,
            'items' => $items,

        ]);
	}

	public function item($id) {
		$item = ObjectItem::find($id);
		if (!$item) abort(404);

		$bread = $this->bread;
		$bread[] = [
			'url'  => $item->url,
			'name' => $item->name
		];

        $item->ogGenerate();
        $item->setSeo();

        if (count(request()->query())) {
            View::share('canonical', $this->object_page->alias);
        }

        $images = $item->images()->paginate(S::get('object_images_per_page', 9));

		return view('objects.item', [
			'item'        => $item,
			'h1'          => $item->getH1(),
			'images'      => $images,
			'bread'       => $bread,
		]);
	}
}
