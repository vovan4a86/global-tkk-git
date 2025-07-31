<?php

use Fanky\Admin\Controllers\AdminCatalogController;

Route::group(['namespace' => 'Fanky\Admin\Controllers', 'prefix' => 'admin', 'as' => 'admin'], function () {
	Route::any('/', ['uses' => 'AdminController@main']);
    Route::post('clear-cache', ['uses' => 'AdminController@postClearCache'])->name('.clear-cache');
    Route::post('update-search-index', ['uses' => 'AdminController@postUpdateSearchIndex'])->name('.update-search-index');

	Route::group(['as' => '.pages', 'prefix' => 'pages'], function () {
		$controller  = 'AdminPagesController@';
		Route::get('/', $controller . 'getIndex');
		Route::get('edit/{id?}', $controller . 'getEdit')
			->name('.edit');

		Route::post('edit/{id?}', $controller . 'postEdit')
			->name('.edit');

		Route::get('get-pages/{id?}', $controller . 'getGetPages')
			->name('.get_pages');

		Route::post('save', $controller . 'postSave')
			->name('.save');

		Route::post('reorder', $controller . 'postReorder')
			->name('.reorder');

		Route::post('delete/{id}', $controller . 'postDelete')
			->name('.del');

		Route::get('filemanager', [
			'as'   => '.filemanager',
			'uses' => $controller . 'getFileManager'
		]);

		Route::get('imagemanager', [
			'as'   => '.imagemanager',
			'uses' => $controller . 'getImageManager'
		]);

        Route::post('add-gost-file/{id}', [
            'as'   => '.addGostFile',
            'uses' => $controller . 'postAddGostFile'
        ]);

        Route::post('del-gost-file/{id}', [
            'as'   => '.delGostFile',
            'uses' => $controller . 'postDelGostFile'
        ]);

        Route::post('update-gost-file-order/{id}', [
            'as'   => '.updateGostFileOrder',
            'uses' => $controller . 'postUpdateGostFileOrder'
        ]);
	});

	Route::group(['as' => '.catalog', 'prefix' => 'catalog'], function () {
		$controller  = 'AdminCatalogController@';
		Route::get('/', [AdminCatalogController::class, 'getIndex']);

        Route::get('search', $controller . 'search')
            ->name('.search');

		Route::get('products/{id?}', $controller . 'getProducts')
			->name('.products');

		Route::post('catalog-edit/{id?}', $controller . 'postCatalogEdit')
			->name('.catalogEdit');

		Route::get('catalog-edit/{id?}', $controller . 'getCatalogEdit')
			->name('.catalogEdit');

		Route::post('catalog-save', $controller . 'postCatalogSave')
			->name('.catalogSave');

		Route::post('catalog-reorder', $controller . 'postCatalogReorder')
			->name('.catalogReorder');

		Route::post('catalog-delete/{id}', $controller . 'postCatalogDelete')
			->name('.catalogDel');

        Route::post('catalog-delete-preview/{id}', $controller . 'postCatalogDeletePreview')
            ->name('.delete-preview');

        Route::get('product-edit/{id?}', $controller . 'getProductEdit')
            ->name('.productEdit');

		Route::get('product-add-exist/{id?}', $controller . 'getProductAddExist')
			->name('.productAddExist');

		Route::post('product-save', $controller . 'postProductSave')
			->name('.productSave');

		Route::post('product-reorder', $controller . 'postProductReorder')
			->name('.productReorder');

		Route::post('update-order/{id}', $controller . 'postUpdateOrder')
			->name('.update-order');

        Route::post('product-delete/{id}', $controller . 'postProductDelete')
            ->name('.productDel');

		Route::post('product-image-upload/{id}', $controller . 'postProductImageUpload')
			->name('.productImageUpload');

		Route::post('product-image-delete/{id}', $controller . 'postProductImageDelete')
			->name('.productImageDel');

		Route::post('product-image-order', $controller . 'postProductImageOrder')
			->name('.productImageOrder');

		Route::get('get-catalogs/{id?}', $controller . 'getGetCatalogs')
			->name('.get_catalogs');

        //product params
        Route::post('product-param-order', $controller . 'postProductParamReorder')
            ->name('.productParamOrder');

        Route::post('add-param/{id}', [
            'as'   => '.add_param',
            'uses' => $controller . 'postAddParam'
        ]);
        Route::post('del-param/{id}', [
            'as'   => '.del_param',
            'uses' => $controller . 'postDelParam'
        ]);
        Route::post('edit-param/{id}', [
            'as'   => '.edit_param',
            'uses' => $controller . 'postEditParam'
        ]);
        Route::post('save-param/{id}', [
            'as'   => '.save_param',
            'uses' => $controller . 'postSaveParam'
        ]);

        //mass
        Route::post('move-products', [
            'as' => '.move-products',
            'uses' => $controller . 'postMoveProducts'
        ]);

        Route::post('delete-products', [
            'as' => '.delete-products',
            'uses' => $controller . 'postDeleteProducts'
        ]);

        //mass images
        Route::post('delete-products-image', [
            'as' => '.delete-products-image',
            'uses' => $controller . 'postDeleteProductsImage'
        ]);

        Route::post('add-products-images', [
            'as' => '.add-products-images',
            'uses' => $controller . 'postAddProductsImages'
        ]);
	});

    Route::group(['as' => '.news', 'prefix' => 'news'], function () {
        $controller = 'AdminNewsController@';
        Route::get('/', $controller . 'getIndex');

        Route::get('edit/{id?}', $controller . 'getEdit')
            ->name('.edit');

        Route::post('save', $controller . 'postSave')
            ->name('.save');

        Route::post('delete/{id}', $controller . 'postDelete')
            ->name('.delete');

        Route::post('delete-image/{id}', $controller . 'postDeleteImage')
            ->name('.delete-image');
    });

	Route::group(['as' => '.gallery', 'prefix' => 'gallery'], function () {
		$controller = 'AdminGalleryController@';
		Route::get('/', $controller . 'anyIndex');
		Route::post('gallery-save', $controller . 'postGallerySave')
			->name('.gallerySave');
		Route::post('gallery-edit/{id?}', $controller . 'postGalleryEdit')
			->name('.gallery_edit');
		Route::post('gallery-delete/{id}', $controller . 'postGalleryDelete')
			->name('.galleryDel');
		Route::any('items/{id}', $controller . 'anyItems')
			->name('.items');
		Route::post('image-upload/{id}', $controller . 'postImageUpload')
			->name('.imageUpload');
		Route::post('image-edit/{id}', $controller . 'postImageEdit')
			->name('.imageEdit');
		Route::post('image-data-save/{id}', $controller . 'postImageDataSave')
			->name('.imageDataSave');
		Route::post('image-del/{id}', $controller . 'postImageDelete')
			->name('.imageDel');
		Route::post('image-order', $controller . 'postImageOrder')
			->name('.order');
	});

	Route::group(['as' => '.settings', 'prefix' => 'settings'], function () {
		$controller = 'AdminSettingsController@';
		Route::get('/', $controller . 'getIndex');

		Route::get('group-items/{id?}', $controller . 'getGroupItems')
			->name('.groupItems');

		Route::post('group-save', $controller . 'postGroupSave')
			->name('.groupSave');

		Route::post('group-delete/{id}', $controller . 'postGroupDelete')
			->name('.groupDel');

		Route::post('clear-value/{id}', $controller . 'postClearValue')
			->name('.clearValue');

		Route::any('edit/{id?}', $controller . 'anyEditSetting')
			->name('.edit');

		Route::any('block-params', $controller . 'anyBlockParams')
			->name('.blockParams');

		Route::post('edit-setting-save', $controller . 'postEditSettingSave')
			->name('.editSave');

		Route::post('save', $controller . 'postSave')
			->name('.save');
	});

	Route::group(['as' => '.redirects', 'prefix' => 'redirects'], function () {
		$controller = 'AdminRedirectsController@';
		Route::get('/', $controller . 'getIndex');

		Route::get('edit/{id?}', $controller . 'getEdit')
			->name('.edit');

		Route::get('delete/{id}', $controller . 'getDelete')
			->name('.delete');

		Route::post('save', $controller . 'postSave')
			->name('.save');
	});

	Route::group(['as' => '.feedbacks', 'prefix' => 'feedbacks'], function () {
		$controller = 'AdminFeedbacksController@';
		Route::get('/', $controller . 'getIndex');

		Route::post('read/{id?}',$controller . 'postRead')
			->name('.read');
		Route::post('delete/{id?}', $controller . 'postDelete')
			->name('.del');
	});

	Route::group(['as' => '.users', 'prefix' => 'users'], function () {
		$controller = 'AdminUsersController@';
		Route::get('/', $controller . 'getIndex');

		Route::post('edit/{id?}', $controller . 'postEdit')
			->name('.edit');

		Route::post('save', $controller . 'postSave')
			->name('.save');

		Route::post('del/{id}', $controller . 'postDelete')
			->name('.del');
	});

	Route::group(['as' => '.cities', 'prefix' => 'cities'], function () {
		$controller = 'AdminCitiesController@';
		Route::get('/', $controller . 'getIndex');

		Route::get('edit/{id?}', $controller . 'getEdit')
			->name('.edit');

		Route::post('delete/{id}', $controller . 'postDelete')
			->name('.del');

		Route::post('save', $controller . 'postSave')
			->name('.save');

		Route::post('tree/{id?}', $controller . 'postTree')
			->name('.tree');
	});
});
