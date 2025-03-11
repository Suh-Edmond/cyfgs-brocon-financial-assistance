@component('mail::message')
## Hello {{$user_name}},

{{$sender}} has invited you to join the {{$organisation_name}} on QuickRecords as an Administrator to manage Financial Transactions<br>

Your role:  <span style="color: #213c65;font-weight: bold">{{str_replace("_", " ", $role)}}</span><br>

Your email: <span style="color: #213c65;font-weight: bold">{{$user_email}}</span>

@component('mail::button', ['url' => $redirectLink])
Click here to Join
@endcomponent

Or copy and paste the link below on your browser
<a class="dn_btn font-bold" href="{{$redirectLink}}">{{$redirectLink}}</a>.


If you don't recognise the sender, please ignore this email, and nothing will happen. <br>

<a style="color: red">This Invitation link will expire within 24 hours</a>.

<br>

Best regards,<br>
QuickRecords Team.<br>

<div style="display: flex;justify-content: center; margin-bottom: 30px">
    <a href="#" style="padding: 10px;color: #213c65;"><img src="{{asset('/images/facebook-logo.png')}}" alt="facebook logo" width="30px" height="30px"></a>
    <a href="#" style="padding: 10px;color: #213c65;"><img src="{{asset('/images/instagram-logo.png')}}" alt="instagram logo" width="30px" height="30px"></a>
    <a href="#" style="padding: 10px;color: #213c65;"><img src="{{asset('/images/twitter-logo.png')}}" alt="twitter logo" width="30px" height="30px"></a>
    <a href="#" style="padding: 10px;color: #213c65;"><img src="{{asset('/images/whatsapp-logo.png')}}" alt="whatsapp logo" width="30px" height="30px"></a>
</div>
<div style="background-color: #e2e8f0;">
    <div style="display: flex;justify-content: center;padding: 10px">
        <span style="display: flex; justify-content: center;font-weight: bold;margin-left: 2px"><a href="{{env('UI_LOGIN_URL')}}" style="text-decoration: none;color: #213c65">QuickRecords</a></span><br>
    </div>
    <div style="display: flex;justify-content: center; margin-bottom: 5px;padding: 10px">
        <span>Buea, Cameroon.</span>
    </div>
    <div style="display: flex;justify-content: center;padding: 10px">
        <span>This email was sent to <a style="color: #213c65;">{{$user_email}}.</a><br>
            Because, you have been invited to be an administrator of QuickRecords.</span>
    </div>
</div>
@endcomponent
