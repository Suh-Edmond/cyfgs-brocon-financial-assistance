<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;

class UsersImport implements ToModel
{
    private $organisation_id;

    public function __construct($organisation_id)
    {
        $this->organisation_id = $organisation_id;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSetting():array
    {
        return [
            'delimiter' => ';'
        ];
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new User([
            'name'            => $row[0],
            'email'           => $row[1],
            'telephone'       => $row[2],
            'gender'          => $row[3],
            'address'         => $row[4],
            'occupation'      => $row[5],
            'organisation_id' => $this->organisation_id
        ]);
    }
}
