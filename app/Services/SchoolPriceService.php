<?php namespace Jakten\Services;

use Illuminate\Foundation\Http\FormRequest;
use Jakten\Models\{School, SchoolSegmentPrice};

/**
 * Class SchoolPriceService
 * @package Jakten\Services
 */
class SchoolPriceService
{
    /**
     * @var ModelService
     */
    private $modelService;

    /**
     * SchoolPriceService constructor.
     *
     * @param ModelService $modelService
     *
     * @internal param ModelMapper $modelMapper
     */
    public function __construct(ModelService $modelService)
    {
        $this->modelService = $modelService;
    }

    /**
     * @param SchoolSegmentPrice $price
     * @param FormRequest $request
     *
     * @return \Illuminate\Database\Eloquent\Model|Price
     */
    public function updatePrice(SchoolSegmentPrice $price, FormRequest $request)
    {
        $price = $this->modelService->updateModel($price, $request->all());
        $price->save();

        return $price;
    }

    /**
     * @param FormRequest $request
     * @param School $school
     *
     * @return \Illuminate\Database\Eloquent\Model|Price
     */
    public function storePrice(FormRequest $request, School $school = null)
    {
        $price = $this->modelService->createModel(SchoolSegmentPrice::class, $request->all());

        if ($school) {
            $price->school_id = $school->id;
        }

        $price->save();

        return $price;
    }
}
