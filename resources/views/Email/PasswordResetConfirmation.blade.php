<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body>

<div style="text-align: center">
    <img src="{{url("/images/App_Logo.jpeg")}}" alt="organisation logo" width="100px;" height="100px;">
</div>
<div style="margin-bottom:2rem;margin-left: 5rem; margin-right: 5rem">
    <div>
        <p>Hello {{$data->name}},</p>
        <p>
           As you requested, your QuickRecords account password have been updated.
        </p>
        <p>
            You can use your new password to log in to your account.
        </p>
        <p>
            Thanks for using QuickRecords.
        </p>
        <p>Sincerely,<br>
            QuickRecords Team.
        </p>
    </div>

    <br>

    <p>Copyright &copy; {{$year}} <span style="font-weight: bold">QuickRecords.</span> All Rights Reserved.</p>
</div>
</body>
</html>
