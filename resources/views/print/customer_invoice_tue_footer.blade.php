<div id="footer">
    @if($request->line_invoiceDetails)
        <div class="" style="">
            @else
                <div class="" style="">
                    @endif
                    <table>
                        <tr>
                            <td width="100px" colspan="2"  style="text-decoration: underline;"><b> Remittance Details  </b></td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold"><b>BANK NAME</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                     @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->bankName}}
                                      @endif
                                    @else
                                    {{($request->bankaccount) ? $request->bankaccount->bankName : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold"><b>ACCOUNT NAME</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountName}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountName : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold"><b>ACCOUNT NO</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->AccountNo}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->AccountNo : ''}}
                                @endif

                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold"><b>IBAN NO</b></span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$request->accountIBANSecondary}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->accountIBAN : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                        <tr>
                            <td width="100px"><span class="font-weight-bold"><b>SWIFT Code</b> </span></td>
                            <td><b> :
                                @if($request->secondaryLogoCompanySystemID)
                                    @if($secondaryBankAccount->contract && $secondaryBankAccount->contract->secondary_bank_account)
                                        {{$secondaryBankAccount->contract->secondary_bank_account->accountSwiftCode}}
                                    @endif
                                @else
                                    {{($request->bankaccount) ? $request->bankaccount->accountSwiftCode : ''}}
                                @endif
                                </b>
                            </td>
                        </tr>
                    </table>
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
