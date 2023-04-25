<?php namespace Jakten\Formatters;

use Illuminate\Support\Str;
use Jakten\Helpers\Prices;
use Jakten\Models\{School, SchoolRating};

/**
 * Class SchoolFormatter
 * @package Jakten\Formatters
 */
class SchoolFormatter
{
    /**
     * @var School
     */
    private $school;

    /**
     * SchoolFormatter constructor.
     *
     * @param School $school
     */
    public function __construct(School $school)
    {
        $this->school = $school;
    }

    /**
     * Format prices
     *
     * @param null $vehicle
     * @return array
     */
    public function prices($vehicle = null)
    {
        $formatted = [];

        foreach ($this->school->prices as $price) {
            if (($vehicle && $price->segment->vehicle_id != $vehicle) || !$price->segment->comparable) {
                continue;
            }

            $calculated = $price->amount;
            if ($price->segment->name == Prices::DRIVING_LESSON_CAR && $price->quantity) {
                $perMinute = $calculated / $price->quantity;
                $calculated = round($perMinute * 600);
            } elseif ($price->segment->name == Prices::DRIVING_LESSON_MC && $price->quantity) {
                $perMinute = $calculated / $price->quantity;
                $calculated = round($perMinute * 400);
            }

            $formatted[$price->segment->name] = [
                'price' => is_null($calculated) ? null : round($calculated),
                'price_suffix' => $calculated ? round($calculated) . Prices::CURRENCY_SUFFIX : '-',
                'comment' => $price->comment,
                'label' => $price->segment->label,
                'vehicle_id' => $price->segment->vehicle_id,
                'quantity' => $price->quantity
            ];

            if ($price->segment->name == Prices::DRIVING_LESSON_CAR || $price->segment->name == Prices::DRIVING_LESSON_MC) {
                $formatted[$price->segment->name]['price_per_lesson'] = round($price->amount);
            }
        }

        return $formatted;
    }

    /**
     * @param int $amount
     * @return float|null
     */
    public function averageRating(int $amount = 5)
    {
        $count = $this->school->ratings->count();
        if ($count >= $amount) {
            $total = $this->school->ratings->sum(function (SchoolRating $rating) {
                return $rating->rating;
            });

            return floor($total / $count);
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function slug()
    {
        $slug = Str::slug($this->school->name) . '-' . $this->school->id;

        return $this->slugRemoveUnwantedLetters($slug);
    }

    /**
     * Remove "AB" abbreviation from slug, it can meets at first position, or elsewhere
     *
     * @param $slug string
     * @return string
     */
    protected function slugRemoveUnwantedLetters($slug)
    {
        $abbreviations = ['ab-', '-ab-', 'hb-', '-hb-'];
        $replace = ['', '-', '', '-'];

        return str_replace($abbreviations, $replace, $slug);
    }
}
