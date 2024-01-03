<div id="footer" style="padding-top: 25px">
    @if(!$request->line_rentalPeriod)
        @if ($request->isPerforma == 0)
            <table style="width:100%">
                <tr>
                    <td width="100px"><span class="font-weight-bold"><b>Created By :</b></span></td>
                </tr>
                <tr>

                    
                </tr>
                <tr>
                    <td>
                        @if ($request->createduser)
                            {{($request->createduser) ? $request->createduser->empFullName : ''}}
                            <br>
                            {{ \App\helper\Helper::dateFormat($request->customerInvoiceDate)}}
                        @endif
                    </td>
                </tr>
            </table>
        @endif
        <table style="width:100%; margin-top: 25px">
            <tr>
                <td><span style="font-weight:bold;">{{ __('custom.electronically_approved_by') }} :</span></td>
            </tr>
            <tr>
                &nbsp;
            </tr>
        </table>
        <table style="width:100%">
            <tr>
                @foreach ($request->approved_by as $det)
                    <td style="padding-right: 25px" class="text-center">
                            @if($det->employee)
                                {{$det->employee->empFullName }}
                                <br>

                                @if($det->employee->details)
                                    @if($det->employee->details->designation)
                                        {{$det->employee->details->designation->designation}}
                                    @endif
                                @endif
                                @if($det->employee)
                                    {{ \App\helper\Helper::dateFormat($det->approvedDate)}}
                                @endif
                            @endif
                    </td>
                @endforeach
            </tr>
        </table>
    @endif
</div>
