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
    <div style="margin-bottom: 20px">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: uppercase;text-decoration: underline">{{ $title }}
        </h3>
    </div>
    <div>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="border: 1px solid black; font-size: smaller;">
                <th style="border: 1px solid black;" style="width: 5%">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Name</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Given(FCFA)</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Given(FCFA)</th>
                <th style="padding: 12px; border: 1px solid black;">Balance</th>
            </tr>
            @foreach ($expenditure_details as $key => $value)
                <tr style="border: 1px solid black; font-size: medium">
                    <td style="padding: 10px">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ $value->name }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($value->amount_given) }}</td>
                    <td style="border: 1px solid black; padding: 11px;">{{ number_format($value->amount_spent) }}</td>
                    <td style="border: 1px solid black; padding: 11px;">
                        {{ number_format($value->amount_given - $value->amount_spent) }}</td>

                </tr>
            @endforeach
        </table>
    </div>

    <!----------------------------------------------------------SUMMARY OF REPORT-------------------------------------------------------------------------------------------->
    <div style="margin-top: 50px;margin-bottom: 10px">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;text-decoration: underline">
            <span style="padding-right: 5px"></span> Summary (F CFA):
        </h3>
    </div>
    <div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num">
                S1
            </div>
            <div class="activity_summary">
                Estimated Expenditure
            </div>
            <div class="activity_summary_end">
                {{number_format($item_amount)}}
            </div>
        </div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num">
                S2
            </div>
            <div class="activity_summary">
                Total Amount Given
            </div>
            <div class="activity_summary_end">
                {{number_format($total_amount_given)}}
            </div>
        </div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num">
                S3
            </div>
            <div class="activity_summary">
                Total Amount Spent
            </div>
            <div class="activity_summary_end">
                {{number_format($total_amount_spent)}}
            </div>
        </div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num">
                S4
            </div>
            <div class="activity_summary">
                Total Balance
            </div>
            <div class="activity_summary_end">
                {{number_format($balance)}}
            </div>
        </div>
    </div>
    <!----------------------------------------------------------END OF SUMMARY OF REPORT------------------------------------------------------------------------------------->


    <!------------------------------------------------------DETAILS OF PRESENTERS--------------------------------------------------------------------------------------------->
    <div style="margin-top: 40px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;text-decoration: underline"><span style="padding-right: 5px"></span> Prepared By:
        </h3>
    </div>
    <div class="detail" style="margin-top: 30px;margin-bottom: 150px">
        <!------------------------------Names of presenters------------------------------------>
        <div style="float: left" class="fin_sec">
            <div class=" " style="font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 5px;text-align: center">
                FINANCIAL SECRETARY
            </div>
            <div style="font-weight: bold;font-size: small; text-transform: uppercase;text-align: center">
                @isset($fin_secretary)
                    <span>{{$fin_secretary->name}}</span>
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: small; margin-top: 20px;text-align: center">
                SIGN
            </div>
            <div  style="border-bottom: 1px solid black; margin-top: 40px">
            </div>
        </div>

        <div style="float: right" class="treasurer">
            <div  class=" " style="text-align: center;font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 5px">
                Treasurer
            </div>
            <div style="font-weight: bold;text-transform: uppercase;text-align: center">
                @isset($treasurer)
                    <span>{{$treasurer->name}}</span>
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: small; margin-top: 20px;text-align: center">
                SIGN
            </div>
            <div  style="border-bottom: 1px solid black; margin-top: 40px">
            </div>
        </div>
        <!------------------------------End of presenters-------------------------------------->
    </div>
    <div class="president" style="text-align: center">
        <div>
            <div class=" " style="font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 5px">
                President
            </div>
            <div style="font-weight: bold;font-size: small; text-transform: uppercase">
                @isset($president)
                    <span>{{$president->name}}</span>
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: small; margin-top: 20px">
                SIGN
            </div>
            <div class="border_line" style="border-bottom: 1px solid black; margin-top: 40px;text-align: center">
            </div>
        </div>
    </div>
    <!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection
