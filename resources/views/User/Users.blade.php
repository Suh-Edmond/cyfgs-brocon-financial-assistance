
@extends('layout.base')

@section('section')
    <div style="margin-bottom: 220px;">
        <div style="float: left;">
            <img src="{{url($organisation_logo)}}" alt="organisation_logo" width="100px;" height="100px;"
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
    <div style="margin-bottom: 2rem;border-bottom: 1px solid black;">
        <h3 style="font-weight: bold;font-size: medium; text-align:center;text-transform: capitalize;">{{ $title }}
        </h3>
    </div>
    <?php $n=1 ?>
    <div>
        <table style="border: 1px solid black; border-collapse: collapse;width: 100%">
            <tr style="padding: 13px;border: 1px solid black; font-size: smaller;">
                <th style="padding: 1px; border: 1px solid black;">S/N</th>
                <th style="padding: 5px; border: 1px solid black;">Name</th>
                <th style="padding: 5px; border: 1px solid black;">Gender</th>
                <th style="padding: 5px; border: 1px solid black;">Email</th>
                <th style="padding: 5px; border: 1px solid black;">Telephone</th>
                <th style="padding: 5px; border: 1px solid black;">Address</th>
                <th style="padding: 5px; border: 1px solid black;">Occupation</th>
            </tr>
            @foreach ($users as $key => $user)
                <tr style="border: 1px solid black; font-size: smaller">
                    <td style="padding: 5px;">{{ $key + 1 }}</td>
                    <td style="border: 1px solid black; padding: 5px;">{{ $user->name }}</td>
                    @if(!is_null($user->email))
                        <td style="border: 1px solid black; padding: 5px;">{{ $user->gender }}</td>
                    @endif
                    <td style="border: 1px solid black; padding: 5px;">{{ $user->email }}</td>
                    <td style="border: 1px solid black; padding: 5px;">{{ $user->telephone }}</td>
                    @if(!is_null($user->address))
                        <td style="border: 1px solid black; padding: 5px;">{{ $user->address }}</td>
                    @else
                        <td style="border: 1px solid black; padding: 5px;"></td>
                    @endif
                    @if(!is_null($user->occupation))
                        <td style="border: 1px solid black; padding: 5px;">{{ $user->occupation }}</td>
                    @else
                        <td style="border: 1px solid black; padding: 5px;"></td>
                    @endif
                </tr>

                @if ( $n % 25 == 0 )
                    <div style="page-break-before:always;page-break-inside: auto;"> </div>
                @endif
                <?php $n++ ?>
            @endforeach
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
