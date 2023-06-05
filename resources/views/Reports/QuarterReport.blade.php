@extends('layout.base')

@section('section')
    <div style="margin-bottom: 220px;">
        <div style="float: left;">
            <img src="{{ $organisation->logo }}" alt="organisation logo" width="100px;" height="100px;"
                 style="border-radius: 2px">
        </div>
        <div style="float: right;text-transform: uppercase;font-weight: bold">
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
    <div class="title">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: uppercase;text-decoration: underline">{{ $title }}
        </h3>
    </div>

    <!-------------------------------------------------------------------income details------------------------------------------------------------------>
    <div>
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px;"></span> Income (F CFA)
        </h3>
    </div>

    <div class="activity">
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
                1
            </div>
            <div class="column2" style="font-weight: bold">
                Balance B/F
            </div>
            <div class="column3"></div>
            <div class="column4"></div>
            <div class="column5">100 000, 000</div>
        </div>
        <!-----end of balance brought forward-->


        <!---start of incomes categories-->
        <div>
            <div class="row quarter_row">
                <div>
                    <div class="column1">
                        2
                    </div>
                    <div class="column2" style="font-weight: bold;text-align: center">
                        Meetings
                    </div>
                    <div class="column3"></div>
                    <div class="column4"></div>
                    <div class="column5"></div>
                </div>
            </div>
            <!------list of activities under the category--->
            <div class="row activity_row">
                <div class="column1">
                    2.1
                </div>
                <div class="column2" style="font-weight: bold">
                    Visits to Petrons Meetings
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
                            2.1.1
                        </div>
                        <div class="column2">
                            Cost during camp fire
                        </div>
                        <div class="column3" >30000</div>
                        <div class="column4" ></div>
                        <div class="column5" ></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.2
                        </div>
                        <div class="column2">
                            Cost during singing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.3
                        </div>
                        <div class="column2">
                            Cost during dancing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.4
                        </div>
                        <div class="column2">
                            Cost exhibition
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.5
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!-------------end of items----------------------->

                <!--------- activity sub total and total -------------------->
                <div class="row activity_total">
                    <div class="column1">
                        2.1
                    </div>
                    <div class="column2">
                        ...
                    </div>
                    <div class="column3"></div>
                    <div class="column4">150000</div>
                    <div class="column5">150000</div>
                </div>
                <!---------end of activity sub total and total--------------->
            </div>

            <div class="row activity_row">
                <div class="column1">
                    2.2
                </div>
                <div class="column2" style="font-weight: bold">
                    Representatives and Petrons Meetings
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
                            2.2.1
                        </div>
                        <div class="column2">
                            Cost during camp fire
                        </div>
                        <div class="column3" >30000</div>
                        <div class="column4" ></div>
                        <div class="column5" ></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.2
                        </div>
                        <div class="column2">
                            Cost during singing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.3
                        </div>
                        <div class="column2">
                            Cost during dancing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.4
                        </div>
                        <div class="column2">
                            Cost exhibition
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.5
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.6
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.7
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.8
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.9
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.10
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!-------------end of items----------------------->

                <!--------- activity sub total and total -------------------->
                <div class="row">
                    <div class="column1">
                        2.2
                    </div>
                    <div class="column2">
                        ...
                    </div>
                    <div class="column3"></div>
                    <div class="column4">150000</div>
                    <div class="column5">150000</div>
                </div>
                <!---------end of activity sub total and total--------------->
            </div>
            <!------end of activities------------------------>
        </div>

        <div>
            <div class="row quarter_row">
                <div>
                    <div class="column1">
                        3
                    </div>
                    <div class="column2" style="font-weight: bold;text-align: center">
                        OutReaches and Internships
                    </div>
                    <div class="column3"></div>
                    <div class="column4"></div>
                    <div class="column5"></div>
                </div>
            </div>
            <!------list of activities under the category--->
            <div class="row activity_row">
                <div class="column1">
                    3.1
                </div>
                <div class="column2" style="font-weight: bold">
                    Visits to Petrons Meetings
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
                            3.1.1
                        </div>
                        <div class="column2">
                            Cost during camp fire
                        </div>
                        <div class="column3" >30000</div>
                        <div class="column4" ></div>
                        <div class="column5" ></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.2
                        </div>
                        <div class="column2">
                            Cost during singing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.3
                        </div>
                        <div class="column2">
                            Cost during dancing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.4
                        </div>
                        <div class="column2">
                            Cost exhibition
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.5
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!-------------end of items----------------------->

                <!--------- activity sub total and total -------------------->
                <div class="row activity_total">
                    <div class="column1">
                        3.1
                    </div>
                    <div class="column2">
                        ...
                    </div>
                    <div class="column3"></div>
                    <div class="column4">150000</div>
                    <div class="column5">150000</div>
                </div>
                <!---------end of activity sub total and total--------------->
            </div>

            <div class="row activity_row">
                <div class="column1">
                    3.2
                </div>
                <div class="column2" style="font-weight: bold">
                    Representatives and Petrons Meetings
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
                            3.2.1
                        </div>
                        <div class="column2">
                            Cost during camp fire
                        </div>
                        <div class="column3" >30000</div>
                        <div class="column4" ></div>
                        <div class="column5" ></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.2
                        </div>
                        <div class="column2">
                            Cost during singing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.3
                        </div>
                        <div class="column2">
                            Cost during dancing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.4
                        </div>
                        <div class="column2">
                            Cost exhibition
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.5
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.6
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.7
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.8
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.9
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.10
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!-------------end of items----------------------->

                <!--------- activity sub total and total -------------------->
                <div class="row">
                    <div class="column1">
                        3.2
                    </div>
                    <div class="column2">
                        ...
                    </div>
                    <div class="column3"></div>
                    <div class="column4">150000</div>
                    <div class="column5">150000</div>
                </div>
                <!---------end of activity sub total and total--------------->
            </div>
            <!------end of activities------------------------>
        </div>

        <!------total of incomes------------------------->
        <div class="row quarter_row">
            <div class="column1">
            </div>
            <div style="font-weight: bold;text-align: center" class="column2" >
                Total
            </div>
            <div class="column3"></div>
            <div class="column4">500,0000</div>
            <div class="column5">100 000, 000</div>
        </div>
        <!------end of total of income------------------->

        <!-----end of income categories-->
    </div>
    <!-------------------------------------------------------------end of income--------------------------------------------------------------------------------------------->


    <!------------------------------------------------------------expenditures----------------------------------------------------------------------------------------------->
    <div style="margin-top: 30px;margin-bottom: 20px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px"></span> Expenditures (F CFA)
        </h3>
    </div>

    <div class="activity">
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
        <div>
            <div class="row quarter_row">
                <div>
                    <div class="column1">
                        2
                    </div>
                    <div class="column2" style="font-weight: bold;text-align: center">
                        Meetings
                    </div>
                    <div class="column3"></div>
                    <div class="column4"></div>
                    <div class="column5"></div>
                </div>
            </div>
            <!------list of activities under the category--->
            <div class="row activity_row">
                <div class="column1">
                    2.1
                </div>
                <div class="column2" style="font-weight: bold">
                    Visits to Petrons Meetings
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
                            2.1.1
                        </div>
                        <div class="column2">
                            Cost during camp fire
                        </div>
                        <div class="column3" >30000</div>
                        <div class="column4" ></div>
                        <div class="column5" ></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.2
                        </div>
                        <div class="column2">
                            Cost during singing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.3
                        </div>
                        <div class="column2">
                            Cost during dancing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.4
                        </div>
                        <div class="column2">
                            Cost exhibition
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.5
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!-------------end of items----------------------->

                <!--------- activity sub total and total -------------------->
                <div class="row activity_total">
                    <div class="column1">
                        2.1
                    </div>
                    <div class="column2">
                        ...
                    </div>
                    <div class="column3"></div>
                    <div class="column4">150000</div>
                    <div class="column5">150000</div>
                </div>
                <!---------end of activity sub total and total--------------->
            </div>

            <div class="row activity_row">
                <div class="column1">
                    2.2
                </div>
                <div class="column2" style="font-weight: bold">
                    Representatives and Petrons Meetings
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
                            2.2.1
                        </div>
                        <div class="column2">
                            Cost during camp fire
                        </div>
                        <div class="column3" >30000</div>
                        <div class="column4" ></div>
                        <div class="column5" ></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.2
                        </div>
                        <div class="column2">
                            Cost during singing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.1.3
                        </div>
                        <div class="column2">
                            Cost during dancing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.4
                        </div>
                        <div class="column2">
                            Cost exhibition
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.5
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.6
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.7
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.8
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.9
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            2.2.10
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!-------------end of items----------------------->

                <!--------- activity sub total and total -------------------->
                <div class="row">
                    <div class="column1">
                        2.2
                    </div>
                    <div class="column2">
                        ...
                    </div>
                    <div class="column3"></div>
                    <div class="column4">150000</div>
                    <div class="column5">150000</div>
                </div>
                <!---------end of activity sub total and total--------------->
            </div>
            <!------end of activities------------------------>
        </div>

        <div>
            <div class="row quarter_row">
                <div>
                    <div class="column1">
                        3
                    </div>
                    <div class="column2" style="font-weight: bold;text-align: center">
                        OutReaches and Internships
                    </div>
                    <div class="column3"></div>
                    <div class="column4"></div>
                    <div class="column5"></div>
                </div>
            </div>
            <!------list of activities under the category--->
            <div class="row activity_row">
                <div class="column1">
                    3.1
                </div>
                <div class="column2" style="font-weight: bold">
                    Visits to Petrons Meetings
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
                            3.1.1
                        </div>
                        <div class="column2">
                            Cost during camp fire
                        </div>
                        <div class="column3" >30000</div>
                        <div class="column4" ></div>
                        <div class="column5" ></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.2
                        </div>
                        <div class="column2">
                            Cost during singing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.3
                        </div>
                        <div class="column2">
                            Cost during dancing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.4
                        </div>
                        <div class="column2">
                            Cost exhibition
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.5
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!-------------end of items----------------------->

                <!--------- activity sub total and total -------------------->
                <div class="row activity_total">
                    <div class="column1">
                        3.1
                    </div>
                    <div class="column2">
                        ...
                    </div>
                    <div class="column3"></div>
                    <div class="column4">150000</div>
                    <div class="column5">150000</div>
                </div>
                <!---------end of activity sub total and total--------------->
            </div>

            <div class="row activity_row">
                <div class="column1">
                    3.2
                </div>
                <div class="column2" style="font-weight: bold">
                    Representatives and Petrons Meetings
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
                            3.2.1
                        </div>
                        <div class="column2">
                            Cost during camp fire
                        </div>
                        <div class="column3" >30000</div>
                        <div class="column4" ></div>
                        <div class="column5" ></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.2
                        </div>
                        <div class="column2">
                            Cost during singing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.1.3
                        </div>
                        <div class="column2">
                            Cost during dancing
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.4
                        </div>
                        <div class="column2">
                            Cost exhibition
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.5
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.6
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.7
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.8
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.9
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                    <div class="row item_row">
                        <div class="column1">
                            3.2.10
                        </div>
                        <div class="column2">
                            Cost of Support
                        </div>
                        <div class="column3">30000</div>
                        <div class="column4"></div>
                        <div class="column5"></div>
                    </div>
                </div>
                <!-------------end of items----------------------->

                <!--------- activity sub total and total -------------------->
                <div class="row">
                    <div class="column1">
                        3.2
                    </div>
                    <div class="column2">
                        ...
                    </div>
                    <div class="column3"></div>
                    <div class="column4">150000</div>
                    <div class="column5">150000</div>
                </div>
                <!---------end of activity sub total and total--------------->
            </div>
            <!------end of activities------------------------>
        </div>

        <!------total of expenditures------------------------->
        <div class="row quarter_row">
            <div class="column1">
            </div>
            <div style="font-weight: bold;text-align: center" class="column2" >
                Total Expenditure
            </div>
            <div class="column3"></div>
            <div class="column4">500,0000</div>
            <div class="column5">100 000, 000</div>
        </div>
        <!------end of total of expenditures------------------->

        <!-----end of expenditure categories-->
    </div>

    <!----------------------------------------------------------end of expenditures------------------------------------------------------------------------------------------>

    <!----------------------------------------------------------summary of report-------------------------------------------------------------------------------------------->

    <div style="margin-top: 20px;margin-bottom: 20px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px"></span> Summary (F CFA)
        </h3>
    </div>

    <div class="activity">
        <div class="row quarter_row">
            <div class="summary">
                Total Income
            </div>
            <div class="totals"> 60000000</div>
        </div>
        <div class="row quarter_row">
            <div class="summary">
                Total Expenditure
            </div>
            <div class="totals"> 10000000</div>
        </div>
        <div class="row quarter_row">
            <div class="summary">
                Balance
            </div>
            <div class="totals"> 10000</div>
        </div>
        <div class="row quarter_row">
            <div class="summary">
                Cash In Hand
            </div>
            <div class="totals"> 5000</div>
        </div>
        <div class="row quarter_row">
            <div class="summary">
                Account
            </div>
            <div class="totals"> 500000</div>
        </div>
    </div>
    <!----------------------------------------------------------end of summary of report------------------------------------------------------------------------------------->

    <!------------------------------------------------------Details of presenter--------------------------------------------------------------------------------------------->
    <div style="margin-top: 20px;margin-bottom: 10px;">
        <h3 style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;padding-bottom: 20px;text-decoration: underline"><span style="padding-right: 5px"></span> Prepared By:
        </h3>
    </div>
    <div class="activity">
        <div class="row quarter_row">
            <div class="pre_column1" style="font-weight: bold">

            </div>
            <div class="pre_column2" style="font-weight: bold;text-transform: uppercase">
                Name
            </div>
            <div class="pre_column3" style="font-weight: bold;text-transform: uppercase">
                Date
            </div>
            <div class="pre_column4" style="font-weight: bold;text-transform: uppercase">
                Sign
            </div>
        </div>

        <!------------------------------Names of presenters------------------------------------>
        <div class="row quarter_row">
            <div class="pre_column1" style="font-weight: bold">
                    FINANCIAL SECRETARY
            </div>
            <div class="pre_column2" style="font-weight: normal;text-transform: capitalize">
                @isset($treasurer)
                    <span>{{$fin_secretary->name}}</span>
                @endisset
            </div>
            <div class="pre_column3" style="font-weight: normal;text-transform: uppercase">
                {{$date}}
            </div>
            <div class="pre_column4" style="font-weight: normal;text-transform: uppercase">

            </div>
        </div>

        <div class="row quarter_row">
            <div class="pre_column1" style="font-weight: bold">
                TREASURER
            </div>
            <div class="pre_column2" style="font-weight: normal;text-transform: capitalize">
                @isset($treasurer)
                    <span>
                    {{$treasurer->name}}
                </span>
                @endisset
            </div>
            <div class="pre_column3" style="font-weight: normal;text-transform: uppercase">
                {{$date}}
            </div>
            <div class="pre_column4" style="font-weight: normal;text-transform: uppercase">

            </div>
        </div>

        <div class="row quarter_row">
            <div class="pre_column1" style="font-weight: bold">
                PRESIDENT
            </div>
            <div class="pre_column2" style="font-weight: normal;text-transform: capitalize">
                @isset($president)
                    <span>
                    {{$president->name}}
                </span>
                @endisset
            </div>
            <div class="pre_column3" style="font-weight: normal;text-transform: uppercase">
                {{$date}}
            </div>
            <div class="pre_column4" style="font-weight: normal;text-transform: uppercase">

            </div>
        </div>
        <!------------------------------End of presenters-------------------------------------->
    </div>
    <!------------------------------------------------------End of details of Presenter-------------------------------------------------------------------------------------->
@endsection
