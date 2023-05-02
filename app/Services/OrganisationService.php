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

        $user = User::findOrFail(Auth::user()['id']);

        $organisation = Organisation::find($request->id);

        if (is_null($organisation)) {
            $saved = Organisation::create([
                'name'             => $request->name,
                'email'            => $request->email,
                'telephone'        => $request->telephone,
                'description'      => $request->description,
                'address'          => $request->address,
                'salutation'       => $request->salutation,
                'box_number'       => $request->box_number,
                'region'           => $request->region,
                'updated_by'       => $request->user()->name,
            ]);
            $user->update([
                'organisation_id' => $saved->id,
                'updated_by'      => $request->user()->name
            ]);
        } else {
            $organisation->name             = $request->name;
            $organisation->email            = $request->email;
            $organisation->telephone        = $request->telephone;
            $organisation->description      = $request->description;
            $organisation->address          = $request->address;
            $organisation->salutation       = $request->salutation;
            $organisation->box_number       = $request->box_number;
            $organisation->updated_by       = $request->user()->name;

            $organisation->save();
        }
    }

    public function getOrganisation($id)
    {
        return Organisation::findOrFail($id);
    }

    public function getOrganisationInfo()
    {
        $id = null;
        if(!is_null(Auth::user()->organisation)){
            $id = Auth::user()->organisation->id;
        }
        return Organisation::findOrFail($id);
    }

    public function updatedOrganisation($request, $id)
    {
        Organisation::findOrFail($id)->update([
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
