@extends('layout.base')

@section('section')
    <div style="margin-bottom:30px;">
        <div class="column_100" style="margin-left: 30px">
            <div class="column_25">
                <img src="{{public_path("/images/pcc_logo.png")}}" alt="organisation logo" width="100px;" height="100px;"
                     style="border-radius: 2px">
            </div>
            <div class="column_50" style="text-align: center;">
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    PRESBYTERIAN CHURCH IN CAMEROON (PCC)</label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    YOUTH WORK DEPARTMENT </label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    CHRISTIAN YOUTH FELLOWSHIP (C.Y.F)</label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    FAKO NORTH PRESBYTERY</label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    BUEA ZONE</label><br />
                <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                    {{ $organisation->name }} - {{ $organisation->address }}</label><br />
            </div>
            <div class="column_25" style="margin-left: 15rem">
                <img src="{{public_path($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;"
                     style="border-radius: 2px">
            </div>
        </div>
        <div class="column_100" style="margin-left: 30px">
            <div class="column_10">
            </div>
            <div class="column_25">
                <label style="font-weight: bold; text-transform: uppercase; font-size: small;">P.O Box {{ $organisation->box_number }}, {{ $organisation->address }}</label><br />
                <label style="font-size: small;font-weight: bold">Email: {{ $organisation->email }}</label><br />
            </div>
            <div class="column_35">

            </div>
            <div class="column_20">
                <div class="column_10">
                    <label style="font-weight: bold; text-transform: uppercase; font-size: small;margin-right: 10rem;">Mobile:
                    </label>
                </div>
                <div class="column_10">
                    <label>
                        <ul style="">
                            @foreach($organisation_telephone as $phone)
                                <li style="font-size: small;font-weight: bold;list-style-type: none;">{{ $phone }}</li>
                            @endforeach
                        </ul>
                    </label>
                </div>
            </div>
            <div class="column_10">
            </div>
        </div>
    </div>
    <hr style="border-bottom: 5px solid #c97a7e; margin-bottom: 4rem"/>

    <div style="margin-bottom: 2rem;">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;border-bottom: 3px solid black;">{{ $title }}
        </h3>
    </div>
    <?php $n=1 ?>
    <div>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="border: 1px solid black; font-size: smaller;">
                <th style="border: 1px solid black;width: 5%">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Name</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Given(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Given(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Balance(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Transaction Status</th>
            </tr>
            @foreach ($expenditure_details as $key => $value)
                <tr style="border: 1px solid black; font-size: medium">
                    <td style="padding: 3px">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ $value->name }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ number_format($value->amount_given) }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ number_format($value->amount_spent) }}</td>
                    <td style="border: 1px solid black; padding: 3px;">
                        {{ number_format($value->amount_given - $value->amount_spent) }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{($value->approve) }}</td>
                </tr>
                @if ( $n % 25 == 0 )
                    <div style="page-break-before:always;page-break-inside: auto;"> </div>
                @endif
                <?php $n++ ?>
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
        <div class="row" style="border: 1px solid black">
            <div class="activity_summary_num">
                S5
            </div>
            <div class="activity_summary">
                Net Balance
            </div>
            <div class="activity_summary_end">
                {{number_format($net_balance)}}
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
            <div class="border_line" style="border-bottom: 1px solid black; margin-top: 10px;margin-left:20rem;text-align: center">
            </div>
        </div>
    </div>
    <!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection
