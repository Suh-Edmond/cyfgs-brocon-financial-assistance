@extends('layout.base')

@section('section')
    <div style="margin-bottom: 220px;">
        <div style="float: left;">
            <img src="{{ public_path('/images/eu_money.png') }}" alt="organisation logo" width="100px;" height="100px;"
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
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">User Name</th>
                <th style="padding: 12px; border: 1px solid black;">Total Amount Deposited (FCFA)</th>
                <th style="padding: 12px; border: 1px solid black;">Telephone</th>
            </tr>
            @foreach ($contributions as $key => $contribution)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $contribution->user_name }}</td>
                    <td style="border: 1px solid black; padding: 11px;">
                        {{ number_format($contribution->amount_deposited) }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $contribution->user_telephone }}</td>
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
                @isset($fin_secretary)
                {{ $fin_secretary->name }}
                @endisset
            </label><br /><br />
        </div>
        <div style="float: right">
            <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">Treasurer
                <br />
                @isset($treasurer)
                {{ $treasurer->name }}
                @endisset
            </label><br /><br />
        </div>
    </div>
    <div style="text-align:center; margin-top:80px">
        <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">President <br />
            @isset($president)
            {{ $president->name }}
            @endisset
        </label><br />
    </div>
@endsection
