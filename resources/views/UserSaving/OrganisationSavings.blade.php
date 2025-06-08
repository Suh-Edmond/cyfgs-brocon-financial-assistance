@extends('layout.base')
@section('title', 'Members savings')
@section('section')
<div style="margin-bottom:30px;">
    <div class="column_100" style="margin-left: 30px">
        <div class="column_25">
            <img src="{{public_path('cyf_logo.png')}}" alt="pcc logo" width="100px;" height="100px;"
                style="border-radius: 2px">
        </div>
        <div class="column_50" style="text-align: center;">
            <label for="organisation" style="font-weight: bold; text-transform: uppercase; font-size: small;">
                CHRISTIAN YOUTH FELLOWSHIP (C.Y.F)</label><br />
            <label for="organisation" style="font-weight: bold; text-transform: uppercase; font-size: small;">
                FAKO NORTH PRESBYTERY</label><br />
            <label for="organisation" style="font-weight: bold; text-transform: uppercase; font-size: small;">
                BUEA ZONE</label><br />
            <label for="organisation" style="font-weight: bold; text-transform: uppercase; font-size: small;">
                CHRISTIAN YOUTH FELLOWSHIP - GREAT SOPPO </label><br />
            <label for="organisation" style="font-weight: bold; text-transform: uppercase; font-size: small;">
                {{ $organisation->name }} - {{ $organisation->address }}</label><br />
        </div>
        <div class="column_25">
            <img src="{{public_path($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;"
                style="border-radius: 2px">
        </div>
    </div>
    <div class="column_100" style="margin-left: 30px;margin-top: 20px">
        <div class="column_10">
        </div>
        <div class="column_25">
            <label style="font-weight: bold; text-transform: uppercase; font-size: small;">P.O Box {{
                $organisation->box_number }}, {{ $organisation->address }}</label><br />
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
<hr style="border-bottom: 5px solid #213c65;margin-top: 2rem" />
<div class="column_100" style="margin-bottom: 3.5rem">
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
        <tr style="border: 1px solid black; font-size: smaller;">
            <th style="border: 1px solid black;width: 3%">S/N</th>
            <th style="padding: 12px; border: 1px solid black;">Name</th>
            <th style="padding: 12px; border: 1px solid black;">Email</th>
            <th style="padding: 12px; border: 1px solid black;">Telephone</th>
            <th style="padding: 12px; border: 1px solid black;">Total Amount Saved(XAF)</th>
        </tr>
        @foreach ($user_savings as $key => $user_saving)
        <tr style="border: 1px solid black; font-size: smaller">
            <td style="padding: 5px;width: 3%">{{ $key + 1 }}</td>
            <td style="border: 1px solid black; padding: 3px;">{{ $user_saving->user->name }}</td>
            <td style="border: 1px solid black; padding: 3px;">{{ $user_saving->user->email }}</td>
            <td style="border: 1px solid black; padding: 3px;">{{ $user_saving->user->telephone }}</td>
            <td style="border: 1px solid black; padding: 3px;">{{ number_format($user_saving->total_amount) }}
            </td>
        </tr>
        @if ( $n % 25 == 0 )
        <div style="page-break-before:always;page-break-inside: auto;"> </div>
        @endif
        <?php $n++ ?>
        @endforeach
        <tr style="border: 1px solid black; font-size: smaller">
            <td style="border: 1px solid black; padding: 3px;font-weight: bold" colspan="2"> Total Amount</td>
            <td style="border: 1px solid black; padding: 3px;font-weight: bold" colspan="3">{{ number_format($total) }}
                XAF</td>

        </tr>
    </table>
</div>

<!------------------------------------------------------DETAILS OF PRESENTERS--------------------------------------------------------------------------------------------->
<div style="margin-top: 40px;margin-bottom: 50px">
    <h3
        style="font-weight: bold;font-size: small; text-align:center;text-transform: uppercase;text-decoration: underline">
        <span style="padding-right: 5px"></span> Prepared By:
    </h3>
</div>

<div class="detail">
    <!------------------------------Names of presenters------------------------------------>
    <div style="float: left" class="fin_sec">
        <div class=" "
            style="font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 3px;text-align: center">
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
        <div style="border-bottom: 1px solid black; margin-top: 10px">
        </div>
    </div>

    <div style="float: right" class="treasurer">
        <div class=" "
            style="text-align: center;font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 3px">
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
        <div style="border-bottom: 1px solid black; margin-top: 10px">
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
        <div class="border_line"
            style="border-bottom: 1px solid black; margin-top: 10px;margin-left:17rem;text-align: center">
        </div>
    </div>
</div>
<!------------------------------------------------------END OF PRESENTERS-------------------------------------------------------------------------------------->

@endsection