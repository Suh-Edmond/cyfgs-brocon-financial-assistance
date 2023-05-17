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
            <label style="font-size: small;">{{ $organisation->telephone }}</label><br />
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
                <th style="padding: 3px; border: 1px solid black;width:5%">S/N</th>
                <th style="padding: 12px; border: 1px solid black; width:50%">Name</th>
                <th style="padding: 12px; border: 1px solid black;">Amount (FCFA)</th>
                <th style="padding: 12px; border: 1px solid black;">Complusory</th>
            </tr>
            @foreach ($payment_items as $key => $value)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px; width:3%">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $value->name }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($value->amount) }}</td>
                   @if ($value->complusory == 1)
                   <td style="border: 1px solid black; padding: 11px;">YES</td>
                   @else
                   <td style="border: 1px solid black; padding: 11px;">NO</td>
                   @endif
                </tr>
            @endforeach
        </table>
        <p> <label style="font-size: 15px; font-weight: bold">Total Amount:
        <span style="padding-left: 5px;">{{ number_format($total) }} </span><span style="padding-left: 5px;">FCFA</span> </label></p>
    </div>
    <div style="margin-top: 100px;">
        <div style="float: left;">
            <label for="organisation" style="font-weight: bold; text-transform: uppercase; font-size: small;">Financial
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
            {{ $president->name }}
        </label><br />
    </div>
@endsection
