<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>

    <meta name="description" content="overview &amp; stats" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
</head>
<body style="display: flex; justify-content: center;margin-bottom: 15px;margin-top: 15px;height: 100%">

<div style="width: 500px;border: dashed 1px black;padding: 20px;height: 600px;display: flex;flex-flow: column">
    <div style="text-align: center">
        <img src="{{url("/images/App_Logo.jpeg")}}" alt="App logo" width="100px;" height="100px;">
    </div>
    <div style="margin-bottom:2rem;margin-left: 5rem; margin-right: 5rem">
        <div>
            <p>Hello {{$user_name}},</p>
            <p>
                {{$sender}} has invited you to join <span style="font-weight: bold">{{$organisation_name}}</span> on <span style="font-weight: bold;color: #213c65;">QuickRecords</span>
                in order to better manage your Financial Transactions<br>
                Your role:  <span style="color: #213c65;font-weight: bold">{{$role}}</span><br>
                Your email: <span style="color: #213c65;font-weight: bold">{{$user_email}}</span>
            </p>
        </div>
        <div>
            <div style="margin-bottom: 7px">
                <a style="text-decoration: none;display:inline-block;text-align:center;padding: 10px;background-color: #213c65 ;color: white;cursor: pointer; border: 1px solid #213c65; border-radius: 5px" href="{{$redirectLink}}">Click here to Join</a>
            </div><br>
        </div>
        <div style="margin-top: 5px">
            <label>Or copy and paste the link below on your browser</label><br>
            <label for="link" style="cursor: pointer;color: #213c65;font-weight: bold">{{$redirectLink}}</label>
        </div>
        <br>

        <div>If you don't recognise the sender, please ignore this email, and nothing will happen. <br>
            <span style="color: red">This Invitation link will expire within 1 hour</span>.
        </div><br>

        <p>&copy; {{$year}} <span style="font-weight: bold;color: #213c65;">QuickRecords.</span> All Rights Reserved.</p>
    </div>

</div>
</body>
</html>
<?php
