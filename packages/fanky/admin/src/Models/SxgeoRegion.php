<?php namespace Fanky\Admin\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Fanky\Admin\Models\SxgeoRegion
 *
 * @property int $id
 * @property string $iso
 * @property string $country
 * @property string $name_ru
 * @property string $name_en
 * @property string $timezone
 * @property string $okato
 * @method static Builder|SxgeoRegion newModelQuery()
 * @method static Builder|SxgeoRegion newQuery()
 * @method static Builder|SxgeoRegion query()
 * @method static Builder|SxgeoRegion whereCountry($value)
 * @method static Builder|SxgeoRegion whereId($value)
 * @method static Builder|SxgeoRegion whereIso($value)
 * @method static Builder|SxgeoRegion whereNameEn($value)
 * @method static Builder|SxgeoRegion whereNameRu($value)
 * @method static Builder|SxgeoRegion whereOkato($value)
 * @method static Builder|SxgeoRegion whereTimezone($value)
 * @mixin \Eloquent
 */
class SxgeoRegion extends Model {

	protected $table = 'sxgeo_regions';

    public function order(): void
    {
        $this->hasOne(Order::class);
    }
}
