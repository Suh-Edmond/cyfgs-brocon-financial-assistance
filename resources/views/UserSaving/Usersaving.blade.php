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
            <label style="font-size: small;">P.O Box {{ $organisation->box_number }}</label><br />
            <label style="font-size: small;">{{ $organisation->address }}</label><br />
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
                <th style="padding: 12px; border: 1px solid black;">Telephone</th>
                <th style="padding: 12px; border: 1px solid black;">Amount (F CFA)</th>
            </tr>
            @foreach ($user_savings as $key => $user_saving)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $user_saving->user->name }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $user_saving->user->telephone }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($user_saving->amount_deposited) }}
                    </td>
                </tr>
            @endforeach
        </table>
        <p> <label style="font-size: 15px; font-weight: bold">Total Amount:
                <span style="padding-left: 5px;">{{ number_format($total) }} </span><span
                    style="padding-left: 5px;">FCFA</span> </label></p>
    </div>
    <div style="margin-top: 100px;">
        <div style="float: left;">
            <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">Financial
                Secretary <br />
                {{ $fin_secretary->name }}
            </label><br /><br />
        </div>
        <div style="float: right">
            <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">Treasurer
                <br />
                {{ $treasurer->name }}
            </label><br /><br />
        </div>
    </div>
    <div style="text-align:center; margin-top:80px">
        <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">President <br />
            {{ $president->name }}
        </label><br />
    </div>
@endsection
