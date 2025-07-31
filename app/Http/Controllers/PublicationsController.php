<?php namespace App\Http\Controllers;

use App;
use Fanky\Admin\Models\News;
use Fanky\Admin\Models\Page;
use Fanky\Admin\Models\Publication;
use Fanky\Auth\Auth;
use S;
use Settings;
use View;

class PublicationsController extends Controller {
	public $bread = [];
	protected $publications_page;

	public function __construct() {
		$this->publications_page = Page::whereAlias('publications')
			->get()
			->first();

		$this->bread[] = [
			'url'  => $this->publications_page['url'],
			'name' => $this->publications_page['name']
		];
	}

	public function index() {
		$page = $this->publications_page;
		if (!$page)
			abort(404, 'Страница не найдена');

		$bread = $this->bread;
        $page->ogGenerate();
        $page->setSeo();

        if (count(request()->query())) {
            View::share('canonical', $this->publications_page->alias);
        }

        $items = Publication::public()
            ->orderByDesc('date')
            ->paginate(S::get('pubs_per_page', 12));

        return view('publications.index', [
            'h1'    => $page->getH1(),
            'bread' => $bread,
            'items' => $items,
        ]);
	}

	public function item($alias) {
		$item = Publication::whereAlias($alias)->public()->first();
        if (!$item) abort(404);

        Auth::init();
        if (Auth::user() && Auth::user()->isAdmin) {
            View::share('admin_edit_link', route('admin.publications.edit', [$item->id]));
        }
        $bread = $this->bread;
        $bread[] = [
            'name' => $item->name,
            'url' => $item->url
        ];

        $item->setSeo();
        $item->ogGenerate();

		return view('publications.item', [
            'bread'       => $bread,
			'item'        => $item,
            'h1'          => $item->getH1(),
			'text'        => $item->text,
		]);
	}
}
