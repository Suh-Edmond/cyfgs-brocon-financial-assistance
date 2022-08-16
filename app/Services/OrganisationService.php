<?php

namespace App\Services;

use App\Interfaces\OrganisationInterface;
use App\Models\Organisation;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OrganisationService implements OrganisationInterface
{
    public function createOrganisation($request)
    {
        $user = User::findOrFail(Auth::user()->id);

        $saved = Organisation::create([
            'name'             => $request->name,
            'email'            => $request->email,
            'telephone'        => $request->telephone,
            'description'      => $request->description,
            'address'          => $request->address,
            'logo'             => $request->logo,
            'saluatation'      => $request->saluatation,
            'box_number'       => $request->box_number,
            'region'           => $request->region
        ]);

        $user->update(['organisation_id' => $saved->id]);

        return $saved->id;
    }

    public function getOrganisation($id)
    {
        return Organisation::findOrFail($id);
    }

    public function getOrganisationInfo()
    {
        $id = Auth::user()->organisation->id;
        return Organisation::findOrFail($id);
    }

    public function updatedOrganisation($request, $id)
    {
        $saved = Organisation::findOrFail($id)->update([
                    'name'             =>$request->name,
                    'email'            => $request->email,
                    'telephone'        => $request->telephone,
                    'description'      => $request->description,
                    'address'          => $request->address,
                    'logo'             => $request->logo,
        ]);
    }

    public function deleteOgranisation($id)
    {
        Organisation::findOrFail($id)->delete();
    }

    public function getOrganisations()
    {
        return Organisation::all();
    }
 }
