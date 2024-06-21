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
            <div class="column_25" style="margin-left: 15rem">
                <img src="{{public_path($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;"
                     style="border-radius: 2px">
            </div>
        </div>
    </div>

    <div style="margin-bottom: 2rem;border-bottom: 1px solid black; width: 25%; margin-left: 36rem">
        <h3 style="font-weight: bold;font-size: 1rem; text-align:center;text-transform: capitalize;">{{ $title }}
        </h3>
    </div>

    <div>
        <h6 style="font-weight: bold;font-size: .7rem; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px;"></span> Income
        </h6>
    </div>

    <?php $n=1 ?>
    <div style="margin-left: 3rem; margin-right: 3rem">
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding:5px; border: 1px solid black;">S/N</th>
                <th style="padding: 5px; border: 1px solid black;">Description/Activity
                </th>
                <th style="padding: 5px; border: 1px solid black;">Amount (XAF)</th>
                <th style="padding: 5px; border: 1px solid black;">SubTotal (XAF)</th>
                <th style="padding: 5px; border: 1px solid black;">Total (XAF)</th>
            </tr>
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <td style="padding:5px; border: 1px solid black;">BBF</td>
                <td style="padding: 5px; border: 1px solid black;"> Balance Brought-Forward
                </td>
                <td style="padding: 5px; border: 1px solid black;"></td>
                <td style="padding: 5px; border: 1px solid black;"></td>
                <td style="padding: 5px; border: 1px solid black;">{{number_format($bal_brought_forward)}}</td>
            </tr>
            @foreach($incomes as $key => $income)
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                    <td style="padding:5px; border: 1px solid black;"> {{$income->code}}
                    </td>
                    <td style="padding: 5px; border: 1px solid black;"> {{$income->name}}
                    </td>
                    <td style="padding: 5px; border: 1px solid black;"></td>
                    <td style="padding: 5px; border: 1px solid black;"></td>
                    <td style="padding: 5px; border: 1px solid black;">
                        @if(empty($income->items))
                            {{number_format($income->total)}}
                        @endif
                    </td>
            </tr>
            @foreach($income->items as $k => $value)
                    <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                        <td style="padding:5px; border: 1px solid black;"> {{$income->code }}.{{$value->code}}
                        </td>
                        <td style="padding: 5px; border: 1px solid black;"> {{$value->name}}
                        </td>
                        <td style="padding: 5px; border: 1px solid black;"></td>
                        <td style="padding: 5px; border: 1px solid black;"></td>
                        <td style="padding: 5px; border: 1px solid black;">
                        </td>
                    </tr>
                    @foreach($value->items as $v => $item)
                        <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                            <td style="padding:5px; border: 1px solid black;"> {{$income->code}}.{{$value->code}}.{{$v + 1}}
                            </td>
                            <td style="padding: 5px; border: 1px solid black;">
                                @if(($item->name) == $year || is_null($item->name))
                                    Member's Contributions
                                @endif
                                @if($item->name != $year)
                                    {{ $item->name }}
                                @endif

                            </td>
                            <td style="padding: 5px; border: 1px solid black;">{{number_format($item->amount)}}</td>
                            <td style="padding: 5px; border: 1px solid black;"></td>
                            <td style="padding: 5px; border: 1px solid black;">
                            </td>
                        </tr>
                    @endforeach
                    <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                        <td style="padding:5px; border: 1px solid black;"> {{$income->code }}.{{$value->code}}
                        </td>
                        <td style="padding: 5px; border: 1px solid black;">
                        </td>
                        <td style="padding: 5px; border: 1px solid black;"></td>
                        <td style="padding: 5px; border: 1px solid black;">{{number_format($value->total)}}</td>
                        <td style="padding: 5px; border: 1px solid black;">
                        </td>
                    </tr>
            @endforeach
                @if(!empty($income->items))
                    <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                        <td style="padding:5px; border: 1px solid black;"> {{$income->code }}
                        </td>
                        <td style="padding: 5px; border: 1px solid black;">
                            Total
                        </td>
                        <td style="padding: 5px; border: 1px solid black;"></td>
                        <td style="padding: 5px; border: 1px solid black;"></td>
                        <td style="padding: 5px; border: 1px solid black;">
                            {{number_format($income->total)}}
                        </td>
                    </tr>
                @endif
                @if ( $n % 25 == 0 )
                    <div style="page-break-before:always;page-break-inside: auto;"> </div>
                @endif
                <?php $n++ ?>
            @endforeach
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <td style="padding:5px; border: 1px solid black;">
                    /
                </td>
                <td style="padding: 5px; border: 1px solid black;">
                    Total Income
                </td>
                <td style="padding: 5px; border: 1px solid black;"></td>
                <td style="padding: 5px; border: 1px solid black;"></td>
                <td style="padding: 5px; border: 1px solid black;">
                    {{number_format($total_income)}}
                </td>
            </tr>
        </table>
    </div>



    <div style="margin-top: 3rem">
        <h6 style="font-weight: bold;font-size: .7rem; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px;"></span> Expenditures/Disbursements
        </h6>
    </div>

    <div style="margin-left: 3rem; margin-right: 3rem">
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding:5px; border: 1px solid black;">S/N</th>
                <th style="padding: 5px; border: 1px solid black;">Description/Activity
                </th>
                <th style="padding: 5px; border: 1px solid black;">Amount (XAF)</th>
                <th style="padding: 5px; border: 1px solid black;">SubTotal (XAF)</th>
                <th style="padding: 5px; border: 1px solid black;">Total (XAF)</th>
            </tr>
            <?php $p=1 ?>
            @foreach($expenditures as $key => $expenditure)
                <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                    <td style="padding:5px; border: 1px solid black;">  {{$expenditure->code}}
                    </td>
                    <td style="padding: 5px; border: 1px solid black;">  {{$expenditure->name}}
                    </td>
                    <td style="padding: 5px; border: 1px solid black;"></td>
                    <td style="padding: 5px; border: 1px solid black;"></td>
                    <td style="padding: 5px; border: 1px solid black;"></td>
                </tr>
                <?php $a=1 ?>
                @foreach($expenditure->items as $k => $value)
                    @if(count($value->items) > 0)
                        <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                            <td style="padding:5px; border: 1px solid black;">   {{$expenditure->code}}.{{$value->code}}
                            </td>
                            <td style="padding: 5px; border: 1px solid black;"> {{$value->name}}
                            </td>
                            <td style="padding: 5px; border: 1px solid black;"></td>
                            <td style="padding: 5px; border: 1px solid black;"></td>
                            <td style="padding: 5px; border: 1px solid black;"></td>
                        </tr>
                        @for($i = 0; $i < count($value->items); $i++)
                            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                                <td style="padding:5px; border: 1px solid black;">{{$expenditure->code}}.{{$k + 1}}.{{$i + 1}}
                                </td>
                                <td style="padding: 5px; border: 1px solid black;"> {{$value->items[$i]->name}}
                                </td>
                                <td style="padding: 5px; border: 1px solid black;">{{number_format($value->items[$i]->amount_spent)}}</td>
                                <td style="padding: 5px; border: 1px solid black;"></td>
                                <td style="padding: 5px; border: 1px solid black;"></td>
                            </tr>
                            @if ( $n % 25 == 0 )
                                <div style="page-break-before:always;page-break-inside: auto;"> </div>
                            @endif
                            <?php $n++ ?>
                        @endfor
                        <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                            <td style="padding:5px; border: 1px solid black;">  {{$expenditure->code}}.{{$value->code}}
                            </td>
                            <td style="padding: 5px; border: 1px solid black;">
                            </td>
                            <td style="padding: 5px; border: 1px solid black;"></td>
                            <td style="padding: 5px; border: 1px solid black;">{{$value->total}}</td>
                            <td style="padding: 5px; border: 1px solid black;"></td>
                        </tr>
                        @if ( $a % 25 == 0 )
                            <div style="page-break-before:always;page-break-inside: auto;"> </div>
                        @endif
                        <?php $a++ ?>
                    @endif
                @endforeach
                <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                    <td style="padding:5px; border: 1px solid black;">  {{$expenditure->code}}
                    </td>
                    <td style="padding: 5px; border: 1px solid black;">
                    </td>
                    <td style="padding: 5px; border: 1px solid black;"></td>
                    <td style="padding: 5px; border: 1px solid black;"></td>
                    <td style="padding: 5px; border: 1px solid black;">{{number_format($expenditure->total)}}</td>
                </tr>
                @if ( $p % 25 == 0 )
                    <div style="page-break-before:always;page-break-inside: auto;"> </div>
                @endif
                <?php $p++ ?>
            @endforeach
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <td style="padding:5px; border: 1px solid black;">  /
                </td>
                <td style="padding: 5px; border: 1px solid black;">
                    Total Expenditure
                </td>
                <td style="padding: 5px; border: 1px solid black;"></td>
                <td style="padding: 5px; border: 1px solid black;"></td>
                <td style="padding: 5px; border: 1px solid black;">
                    {{number_format($total_expenditure)}}
                </td>
            </tr>
        </table>
    </div>


    <div style="margin-top: 30px;border-bottom: 1px solid black; width: 15%; margin-left: 43rem">
        <h5 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;">Summary
        </h5>
    </div>
    <div style="margin: 10px 3rem 50px;">
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="border: 1px solid black; font-size: smaller">
                <td style="padding: 5px; border: 1px solid black;">
                    S1
                </td>
                <td style="padding: 5px;font-weight: bold;border: 1px solid black; text-align: center">Total Income</td>
                <td style="padding: 5px;font-weight: bold;border: 1px solid black; text-align: center">{{number_format($total_income)}} XAF </td>
            </tr>
            <tr style="border: 1px solid black; font-size: smaller">
                <td style="padding: 5px; border: 1px solid black;">
                    S2
                </td>
                <td style="padding: 5px;font-weight: bold;border: 1px solid black; text-align: center">Total Expenditure</td>
                <td style="padding: 5px;font-weight: bold;border: 1px solid black; text-align: center">{{number_format($total_expenditure)}} XAF </td>
            </tr>
            <tr style="border: 1px solid black; font-size: smaller">
                <td style="padding: 5px; border: 1px solid black;">
                    S3
                </td>
                <td style="padding: 5px;font-weight: bold;border: 1px solid black; text-align: center">Total Balance</td>
                <td style="padding: 5px;font-weight: bold;border: 1px solid black; text-align: center">{{number_format($balance)}} XAF </td>
            </tr>
        </table>
    </div>



    <!------------------------------------------------------DETAILS OF PRESENTERS--------------------------------------------------------------------------------------------->
    <div style="margin-top: 40px;text-align: center;border-bottom: 1px solid black; width: 15%; margin-left: 43rem">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;"><span style="padding-right: 5px"></span> Prepared By
        </h3>
    </div>
    <div class="detail">
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
            <div class="border_line_2" style="border-bottom: 1px solid black; margin-top: 10px;text-align: center; justify-content: center;align-content: center;margin-left: 8rem">
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
            <div class="border_line_2" style="border-bottom: 1px solid black; margin-top: 10px;text-align: center; justify-content: center;align-content: center;margin-left: 8rem">
            </div>
        </div>
        <!------------------------------End of presenters-------------------------------------->
    </div>
    <div class="president" style="text-align: center; margin-top: 9rem">
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
            <div class="border_line" style="border-bottom: 1px solid black; margin-top: 10px;text-align: center; justify-content: center;align-content: center;margin-left: 41rem">
            </div>
        </div>
    </div>
    <!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection
