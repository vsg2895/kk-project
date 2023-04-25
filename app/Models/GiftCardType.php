<?php namespace Jakten\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

//TODO: Zlatan: This is currently not used as gift cards are statically declared, need to make it into a real model
/**
 * Class GiftCardType
 * @package Jakten\Models
 */
class GiftCardType extends Model
{
    /**
     * @constant VALUES
     */
    //promokkj(index(gift_card_type_id):19), NORR50(index:20), VÄST50(index:21), STHLM50(22), MALMÖ50(23), ÖREBRO50(24), UPPSALA50(25)
    //index 26 = 50 kr(Introduktionskurs gift), 27 = 75 (Körlektioner gift), 28 = 75(Riskettan), 29 = 100(Risktvåan), 30 = 175 (Risk 1&2 combo),
    //31 = 50 (Teori), 32 = 10%, MOPED100(33)
    const VALUES = [100, 200, 300, 400, 500, 600, 700, 800, 900, 1000, 1500, 2000, 2500, 3000, 5000, 7500, 10000, 15000, 50, 50, 50, 50, 50, 50, 50,
        50, 75, 75, 100, 175, 50, 10, 100];

    const AMOUNT_FOR_VIEW = [300, 400, 500, 600, 700, 800, 900, 1000, 1500, 2000, 2500, 3000, 5000, 7500, 10000, 15000];

    const PERCENT_TYPES = [100000, 100001];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function giftCards()
    {
        return $this->hasMany(GiftCard::class);
    }

    /**
     * @return array
     */
    public static function getGiftCardTypes()
    {
        $increasedValue = floatval(Cache::get('GIFT_CARD_INCREASED_VALUE', 1));

        $items = [];
        foreach (self::VALUES as $key => $value) {
            $items[] = [
                'id' => $key + 1,
                'price' => $value,
                'value' => $value * $increasedValue,
                'name' => config('klarna.gift_cart_name'),
                'valid_days' => 365
            ];
        }

        return $items;
    }

    /**
     * @return array
     */
    public static function getGiftCardTypesForView()
    {
        $increasedValue = floatval(Cache::get('GIFT_CARD_INCREASED_VALUE', 1));

        $items = [];
        foreach (self::AMOUNT_FOR_VIEW as $key => $value) {
            $items[] = [
                'id' => $key + 3,
                'price' => $value,
                'value' => $value * $increasedValue,
                'name' => config('klarna.gift_cart_name'),
                'valid_days' => 365
            ];
        }

        return $items;
    }

    /**
     * @param $id
     * @return array|mixed
     */
    public static function getDataById($id)
    {
        $giftCardData = [];
        $giftCardsData = static::getGiftCardTypes();

        foreach ($giftCardsData as $data) {
            if ($data['id'] == $id) {
                $giftCardData = $data;
                break;
            }
        }

        if ($id === 100000) {
            $giftCardData = [
                'id' => 100000 + 1,
                'price' => 11,
                'value' => 20 * floatval(Cache::get('GIFT_CARD_INCREASED_VALUE', 1)),
                'name' => config('klarna.promo_cart_name'),
                'valid_days' => 705
            ];
        }

        return $giftCardData;
    }
}
