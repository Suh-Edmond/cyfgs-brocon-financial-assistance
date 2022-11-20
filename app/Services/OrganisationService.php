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

        $organisation = Organisation::where('id', $request->id)->first();

        if (is_null($organisation)) {
            $saved = Organisation::create([
                'name'             => $request->name,
                'email'            => $request->email,
                'telephone'        => $request->telephone,
                'description'      => $request->description,
                'address'          => $request->address,
                'logo'             => $request->logo,
                'salutation'       => $request->salutation,
                'box_number'       => $request->box_number,
                'region'           => $request->region
            ]);
            $user->update(['organisation_id' => $saved->id]);
        } else {
            $organisation->name             = $request->name;
            $organisation->email            = $request->email;
            $organisation->telephone        = $request->telephone;
            $organisation->description      = $request->description;
            $organisation->address          = $request->address;
            $organisation->logo             = $request->logo;
            $organisation->salutation       = $request->salutation;
            $organisation->box_number       = $request->box_number;

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
        $saved = Organisation::findOrFail($id)->update([
            'name'             => $request->name,
            'email'            => $request->email,
            'telephone'        => $request->telephone,
            'description'      => $request->description,
            'address'          => $request->address,
            'logo'             => $request->logo,
           'box_number'        => $request->box_number,
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
