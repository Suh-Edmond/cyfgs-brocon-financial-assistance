<?php

namespace App\Imports;

use App\Models\CustomRole;
use App\Models\User;
use App\Traits\HelpTrait;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Services\RoleService;
use App\Constants\Roles;


class UsersImport implements ToModel
{
    use HelpTrait;

    private string $organisation_id;
    private RoleService $role_service;
    private string $updated_by;
    private  $assignRole;

    public function __construct($organisation_id,  RoleService $role_service, $updated_by, $assignRole)
    {
        $this->organisation_id = $organisation_id;
        $this->role_service = $role_service;
        $this->updated_by = $updated_by;
        $this->assignRole = $assignRole;
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
    * @return void
     */
    public function model(array $row)
    {
        $created = User::create([
            'name'            => $row[0],
            'email'           => $row[1],
            'telephone'       => str_replace(" ", "", $row[2]),
            'gender'          => $row[3],
            'address'         => $row[4],
            'occupation'      => $row[5],
            'organisation_id' => $this->organisation_id,
            'updated_by'      => $this->updated_by
        ]);
        $this->saveUserRole($created, $this->assignRole, $this->updated_by);
    }
}
