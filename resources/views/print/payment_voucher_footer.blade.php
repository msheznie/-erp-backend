
<div style="width: 100%; text-align: center; font-size: 10px; padding-top: -20px; bottom: 0; height: 60px;">
    <table style="width:100%;">
        <tr>
            <td colspan="5" style="width:100%">
                <hr style="margin-top: 12px; margin-bottom: 12px; border-top: 2px solid black; height: 2px; color: black" >
            </td>
        </tr>
        <tr>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="white-space: pre; word-wrap: normal; font-weight: bold">{!! nl2br($docRef) !!}</span>
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">{{ __('custom.page') }} <span style="content: counter(page);">{PAGENO}</span>

                </span><br>
                @if ($masterdata->company)
                    {{$masterdata->company->CompanyName}}
                @endif
            </td>
            <td style="width:22%;font-size: 10px;vertical-align: top; text-align: right; margin-left: 100px">
            </td>
            <td style="width:10%;font-size: 10px;vertical-align: top;">
                <span style="text-align: right">{{ __('custom.printed_date') }} :</span>
            </td>
            <td style="width:7%;font-size: 10px;vertical-align: top;">
                <span style="text-align: right">{{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>
