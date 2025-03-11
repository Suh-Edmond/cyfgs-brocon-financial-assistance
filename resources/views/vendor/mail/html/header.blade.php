<tr>
<td class="header">
<a href="{{ env('UI_LOGIN_URL') }}" style="display: inline-block;">
@if (trim($slot) === 'Quick Records App')
<img src="{{asset('images/App_Logo.png')}}" class="logo" alt="QuickRecords Logo" height="300px" width="300px">
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
