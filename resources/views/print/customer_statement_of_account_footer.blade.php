<div style="width: 100%; text-align: center; font-size: 10px; padding-top: 10px;">
    <table style="width:100%; @if(isset($lang) && $lang === 'ar') margin-left: 60px !important; @endif">
        <tr>
            <td colspan="2" style="width:100%; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <span style="font-weight: bold;">{{ trans('custom.kindly_confirm_the_balance_and_settle_the_pending_invoices_at_the_earliest') }}</span>
            </td>
        </tr>
        <tr>
            <td style="width:50%;font-size: 10px;vertical-align: bottom; @if(isset($lang) && $lang === 'ar') text-align: right; @endif">
                <span>{{ trans('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
            <td style="width:50%; font-size: 10px;vertical-align: bottom;" class="@if(isset($lang) && $lang === 'ar') text-left @else text-right @endif">
                <span style="@if(isset($lang) && $lang === 'ar') float: left !important; @else float: right !important; @endif">
                    {{ trans('custom.page') }} <span>{PAGENO}</span></span><br>
            </td>
        </tr>
    </table>
</div>
