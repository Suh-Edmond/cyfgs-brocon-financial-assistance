<?php

namespace App\Interfaces;

interface ActivitySupportInterface {
    public function createActivitySupport($request);

    public function  updateActivitySupport($id, $request);

    public function  getActivitySupportsByPaymentItem($id);

    public function getActivitySupport($id);

    public function deleteActivitySupport($id);

    public function filterActivitySupport($request);
}
