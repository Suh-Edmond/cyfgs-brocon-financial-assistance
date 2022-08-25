@extends('layout.base')

@section('section')

<div class="row">
    <div class="col-md-3">
        {{-- <h2>{{organisation->name}}</h2>
        <h2>{{organisation->salution}}</h2>
        <h2>{{organisation->telephone}}</h2>
        <h2>{{organisation->email}}</h2> --}}
        <h5>
            <label class="p-2 h4">Brother's Corner CYF Great Soppo</label><br>
            <label class="p-2">Brothers... In Christ</label><br>
            <label class="p-2">679340191</label><br>
            <label class="p-2">email@gmail.com</label><br>
            <button class="btn btn-primary">fs</button>
        </h5>
    </div>
    <div class="col-md-3">

    </div>
    <div class="col-md-3">
        {{-- <h2>{{organisation->address}}</h2>
        <h2>{{organisation->box_number}}</h2>
        <h2>{{organisation->region}}</h2>
        <h3>{{date}}</h3> --}}
        <h6>
            <label for="address" class="p-2">Street 4, Great Soppo</label><br>
            <label for="box_number" class="p-2">P.O Box 234</label><br>
            <label for="region" class="p-2">South West Region</label><br>
            <label for="date" class="p-2">{{$date}}</label>
        </h6>
    </div>
</div>
@endsection
