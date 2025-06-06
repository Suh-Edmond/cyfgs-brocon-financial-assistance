@extends('layout.base')

@section('section')
    <div style="margin-bottom:30px;">
        <div class="column_100" style="margin-left: 30px">
            <div class="column_25">
                <img src="{{public_path("/images/pcc_logo.png")}}" alt="pcc logo" width="100px;" height="100px;"
                     style="border-radius: 2px">
            </div>
            <div class="column_50" style="text-align: center;">
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    CHRISTIAN YOUTH FELLOWSHIP (C.Y.F)</label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    FAKO NORTH PRESBYTERY</label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    BUEA ZONE</label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                CHRISTIAN YOUTH FELLOWSHIP - GREAT SOPPO </label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    {{ $organisation->name }} - {{ $organisation->address }}</label><br />
            </div>
            <div class="column_25">
                <img src="{{public_path($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;" style="border-radius: 2px">
            </div>
        </div>
        <div class="column_100" style="margin-left: 30px;margin-top: 20px">
            <div class="column_10">
            </div>
            <div class="column_25">
                <label style="font-weight: bold; text-transform: uppercase; font-size: small;">P.O Box {{ $organisation->box_number }}, {{ $organisation->address }}</label><br />
                <label style="font-size: small;font-weight: bold">Email: {{ $organisation->email }}</label><br />
            </div>
            <div class="column_25">

            </div>
            <div class="column_10">
                <label style="font-weight: bold; text-transform: uppercase; font-size: small;margin-right: 10rem;">Mobile:
                </label>
            </div>
            <div class="column_30">
                <ul>
                    @foreach($organisation_telephone as $phone)
                        <li style="font-size: small;font-weight: bold;list-style-type: none;">{{ $phone }}</li><br>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
    <hr style="border-bottom: 5px solid #213c65;margin-top: 2rem"/>
    <div class="column_100" style="margin-bottom: 3.5rem" >
        <div class="column_90">

        </div>
        <div class="column_10">
            <label style="font-size: small;">Printed date {{ $date }}</label>
        </div>
    </div>
     <div style="margin-bottom: 2rem;border-bottom: 3px solid black;">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;">{{ $title }}
        </h3>
    </div>
    <?php $n=1 ?>
    <div>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Frequency</th>
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
                        <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ $contribution->quarterly_name }}</td>
                    @elseif($contribution->payment_item_frequency == \App\Constants\PaymentItemFrequency::MONTHLY)
                        <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ $contribution->month_name }}</td>
                    @else
                        <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ str_replace('_', ' ', $contribution->payment_item_frequency) }}</td>
                    @endif
                    <td style="border: 1px solid black; padding: 3px; text-align: center">
                        {{ number_format($contribution->amount_deposited) }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center">
                        {{ number_format($contribution->balance) }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ $contribution->status }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ $contribution->approve }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ date('d-m-Y', strtotime($contribution->created_at)) }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ $contribution->code }}</td>

                    @if ( $n % 25 == 0 )
                        <div style="page-break-before:always;page-break-inside: auto;"> </div>
                    @endif
                    <?php $n++ ?>

                </tr>
            @endforeach
        </table>
    </div>


    <!----------------------------------------------------------SUMMARY OF REPORT-------------------------------------------------------------------------------------------->
    <div style="margin-top: 20px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;text-decoration: underline">
            <span style="padding-right: 5px;"></span> Summary
        </h3>
    </div>
    <div style="margin-top: 4px;">
        @if($paymentItem->frequency == \App\Constants\PaymentItemFrequency::MONTHLY || $paymentItem->frequency == \App\Constants\PaymentItemFrequency::QUARTERLY)
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S1
                </div>
                <div class="activity_summary">
                    Payment Frequency
                </div>
                <div class="activity_summary_end">
                    {{ str_replace('_', ' ', $paymentItem->frequency) }}
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S2
                </div>
                <div class="activity_summary">
                    Payment Type
                </div>
                <div class="activity_summary_end">
                    {{ str_replace('_', ' ', $paymentItem->type) }} 
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S3
                </div>
                <div class="activity_summary">
                    Member Size
                </div>
                <div class="activity_summary_end">
                    {{$member_size}}
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S4
                </div>
                <div class="activity_summary" >
                    Payment Durations
                </div>
                <div class="activity_summary_end">
                    @foreach($payment_durations as $duration)
                        <span><small>{{$duration}}</small></span>,
                    @endforeach
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S5
                </div>
                <div class="activity_summary" >
                    Unpaid Durations
                </div>
                <div class="activity_summary_end">
                    @foreach($unpaid_durations as $duration)
                        <span><small>{{$duration}}</small></span>,
                    @endforeach
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S6
                </div>
                <div class="activity_summary">
                    Payment Amount/Frequency
                </div>
                <div class="activity_summary_end">
                    {{number_format($paymentItem->amount)}} XAF
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S7
                </div>
                <div class="activity_summary">
                    Expected Contribution
                </div>
                <div class="activity_summary_end">
                    {{number_format($total_amount_payable)}} XAF
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S8
                </div>
                <div class="activity_summary">
                    Total Amount Contributed
                </div>
                <div class="activity_summary_end">
                    {{number_format($total)}} XAF
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S9
                </div>
                <div class="activity_summary">
                    Total Balance
                </div>
                <div class="activity_summary_end">
                    {{number_format($balance)}} XAF
                </div>
            </div>
        @else
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S1
                </div>
                <div class="activity_summary">
                    Payment Frequency
                </div>
                <div class="activity_summary_end">
                    {{$paymentItem->frequency}}
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S2
                </div>
                <div class="activity_summary">
                    Payment Type
                </div>
                <div class="activity_summary_end">
                    {{$paymentItem->type}}
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S3
                </div>
                <div class="activity_summary">
                    Member Size
                </div>
                <div class="activity_summary_end">
                    {{$member_size}}
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S4
                </div>
                <div class="activity_summary">
                    Payment Amount/Frequency
                </div>
                <div class="activity_summary_end">
                    {{number_format($paymentItem->amount)}} XAF
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S5
                </div>
                <div class="activity_summary">
                    Expected Contribution
                </div>
                <div class="activity_summary_end">
                    {{number_format($total_amount_payable)}} XAF
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S6
                </div>
                <div class="activity_summary">
                    Total Amount Contributed
                </div>
                <div class="activity_summary_end">
                    {{number_format($total)}} XAF
                </div>
            </div>
            <div class="row" style="border: 1px solid black">
                <div class="activity_summary_num">
                    S7
                </div>
                <div class="activity_summary">
                    Total Balance
                </div>
                <div class="activity_summary_end">
                    {{number_format($balance)}} XAF
                </div>
            </div>
        @endif

    </div>
    <!----------------------------------------------------------END OF SUMMARY OF REPORT------------------------------------------------------------------------------------->


    <!------------------------------------------------------DETAILS OF PRESENTERS--------------------------------------------------------------------------------------------->
    <div style="margin-top: 40px;margin-bottom: 50px">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;text-decoration: underline"><span style="padding-right: 5px"></span> Prepared By:
        </h3>
    </div>

    <div class="detail">
        <!------------------------------Names of presenters------------------------------------>
        <div style="float: left" class="fin_sec">
            <div class=" " style="font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 3px;text-align: center">
                FINANCIAL SECRETARY
            </div>
            <div style="font-weight: normal;font-size: small; text-transform: capitalize;text-align: center">
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
            <div style="font-weight: normal;text-transform: capitalize;text-align: center">
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
    <div class="president" style="text-align: center;margin-top: 40px">
        <div>
            <div class=" " style="font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 3px">
                President
            </div>
            <div style="font-weight: normal;font-size: small; text-transform: capitalize">
                @isset($president)
                    @foreach($president as $key => $value)
                        <span>{{$value->name}}</span><br>
                    @endforeach
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: small; margin-top: 20px">
                SIGN
            </div>
            <div class="border_line" style="border-bottom: 1px solid black; margin-top: 10px;margin-left:20rem;text-align: center">
            </div>
        </div>
    </div>
    <!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection

