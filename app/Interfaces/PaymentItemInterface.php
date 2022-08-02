<?php

namespace App\Interfaces;

interface PaymentItemInterface
{
    public function createPaymentItem($request, $paymant_category_id);

    public function updatePaymentItem($request, $payment_item_id, $paymant_category_id);

    public function getPaymentItemsByCategory($payment_category_id);

    public function getPaymentItem($id, $paymant_category_id);

    public function deletePaymentItem($id, $paymant_category_id);
}
