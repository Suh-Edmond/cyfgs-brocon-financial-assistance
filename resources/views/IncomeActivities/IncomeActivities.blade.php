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
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Name</th>
                <th style="padding: 12px; border: 1px solid black;">Amount (FCFA)</th>
                <th style="padding: 12px; border: 1px solid black;">Date</th>
                <th style="padding: 12px; border: 1px solid black;">Venue</th>
            </tr>
            @foreach ($income_activities as $key => $income_activity)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $income_activity->name }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($income_activity->amount) }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $income_activity->date }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $income_activity->venue }}</td>
                </tr>
            @endforeach
            <tr style="padding: 12px; border: 1px solid black; font-size: smaller">
                <td style="padding: 15px; font-weight: 200" colspan="2"> Total Amount: {{ number_format($total) }} FCFA
                </td>
                <td style="padding: 15px;font-weight: 200"> </td>
                <td style="padding: 15px;font-weight: 200"> </td>
                <td style="padding: 15px;font-weight: 200"> </td>
            </tr>
        </table>
    </div>
    <div style="margin-top: 100px;">
        <div style="float: left;">
            @isset($fin_secretary)
            <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">Financial
                Secretary <br />
                {{ $fin_secretary->name }}
            </label><br /><br />
            @endisset
        </div>
        <div style="float: right">
            @isset($treasurer)
            <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">Treasurer
                <br />
                {{ $treasurer->name }}
            </label><br /><br />
            @endisset
        </div>
    </div>
    <div style="text-align:center; margin-top:80px">
        <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">President <br />
            {{ $president->name }}
        </label><br />
    </div>
@endsection
