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
                    {{ $organisation->name }}</label><br />
                <label style="font-size: small;">{{ $organisation->salutation }}</label><br />
                <label style="font-size: small;">{{ $organisation->region }}, {{ $organisation->address }}, {{ $organisation->box_number }}</label><br />
                <label style="font-size: small;">Phone_No:<span style="font-size: small;">{{ $organisation_telephone }}</span></label><br />
                <label style="font-size: small;">Email: {{ $organisation->email }}</label><br />
                <label style="font-size: small;">Printed date: {{ $date }}</label>
            </div>
            <div class="column_25" style="margin-left: 40px">
                <img src="{{public_path($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;"
                     style="border-radius: 2px">
            </div>
        </div>
    </div>

    <div style="margin-bottom: 2rem;">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;border-bottom: 1px solid black;">{{ $title }}
        </h3>
    </div>

    <?php $n=1 ?>
    <div>
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
                        {{ number_format($column->total_amount_deposited)  }} XAF
                    </td>
                @endforeach

                <td style="padding: 3px;font-weight: bold;border: 1px solid black; text-align: center;font-size: .8rem">{{ number_format($yearly_expected) }} XAF </td>
                <td style="padding: 3px;font-weight: bold;border: 1px solid black; text-align: center;font-size: .8rem">{{ number_format($yearly_total) }} XAF </td>
                <td style="padding: 3px;font-weight: bold;text-align: center;font-size: .8rem">{{ number_format($year_balance) }} XAF </td>
            </tr>
        </table>
    </div>

    <div style="margin-top: 30px">
        <h5 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;border-bottom: 1px solid black;">{{ $column_title }}
        </h5>
    </div>
    <div style="margin-bottom: 50px;margin-top: 10px;">
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 3px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">Code</th>
                <th style="padding: 3px; border: 1px solid black;">Names</th>
                <th style="padding: 3px; border: 1px solid black;">Member size</th>
                <th style="padding: 3px; border: 1px solid black;">Compulsory</th>
                <th style="padding: 3px; border: 1px solid black;">Type</th>
                <th style="padding: 3px; border: 1px solid black;">Frequency</th>
                <th style="padding: 3px; border: 1px solid black;">Amount</th>
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
                    {{number_format($colum->amount)}}
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


    <div style="margin-top: 30px">
        <h5 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;border-bottom: 1px solid black;">Summary
        </h5>
    </div>
    <div style="margin-bottom: 50px;margin-top: 10px">
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
    <div style="margin-top: 40px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;text-decoration: underline"><span style="padding-right: 5px"></span> Prepared By
        </h3>
    </div>
    <div class="detail" style="margin-bottom: 200px">
        <!------------------------------Names of presenters------------------------------------>
        <div style="float: left;margin-top: 10px" class="fin_sec">
            <div class=" " style="font-weight: bold;font-size: .7rem;text-transform: uppercase; margin-bottom: 3px;text-align: center;">
                FINANCIAL SECRETARY
            </div>
            <div style="font-weight: bold;text-transform: uppercase;text-align: center;font-size: .7rem">
                @isset($fin_secretary)
                    @foreach($fin_secretary as $key => $value)
                        <span>{{$value->name}}</span><br>
                    @endforeach
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: .7rem; margin-top: 20px;text-align: center">
                SIGN
            </div>
            <div  style="border-bottom: 1px solid black; margin-top: 10px">
            </div>
        </div>

        <div style="float: right" class="treasurer">
            <div  class=" " style="text-align: center;font-weight: bold;font-size: .7rem;text-transform: uppercase; margin-bottom: 3px">
                Treasurer
            </div>
            <div style="font-weight: bold;text-transform: uppercase;text-align: center;font-size: .7rem">
                @isset($treasurer)
                    @foreach($treasurer as $key => $value)
                        <span>{{$value->name}}</span><br>
                    @endforeach
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: .7rem; margin-top: 20px;text-align: center">
                SIGN
            </div>
            <div  style="border-bottom: 1px solid black; margin-top: 10px">
            </div>
        </div>
        <!------------------------------End of presenters-------------------------------------->
    </div>
    <div class="president" style="text-align: center">
        <div>
            <div class=" " style="font-weight: bold;font-size: .7rem;text-transform: uppercase; margin-bottom: 3px">
                President
            </div>
            <div style="font-weight: bold;font-size: .7rem; text-transform: uppercase">
                @isset($president)
                    @foreach($president as $key => $value)
                        <span>{{$value->name}}</span><br>
                    @endforeach
                @endisset
            </div>
            <div style="font-weight: bold;text-transform: uppercase;font-size: .7rem; margin-top: 20px">
                SIGN
            </div>
            <div class="border_line" style="border-bottom: 1px solid black; margin-top: 10px;text-align: center">
            </div>
        </div>
    </div>
    <!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection
