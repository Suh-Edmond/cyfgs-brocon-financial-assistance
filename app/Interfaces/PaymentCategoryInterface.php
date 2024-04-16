<?php
namespace App\Interfaces;

interface PaymentCategoryInterface {

    public function createPaymentCategory($request, $id);

    public function updatePaymentCategory($request, $id, $organisation_id);

    public function getPaymentCategories($request);

    public function getPaymentCategory($id, $organisation_id);

    public function deletePaymentCategory($id, $organisation_id);
}
