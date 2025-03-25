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
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Title</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Payable(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Frequency</th>
                <th style="padding: 12px; border: 1px solid black;">Month</th>
                <th style="padding: 12px; border: 1px solid black;">Quarter</th>
                <th style="padding: 12px; border: 1px solid black;">Year</th>
            </tr>
            @foreach ($contributions as $key => $contribution)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 3px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ $contribution->name }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center">
                        {{ number_format($contribution->item_amount) }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center">
                        {{ $contribution->frequency }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ isset($contribution->month_name)?$contribution->month_name: 'N/A' }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ isset($contribution->quarterly_name) ? $contribution->quarterly_name:  'N/A' }}</td>
                    <td style="border: 1px solid black; padding: 3px; text-align: center" >{{ isset($contribution->session->year)? $contribution->session->year: \App\Models\Session::find($contribution->session)->year }}</td>

                    @if ( $n % 25 == 0 )
                        <div style="page-break-before:always;page-break-inside: auto;"> </div>
                    @endif
                        <?php $n++ ?>

                </tr>
            @endforeach
            <tr style="border: 1px solid black; font-size: smaller">
                <td style="border: 1px solid black; padding: 3px; text-align: center" colspan="2">
                    <b>Total</b>
                </td>
                <td style="border: 1px solid black; padding: 3px; text-align: center" colspan="5">
                    <b>{{number_format($total)}} XAF</b>
                </td>
            </tr>
        </table>
    </div>


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

