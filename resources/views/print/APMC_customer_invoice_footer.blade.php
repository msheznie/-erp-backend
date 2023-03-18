<div id="footer">
    @if(!$request->line_rentalPeriod)
        <div style="margin-top: -50px">
            <table class="normal_font" width="100%">
                <tr style="width: 100%">
                    <td width="10%" style="vertical-align: top;">
                    </td>
                    <td width="20%" style="vertical-align: top;">
                        <span class="font-weight-bold"><B>{{ __('custom.electronically_approved_by') }} :</B></span>
                    </td>
                    <td width="20%" style="vertical-align: top;">
                        <span class="font-weight-bold">
                            @php
                                $employee = \App\Models\Employee::find($request->approvedByUserSystemID);
                            @endphp
                            @if($employee)
                                <B>{{ $employee->empName }}</B>
                            @endif
                        </span>
                    </td>
                    <td width="20%" style="vertical-align: top;">
                        <span class="font-weight-bold"><B>{{ __('custom.electronically_approved_date') }} :</B></span>
                    </td>
                    <td width="25%" style="vertical-align: top;">
                        <span class="font-weight-bold">


                            <B>{{ \App\helper\Helper::convertDateWithTime($request->approvedDate)}}</B>

                        </span>
                    </td>
                </tr>
            </table>
        </div>
    @endif
</div>


