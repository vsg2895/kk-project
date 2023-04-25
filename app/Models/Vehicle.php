<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Jakten\Helpers\Vehicles;

/**
 * Class Vehicle
 *
 * @property int id
 * @property string label
 * @property string name
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class Vehicle extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'vehicles';

    /**
     * @var array $fillable
     */
    protected $fillable = ['id', 'name', 'label'];

    /**
     * @var array $appends
     */
    protected $appends = ['identifier', 'label'];

    /**
     * @param $value
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public function getLabelAttribute($value)
    {
        return trans('vehicles.' . strtolower($this->name));
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function segments()
    {
        return $this->hasMany(VehicleSegment::class);
    }

    /**
     * @param $value
     * @return string
     */
    public function getIdentifierAttribute($value)
    {
        switch ($this->name) {
            case Vehicles::TYPE_CAR:
                return 'B';
            case Vehicles::TYPE_MC:
                return 'A';
            case Vehicles::TYPE_MOPED:
                return 'AM';
            case Vehicles::TYPE_YKB:
                return 'YKB';
            default:
                return 'asdf';
        }
    }
}
