<?php

namespace App\Exports;

use Fanky\Admin\Models\Product;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;


class ProductsExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        return view('exports.products', [
            'items' => Product::with(['brand', 'additional_catalogs', 'related'])->get()
        ]);
    }
}
