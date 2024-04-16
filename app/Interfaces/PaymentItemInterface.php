<?php

namespace App\Interfaces;

interface PaymentItemInterface
{
    public function createPaymentItem($request, $paymant_category_id);

    public function updatePaymentItem($request, $payment_item_id, $payment_category_id);

    public function getPaymentItemsByCategory($payment_category_id, $request);

    public function getPaymentItem($id, $payment_category_id);

    public function deletePaymentItem($id, $payment_category_id);

    public function getPaymentItems();

    public function getPaymentItemByType($type);

    public function updatePaymentItemReference($request);

    public function getPaymentItemReferences($id);
}
