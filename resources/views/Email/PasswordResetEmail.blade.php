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
        <img src="{{url("/images/App_Logo.jpeg")}}" alt="organisation logo" width="100px;" height="100px;">
    </div>
    <div>
        <p>Hello {{$data->name}},</p>
        <p>
            Forgot your password ? <br>
            We received a password reset request for this Account<br>
            Your Email: <span style="color: #213c65;font-weight: bold">{{$data->email}}</span>
        </p>
    </div>
    <div>
        Password Reset Token: <span style="color: #213c65;font-weight: bold">{{$token}}</span>
    </div><br>
    <div>
        <label>To Reset your password, please copy the token and click on the button below</label>
        <div style="margin-bottom: 7px">
            <a style="text-decoration: none;display:inline-block;text-align:center;padding: 10px;background-color: #213c65;color: white;cursor: pointer; border: 1px solid #213c65; border-radius: 5px" href="{{$redirectLink}}">Click here to reset password</a>
        </div><br>
    </div>
    <div style="margin-top: 5px">
        <label>Or copy and paste the link below on your browser</label><br>
        <label for="link" style="cursor: pointer;color: #213c65;font-weight: bold">{{$redirectLink}}</label>
    </div>
    <br>

    <div>If this was not requested by you, please ignore this email, and nothing will happen. <br>
        <span style="color: red">The password reset link will expire within 10 minutes</span>.
    </div><br>

    <p>&copy; {{$year}} <span style="font-weight: bold;color: #213c65;">QuickRecords.</span> All Rights Reserved.</p>
</div>
</body>
</html>
