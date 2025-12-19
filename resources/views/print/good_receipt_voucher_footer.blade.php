<style>
    .footer {
        position: absolute;
    }

    .footer {
        bottom: 0;
        height: 100px;
    }

    .footer {
        width: 100%;
        text-align: center;
        position: fixed;
        font-size: 10px;
        padding-top: -20px;
    }

    .font-weight-bold {
        font-weight: 700 !important;
    }

    .white-space-pre-line {
        white-space: pre-line;
        white-space: pre;
        word-wrap: normal;
    }

    .pagenum:before {
        content: counter(page);
    }
</style>
<div class="footer" style="height: 100px;">
    <table style="width:100%;">
        <tr>
            <td width="40%"><span style="font-weight: bold">{{ __('custom.confirmed_by') }} :</span> {{ $grvData->confirmed_by? $grvData->confirmed_by->empFullName:'' }}</td>
            <td><span style="font-weight: bold">{{ __('custom.reviewed_by') }} :</span> </td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td><span style="font-weight: bold">{{ __('custom.electronically_approved_by') }} :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            @if ($grvData->approved_by)
                @foreach ($grvData->approved_by as $det)
                    <td style="padding-right: 25px;font-size: 9px;">
                        <div>
                            @if($det->employee)
                                {{$det->employee->empFullName }}
                            @endif
                        </div>
                        <div><span>
                @if(!empty($det->approvedDate))
                                    {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                                @endif
              </span></div>
                        <div style="width: 3px"></div>
                    </td>
                @endforeach
            @endif
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td colspan="3" style="width:100%">
                <hr style="color: #d3d9df border-top: 2px solid black; height: 2px; color: black">
            </td>
        </tr>
        <tr>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                @if ($grvData->companydocumentattachment_by)
                    <p><span style="font-weight: bold"><span
                                    class="white-space-pre-line">{!! nl2br($grvData->companydocumentattachment_by?$grvData->companydocumentattachment_by[0]->docRefNumber:'') !!}</span></span>
                    </p>
                @endif
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">{{ __('custom.Page') }}<span class="pagenum"></span></span><br>
                @if ($grvData->company)
                    {{$grvData->company->CompanyName}}
                @endif
            </td>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span style="margin-left: 55%;">{{ __('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>