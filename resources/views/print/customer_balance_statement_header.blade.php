<div style="width: 100%; text-align: center; font-size: 10px;">
    <table style="width: 100%">
        <tr>
            <td valign="top" style="width: 45%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <img src="{{$companylogo}}" width="180px" height="60px"><br>
            </td>
            <td valign="top" style="width: 55%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <br><br>
                <span style="font-weight: bold; font-size: 12px;">@if(isset($lang) && $lang === 'ar') &nbsp;&nbsp;&nbsp; @endif
                    {{ trans('custom.customer_balance_statement') }}</span><br>
                <span style="font-weight: bold; font-size: 12px;">@if(isset($lang) && $lang === 'en') &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; @endif
                    {{ trans('custom.as_of') }} {{ $fromDate }}</span>
            </td>
        </tr>
        <tr>
            <td valign="top" style="width: 45%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <span style="font-weight: bold;"> {{$companyName}}</span>
            </td>
            <td>
            </td>
        </tr>
    </table>
</div>
