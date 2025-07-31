<?php
namespace App\Http\Controllers;

use App\Classes\SiteHelper;
use Cache;
use Doctrine\DBAL\Query\QueryBuilder;
use Fanky\Admin\Models\Catalog;
use Fanky\Admin\Models\Page;
use Fanky\Admin\Models\Product;
use Fanky\Admin\Models\SearchIndex;
use Fanky\Admin\Settings;
use Fanky\Auth\Auth;
use S;
use SEOMeta;
use Session;
use Request;
use View;

class CatalogController extends Controller
{

    public function index()
    {
        $page = Page::where('alias', 'catalog')->first();
        if (!$page) {
            return abort(404);
        }
        $bread = $page->getBread();
        $page->h1 = $page->getH1();
        $page->setSeo();

        $categories = Catalog::public()
            ->where('parent_id', 0)
            ->with(['public_children'])
            ->orderBy('order')
            ->get();

        return view('catalog.index', [
            'h1' => $page->h1,
            'text' => $page->text,
            'bread' => $bread,
            'categories' => $categories
        ]);
    }

    public function view($alias)
    {
        $path = explode('/', $alias);
        /* проверка на продукт в категории */
        $product = null;
        $end = array_pop($path);
        $category = Catalog::getByPath($path);
        if ($category && $category->published) {
            $product = Product::whereAlias($end)
                ->public()
                ->first();
        }
        if ($product) {
            return $this->product($product, $category);
        } else {
            array_push($path, $end);
            return $this->category($path + [$end]);
        }
    }

    public function category($path)
    {
        /** @var Catalog $category */
        $category = Catalog::getByPath($path);
        if (!$category || !$category->published) {
            abort(404, 'Страница не найдена');
        }
        $bread = $category->getBread();
        $category->generateTitle();
        $category->generateDescription();
        $category = $this->add_region_seo($category);
        $category->setSeo();
        $category->ogGenerate();

        if (count(request()->query())) {
            View::share('canonical', $category->url);
        }

        Auth::init();
        if (Auth::user() && Auth::user()->isAdmin) {
            View::share('admin_edit_link', route('admin.catalog.catalogEdit', [$category->id]));
        }

        $data = [
            'bread' => $bread,
            'category' => $category,
            'text' => $category->text,
            'h1' => $category->getH1(),
            'products' => $category->products
        ];

        return view('catalog.category', $data);
    }

    public function product(Product $product, Catalog $category)
    {
        $bread = $product->getBread($category);
        $product->generateTitle();
        $product->generateDescription();
        $product = $this->add_region_seo($product);
        $product->setSeo();
        $product->ogGenerate();

        $images = $product->images;

        Auth::init();
        if (Auth::user() && Auth::user()->isAdmin) {
            View::share('admin_edit_link', route('admin.catalog.productEdit', [$product->id]));
        }

        return view('catalog.product', [
            'h1' => $product->getH1(),
            'product' => $product,
            'bread' => $bread,
            'images' => $images,
            'text' => $product->text,
            'params' => $product->params
        ]);
    }

    public function getChildrenIds(Catalog $category)
    {
        $children_ids = [];
        if (count($category->children)) {
            $children_ids = $category->getRecurseChildrenIds();
        }
        if (!in_array($category->id, $children_ids)) {
            $children_ids[] = $category->id;
        }

        return $children_ids;
    }
}
