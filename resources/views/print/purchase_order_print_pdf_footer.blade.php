<div class="footer" style="height: 70px;">
    <table style="width:100%;">
        <tr>
            <td><span style="font-weight:bold; font-size: 20px;">{{ __('custom.electronically_approved_by') }} :</span></td>
        </tr>
        <tr>
            &nbsp;
        </tr>
    </table>
    <table style="width:100%">
        <tr>
            @if ($podata->approved_by)
                @foreach ($podata->approved_by as $det)
                    <td style="font-size: 20px; width: 60%">
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
            @else
                <td style="font-size: 9px; width: 60%">&nbsp;</td>
            @endif
            @if ($digitalStamp == 1 && isset($digitalStampDetails->image_url))
                <td style="width: 40%; font-size: 9px; text-align: right;">

                    <img src="{{$digitalStampDetails->image_url}}" width="180px" height="60px" class="container">
                </td>
            @endif
        </tr>
    </table>

</div>
