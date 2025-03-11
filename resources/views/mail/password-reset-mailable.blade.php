@component('mail::message')
## Hello {{$data->name}},

Forgot your password ? <br>
We received a password reset request for this Account

Your Email: <span style="color: #213c65;font-weight: bold">{{$data->email}}</span>

Password Reset Token: <span style="color: #213c65;font-weight: bold">{{$token}}</span>

To Reset your password, please copy the token and click on the button below
@component('mail::button', ['url' => $redirectLink])
Click here to reset password
@endcomponent

Or copy and paste the link below on your browser
<a class="dn_btn font-bold" href="{{$redirectLink}}">{{$redirectLink}}</a>.

If this was not requested by you, please ignore this email, and nothing will happen.

<a style="color: red">The password reset link will expire within 10 minutes</a>

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
        <span style="display: flex; justify-content: center;font-weight: bold;margin-left: 2px"><a href="{{env('UI_LOGIN_URL')}}" style="text-decoration: none;color: #213c65;">QuickRecords</a></span><br>
    </div>
    <div style="display: flex;justify-content: center; margin-bottom: 5px;padding: 10px">
        <span>Buea, Cameroon.</span>
    </div>
    <div style="display: flex;justify-content: center;padding: 10px">
        <span>This email was sent to <a style="color: #213c65;">{{$data->email}}.</a><br>
            Because, you have been invited to be an administrator of QuickRecords.</span>
    </div>
</div>
@endcomponent
