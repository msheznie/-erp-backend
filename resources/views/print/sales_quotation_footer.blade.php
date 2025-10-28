<table style="width:100%;">
    <tr>
        <td width="40%"><span
                    style="font-weight: bold;">{{ __('custom.confirmed_by') }} :</span> {{ $masterdata->confirmed_by? $masterdata->confirmed_by->empFullName:'' }}
        </td>
        <td><span style="font-weight: bold;">{{ __('custom.reviewed_by') }} :</span></td>
    </tr>
</table>
<table style="width:100%;">
    <tr>
        <td><span style="font-weight: bold;">{{ __('custom.electronically_approved_by') }} :</span></td>
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
        <td colspan="3" style="width:100%">
            <hr style="background-color: black">
        </td>
    </tr>
    <tr>
        <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
        </td>
        <td style="width:33%; text-align: center;font-size: 10px;vertical-align: top;">
            <span style="text-align: center">{{ __('custom.page') }} <span class="pagenum"></span></span><br>
            @if ($masterdata->company)
                {{$masterdata->company->CompanyName}}
            @endif
        </td>
        <td style="width:33%;font-size: 10px;vertical-align: top;">
            <span style="margin-left: 50%;">{{ __('custom.printed_date') }} : {{date("d-M-y", strtotime(now()))}}</span>
        </td>
    </tr>
</table>

