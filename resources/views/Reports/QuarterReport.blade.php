@extends('layout.base')

@section('section')
    <div style="margin-bottom: 220px;">
        <div style="float: left;">
            <img src="{{url($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;"
                 style="border-radius: 2px">
        </div>
        <div style="float: right;text-transform: capitalize;font-weight: bold">
            <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                {{ $organisation->name }}</label><br />
            <label style="font-size: small;">{{ $organisation->salutation }}</label><br />
            <label style="font-size: small;">P.O Box {{ $organisation->box_number }}</label><br />
            <label style="font-size: small;">{{ $organisation->address }}</label><br />
            <label style="font-size: small;">Phone_No:</label><br />
            <label style="font-size: small;">{{ $organisation_telephone }}</label><br />
            <label style="font-size: small;">Email: {{ $organisation->email }}</label><br /><br />
        </div>
    </div>
    <div style="margin-bottom: 2rem;">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;border-bottom: 1px solid black;">{{ $title }}
        </h3>
    </div>

    <!-------------------------------------------------------------------income details------------------------------------------------------------------>
    <div>
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px;"></span> Income (XAF)
        </h3>
    </div>

    <?php $n=1 ?>
    <div class="activity" >
        <!---start of title-->
        <div class="row quarter_row">
            <div class="column1" style="font-weight: bold">
                S/N
            </div>
            <div class="column2" style="font-weight: bold">
                Description/Activity
            </div>
            <div class="column3" style="font-weight: bold">
                Amount
            </div>
            <div class="column4" style="font-weight: bold">
                SubTotal
            </div>
            <div class="column5" style="font-weight: bold">
                Total
            </div>
        </div>
        <!---end of title-->

        <!---start of balance brought forward-->
        <div class="row amount_brought_forward">
            <div class="column1">
                BBF
            </div>
            <div class="column2" style="font-weight: bold; text-align: center">
                Balance Brought-Forward
            </div>
            <div class="column3"></div>
            <div class="column4"></div>
            <div class="column5">{{number_format($bal_brought_forward)}}</div>
        </div>
        <!-----end of balance brought forward-->


        <!---start of incomes categories-->
        @foreach($incomes as $key => $income)
            <div>
                <div class="row quarter_row">
                    <div>
                        <div class="column1">
                            {{$income->code}}
                        </div>
                        <div class="column2" style="font-weight: bold;text-align: center">
                            {{$income->name}}
                        </div>
                        <div class="column3"></div>
                        <div class="column4"></div>
                        <div class="column5">
                            @if(empty($income->items))
                                {{number_format($income->total)}}
                            @endif
                        </div>
                    </div>
                </div>
                <!------list of activities under the category--->
                @foreach($income->items as $k => $value)
                    <div class="row activity_row">
                        <div class="column1">
                            {{$income->code }}.{{$value->code}}
                        </div>
                        <div class="column2" style="font-weight: bold">
                            {{$value->name}}
                        </div>
                        <div class="column3" >
                        </div>
                        <div class="column4">
                        </div>
                        <div class="column5"></div>

                        <!-------------items under an activity------------>
                        <div>
                            <div class="row item_row">
                                <div class="column1">
                                    {{$income->code}}.{{$value->code}}.{{array_key_first($value->items) + 1}}
                                </div>
                                <div class="column2">
                                    Member's Contributions
                                </div>
                                <div class="column3">{{number_format($value->items[0]->amount)}}</div>
                                <div class="column4"></div>
                                <div class="column5"></div>
                            </div>
                            @foreach($value->items as $v => $item)
                                <div class="row item_row">
                                    <div class="column1">
                                        {{$income->code}}.{{$value->code}}.{{$v + 1}}
                                    </div>
                                    <div class="column2">
                                        @if(($item->name) == $year || is_null($item->name))
                                            Member's Contributions
                                        @endif
                                        @if($item->name != $year)
                                            {{ $item->name }}
                                        @endif
                                    </div>
                                    <div class="column3">{{number_format($item->amount)}}</div>
                                    <div class="column4"></div>
                                    <div class="column5"></div>
                                </div>
                            @endforeach
                        </div>
                        <!-------------end of items----------------------->

                        <!--------- activity sub total and total -------------------->
                        <div class="row activity_total">
                            <div class="column1">
                                {{$income->code}}.{{$value->code}}
                            </div>
                            <div class="column2" style="font-weight: bold">

                            </div>
                            <div class="column3"></div>
                            <div class="column4"> {{number_format($value->total)}}</div>
                            <div class="column5"> </div>
                        </div>
                        <!---------end of activity sub total and total--------------->
                    </div>
            @endforeach
                <!------end of activities------------------------>
            </div>
            @if(!empty($income->items))
                <div class="row quarter_row">
                    <div class="column1">
                        {{$income->code}}
                    </div>
                    <div style="font-weight: bold;text-align: center" class="column2" >
                        Total
                    </div>
                    <div class="column3"></div>
                    <div class="column4">{{number_format($income->total)}}</div>
                    <div class="column5">{{number_format($income->total)}}</div>
                </div>
            @endif
            @if ( $n % 25 == 0 )
                <div style="page-break-before:always;page-break-inside: auto;"> </div>
            @endif
        <?php $n++ ?>

        @endforeach
        <!------total of incomes------------------------->
        <div class="row quarter_row">
            <div class="column1">
            </div>
            <div style="font-weight: bold;text-align: center" class="column2" >
                Total Income
            </div>
            <div class="column3"></div>
            <div class="column4"></div>
            <div class="column5">{{number_format($total_income)}}</div>
        </div>
        <!------end of total of income------------------->

        <!-----end of income categories-->
    </div>
    <!-------------------------------------------------------------end of income--------------------------------------------------------------------------------------------->


    <!------------------------------------------------------------expenditures----------------------------------------------------------------------------------------------->
    <div style="margin-top: 30px;margin-bottom: 20px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px"></span> Expenditures (XAF)
        </h3>
    </div>

    <?php $p=1 ?>
    <div class="activity" >
        <!---start of title-->
        <div class="row quarter_row">
            <div class="column1" style="font-weight: bold">
                S/N
            </div>
            <div class="column2" style="font-weight: bold">
                Description/Activity
            </div>
            <div class="column3" style="font-weight: bold">
                Amount
            </div>
            <div class="column4" style="font-weight: bold">
                SubTotal
            </div>
            <div class="column5" style="font-weight: bold">
                Total
            </div>
        </div>
        <!---end of title-->

        <!---start of expenditure categories-->
        @foreach($expenditures as $key => $expenditure)
            <div>
                <div class="row quarter_row">
                    <div>
                        <div class="column1">
                            {{$expenditure->code}}
                        </div>
                        <div class="column2" style="font-weight: bold;text-align: center">
                            {{$expenditure->name}}
                        </div>
                        <div class="column3"></div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!------list of activities under the category--->
                <?php $t=1 ?>
                @foreach($expenditure->items as $k => $value)
                    <div class="row activity_row">
                        <div class="column1">
                            {{$expenditure->code}}.{{$value->code}}
                        </div>
                        <div class="column2" style="font-weight: bold">
                            {{$value->name}}
                        </div>
                        <div class="column3" >
                        </div>
                        <div class="column4">
                        </div>
                        <div class="column5"></div>

                        <!-------------items under an activity------------>
                        <div class="row item_row">
                            <div class="column1">
                                {{$expenditure->code}}.{{$k + 1}}.{{array_key_first($value->items) + 1}}
                            </div>
                            <div class="column2">
                                {{$value->items[0]->name}}
                            </div>
                            <div class="column3">{{number_format($value->items[0]->amount_spent)}}</div>
                            <div class="column4"></div>
                            <div class="column5"></div>
                        </div>
                        <?php $counter=1 ?>
                        @for($i = 0; $i < count($value->items); $i++)
                            <div>
                                <div class="row item_row">
                                    <div class="column1">
                                        {{$expenditure->code}}.{{$k + 1}}.{{$i + 1}}
                                    </div>
                                    <div class="column2">
                                        {{$value->items[$i]->name}}
                                    </div>
                                    <div class="column3" >{{number_format($value->items[$i]->amount_spent)}}</div>
                                    <div class="column4" ></div>
                                    <div class="column5" ></div>
                                </div>
                            </div>
                            @if ( $counter % 25 == 0 )
                                <div style="page-break-before:always;page-break-inside: auto;"> </div>
                        @endif
                        <?php $counter++ ?>
                    @endfor
                        <!-------------end of items----------------------->

                        <!--------- activity sub total and total -------------------->
                        <div class="row activity_total">
                            <div class="column1">
                                {{$expenditure->code}}.{{$value->code}}
                            </div>
                            <div class="column2" style="font-weight: bold">

                            </div>
                            <div class="column3"></div>
                            <div class="column4">{{$value->total}}</div>
                            <div class="column5"></div>
                        </div>
                        <!---------end of activity sub total and total--------------->
                    </div>
                    @if ( $t % 25 == 0 )
                        <div style="page-break-before:always;page-break-inside: auto;"> </div>
                @endif
                <?php $t++ ?>
                @endforeach
                <!------end of activities------------------------>
            </div>
            <div class="row quarter_row">
                <div class="column1">
                    {{$expenditure->code}}
                </div>
                <div style="font-weight: bold;text-align: center" class="column2" >
                    Total
                </div>
                <div class="column3"></div>
                <div class="column4">{{number_format($total_expenditure)}}</div>
                <div class="column5">{{number_format($total_expenditure)}}</div>
            </div>
            @if ( $p % 25 == 0 )
                <div style="page-break-before:always;page-break-inside: auto;"> </div>
        @endif
        <?php $p++ ?>
        @endforeach
        <!------total of expenditures------------------------->
        <div class="row quarter_row">
            <div class="column1">
            </div>
            <div style="font-weight: bold;text-align: center" class="column2" >
                Total Expenditure
            </div>
            <div class="column3"></div>
            <div class="column4">{{number_format($total_expenditure)}}</div>
            <div class="column5">{{number_format($total_expenditure)}}</div>
        </div>
        <!------end of total of expenditures------------------->

        <!-----end of expenditure categories-->
    </div>

    <!----------------------------------------------------------end of expenditures------------------------------------------------------------------------------------------>

    <!----------------------------------------------------------summary of report-------------------------------------------------------------------------------------------->

    <div style="margin-top: 20px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px"></span> Summary (XAF)
        </h3>
    </div>

    <div class="activity">
        <div class="row quarter_row">
            <div class="summary_num">
                S1
            </div>
            <div class="summary">
                Total Income
            </div>
            <div class="totals">{{number_format($total_income)}}</div>
        </div>
        <div class="row quarter_row">
            <div class="summary_num">
                S2
            </div>
            <div class="summary">
                Total Expenditure
            </div>
            <div class="totals">{{number_format($total_expenditure)}}</div>
        </div>
        <div class="row quarter_row">
            <div class="summary_num">
                S3
            </div>
            <div class="summary">
                Balance
            </div>
            <div class="totals">{{number_format($balance)}}</div>
        </div>
    </div>
    <!----------------------------------------------------------end of summary of report------------------------------------------------------------------------------------->

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
                @isset($treasurer)
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
