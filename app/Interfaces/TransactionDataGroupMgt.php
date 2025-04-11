<?php

namespace App\Interfaces;

use App\Models\TransactionHistory;

interface TransactionDataGroupMgt
{
 public function getTransactionData($id);

 public function saveTransactionData(TransactionHistory $transactionHistory, $updatedTransactionData);
}
