@extends('layout.base')

@section('section')
    <div style="margin-bottom: 220px;margin-top: 50px">
        <div style="float: left;">
            <img src="{{url($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;"
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
    <div style="margin-bottom: 2rem;">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;border-bottom: 1px solid black;">{{ $title }}
        </h3>
    </div>

    <!----------------------------------------------------------START OF INCOME------------------------------------------------------------------------------------------>
    <?php $n=1 ?>
    <div>
        <div style="margin-bottom: 10px;margin-top: 10px">
            <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px;"></span> Income (XAF)
            </h3>
        </div>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="border: 1px solid black; font-size: smaller;">
                <th style="border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Description</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Deposited(XAF)</th>
            </tr>
            @foreach ($incomes as $key => $value)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 3px;">I.{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ $value->name }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ number_format($value->amount) }}
                    </td>
                </tr>
                @if ( $n % 25 == 0 )
                    <div style="page-break-before:always;page-break-inside: auto;"> </div>
                @endif
                <?php $n++ ?>
            @endforeach
            <tr style="border: 1px solid black; font-size: smaller;">
                <td style="padding: 5px;font-weight: bold;"></td>
                <td style="padding: 5px;font-weight: bold;">Total</td>
                <td style="padding: 5px;font-weight: bold;border-left: 1px solid black">{{$total_income}}</td>
            </tr>
        </table>
    </div>
    <!----------------------------------------------------------END OF INCOME------------------------------------------------------------------------------------------>


    <!----------------------------------------------------------START OF EXPENDITURES------------------------------------------------------------------------------------------>
    <?php $n=1 ?>
    <div style="margin-top: 10px;" >
        <div style="margin-bottom: 20px;margin-top: 20px">
            <h5 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px;"></span>
                Expenditures/Disbursements
            </h5>
        </div>
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
                    <td style="padding: 3px;">E.{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ $value->name }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ number_format($value->amount_given) }}
                    </td>
                    <td style="border: 1px solid black; padding: 3px;">{{ number_format($value->amount_spent) }}
                    </td>
                    <td style="border: 1px solid black; padding: 3px;">{{ number_format($value->balance) }}
                    </td>
                </tr>

                @if ( $n % 25 == 0 )
                    <div style="page-break-before:always;page-break-inside: auto;"> </div>
                @endif
                <?php $n++ ?>
            @endforeach
            <tr style="border: 1px solid black; font-size: smaller;">
                <td style="padding: 5px;font-weight: bold;border-right: 1px solid black;"></td>
                <td style="padding: 5px;font-weight: bold;border-right: 1px solid black;">Total</td>
                <td style="padding: 5px;font-weight: bold;border-right: 1px solid black;">{{$total_amount_given}}</td>
                <td style="padding: 5px;font-weight: bold;border-right: 1px solid black;">{{$total_amount_spent}}</td>
                <td style="padding: 5px;font-weight: bold;">{{$balance}}</td>
            </tr>
        </table>
    </div>
    <!----------------------------------------------------------END OF EXPENDITURES------------------------------------------------------------------------------------------>

    <!----------------------------------------------------------SUMMARY OF REPORT-------------------------------------------------------------------------------------------->
    <div style="margin-top: 20px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;text-decoration: underline">
            <span style="padding-right: 5px"></span> Summary (XAF):
        </h3>
    </div>
    <div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num">
                S1
            </div>
            <div class="activity_summary">
                Total Income
            </div>
            <div class="activity_summary_end">
                {{number_format($total_income)}}
            </div>
        </div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num">
                S2
            </div>
            <div class="activity_summary">
                Total Expenditure
            </div>
            <div class="activity_summary_end">
                {{number_format($total_amount_spent)}}
            </div>
        </div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num">
                S3
            </div>
            <div class="activity_summary">
                Total Balance
            </div>
            <div class="activity_summary_end">
                {{number_format($total_balance)}}
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
            <div class=" " style="font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 3px;text-align: center">
                FINANCIAL SECRETARY
            </div>
            <div style="font-weight: bold;font-size: small; text-transform: uppercase;text-align: center">
                @isset($fin_secretary)
                    @foreach($fin_secretary as $key => $value)
                        <span>{{$value->name}}</span><br>
                    @endforeach
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: small; margin-top: 20px;text-align: center">
                SIGN
            </div>
            <div  style="border-bottom: 1px solid black; margin-top: 10px">
            </div>
        </div>

        <div style="float: right" class="treasurer">
            <div  class=" " style="text-align: center;font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 3px">
                Treasurer
            </div>
            <div style="font-weight: bold;text-transform: uppercase;text-align: center">
                @isset($treasurer)
                    @foreach($treasurer as $key => $value)
                        <span>{{$value->name}}</span><br>
                    @endforeach
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: small; margin-top: 20px;text-align: center">
                SIGN
            </div>
            <div  style="border-bottom: 1px solid black; margin-top: 10px">
            </div>
        </div>
        <!------------------------------End of presenters-------------------------------------->
    </div>
    <div class="president" style="text-align: center">
        <div>
            <div class=" " style="font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 3px">
                President
            </div>
            <div style="font-weight: bold;font-size: small; text-transform: uppercase">
                @isset($president)
                    @foreach($president as $key => $value)
                        <span>{{$value->name}}</span><br>
                    @endforeach
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: small; margin-top: 20px">
                SIGN
            </div>
            <div class="border_line" style="border-bottom: 1px solid black; margin-top: 10px;text-align: center">
            </div>
        </div>
    </div>
    <!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection
