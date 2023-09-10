<?php

namespace App\Imports;

use App\Models\User;
use App\Traits\HelpTrait;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;


class UsersImport implements ToModel, WithStartRow, WithCustomCsvSettings
{
    use HelpTrait;

    private string $organisation_id;
    private string $updated_by;
    private  $assignRole;

    public function __construct($organisation_id, $updated_by, $assignRole)
    {
        $this->organisation_id = $organisation_id;
        $this->updated_by = $updated_by;
        $this->assignRole = $assignRole;
    }

    public function startRow(): int
    {
        return 2;
    }

    public function getCsvSettings():array
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
        $saved = User::create([
            'name'            => $row[0],
            'email'           => $row[1],
            'telephone'       => str_replace(" ", "", $row[2]),
            'gender'          => $row[3],
            'address'         => $row[4],
            'occupation'      => $row[5],
            'organisation_id' => $this->organisation_id,
            'updated_by'      => $this->updated_by,
            'password'        => ""
        ]);
        $this->saveUserRole($saved->id, $this->assignRole, $this->updated_by);
    }

    public function saveUserRole($user_id, $role, $updated_by)
    {
        DB::table('model_has_roles')->insert([
            'role_id'       => $role,
            'model_id'      => $user_id,
            'model_type'    => 'App\Models\User',
            'updated_by'    => $updated_by
        ]);
    }
}
