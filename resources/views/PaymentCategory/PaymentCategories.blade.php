@extends('layout.base')

@section('section')
    <div style="margin-bottom: 220px;">
        <div style="float: left;">
            <img src="{{ $organisation->logo }}" alt="organisation logo" width="100px;" height="100px;"
                style="border-radius: 2px">
        </div>
        <div style="float: right">
            <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                {{ $organisation->name }}</label><br />
            <label style="font-size: small;">{{ $organisation->salutation }}</label><br />
            <label style="font-size: small;">{{ $organisation->region }}, {{ $organisation->address }}, {{ $organisation->box_number }}</label><br />
            <label style="font-size: small;">Phone_No:</label><br />
            <label style="font-size: small;">{{ $organisation_telephone }}</label><br />
            <label style="font-size: small;">Email: {{ $organisation->email }}</label><br /><br />
            <label style="font-size: small;">Printed date: {{ $date }}</label>
        </div>
    </div>
    <div>
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: uppercase;">{{ $title }}
        </h3>
    </div>
    <div>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="border: 1px solid black; font-size: smaller;">
                <th style="border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Name</th>
            </tr>
            @foreach ($categories as $key => $value)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $value->name }}</td>
                </tr>
            @endforeach
        </table>
    </div>
    <div style="margin-top: 100px;">
        <div style="float: left;">
            <label for="organisation"style="font-weight: 400; text-transform: uppercase; font-size: smaller;">Financial
                Secretary <br />
                @if(!is_null($fin_secretary))
                    {{ $fin_secretary->name }}
                @endif
            </label><br /><br />
        </div>
        <div style="float: right">
            <label for="organisation"style="font-weight: 400; text-transform: uppercase; font-size: smaller;">Treasurer
                <br />
                @if(!is_null($treasurer))
                    {{ $treasurer->name }}
                @endif
            </label><br /><br />
        </div>
    </div>
    <div style="text-align:center; margin-top:80px">
        <label for="organisation"style="font-weight: 400; text-transform: uppercase; font-size: smaller;" >President <br />
            @if(!is_null($president))
                {{ $president->name }}
            @endif
        </label><br />
    </div>
@endsection
