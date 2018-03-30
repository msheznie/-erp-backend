<table class="table table-responsive" id="procumentOrders-table">
    <thead>
        <tr>
            <th>Poprocessid</th>
        <th>Companyid</th>
        <th>Departmentid</th>
        <th>Serviceline</th>
        <th>Companyaddress</th>
        <th>Documentid</th>
        <th>Purchaseordercode</th>
        <th>Serialnumber</th>
        <th>Supplierid</th>
        <th>Supplierprimarycode</th>
        <th>Suppliername</th>
        <th>Supplieraddress</th>
        <th>Suppliertelephone</th>
        <th>Supplierfax</th>
        <th>Supplieremail</th>
        <th>Creditperiod</th>
        <th>Expecteddeliverydate</th>
        <th>Narration</th>
        <th>Polocation</th>
        <th>Financecategory</th>
        <th>Referencenumber</th>
        <th>Shippingaddressid</th>
        <th>Shippingaddressdescriprion</th>
        <th>Invoicetoaddressid</th>
        <th>Invoicetoaddressdescription</th>
        <th>Soldtoaddressid</th>
        <th>Soldtoaddressdescriprion</th>
        <th>Paymentterms</th>
        <th>Deliveryterms</th>
        <th>Panaltyterms</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Supplierdefaultcurrencyid</th>
        <th>Supplierdefaulter</th>
        <th>Suppliertransactioncurrencyid</th>
        <th>Suppliertransactioner</th>
        <th>Poconfirmedyn</th>
        <th>Poconfirmedbyempid</th>
        <th>Poconfirmedbyname</th>
        <th>Poconfirmeddate</th>
        <th>Pocancelledyn</th>
        <th>Pocancelledby</th>
        <th>Pocancelledbyname</th>
        <th>Pocancelleddate</th>
        <th>Cancelledcomments</th>
        <th>Pototalcomrptcurrency</th>
        <th>Pototallocalcurrency</th>
        <th>Pototalsupplierdefaultcurrency</th>
        <th>Pototalsuppliertransactioncurrency</th>
        <th>Podiscountpercentage</th>
        <th>Podiscountamount</th>
        <th>Suppliervateligible</th>
        <th>Vatpercentage</th>
        <th>Vatamount</th>
        <th>Vatamountlocal</th>
        <th>Vatamountrpt</th>
        <th>Shiptocontactpersonid</th>
        <th>Shiptocontactpersontelephone</th>
        <th>Shiptocontactpersonfaxno</th>
        <th>Shiptocontactpersonemail</th>
        <th>Invoicetocontactpersonid</th>
        <th>Invoicetocontactpersontelephone</th>
        <th>Invoicetocontactpersonfaxno</th>
        <th>Invoicetocontactpersonemail</th>
        <th>Soldtocontactpersonid</th>
        <th>Soldtocontactpersontelephone</th>
        <th>Soldtocontactpersonfaxno</th>
        <th>Soldtocontactpersonemail</th>
        <th>Priority</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Addonpercent</th>
        <th>Addondefaultpercent</th>
        <th>Grvtrackingid</th>
        <th>Logisticdoneyn</th>
        <th>Poclosedyn</th>
        <th>Grvrecieved</th>
        <th>Invoicedbooked</th>
        <th>Timesreferred</th>
        <th>Potype</th>
        <th>Potype N</th>
        <th>Docrefno</th>
        <th>Rolllevforapp Curr</th>
        <th>Senttosupplier</th>
        <th>Senttosupplierbyempid</th>
        <th>Senttosupplierbyempname</th>
        <th>Senttosupplierdate</th>
        <th>Budgetblockyn</th>
        <th>Budgetyear</th>
        <th>Hidepoyn</th>
        <th>Hidebyempid</th>
        <th>Hidebyempname</th>
        <th>Hidedate</th>
        <th>Hidecomments</th>
        <th>Wo Purchaseorderid</th>
        <th>Wo Periodfrom</th>
        <th>Wo Periodto</th>
        <th>Wo Noofautogenerationtimes</th>
        <th>Wo Noofgeneratedtimes</th>
        <th>Wo Fullygenerated</th>
        <th>Wo Amendyn</th>
        <th>Wo Amendrequesteddate</th>
        <th>Wo Amendrequestedbyempid</th>
        <th>Wo Confirmedyn</th>
        <th>Wo Confirmeddate</th>
        <th>Wo Confirmedbyempid</th>
        <th>Wo Terminateyn</th>
        <th>Wo Terminateddate</th>
        <th>Wo Terminatedbyempid</th>
        <th>Wo Terminatecomments</th>
        <th>Partiallygrvallowed</th>
        <th>Logisticsavailable</th>
        <th>Vatregisteredyn</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Isselected</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($procumentOrders as $procumentOrder)
        <tr>
            <td>{!! $procumentOrder->poProcessId !!}</td>
            <td>{!! $procumentOrder->companyID !!}</td>
            <td>{!! $procumentOrder->departmentID !!}</td>
            <td>{!! $procumentOrder->serviceLine !!}</td>
            <td>{!! $procumentOrder->companyAddress !!}</td>
            <td>{!! $procumentOrder->documentID !!}</td>
            <td>{!! $procumentOrder->purchaseOrderCode !!}</td>
            <td>{!! $procumentOrder->serialNumber !!}</td>
            <td>{!! $procumentOrder->supplierID !!}</td>
            <td>{!! $procumentOrder->supplierPrimaryCode !!}</td>
            <td>{!! $procumentOrder->supplierName !!}</td>
            <td>{!! $procumentOrder->supplierAddress !!}</td>
            <td>{!! $procumentOrder->supplierTelephone !!}</td>
            <td>{!! $procumentOrder->supplierFax !!}</td>
            <td>{!! $procumentOrder->supplierEmail !!}</td>
            <td>{!! $procumentOrder->creditPeriod !!}</td>
            <td>{!! $procumentOrder->expectedDeliveryDate !!}</td>
            <td>{!! $procumentOrder->narration !!}</td>
            <td>{!! $procumentOrder->poLocation !!}</td>
            <td>{!! $procumentOrder->financeCategory !!}</td>
            <td>{!! $procumentOrder->referenceNumber !!}</td>
            <td>{!! $procumentOrder->shippingAddressID !!}</td>
            <td>{!! $procumentOrder->shippingAddressDescriprion !!}</td>
            <td>{!! $procumentOrder->invoiceToAddressID !!}</td>
            <td>{!! $procumentOrder->invoiceToAddressDescription !!}</td>
            <td>{!! $procumentOrder->soldToAddressID !!}</td>
            <td>{!! $procumentOrder->soldToAddressDescriprion !!}</td>
            <td>{!! $procumentOrder->paymentTerms !!}</td>
            <td>{!! $procumentOrder->deliveryTerms !!}</td>
            <td>{!! $procumentOrder->panaltyTerms !!}</td>
            <td>{!! $procumentOrder->localCurrencyID !!}</td>
            <td>{!! $procumentOrder->localCurrencyER !!}</td>
            <td>{!! $procumentOrder->companyReportingCurrencyID !!}</td>
            <td>{!! $procumentOrder->companyReportingER !!}</td>
            <td>{!! $procumentOrder->supplierDefaultCurrencyID !!}</td>
            <td>{!! $procumentOrder->supplierDefaultER !!}</td>
            <td>{!! $procumentOrder->supplierTransactionCurrencyID !!}</td>
            <td>{!! $procumentOrder->supplierTransactionER !!}</td>
            <td>{!! $procumentOrder->poConfirmedYN !!}</td>
            <td>{!! $procumentOrder->poConfirmedByEmpID !!}</td>
            <td>{!! $procumentOrder->poConfirmedByName !!}</td>
            <td>{!! $procumentOrder->poConfirmedDate !!}</td>
            <td>{!! $procumentOrder->poCancelledYN !!}</td>
            <td>{!! $procumentOrder->poCancelledBy !!}</td>
            <td>{!! $procumentOrder->poCancelledByName !!}</td>
            <td>{!! $procumentOrder->poCancelledDate !!}</td>
            <td>{!! $procumentOrder->cancelledComments !!}</td>
            <td>{!! $procumentOrder->poTotalComRptCurrency !!}</td>
            <td>{!! $procumentOrder->poTotalLocalCurrency !!}</td>
            <td>{!! $procumentOrder->poTotalSupplierDefaultCurrency !!}</td>
            <td>{!! $procumentOrder->poTotalSupplierTransactionCurrency !!}</td>
            <td>{!! $procumentOrder->poDiscountPercentage !!}</td>
            <td>{!! $procumentOrder->poDiscountAmount !!}</td>
            <td>{!! $procumentOrder->supplierVATEligible !!}</td>
            <td>{!! $procumentOrder->VATPercentage !!}</td>
            <td>{!! $procumentOrder->VATAmount !!}</td>
            <td>{!! $procumentOrder->VATAmountLocal !!}</td>
            <td>{!! $procumentOrder->VATAmountRpt !!}</td>
            <td>{!! $procumentOrder->shipTocontactPersonID !!}</td>
            <td>{!! $procumentOrder->shipTocontactPersonTelephone !!}</td>
            <td>{!! $procumentOrder->shipTocontactPersonFaxNo !!}</td>
            <td>{!! $procumentOrder->shipTocontactPersonEmail !!}</td>
            <td>{!! $procumentOrder->invoiceTocontactPersonID !!}</td>
            <td>{!! $procumentOrder->invoiceTocontactPersonTelephone !!}</td>
            <td>{!! $procumentOrder->invoiceTocontactPersonFaxNo !!}</td>
            <td>{!! $procumentOrder->invoiceTocontactPersonEmail !!}</td>
            <td>{!! $procumentOrder->soldTocontactPersonID !!}</td>
            <td>{!! $procumentOrder->soldTocontactPersonTelephone !!}</td>
            <td>{!! $procumentOrder->soldTocontactPersonFaxNo !!}</td>
            <td>{!! $procumentOrder->soldTocontactPersonEmail !!}</td>
            <td>{!! $procumentOrder->priority !!}</td>
            <td>{!! $procumentOrder->approved !!}</td>
            <td>{!! $procumentOrder->approvedDate !!}</td>
            <td>{!! $procumentOrder->addOnPercent !!}</td>
            <td>{!! $procumentOrder->addOnDefaultPercent !!}</td>
            <td>{!! $procumentOrder->GRVTrackingID !!}</td>
            <td>{!! $procumentOrder->logisticDoneYN !!}</td>
            <td>{!! $procumentOrder->poClosedYN !!}</td>
            <td>{!! $procumentOrder->grvRecieved !!}</td>
            <td>{!! $procumentOrder->invoicedBooked !!}</td>
            <td>{!! $procumentOrder->timesReferred !!}</td>
            <td>{!! $procumentOrder->poType !!}</td>
            <td>{!! $procumentOrder->poType_N !!}</td>
            <td>{!! $procumentOrder->docRefNo !!}</td>
            <td>{!! $procumentOrder->RollLevForApp_curr !!}</td>
            <td>{!! $procumentOrder->sentToSupplier !!}</td>
            <td>{!! $procumentOrder->sentToSupplierByEmpID !!}</td>
            <td>{!! $procumentOrder->sentToSupplierByEmpName !!}</td>
            <td>{!! $procumentOrder->sentToSupplierDate !!}</td>
            <td>{!! $procumentOrder->budgetBlockYN !!}</td>
            <td>{!! $procumentOrder->budgetYear !!}</td>
            <td>{!! $procumentOrder->hidePOYN !!}</td>
            <td>{!! $procumentOrder->hideByEmpID !!}</td>
            <td>{!! $procumentOrder->hideByEmpName !!}</td>
            <td>{!! $procumentOrder->hideDate !!}</td>
            <td>{!! $procumentOrder->hideComments !!}</td>
            <td>{!! $procumentOrder->WO_purchaseOrderID !!}</td>
            <td>{!! $procumentOrder->WO_PeriodFrom !!}</td>
            <td>{!! $procumentOrder->WO_PeriodTo !!}</td>
            <td>{!! $procumentOrder->WO_NoOfAutoGenerationTimes !!}</td>
            <td>{!! $procumentOrder->WO_NoOfGeneratedTimes !!}</td>
            <td>{!! $procumentOrder->WO_fullyGenerated !!}</td>
            <td>{!! $procumentOrder->WO_amendYN !!}</td>
            <td>{!! $procumentOrder->WO_amendRequestedDate !!}</td>
            <td>{!! $procumentOrder->WO_amendRequestedByEmpID !!}</td>
            <td>{!! $procumentOrder->WO_confirmedYN !!}</td>
            <td>{!! $procumentOrder->WO_confirmedDate !!}</td>
            <td>{!! $procumentOrder->WO_confirmedByEmpID !!}</td>
            <td>{!! $procumentOrder->WO_terminateYN !!}</td>
            <td>{!! $procumentOrder->WO_terminatedDate !!}</td>
            <td>{!! $procumentOrder->WO_terminatedByEmpID !!}</td>
            <td>{!! $procumentOrder->WO_terminateComments !!}</td>
            <td>{!! $procumentOrder->partiallyGRVAllowed !!}</td>
            <td>{!! $procumentOrder->logisticsAvailable !!}</td>
            <td>{!! $procumentOrder->vatRegisteredYN !!}</td>
            <td>{!! $procumentOrder->createdUserGroup !!}</td>
            <td>{!! $procumentOrder->createdPcID !!}</td>
            <td>{!! $procumentOrder->createdUserID !!}</td>
            <td>{!! $procumentOrder->modifiedPc !!}</td>
            <td>{!! $procumentOrder->modifiedUser !!}</td>
            <td>{!! $procumentOrder->createdDateTime !!}</td>
            <td>{!! $procumentOrder->isSelected !!}</td>
            <td>{!! $procumentOrder->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['procumentOrders.destroy', $procumentOrder->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('procumentOrders.show', [$procumentOrder->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('procumentOrders.edit', [$procumentOrder->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>