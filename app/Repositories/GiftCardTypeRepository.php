<?php namespace Jakten\Repositories;

use Illuminate\Database\Eloquent\Model;
use Jakten\Models\GiftCardType;
use Jakten\Repositories\Contracts\GiftCardTypeRepositoryContract;

/**
 * Class GiftCardTypeRepository
 * @package Jakten\Repositories
 */
class GiftCardTypeRepository extends BaseRepository implements GiftCardTypeRepositoryContract
{
    /**
     * @return Model
     */
    protected function model()
    {
        return GiftCardType::class;
    }

    /**
     * @return array|mixed
     */
    public function getAll()
    {
        return GiftCardType::getGiftCardTypesForView();
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public function getById($id)
    {
        $returnVal = [];
        foreach (GiftCardType::getGiftCardTypes() as $cardType) {
            if ($cardType['id'] == $id) {
                $returnVal = $cardType;
                break;
            }
        }
        return $returnVal;
    }

    /**
     * @param array $ids
     * @return array|mixed
     */
    public function getByIds(array $ids)
    {
        $requestedGiftCardTypes = [];
        foreach (GiftCardType::getGiftCardTypes() as $giftCardTypeData) {
            foreach ($ids as $giftCardTypeId) {
                if (intval($giftCardTypeData['id']) == $giftCardTypeId) {
                    $requestedGiftCardTypes[] = $giftCardTypeData;
                    break;
                }
            }
        }

        return $requestedGiftCardTypes;
    }
}
