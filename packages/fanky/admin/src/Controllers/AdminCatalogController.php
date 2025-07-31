<?php
namespace Fanky\Admin\Controllers;

use App\Exports\ProductsExport;
use App\Imports\ProductsImport;
use Carbon\Carbon;
use DB;
use Exception;
use Fanky\Admin\Models\AdminLog;
use Fanky\Admin\Models\Brand;
use Fanky\Admin\Models\Catalog;
use Fanky\Admin\Models\Product;
use Fanky\Admin\Models\ProductImage;
use Fanky\Admin\Models\ProductParam;
use Fanky\Admin\Pagination;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use Request;
use Text;
use Validator;

class AdminCatalogController extends AdminController
{

    public function getIndex()
    {
        $catalogs = Catalog::orderBy('order')->get();

        $last_update = Carbon::createFromTimestamp(0);
        $last_update_file = resource_path('.last_update');
        if (File::exists($last_update_file)) {
            $last_update = Carbon::createFromTimestamp(File::lastModified($last_update_file));
        }
        $content = view(
            'admin::catalog.index',
            [
                'last_update' => $last_update
            ]
        );

        return view('admin::catalog.main', [
            'catalogs' => $catalogs,
            'content' => $content
        ]);
    }

    public function postProducts($catalog_id)
    {
        $per_page = Request::get('per_page');
        if (!$per_page) {
            $per_page = session('per_page', 50);
        }
        $catalog = Catalog::findOrFail($catalog_id);
        $products = $catalog->products()    ;
        if ($q = Request::get('q')) {
            $products->where(
                function ($query) use ($q) {
                    $query->orWhere('name', 'LIKE', '%' . $q . '%')
                        ->orWhere('articul', 'LIKE', '%' . $q . '%');
                }
            );
        }
        $products = Pagination::init($products, $per_page)->get();
        $catalog_list = Catalog::getCatalogList();
        session(['per_page' => $per_page]);

        return view('admin::catalog.products', [
            'catalog' => $catalog,
            'products' => $products,
            'catalog_list' => $catalog_list,
        ]);
    }

    public function getProducts($catalog_id)
    {
        $catalogs = Catalog::orderBy('order')->get();

        return view('admin::catalog.main', [
            'catalogs' => $catalogs,
            'content' => $this->postProducts($catalog_id)
        ]);
    }

    public function getCatalogEdit($id = null)
    {
        $catalogs = Catalog::orderBy('order')->get();

        return view('admin::catalog.main', [
            'catalogs' => $catalogs,
            'content' => $this->postCatalogEdit($id)
        ]);
    }

    public function postCatalogEdit($id = null)
    {
        /** @var Catalog $catalog */
        if (!$id || !($catalog = Catalog::findOrFail($id))) {
            $catalog = new Catalog([
                'parent_id' => Request::get('parent'),
                'published' => 1
            ]);
        }
        $catalogs = Catalog::orderBy('order')
            ->where('id', '!=', $catalog->id)
            ->get();

        return view('admin::catalog.catalog_edit', [
            'catalog' => $catalog,
            'catalogs' => $catalogs,
        ]);
    }

    public function postCatalogSave(): array
    {
        $id = Request::input('id');
        $data = Request::except(['id']);
        if (!array_get($data, 'alias')) {
            $data['alias'] = Text::translit($data['name']);
        }
        if (!array_get($data, 'title')) {
            $data['title'] = $data['name'];
        }
        if (!array_get($data, 'h1')) {
            $data['h1'] = $data['name'];
        }
        if (!array_get($data, 'on_main')) {
            $data['on_main'] = 0;
        }
        if (!array_get($data, 'on_menu')) {
            $data['on_menu'] = 0;
        }
        if (!array_get($data, 'on_footer_menu')) {
            $data['on_footer_menu'] = 0;
        }
        $image = Request::file('image');
        $image_text_preview = Request::file('image_text_preview');

        // валидация данных
        $validator = Validator::make(
            $data,
            [
                'name' => 'required',
            ]
        );
        if ($validator->fails()) {
            return ['errors' => $validator->messages()];
        }
        // Загружаем изображение
        if ($image) {
            $file_name = Catalog::uploadImage($image);
            $data['image'] = $file_name;
        }
        // Загружаем превью текста
        if ($image_text_preview) {
            $file_prev_name = Catalog::uploadPreviewImage($image_text_preview);
            $data['image_text_preview'] = $file_prev_name;
        }

        // сохраняем страницу
        $catalog = Catalog::find($id);
        $redirect = false;
        if (!$catalog) {
            $data['order'] = Catalog::where('parent_id', $data['parent_id'])->max('order') + 1;
            $catalog = Catalog::create($data);
            $redirect = true;
        } else {
            $catalog->update($data);
        }

        if ($redirect) {
            return ['redirect' => route('admin.catalog.catalogEdit', [$catalog->id])];
        } else {
            return ['success' => true, 'msg' => 'Изменения сохранены'];
        }
    }

    /**
     * @throws Exception
     */
    public function postCatalogDelete($id): array
    {
        $catalog = Catalog::findOrFail($id);
        $catalog->delete();

        return ['success' => true];
    }

    public function postCatalogDeletePreview($id) {
        $catalog = Catalog::find($id);
        if(!$catalog) return ['success' => false, 'error' => 'Раздел не найден'];

        $catalog->deletePreviewImage();
        $catalog->update(['image_text_preview' => null]);

        return ['success' => true];
    }

    public function postCatalogReorder(): array
    {
        // изменение родителя
        $id = Request::input('id');
        $parent = Request::input('parent');
        DB::table('catalogs')->where('id', $id)->update(array('parent_id' => $parent));
        // сортировка
        $sorted = Request::input('sorted', []);
        foreach ($sorted as $order => $id) {
            DB::table('catalogs')->where('id', $id)->update(array('order' => $order));
        }

        return ['success' => true];
    }

    public function getProductEdit($id = null)
    {
        $catalogs = Catalog::orderBy('order')->get();

        return view('admin::catalog.main', [
            'catalogs' => $catalogs,
            'content' => $this->postProductEdit($id)
        ]);
    }

    public function postProductEdit($id = null)
    {
        $pinned_catalogs = [];
        /** @var Product $product */
        if (!$id || !($product = Product::findOrFail($id))) {
            $product = new Product([
                'catalog_id' => request()->get('catalog_id'),
                'published' => 1,
                'brand_id' => 0,
                'in_stock' => 1,
            ]);
        }

        $catalogs = Catalog::getCatalogList();

        $data = [
            'product' => $product,
            'catalogs' => $catalogs,
        ];
        return view('admin::catalog.product_edit', $data);
    }

    public function postProductSave(): array
    {
        $id = Request::get('id');
        $data = Request::except(['id', 'chars', 'additional_catalog', 'related']);

        if (!array_get($data, 'published')) {
            $data['published'] = 0;
        }
        if (!array_get($data, 'alias')) {
            $data['alias'] = Text::translit($data['name']);
        }
        if (!array_get($data, 'title')) {
            $data['title'] = $data['name'];
        }
        if (!array_get($data, 'h1')) {
            $data['h1'] = $data['name'];
        }

        $rules = [
            'name' => 'required'
        ];

        $rules['alias'] = $id
            ? 'required|unique:products,alias,' . $id . ',id'
            : 'required|unique:products,alias,null,id';
        // валидация данных
        $validator = Validator::make(
            $data,
            $rules
        );
        if ($validator->fails()) {
            return ['errors' => $validator->messages()];
        }
        $redirect = false;

        // сохраняем страницу
        $product = Product::find($id);

        if (!$product) {
            $product = Product::create($data);
            $redirect = true;
        } else {
            $product->update($data);
        }

        return $redirect
            ? ['redirect' => route('admin.catalog.productEdit', $product->id)]
            : ['success' => true, 'msg' => 'Изменения сохранены'];
    }

    public function postProductReorder(): array
    {
        $sorted = Request::input('sorted', []);
        foreach ($sorted as $order => $id) {
            DB::table('products')->where('id', $id)->update(array('order' => $order));
        }

        return ['success' => true];
    }

    public function postProductParamReorder(): array
    {
        $sorted = Request::input('sorted', []);
        foreach ($sorted as $order => $id) {
            DB::table('product_params')->where('id', $id)->update(array('order' => $order));
        }

        return ['success' => true];
    }

    public function postUpdateOrder($id): array
    {
        $order = Request::get('order');
        Product::whereId($id)->update(['order' => $order]);

        return ['success' => true];
    }

    public function postProductDelete($id) {
        $product = Product::findOrFail($id);
        foreach($product->images as $item) {
            $item->deleteImage();
            $item->delete();
        }
        $product->delete();

        return ['success' => true];
    }

    public function postProductImageUpload($product_id): array
    {
        $product = Product::findOrFail($product_id);
        $images = Request::file('images');
        $items = [];
        if ($images) {
            foreach ($images as $image) {
                $file_name = ProductImage::uploadImage($image);
                $order = ProductImage::where('product_id', $product_id)->max('order') + 1;
                $item = ProductImage::create(['product_id' => $product_id, 'image' => $file_name, 'order' => $order]);
                $items[] = $item;
            }
        }

        $html = '';
        foreach ($items as $item) {
            $html .= view('admin::catalog.product_image', ['image' => $item, 'active' => '']);
        }

        return ['html' => $html];
    }

    public function postProductImageOrder(): array
    {
        $sorted = Request::get('sorted', []);
        foreach ($sorted as $order => $id) {
            ProductImage::whereId($id)->update(['order' => $order]);
        }

        return ['success' => true];
    }

    public function postAddParam($product_id) {
        $product = Product::findOrFail($product_id);
        $data = Request::only(['name', 'value']);
        $valid = Validator::make($data, [
            'name' => 'required',
            //			'value' => 'required',
        ]);

        if($valid->fails()) {
            return ['errors' => $valid->messages()];
        } else {
            $param = ProductParam::create([
                'product_id' => $product->id,
                'catalog_id' => $product->catalog_id,
                'name'       => trim($data['name']),
                'value'      => trim($data['value']),
                'order'      => $product->params()->max('order') + 1
            ]);
            $row = view('admin::catalog.param_row', ['param' => $param])->render();

            return ['row' => $row];
        }
    }

    public function postDelParam($param_id) {
        $param = ProductParam::findOrFail($param_id);
        $param->delete();

        return ['success' => true];
    }

    public function postEditParam($param_id) {
        $param = ProductParam::findOrFail($param_id);

        return view('admin::catalog.param_edit', ['param' => $param])->render();
    }

    public function postSaveParam($param_id) {
        $param = ProductParam::findOrFail($param_id);
        $data = Request::only(['name', 'value']);

        $valid = Validator::make($data, [
            'name'  => 'required',
        ]);

        if(!$valid->fails()) {
            $param->fill($data);
            $param->save();
        }

        return view('admin::catalog.param_row', ['param' => $param])->render();
    }

    public function getProductCopy($id) {
        $orig_product = Product::findOrfail($id);

        $product_copy = clone($orig_product);
        $product_copy->id = 0;

        $catalogs = $this->getRecurseCatalog();
        $additional_catalogs = $orig_product->additional_catalogs;


        $content = view('admin::catalog.product_edit', [
            'product'            => $product_copy,
            'catalogs'           => $catalogs,
            'additional_catalogs' => $additional_catalogs,
            'related'            => $orig_product->related,
        ]);

        return view('admin::catalog.main', [
            'catalogs' => $catalogs,
            'content'  => $content,
        ]);
    }

    public function getRelatedFind() {
        $search = Request::input('query');
        if(!strlen($search)) return ['data' => []];
        $pubs = Product::where('name', 'like', '%' . $search . '%')
            ->get();
        $data = [];
        if(count($pubs)) {
            foreach($pubs as $item) {
                $data[] = ['id' => $item->id, 'name' => $item->name];
            }
        }

        return ['data' => $data];
    }

    public function postRelatedAttach() {
        $related_id = Request::get('pub_id');
        $pub = Product::find($related_id);
        $row = !$pub ? '' : view('admin::catalog.product_edit_tabs.related_row', ['relative_item' => $pub])->render();

        return ['row' => $row, 'success' => (bool)$pub];
    }

    /**
     * @throws Exception
     */
    public function postProductImageDelete($id): array
    {
        /** @var ProductImage $item */
        $item = ProductImage::findOrFail($id);
        $item->deleteImage();
        $item->delete();

        return ['success' => true];
    }

    public function getGetCatalogs($id = 0): array
    {
        $catalogs = Catalog::whereParentId($id)->with(['children'])->orderBy('order')->get();
        $result = [];
        foreach ($catalogs as $catalog) {
            $has_children = (bool)count($catalog->children);
            $result[] = [
                'id' => $catalog->id,
                'text' => $catalog->name,
                'children' => $has_children,
                'icon' => ($catalog->published) ? 'fa fa-eye text-green' : 'fa fa-eye-slash text-muted',
            ];
        }

        return $result;
    }

    public function postProductDocUpload($product_id): array
    {
        $docs = Request::file('docs');
        $items = [];
        if ($docs) {
            foreach ($docs as $doc) {
                $file_name = Document::uploadFile($doc);
                $order = Document::where('product_id', $product_id)->max('order') + 1;
                $item = Document::create(['product_id' => $product_id, 'src' => $file_name, 'order' => $order]);
                $items[] = $item;
            }
        }

        $html = '';
        foreach ($items as $item) {
            $html .= view('admin::catalog.product_doc', ['doc' => $item]);
        }

        return ['html' => $html];
    }

    public function postProductDocOrder(): array
    {
        $sorted = Request::get('sorted', []);
        foreach ($sorted as $order => $id) {
            Document::whereId($id)->update(['order' => $order]);
        }

        return ['success' => true];
    }

    public function postProductDocDelete($id): array
    {
        $item = Document::findOrFail($id);
        $item->deleteSrcFile();
        $item->delete();

        return ['success' => true];
    }

    public function postProductDocEdit($id)
    {
        $doc = Document::findOrFail($id);
        return view('admin::catalog.product_doc_edit', ['doc' => $doc]);
    }

    public function postProductDocDataSave($id)
    {
        $image = Document::findOrFail($id);
        $data = Request::only('name');
        $image->name = $data['name'];
        $image->save();
        return ['success' => true];
    }

    //export|import
    public function getExportFile()
    {
        return Excel::download(new ProductsExport(), 'mmo-list.xlsx');
    }

    public function postImportPrice()
    {
        $file = Request::file('price');
        $file_name = 'price.xlsx';
        $file->move(resource_path('/'), $file_name);
        AdminLog::add('Файл для обновления каталога загружен успешно. Ожидается обновление.');

        $catalogs = Catalog::orderBy('order')->get();
        $content = view('admin::catalog.upload_price')->render();
        return view('admin::catalog.main', ['catalogs' => $catalogs, 'content' => $content]);
    }

    public function postImportPriceNow()
    {
        $file = resource_path('price.xlsx');

        if (File::exists($file)) {
            $last_update = Carbon::createFromTimestamp(0);
            $last_update_file = resource_path('.last_update');
            if (File::exists($last_update_file)) {
                $last_update = Carbon::createFromTimestamp(File::get($last_update_file));
            }
            $file_modify = Carbon::createFromTimestamp(File::lastModified($file));
            if ($file_modify->greaterThan($last_update)) {
                AdminLog::$processLog = false;

                (new ProductsImport())->import($file);

                File::put($last_update_file, $file_modify->timestamp);

                $catalogs = Catalog::orderBy('order')->get();
                $content = view('admin::catalog.upload_price_done')->render();

                return view('admin::catalog.main', ['catalogs' => $catalogs, 'content' => $content]);
            }
        }
    }

    //search
    public function search()
    {
        $q = Request::get('q');
        if (!$q) {
            $products = [];
        } else {
            $products = Product::query()->where(
                function ($query) use ($q) {
                    $query->orWhere('name', 'LIKE', '%' . $q . '%');
                }
            )->with('catalog')->paginate(50)->appends(['q' => $q]);
        }
        $catalogs = Catalog::orderBy('order')->get();
        $catalog_list = Catalog::getCatalogList();
        $content = view(
            'admin::catalog.search',
            compact('catalogs', 'catalog_list', 'products')
        )->render();
        return view(
            'admin::catalog.main',
            compact('content', 'catalogs')
        );
    }

    //mass
    public function postMoveProducts()
    {
        $catalog_id = Request::get('catalog_id');
        $item_ids = Request::get('items', []);
        if ($item_ids && $catalog_id) {
            Product::whereIn('id', $item_ids)
                ->update(['catalog_id' => $catalog_id]);
        }

        return ['success' => true];
    }

    public function postDeleteProducts()
    {
        $item_ids = Request::get('items', []);
        if ($item_ids) {
            $products = Product::whereIn('id', $item_ids)->get();
            foreach ($products as $product) {
                $product->additional_catalogs()->detach();
                $product->delete();
            }
        }

        return ['success' => true];
    }

    public function postDeleteProductsImage()
    {
        $item_ids = Request::get('items', []);
        if ($item_ids) {
            $products = Product::whereIn('id', $item_ids)->get();
            foreach ($products as $product) {
                $images = $product->images;

                if ($images) {
                    foreach ($images as $image) {
                        $image->deleteImage();
                        $image->delete();
                    }
                }
            }
        }

        return ['success' => true];
    }

    public function postAddProductsImages()
    {
        $images = Request::file('mass_images');
        $ids = Request::get('product_ids');

//        \Debugbar::log($ids);

        if ($ids && $images) {
            foreach ($ids as $n => $id) {
                $product = Product::find($id);
                if ($product) {
                    if (count($product->images)) {
                        foreach ($product->images as $img) {
                            $order = $img->order + count($images);
                            $img->update(['order' => $order]);
                        }
                    }

                    foreach ($images as $i => $image) {
                        $file_name = ProductImage::uploadImage($image, count($ids) === $n + 1);
                        ProductImage::create(['product_id' => $product->id, 'image' => $file_name, 'order' => $i]);
                    }
                }
            }
        } else {
            return ['success' => false];
        }

        return ['success' => true];
    }
}
