<table class="table table-responsive" id="gRVMasters-table">
    <thead>
        <tr>
            <th>Grvtype</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Companyaddress</th>
        <th>Companyfinanceyearid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Grvdate</th>
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
        <th>Grvconfirmedbyempid</th>
        <th>Grvconfirmedbyname</th>
        <th>Grvconfirmeddate</th>
        <th>Grvcancelledyn</th>
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
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Invoicebeforegrvyn</th>
        <th>Deliveryconfirmedyn</th>
        <th>Intercompanytransferyn</th>
        <th>Fromcompanyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($gRVMasters as $gRVMaster)
        <tr>
            <td>{!! $gRVMaster->grvType !!}</td>
            <td>{!! $gRVMaster->companySystemID !!}</td>
            <td>{!! $gRVMaster->companyID !!}</td>
            <td>{!! $gRVMaster->serviceLineSystemID !!}</td>
            <td>{!! $gRVMaster->serviceLineCode !!}</td>
            <td>{!! $gRVMaster->companyAddress !!}</td>
            <td>{!! $gRVMaster->companyFinanceYearID !!}</td>
            <td>{!! $gRVMaster->FYBiggin !!}</td>
            <td>{!! $gRVMaster->FYEnd !!}</td>
            <td>{!! $gRVMaster->documentSystemID !!}</td>
            <td>{!! $gRVMaster->documentID !!}</td>
            <td>{!! $gRVMaster->grvDate !!}</td>
            <td>{!! $gRVMaster->grvSerialNo !!}</td>
            <td>{!! $gRVMaster->grvPrimaryCode !!}</td>
            <td>{!! $gRVMaster->grvDoRefNo !!}</td>
            <td>{!! $gRVMaster->grvNarration !!}</td>
            <td>{!! $gRVMaster->grvLocation !!}</td>
            <td>{!! $gRVMaster->grvDOpersonName !!}</td>
            <td>{!! $gRVMaster->grvDOpersonResID !!}</td>
            <td>{!! $gRVMaster->grvDOpersonTelNo !!}</td>
            <td>{!! $gRVMaster->grvDOpersonVehicleNo !!}</td>
            <td>{!! $gRVMaster->supplierID !!}</td>
            <td>{!! $gRVMaster->supplierPrimaryCode !!}</td>
            <td>{!! $gRVMaster->supplierName !!}</td>
            <td>{!! $gRVMaster->supplierAddress !!}</td>
            <td>{!! $gRVMaster->supplierTelephone !!}</td>
            <td>{!! $gRVMaster->supplierFax !!}</td>
            <td>{!! $gRVMaster->supplierEmail !!}</td>
            <td>{!! $gRVMaster->liabilityAccountSysemID !!}</td>
            <td>{!! $gRVMaster->liabilityAccount !!}</td>
            <td>{!! $gRVMaster->UnbilledGRVAccountSystemID !!}</td>
            <td>{!! $gRVMaster->UnbilledGRVAccount !!}</td>
            <td>{!! $gRVMaster->localCurrencyID !!}</td>
            <td>{!! $gRVMaster->localCurrencyER !!}</td>
            <td>{!! $gRVMaster->companyReportingCurrencyID !!}</td>
            <td>{!! $gRVMaster->companyReportingER !!}</td>
            <td>{!! $gRVMaster->supplierDefaultCurrencyID !!}</td>
            <td>{!! $gRVMaster->supplierDefaultER !!}</td>
            <td>{!! $gRVMaster->supplierTransactionCurrencyID !!}</td>
            <td>{!! $gRVMaster->supplierTransactionER !!}</td>
            <td>{!! $gRVMaster->grvConfirmedYN !!}</td>
            <td>{!! $gRVMaster->grvConfirmedByEmpID !!}</td>
            <td>{!! $gRVMaster->grvConfirmedByName !!}</td>
            <td>{!! $gRVMaster->grvConfirmedDate !!}</td>
            <td>{!! $gRVMaster->grvCancelledYN !!}</td>
            <td>{!! $gRVMaster->grvCancelledBy !!}</td>
            <td>{!! $gRVMaster->grvCancelledByName !!}</td>
            <td>{!! $gRVMaster->grvCancelledDate !!}</td>
            <td>{!! $gRVMaster->grvTotalComRptCurrency !!}</td>
            <td>{!! $gRVMaster->grvTotalLocalCurrency !!}</td>
            <td>{!! $gRVMaster->grvTotalSupplierDefaultCurrency !!}</td>
            <td>{!! $gRVMaster->grvTotalSupplierTransactionCurrency !!}</td>
            <td>{!! $gRVMaster->grvDiscountPercentage !!}</td>
            <td>{!! $gRVMaster->grvDiscountAmount !!}</td>
            <td>{!! $gRVMaster->approved !!}</td>
            <td>{!! $gRVMaster->approvedDate !!}</td>
            <td>{!! $gRVMaster->timesReferred !!}</td>
            <td>{!! $gRVMaster->RollLevForApp_curr !!}</td>
            <td>{!! $gRVMaster->invoiceBeforeGRVYN !!}</td>
            <td>{!! $gRVMaster->deliveryConfirmedYN !!}</td>
            <td>{!! $gRVMaster->interCompanyTransferYN !!}</td>
            <td>{!! $gRVMaster->FromCompanyID !!}</td>
            <td>{!! $gRVMaster->createdUserGroup !!}</td>
            <td>{!! $gRVMaster->createdPcID !!}</td>
            <td>{!! $gRVMaster->createdUserID !!}</td>
            <td>{!! $gRVMaster->modifiedPc !!}</td>
            <td>{!! $gRVMaster->modifiedUser !!}</td>
            <td>{!! $gRVMaster->createdDateTime !!}</td>
            <td>{!! $gRVMaster->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['gRVMasters.destroy', $gRVMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('gRVMasters.show', [$gRVMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('gRVMasters.edit', [$gRVMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>