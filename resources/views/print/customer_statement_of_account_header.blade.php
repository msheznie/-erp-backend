<div style="width: 100%; text-align: center; font-size: 10px;">
    <table style="width: 100%">
        <tr>
            <td valign="top" style="width: 30%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <img src="{{$companylogo}}" width="180px" height="60px"><br>
            </td>
            <td valign="top" style="width: 70%; @if(isset($lang) && $lang === 'ar') text-align: right; @else text-align: left; @endif">
                <br><br>
                <span style="font-weight: bold; font-size: 12px;">{{ trans('custom.statement_of_account_for_the_period') }} {{ $fromDate }}
                    {{ trans('custom.to') }} {{ $toDate }}</span>
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
