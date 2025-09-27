<div style="width: 100%; text-align: center; font-size: 10px; padding-top: 10px;">
    <table style="width:100%;">
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <span>{{trans('custom.printed_date')}} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; text-align: center;font-size: 10px;vertical-align: bottom;">
                <span style="float: right;">{{trans('custom.page')}} <span>{PAGENO}</span></span><br>
            </td>
        </tr>
    </table>
</div>
