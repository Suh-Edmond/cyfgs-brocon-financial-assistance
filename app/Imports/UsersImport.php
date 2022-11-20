<?php

namespace App\Imports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Services\RoleService;
use App\Constants\Roles;


class UsersImport implements ToModel
{
    private $organisation_id;
    private  $role_service;

    public function __construct($organisation_id,  RoleService $role_service)
    {
        $this->organisation_id = $organisation_id;
        $this->role_service = $role_service;
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
        $saved =  new User([
            'name'            => $row[0],
            'email'           => $row[1],
            'telephone'       => $row[2],
            'gender'          => $row[3],
            'address'         => $row[4],
            'occupation'      => $row[5],
            'organisation_id' => $this->organisation_id
        ]);

        $this->role_service->addUserRole($saved->id, Roles::USER);
    }
}
