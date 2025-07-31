<?php namespace Fanky\Admin\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model {

    protected $table = 'orders';

    protected $guarded = ['id'];

    const UPLOAD_PATH = '/public/uploads/orders/';
    const UPLOAD_URL  = '/uploads/orders/';

    public function dateFormat($format = 'd.m.Y')
    {
        if (!$this->created_at) return null;
        return date($format, strtotime($this->created_at));
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('count', 'price');
    }

    public function products_count()
    {
        $count = 0;
        foreach ($this->products as $p) {
            $count += $p->pivot->count;
        }
        return $count;
    }

    public function scopeNewOrder($query) {
        return $query->whereNew(1);
    }

}
