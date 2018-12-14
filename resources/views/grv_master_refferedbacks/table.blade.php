<table class="table table-responsive" id="grvMasterRefferedbacks-table">
    <thead>
        <tr>
            <th>Grvautoid</th>
        <th>Grvtypeid</th>
        <th>Grvtype</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Companyaddress</th>
        <th>Companyfinanceyearid</th>
        <th>Companyfinanceperiodid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Grvdate</th>
        <th>Stampdate</th>
        <th>Grvserialno</th>
        <th>Grvprimarycode</th>
        <th>Grvdorefno</th>
        <th>Grvnarration</th>
        <th>Grvlocation</th>
        <th>Grvdopersonname</th>
        <th>Grvdopersonresid</th>
        <th>Grvdopersontelno</th>
        <th>Grvdopersonvehicleno</th>
        <th>Supplierid</th>
        <th>Supplierprimarycode</th>
        <th>Suppliername</th>
        <th>Supplieraddress</th>
        <th>Suppliertelephone</th>
        <th>Supplierfax</th>
        <th>Supplieremail</th>
        <th>Liabilityaccountsysemid</th>
        <th>Liabilityaccount</th>
        <th>Unbilledgrvaccountsystemid</th>
        <th>Unbilledgrvaccount</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Supplierdefaultcurrencyid</th>
        <th>Supplierdefaulter</th>
        <th>Suppliertransactioncurrencyid</th>
        <th>Suppliertransactioner</th>
        <th>Grvconfirmedyn</th>
        <th>Grvconfirmedbyempsystemid</th>
        <th>Grvconfirmedbyempid</th>
        <th>Grvconfirmedbyname</th>
        <th>Grvconfirmeddate</th>
        <th>Grvcancelledyn</th>
        <th>Grvcancelledbysystemid</th>
        <th>Grvcancelledby</th>
        <th>Grvcancelledbyname</th>
        <th>Grvcancelleddate</th>
        <th>Grvtotalcomrptcurrency</th>
        <th>Grvtotallocalcurrency</th>
        <th>Grvtotalsupplierdefaultcurrency</th>
        <th>Grvtotalsuppliertransactioncurrency</th>
        <th>Grvdiscountpercentage</th>
        <th>Grvdiscountamount</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Invoicebeforegrvyn</th>
        <th>Deliveryconfirmedyn</th>
        <th>Intercompanytransferyn</th>
        <th>Fromcompanysystemid</th>
        <th>Fromcompanyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($grvMasterRefferedbacks as $grvMasterRefferedback)
        <tr>
            <td>{!! $grvMasterRefferedback->grvAutoID !!}</td>
            <td>{!! $grvMasterRefferedback->grvTypeID !!}</td>
            <td>{!! $grvMasterRefferedback->grvType !!}</td>
            <td>{!! $grvMasterRefferedback->companySystemID !!}</td>
            <td>{!! $grvMasterRefferedback->companyID !!}</td>
            <td>{!! $grvMasterRefferedback->serviceLineSystemID !!}</td>
            <td>{!! $grvMasterRefferedback->serviceLineCode !!}</td>
            <td>{!! $grvMasterRefferedback->companyAddress !!}</td>
            <td>{!! $grvMasterRefferedback->companyFinanceYearID !!}</td>
            <td>{!! $grvMasterRefferedback->companyFinancePeriodID !!}</td>
            <td>{!! $grvMasterRefferedback->FYBiggin !!}</td>
            <td>{!! $grvMasterRefferedback->FYEnd !!}</td>
            <td>{!! $grvMasterRefferedback->documentSystemID !!}</td>
            <td>{!! $grvMasterRefferedback->documentID !!}</td>
            <td>{!! $grvMasterRefferedback->grvDate !!}</td>
            <td>{!! $grvMasterRefferedback->stampDate !!}</td>
            <td>{!! $grvMasterRefferedback->grvSerialNo !!}</td>
            <td>{!! $grvMasterRefferedback->grvPrimaryCode !!}</td>
            <td>{!! $grvMasterRefferedback->grvDoRefNo !!}</td>
            <td>{!! $grvMasterRefferedback->grvNarration !!}</td>
            <td>{!! $grvMasterRefferedback->grvLocation !!}</td>
            <td>{!! $grvMasterRefferedback->grvDOpersonName !!}</td>
            <td>{!! $grvMasterRefferedback->grvDOpersonResID !!}</td>
            <td>{!! $grvMasterRefferedback->grvDOpersonTelNo !!}</td>
            <td>{!! $grvMasterRefferedback->grvDOpersonVehicleNo !!}</td>
            <td>{!! $grvMasterRefferedback->supplierID !!}</td>
            <td>{!! $grvMasterRefferedback->supplierPrimaryCode !!}</td>
            <td>{!! $grvMasterRefferedback->supplierName !!}</td>
            <td>{!! $grvMasterRefferedback->supplierAddress !!}</td>
            <td>{!! $grvMasterRefferedback->supplierTelephone !!}</td>
            <td>{!! $grvMasterRefferedback->supplierFax !!}</td>
            <td>{!! $grvMasterRefferedback->supplierEmail !!}</td>
            <td>{!! $grvMasterRefferedback->liabilityAccountSysemID !!}</td>
            <td>{!! $grvMasterRefferedback->liabilityAccount !!}</td>
            <td>{!! $grvMasterRefferedback->UnbilledGRVAccountSystemID !!}</td>
            <td>{!! $grvMasterRefferedback->UnbilledGRVAccount !!}</td>
            <td>{!! $grvMasterRefferedback->localCurrencyID !!}</td>
            <td>{!! $grvMasterRefferedback->localCurrencyER !!}</td>
            <td>{!! $grvMasterRefferedback->companyReportingCurrencyID !!}</td>
            <td>{!! $grvMasterRefferedback->companyReportingER !!}</td>
            <td>{!! $grvMasterRefferedback->supplierDefaultCurrencyID !!}</td>
            <td>{!! $grvMasterRefferedback->supplierDefaultER !!}</td>
            <td>{!! $grvMasterRefferedback->supplierTransactionCurrencyID !!}</td>
            <td>{!! $grvMasterRefferedback->supplierTransactionER !!}</td>
            <td>{!! $grvMasterRefferedback->grvConfirmedYN !!}</td>
            <td>{!! $grvMasterRefferedback->grvConfirmedByEmpSystemID !!}</td>
            <td>{!! $grvMasterRefferedback->grvConfirmedByEmpID !!}</td>
            <td>{!! $grvMasterRefferedback->grvConfirmedByName !!}</td>
            <td>{!! $grvMasterRefferedback->grvConfirmedDate !!}</td>
            <td>{!! $grvMasterRefferedback->grvCancelledYN !!}</td>
            <td>{!! $grvMasterRefferedback->grvCancelledBySystemID !!}</td>
            <td>{!! $grvMasterRefferedback->grvCancelledBy !!}</td>
            <td>{!! $grvMasterRefferedback->grvCancelledByName !!}</td>
            <td>{!! $grvMasterRefferedback->grvCancelledDate !!}</td>
            <td>{!! $grvMasterRefferedback->grvTotalComRptCurrency !!}</td>
            <td>{!! $grvMasterRefferedback->grvTotalLocalCurrency !!}</td>
            <td>{!! $grvMasterRefferedback->grvTotalSupplierDefaultCurrency !!}</td>
            <td>{!! $grvMasterRefferedback->grvTotalSupplierTransactionCurrency !!}</td>
            <td>{!! $grvMasterRefferedback->grvDiscountPercentage !!}</td>
            <td>{!! $grvMasterRefferedback->grvDiscountAmount !!}</td>
            <td>{!! $grvMasterRefferedback->approved !!}</td>
            <td>{!! $grvMasterRefferedback->approvedDate !!}</td>
            <td>{!! $grvMasterRefferedback->approvedByUserID !!}</td>
            <td>{!! $grvMasterRefferedback->approvedByUserSystemID !!}</td>
            <td>{!! $grvMasterRefferedback->refferedBackYN !!}</td>
            <td>{!! $grvMasterRefferedback->timesReferred !!}</td>
            <td>{!! $grvMasterRefferedback->RollLevForApp_curr !!}</td>
            <td>{!! $grvMasterRefferedback->invoiceBeforeGRVYN !!}</td>
            <td>{!! $grvMasterRefferedback->deliveryConfirmedYN !!}</td>
            <td>{!! $grvMasterRefferedback->interCompanyTransferYN !!}</td>
            <td>{!! $grvMasterRefferedback->FromCompanySystemID !!}</td>
            <td>{!! $grvMasterRefferedback->FromCompanyID !!}</td>
            <td>{!! $grvMasterRefferedback->createdUserGroup !!}</td>
            <td>{!! $grvMasterRefferedback->createdPcID !!}</td>
            <td>{!! $grvMasterRefferedback->createdUserSystemID !!}</td>
            <td>{!! $grvMasterRefferedback->createdUserID !!}</td>
            <td>{!! $grvMasterRefferedback->modifiedPc !!}</td>
            <td>{!! $grvMasterRefferedback->modifiedUserSystemID !!}</td>
            <td>{!! $grvMasterRefferedback->modifiedUser !!}</td>
            <td>{!! $grvMasterRefferedback->createdDateTime !!}</td>
            <td>{!! $grvMasterRefferedback->TIMESTAMP !!}</td>
            <td>
                {!! Form::open(['route' => ['grvMasterRefferedbacks.destroy', $grvMasterRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('grvMasterRefferedbacks.show', [$grvMasterRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('grvMasterRefferedbacks.edit', [$grvMasterRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>