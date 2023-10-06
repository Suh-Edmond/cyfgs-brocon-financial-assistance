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
    <img src="{{url($organisation_logo)}}" alt="organisation logo" width="100px;" height="100px;">
</div>
<div style="margin-bottom:2rem;margin-left: 5rem; margin-right: 5rem">
    <div>
        <p>Hello {{$user_name}},</p>
        <p>
            {{$sender}} has invited you to join <span style="font-weight: bold">{{$organisation_name}}</span> on <span style="font-weight: bold">QuickRecords</span>
            in order to better manage your Financial Transactions<br>
            Your role:  <span style="color: blue">{{$role}}</span><br>
            Your email: <span style="color: blue">{{$user_email}}</span>
        </p>
    </div>
    <div>
        <label>Click on the button to join</label>
        <div style="margin-bottom: 7px">
            <a style="text-decoration: none;display:inline-block;text-align:center;padding: 10px;background-color: dodgerblue;color: white;cursor: pointer; border: 1px solid dodgerblue; border-radius: 5px" href="{{$redirectLink}}">Click to Join</a>
        </div><br>
    </div>
    <div style="margin-top: 5px">
        <label>Or copy and paste the link below on your browser</label><br>
        <label for="link" style="cursor: pointer;color: blue">{{$redirectLink}}</label>
    </div>
    <br>

    <div>If you don't recognise the sender, please ignore this email, and nothing will happen. <br>
        <span style="color: red">This Invitation link will expire within 7 days</span>.
    </div><br>

    <p>Copyright &copy; 2023 <span style="font-weight: bold">QuickRecords.</span> All Rights Reserved.</p>
</div>
</body>
</html>
<?php
