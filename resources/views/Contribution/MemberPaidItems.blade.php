@extends('layout.base')

@section('section')
    <div style="margin-bottom: 220px;">
        <div style="float: left;">
            <img src="{{url($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;"
                 style="border-radius: 2px">
        </div>
        <div style="float: right">
            <label for="organisation"style="font-weight: bold; text-transform: uppercase; font-size: small;">
                {{ $organisation->name }}</label><br />
            <label style="font-size: small;">{{ $organisation->salutation }}</label><br />
            <label style="font-size: small;">P.O Box {{ $organisation->box_number }}</label><br />
            <label style="font-size: small;">{{ $organisation->address }}</label><br />
            <label style="font-size: small;">Phone_No:</label><br />
            <label style="font-size: small;">{{ $organisation_telephone }}</label><br />
            <label style="font-size: small;">Email: {{ $organisation->email }}</label><br /><br />
            <label style="font-size: small;">Printed date: {{ $date }}</label>
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
                <th style="padding: 12px; border: 1px solid black;">Title</th>
                <th style="padding: 12px; border: 1px solid black;">Amount Paid(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Balance(XAF)</th>
                <th style="padding: 12px; border: 1px solid black;">Payment Status</th>
                <th style="padding: 12px; border: 1px solid black;">Transaction Status</th>
                <th style="padding: 12px; border: 1px solid black;">Frequency</th>
            </tr>
            @foreach ($contributions as $key => $contribution)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center" >{{ $contribution->name }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center">
                        {{ number_format($contribution->payment_item_amount) }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center">
                        {{ number_format($contribution->balance) }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center">
                        {{ $contribution->approve }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center">
                        {{ $contribution->payment_status }}</td>
                    <td style="border: 1px solid black; padding: 11px; text-align: center">
                        {{ $contribution->frequency }}</td>
                    @if ( $n % 25 == 0 )
                        <div style="page-break-before:always;page-break-inside: auto;"> </div>
                    @endif
                        <?php $n++ ?>

                </tr>
            @endforeach
            <tr style="border: 1px solid black; font-size: smaller">
                <td style="border: 1px solid black; padding: 11px; text-align: center" colspan="2">
                    <b>Total</b>
                </td>
                <td style="border: 1px solid black; padding: 11px; text-align: center" colspan="5">
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
            <div class=" " style="font-weight: bold;font-size: small;text-transform: uppercase; margin-bottom: 5px;text-align: center">
                FINANCIAL SECRETARY
            </div>
            <div style="font-weight: bold;font-size: small; text-transform: uppercase;text-align: center">
                @isset($fin_secretary)
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

