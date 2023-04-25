<?php
namespace Jakten\Services\Payment\Klarna;

/**
 * Class KlarnaCartItem
 * @package Jakten\Services\Payment\Klarna
 */
class KlarnaCartItem
{
    /**
     * @var array
     */
    public $data;

    /**
     * KlarnaCartItem constructor.
     * @param array $data
     * @param bool $discount
     */
    public function __construct(array $data, bool $discount = false)
    {
        $this->data = $this->buildItem($data, $discount);
    }

    /**
     * @param array $data
     * @param bool $discount
     * @return array
     */
    private function buildItem(array $data, bool $discount)
    {
        $qty = $data['quantity'] ? $data['quantity'] : 1;
        return [
            'type'              => $discount ? 'discount' : 'digital',
            'reference'         => $data['reference'],
            'name'              => $data['name'],
            'quantity'          => $qty,
            'unit_price'        => $data['price'],
            'tax_rate'          => $discount ? 0 : 2500,
            "total_amount"      => $data['price'] * $qty,
            "total_tax_amount"  => $discount ? 0 : (int)($qty * $data['price'] * 0.2)
        ];
    }
}
