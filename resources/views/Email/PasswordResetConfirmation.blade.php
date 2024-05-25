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
     <div style="margin-bottom:2rem;margin-left: 5rem; margin-right: 5rem">
         <div>
             <p>Hello {{$data->name}},</p>
             <p>
                 As you requested, your QuickRecords account password has been updated.
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

         <p>&copy; {{$year}} <span style="font-weight: bold;color: #213c65">QuickRecords.</span> All Rights Reserved.</p>
     </div>
 </div>
</body>
</html>
