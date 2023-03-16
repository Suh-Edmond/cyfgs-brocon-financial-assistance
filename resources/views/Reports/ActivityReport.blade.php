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
{{--    imcome details--}}
    <div>
        <h3 style="font-weight: bold;font-size: small; text-align:left;text-transform: uppercase;">Income
        </h3>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="border: 1px solid black; font-size: smaller;">
                <th style="border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Description</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Deposited(XAF)</th>
            </tr>
            @foreach ($incomes as $key => $value)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $value->name }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($value->amount) }}
                    </td>
                </tr>
            @endforeach
            <tr style="border: 1px solid black; font-size: smaller;">
                <td style="padding: 5px;font-weight: bold;"></td>
                <td style="padding: 10px;font-weight: bold;">Total</td>
                <td style="padding: 10px;font-weight: bold;">{{$total_income}}</td>
            </tr>
        </table>
    </div>
{{--    expenditure details--}}
    <div style="margin-top: 20px">
        <h3 style="font-weight: bold;font-size: small; text-align:left;text-transform: uppercase;">Expenditures/Disbursements
        </h3>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="border: 1px solid black; font-size: smaller;">
                <th style="border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Description</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Given(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Spent(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Balance (XAF)</th>
            </tr>
            @foreach ($expenditures as $key => $value)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $value->name }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($value->amount_given) }}
                    </td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($value->amount_spent) }}
                    </td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($value->balance) }}
                    </td>
                </tr>
            @endforeach
            <tr style="border: 1px solid black; font-size: smaller;">
                <td style="padding: 5px;font-weight: bold;"></td>
                <td style="padding: 10px;font-weight: bold;">Total</td>
                <td style="padding: 10px;font-weight: bold;">{{$total_amount_given}}</td>
                <td style="padding: 10px;font-weight: bold;">{{$total_amount_spent}}</td>
                <td style="padding: 10px;font-weight: bold;">{{$balance}}</td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 10px">
        <h3 style="font-weight: bold;font-size: medium; text-align:left;text-transform: capitalize;">Net Balance (Total income - Actual expenditure + Total balance):
            <span style="padding-left: 20px;">{{number_format($net_balance)}} XAF</span>
        </h3>
    </div>

    <div style="margin-top: 40px;">
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
