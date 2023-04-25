<?php namespace Jakten\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class VehicleSegment
 *
 * @property int id
 * @property int vehicle_id
 * @property string name
 * @property string label
 * @property Vehicle vehicle
 * @property bool editable
 * @property bool bookable
 * @property bool comparable
 * @property int default_price
 * @property string default_comment
 * @property string description
 * @property string explanation
 * @property Carbon created_at
 * @property Carbon updated_at
 */
class VehicleSegment extends Model
{
    const RISK_ONE_CAR = 6;
    const RISK_TWO_CAR = 13;
    const INTRODUKTIONSKURS = 7;
    const THEORY_LESSON_CAR = 16;
    const PUBLIC_DIGITAL_COURSE_ID = 19;
    const DIGITAL_INTRODUKTIONSKURS_ENGLISH = 21;
    const DIGITAL_INTRODUKTIONSKURS_ARABISKA = 22;
    const DIGITAL_COURSE_ID = 25;
    const RISK_ONE_TWO_COMBO = 26;
    const ONLINE_LICENSE_THEORY = 32;
    const YKB_35_H = 33;
    const YKB_140_H = 34;

    const YKB = [35,36,37,38,39];
    const SHARED_COURSES = [self::DIGITAL_COURSE_ID, self::ONLINE_LICENSE_THEORY];

    //list, that school loyalty program discount must be applied
    //the same applied to top partner fee
    const LOYALTY_DISCOUNT_LIST = [17, 19, 13, 6, 7, 21, 25, 26, 27, 28, 30, 31];

    const MONTHLY_REPORTS_MONEY_TITLES = [
        'Introduktionskurs',
        'Digital_Introduktionskurs',
        'Digital_Introduktionskurs_kkj',
        'Introduction_Course_English',
        'Riskettan',
        'Riskettan_Arabiska',
        'Riskettan_Spanish',
        'Engelska_Riskettan',
        'Riskettan_MC',
        'Risktvåan',
        'Risktvåan_English',
        'Risktvåan_MC',
        'Risk_1_2_combo',
        'Risk_1_2_combo_English',
        'Körlektioner',
        'Körkortsteori_och_Testprov',
        'Moped_AM',
        'YKB_Grundkurs_140_h',
        'YKB_Fortutbildning_35_h',
        'Package',
        'Övrigt',
        'Bokningsavgift',
        'Summa_bokningar',
        'Bonus_saldo',
    ];

    /**
     * @var string $table
     */
    protected $table = 'vehicle_segments';

    /**
     * @var array $fillable
     */
    protected $fillable = [
        'vehicle_id', 'name', 'label', 'editable', 'bookable', 'comparable', 'default_price',
        'default_comment', 'description', 'sub_description', 'explanation', 'sub_explanation', 'title',
        'calendar_description', 'slug', 'color'
    ];

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * @var array $with
     */
    protected $with = ['vehicle'];

    /**
     * @var array $appends
     */
    protected $appends = ['label'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    public function availableCourses()
    {
        return $this->hasMany(Course::class)
            ->where('seats','>',0)
            ->where('start_time','>',Carbon::now()->format('Y-m-d H-i-s'));
    }

    /**
     * @param $value
     * @return array|\Illuminate\Contracts\Translation\Translator|null|string
     */
    public function getLabelAttribute($value)
    {
        return trans('vehicle_segments.' . strtolower($this->name));
    }
}
