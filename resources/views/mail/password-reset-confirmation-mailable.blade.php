@component('mail::message')
## Hello {{$data->name}},

As you requested, your QuickRecords account password has been updated.

You can use your new password to log in to your account.

Thanks for using QuickRecords.

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
        <span>This email was sent to <a style="color: #213c65;">{{$data->email}}.</a><br>
            Because, you are an administrator of QuickRecords.</span>
    </div>
</div>
@endcomponent
