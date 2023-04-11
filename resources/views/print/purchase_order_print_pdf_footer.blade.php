<div class="footer">
    <table style="width:100%;">
        <tr>
            <td><span style="font-weight:bold;">{{ __('custom.electronically_approved_by') }} :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%">
        <tr>
            @if ($podata->approved_by)
                @foreach ($podata->approved_by as $det)
                    <td style="font-size: 9px;">
                        <div>
                            <span>
                                @if($det->employee)
                                    {{$det->employee->empFullName }}
                                @endif
                            </span>
                        </div>
                        <div>
                            <span>
                                @if(isset($det->employee->hr_emp->designation))
                                    {{$det->employee->hr_emp->designation->DesDescription }}
                                @endif
                            </span>
                        </div>
                        <div>
                            <span>
                                @if(!empty($det->approvedDate))
                                    {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
                                @endif
                            </span>
                        </div>
                    </td>
                @endforeach
            @endif
        </tr>
    </table>

</div>
