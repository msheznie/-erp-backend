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
<div class="footer">
    <table style="width:100%;">
        <tr>
            <td width="40%"><span
                        style="font-weight:bold;">{{ __('custom.confirmed_by') }} :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
            </td>
            <td><span style="font-weight:bold;">{{ __('custom.review_by') }} :</span></td>
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            <td><span style="font-weight:bold;">{{ __('custom.electronically_approved_by') }} :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%;">
        <tr>
            @if ($masterdata->approved_by)
                @foreach ($masterdata->approved_by as $det)
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
            <td colspan="5" style="width:100%">
                <hr style="color: #d3d9df border-top: 2px solid black; height: 2px; color: black">
            </td>
        </tr>
        <tr>
            <td style="width:33%;font-size: 10px;vertical-align: top;">
                <span class="white-space-pre-line font-weight-bold">{!! nl2br($docRef) !!}</span>
            </td>
            <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
                <span style="text-align: center">{{ __('custom.page') }} <span class="pagenum">{nbpg}</span></span><br>
                @if ($masterdata->company)
                    {{$masterdata->company->CompanyName}}
                @endif
            </td>
            <td style="width:24%;font-size: 10px;vertical-align: top; text-align: right; margin-left: 100px">
            </td>
            <td style="width:7%;font-size: 10px;vertical-align: top;">
                <span style="text-align: right">{{ __('custom.printed_date') }} :</span>
            </td>
            <td style="width:5%;font-size: 10px;vertical-align: top;">
                <span style="text-align: right">{{date("d-M-y", strtotime(now()))}}</span>
            </td>
        </tr>
    </table>
</div>
