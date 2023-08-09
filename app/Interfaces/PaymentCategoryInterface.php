<?php
namespace App\Interfaces;

interface PaymentCategoryInterface {

    public function createPaymentCategory($request, $id);

    public function updatePaymentCategory($request, $id, $organisation_id);

    public function getPaymentCategories($organisation_id, $year);

    public function getPaymentCategory($id, $organisation_id);

    public function deletePaymentCategory($id, $organisation_id);
}
