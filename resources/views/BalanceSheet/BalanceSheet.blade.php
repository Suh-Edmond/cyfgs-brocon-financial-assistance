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
            <label style="font-size: small;">Printed date: {{ $date }}</label>
        </div>
    </div>

    <div style="margin-bottom: 2rem;border-bottom: 3px solid black;">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;">{{ $title }}
        </h3>
    </div>

    <?php $n=1 ?>
    <div style="margin-left: 3rem; margin-right: 3rem">
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Names</th>
                @foreach($columns as $colum)
                    <th style="padding: 12px; border: 1px solid black;">
                        {{$colum->code}}
                    </th>
                @endforeach
                <th style="padding: 12px; border: 1px solid black;">Expected</th>
                <th style="padding: 12px; border: 1px solid black;">Total</th>
                <th style="padding: 12px; border: 1px solid black;">Balance</th>
            </tr>
            @foreach ($contributions as $key => $value)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center">
                        {{ $value->member_info->name }}</td>
                    @foreach($value->contributions as $contribution)
                        <td style="border: 1px solid black; padding: 3px; text-align: center">
                            {{ number_format($contribution->amount_deposited) }}</td>
                    @endforeach
                    <td style="border: 1px solid black; padding: 3px; text-align: center">
                        {{ number_format($value->expected_amount) }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center">
                        {{ number_format($value->total_contribution) }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center">
                        {{ number_format($value->total_balance) }}</td>
                    @if ( $n % 25 == 0 )
                        <div style="page-break-before:always;page-break-inside: auto;"> </div>
                    @endif
                        <?php $n++ ?>
                </tr>
            @endforeach
            <tr style="padding: 12px; border: 1px solid black; font-size: smaller">
                <td style="border: 1px solid black;text-align: center; padding: 1px;font-weight: bold;font-size: .8rem" colspan="{{$col_span}}">Tally
                </td>
                @foreach($columns as $column)
                    <td style="padding: 3px;font-weight: bold;border: 1px solid black; text-align: center;font-size: .8rem">
                        {{ number_format($column->total_amount_deposited)  }}
                    </td>
                @endforeach

                <td style="padding: 3px;font-weight: bold;border: 1px solid black; text-align: center;font-size: .8rem">{{ number_format($yearly_expected) }}   </td>
                <td style="padding: 3px;font-weight: bold;border: 1px solid black; text-align: center;font-size: .8rem">{{ number_format($yearly_total) }}   </td>
                <td style="padding: 3px;font-weight: bold;text-align: center;font-size: .8rem">{{ number_format($year_balance) }}   </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px;border-bottom: 1px solid black; width: 15%; margin-left: 43rem">
        <h5 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;">{{ $column_title }}
        </h5>
    </div>
    <div style="margin: 10px 3rem 50px;">
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 3px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">Code</th>
                <th style="padding: 3px; border: 1px solid black;">Names</th>
                <th style="padding: 3px; border: 1px solid black;">Member size</th>
                <th style="padding: 3px; border: 1px solid black;">Compulsory</th>
                <th style="padding: 3px; border: 1px solid black;">Type</th>
                <th style="padding: 3px; border: 1px solid black;">Frequency</th>
                <th style="padding: 3px; border: 1px solid black;">Amount</th>
                <th style="padding: 3px; border: 1px solid black;">Expected Amount</th>
                <th style="padding: 3px; border: 1px solid black;">Amount Deposited</th>
                <th style="padding: 3px; border: 1px solid black;">Balance</th>
                <th style="padding: 3px; border: 1px solid black;">Payment Durations</th>
            </tr>
            @foreach($columns as $colum)
            <tr style="border: 1px solid black; font-size: smaller">
                <td style="padding: 3px; border: 1px solid black;">
                    {{$colum->code}}
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    {{$colum->name}}
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    {{$colum->member_size}}
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    @if($colum->compulsory)
                        YES
                    @else
                        NO
                    @endif
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    {{$colum->type}}
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    {{$colum->frequency}}
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    {{number_format($colum->amount)}} XAF
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    {{\App\Constants\Constants::SAVINGS == $colum->type ? 0 : number_format($colum->total_expect_amount)}} XAF
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    {{\App\Constants\Constants::SAVINGS == $colum->type ? 0 : number_format($colum->total_amount_deposited)}}  XAF
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    {{\App\Constants\Constants::SAVINGS == $colum->type ? 0 : number_format(($colum->total_expect_amount - $colum->total_amount_deposited))}} XAF
                </td>
                <td style="padding: 3px; border: 1px solid black;">
                    @foreach($colum->payment_durations as $duration)
                        <span><small>{{$duration}}</small></span>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </table>
    </div>


    <div style="margin-top: 30px;border-bottom: 1px solid black; width: 15%; margin-left: 43rem">
        <h5 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;">Summary
        </h5>
    </div>
    <div style="margin: 10px 3rem 50px;">
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 3px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 4px; border: 1px solid black;">Total Expected Contribution</th>
                <th style="padding: 4px; border: 1px solid black;">Total Amount Contribution</th>
                <th style="padding: 4px; border: 1px solid black;">Total Balance</th>
            </tr>
            <tr style="border: 1px solid black; font-size: smaller">
                <td style="padding: 4px; border: 1px solid black;">
                    S1
                </td>
                <td style="padding: 4px;font-weight: bold;border: 1px solid black; text-align: center">{{ number_format($yearly_expected) }} XAF </td>
                <td style="padding: 4px;font-weight: bold;border: 1px solid black; text-align: center">{{ number_format($yearly_total) }} XAF </td>
                <td style="padding: 4px;font-weight: bold;text-align: center">{{ number_format($year_balance) }} XAF </td>
            </tr>

        </table>
    </div>



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
           <div class="border_line" style="border-bottom: 1px solid black; margin-top: 10px;margin-left:35rem;text-align: center">
            </div>
            
        </div>
    </div>
    <!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection
