<div class="footer">
    <table style="width:100%;">
        <tr>
            <td>
                <span style="font-weight:bold;">{{ __('custom.confirmed_by') }} : </span> {{ $request->confirmed_by?$request->confirmed_by->empName:'' }}
            </td>
        </tr>
        <tr>
            &nbsp;
        </tr>
        <tr>
            <td>
                <span style="font-weight:bold;">{{ __('custom.approved_by') }} : </span>

            <td>
                
            </td>
        </tr>
    </table>

    <table style="width:100%">
        <tr>
            @if ($request->approved_by)
                @foreach ($request->approved_by as $det)
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
