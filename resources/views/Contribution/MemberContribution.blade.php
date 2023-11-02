@extends('layout.base')

@section('section')
    <div style="margin-bottom: 220px;">
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
    <div class="page-break">
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Quarter/Month</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Deposited(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Balance(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Payment Status</th>
                <th style="padding: 12px; border: 1px solid black;">Transaction Status</th>
                <th style="padding: 12px; border: 1px solid black;">Payment Date</th>
                <th style="padding: 12px; border: 1px solid black;">Transaction Code</th>
            </tr>
            @foreach ($contributions as $key => $contribution)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                   @if($contribution->payment_item_frequency == \App\Constants\PaymentItemFrequency::QUARTERLY)
                        <td style="border: 1px solid black; padding: 11px; text-align: center" >{{ $contribution->quarterly_name }}</td>
                    @elseif($contribution->payment_item_frequency == \App\Constants\PaymentItemFrequency::MONTHLY)
                        <td style="border: 1px solid black; padding: 11px; text-align: center" >{{ $contribution->month_name }}</td>
                    @else
                        <td style="border: 1px solid black; padding: 11px; text-align: center" >{{ $contribution->payment_item_frequency }}</td>
                    @endif
                    <td style="border: 1px solid black; padding: 11px; text-align: center">
                        {{ number_format($contribution->amount_deposited) }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center">
                        {{ number_format($contribution->balance) }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center" >{{ $contribution->status }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center" >{{ $contribution->approve }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center" >{{ date('d-m-Y', strtotime($contribution->created_at)) }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center" >{{ $contribution->code }}</td>
                </tr>
            @endforeach
        </table>
    </div>


    <!----------------------------------------------------------SUMMARY OF REPORT-------------------------------------------------------------------------------------------->
    <div style="margin-bottom: 20px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;text-decoration: underline"><span style="padding-right: 5px"></span> Summary:
        </h3>
    </div>
    <div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num_contribution">
                1
            </div>
            <div class="activity_summary_label">
                Payment Item
            </div>
            <div class="activity_summary_end">
                {{$payment_item_name}}
            </div>
        </div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num_contribution">
                2
            </div>
            <div class="activity_summary_label">
                Total Amount Payable
            </div>
            <div class="activity_summary_end">
                {{number_format($payment_item_amount)}}
            </div>
        </div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num_contribution">
                3
            </div>
            <div class="activity_summary_label">
                Payment Item Frequency
            </div>
            <div class="activity_summary_end">
                {{$payment_item_frequency}}
            </div>
        </div>
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num_contribution">
                4
            </div>
            <div class="activity_summary_label">
                Total Amount Contributed
            </div>
            <div class="activity_summary_end">
                {{number_format($total)}}
            </div>
        </div>
        @if($payment_item_frequency == \App\Constants\PaymentItemFrequency::QUARTERLY || $payment_item_frequency == \App\Constants\PaymentItemFrequency::MONTHLY)
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num_contribution">
                5
            </div>
            @if($payment_item_frequency == \App\Constants\PaymentItemFrequency::QUARTERLY)
            <div class="activity_summary_label">
                Unpaid Quarters
            </div>
            @elseif($payment_item_frequency == \App\Constants\PaymentItemFrequency::MONTHLY)
                <div class="activity_summary_label">
                    Unpaid Months
                </div>
            @endif
            <div class="activity_summary_end">
                @foreach($unpaid_durations as $key => $value)
                   [<span>{{$value}}</span>],
                @endforeach
            </div>
        </div>
        @endif
        <div class="row" style="border: 1px solid black">
            @if($payment_item_frequency == \App\Constants\PaymentItemFrequency::QUARTERLY || $payment_item_frequency == \App\Constants\PaymentItemFrequency::MONTHLY)
            <div class="activity_summary_num_contribution">
                6
            </div>
            @endif
            @if($payment_item_frequency == \App\Constants\PaymentItemFrequency::ONE_TIME || $payment_item_frequency == \App\Constants\PaymentItemFrequency::YEARLY)
                5
                @endif
            <div class="activity_summary_label">
                Total Balance
            </div>
            <div class="activity_summary_end">
                {{number_format($balance)}}
            </div>
        </div>
        @if($payment_item_frequency == \App\Constants\PaymentItemFrequency::QUARTERLY)
        <div class="row" style="margin-top: 20px">
            <label style="font-style: italic; color: lightskyblue;font-weight: bold">
                NB: For Quarterly Contributions, Total Amount Payable equals the Payment Item Amount multiply by
                the SUM of the number of Quarters from when the Payment Item was created. </label>
        </div>
        @elseif($payment_item_frequency == \App\Constants\PaymentItemFrequency::MONTHLY)
        <div class="row" style="margin-top: 20px">
            <label style="font-style: italic; color: lightskyblue;font-weight: bold">
                NB: For Monthly Contributions, Total Amount Payable equals the Payment Item Amount multiply by the SUM of the number of Months from when the Payment Item was created. </label>
        </div>
        @endif
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

