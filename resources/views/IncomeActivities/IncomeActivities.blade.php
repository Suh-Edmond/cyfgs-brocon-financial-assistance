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
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;border-bottom: 1px solid black;">{{$title}}</h3>
    </div>
    <?php $n=1 ?>
    <div>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 12px; border: 1px solid black;">Name</th>
                <th style="padding: 12px; border: 1px solid black;">Amount (XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Payment Status</th>
                <th style="padding: 12px; border: 1px solid black;">Date</th>
                <th style="padding: 12px; border: 1px solid black;">Venue</th>
            </tr>
            @foreach ($income_activities as $key => $income_activity)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 3px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ $income_activity->name }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ number_format($income_activity->amount) }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ $income_activity->approve }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ date('j F, Y', strtotime($income_activity->date)) }}</td>
                    <td style="border: 1px solid black; padding: 3px;">{{ $income_activity->venue }}</td>
                </tr>
                @if ( $n % 25 == 0 )
                    <div style="page-break-before:always;page-break-inside: auto;"> </div>
                @endif
                <?php $n++ ?>
            @endforeach
            <tr style="padding: 12px; border: 1px solid black; font-size: smaller">
                <td style="border: 1px solid black; padding: 3px;font-weight: bold" colspan="2"> Total Amount
                </td>
                <td style="padding: 3px;font-weight: bold" colspan="4">{{ number_format($total) }} XAF </td>
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
            <div class="border_line" style="border-bottom: 1px solid black; margin-top: 10px;text-align: center">
            </div>
        </div>
    </div>
    <!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection
