<?php namespace App\Http\Controllers;

use Auth;
use Fanky\Admin\Models\Brand;
use Fanky\Admin\Models\Page;
use S;
use View;

class BrandsController extends Controller {
	public array $bread = [];
	protected Page $brands_page;

	public function __construct() {
		$this->brands_page = Page::whereAlias('brands')
			->get()
			->first();
		$this->bread[] = [
			'url'  => $this->brands_page['url'],
			'name' => $this->brands_page['name']
		];
	}

	public function index() {
		$page = $this->brands_page;
        if (!$page) abort(404, 'Страница не найдена');

        $bread = $this->bread;
        $page->setSeo();

        $brands = Brand::public()
            ->orderBy('order')
            ->get();

        return view('brands.index', [
            'bread' => $bread,
            'brands' => $brands,
            'h1' => $page->getH1(),
        ]);
	}

    public function item($alias) {
        $item = Brand::whereAlias($alias)->public()->first();

        if (!$item) abort(404);

        Auth::init();
        if (Auth::user() && Auth::user()->isAdmin) {
            View::share('admin_edit_link', route('admin.brands.edit', [$item->id]));
        }

        if (count(request()->query())) {
            View::share('canonical', $this->brands_page->alias);
        }

        $item->setSeo();
        $item->ogGenerate();
        $bread = $this->bread;
        $bread[] = [
            'url' => $item->url,
            'name' => $item->name
        ];

        $products = $item->products()->paginate(S::get('brand_products_per_page', 12));

        return view('brands.item', [
            'bread' => $bread,
            'item'        => $item,
            'h1'          => $item->getH1(),
            'name'        => $item->name,
            'text'        => $item->text,
            'products'    => $products,
        ]);
    }
}
