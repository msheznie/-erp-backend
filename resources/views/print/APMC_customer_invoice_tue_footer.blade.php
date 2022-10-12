<div id="footer">
    @if($request->line_invoiceDetails)
        <div class="" style="">
            @else
                <div class="" style="">
                    @endif
                </div>

                @if(!$request->line_rentalPeriod)
                    <div class="" style="margin-top: 10px">
                        <table style="width: 100%">
                            <tr>
                                <td>
                                    <span class="font-weight-bold"><b>Approved By :</b></span>
                                </td>
                            </tr>
                            <tr>
                                @foreach ($request->approved_by as $det)
                                    <td style="padding-right: 25px" class="text-center">
                                        <b>
                                        @if($det->employee)
                                            {{$det->employee->empFullName }}
                                            <br>

                                            @if($det->employee->details)
                                                @if($det->employee->details->designation)
                                                    {{$det->employee->details->designation->designation}}
                                                @endif
                                            @endif
                                            <br><br>
                                            @if($det->employee)
                                                {{ \App\helper\Helper::convertDateWithTime($det->approvedDate)}}
                                            @endif
                                        @endif
                                        </b>
                                    </td>
                                @endforeach
                            </tr>
                        </table>
                    </div>
                @endif
        </div>
</div>
