<table class="table table-responsive" id="quotationMasterRefferedbacks-table">
    <thead>
        <tr>
            <th>Quotationmasterid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Quotationcode</th>
        <th>Serialnumber</th>
        <th>Documentdate</th>
        <th>Documentexpdate</th>
        <th>Salespersonid</th>
        <th>Versionno</th>
        <th>Referenceno</th>
        <th>Narration</th>
        <th>Note</th>
        <th>Contactpersonname</th>
        <th>Contactpersonnumber</th>
        <th>Customersystemcode</th>
        <th>Customercode</th>
        <th>Customername</th>
        <th>Customeraddress</th>
        <th>Customertelephone</th>
        <th>Customerfax</th>
        <th>Customeremail</th>
        <th>Customerreceivableautoid</th>
        <th>Customerreceivablesystemglcode</th>
        <th>Customerreceivableglaccount</th>
        <th>Customerreceivabledescription</th>
        <th>Customerreceivabletype</th>
        <th>Transactioncurrencyid</th>
        <th>Transactioncurrency</th>
        <th>Transactionexchangerate</th>
        <th>Transactionamount</th>
        <th>Transactioncurrencydecimalplaces</th>
        <th>Companylocalcurrencyid</th>
        <th>Companylocalcurrency</th>
        <th>Companylocalexchangerate</th>
        <th>Companylocalamount</th>
        <th>Companylocalcurrencydecimalplaces</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportingcurrency</th>
        <th>Companyreportingexchangerate</th>
        <th>Companyreportingamount</th>
        <th>Companyreportingcurrencydecimalplaces</th>
        <th>Customercurrencyid</th>
        <th>Customercurrency</th>
        <th>Customercurrencyexchangerate</th>
        <th>Customercurrencyamount</th>
        <th>Customercurrencydecimalplaces</th>
        <th>Isdeleted</th>
        <th>Deletedempid</th>
        <th>Deleteddate</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approvedyn</th>
        <th>Approveddate</th>
        <th>Approvedempsystemid</th>
        <th>Approvedbyempid</th>
        <th>Approvedbyempname</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Closedyn</th>
        <th>Closeddate</th>
        <th>Closedreason</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusersystemid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedusersystemid</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($quotationMasterRefferedbacks as $quotationMasterRefferedback)
        <tr>
            <td>{!! $quotationMasterRefferedback->quotationMasterID !!}</td>
            <td>{!! $quotationMasterRefferedback->documentSystemID !!}</td>
            <td>{!! $quotationMasterRefferedback->documentID !!}</td>
            <td>{!! $quotationMasterRefferedback->quotationCode !!}</td>
            <td>{!! $quotationMasterRefferedback->serialNumber !!}</td>
            <td>{!! $quotationMasterRefferedback->documentDate !!}</td>
            <td>{!! $quotationMasterRefferedback->documentExpDate !!}</td>
            <td>{!! $quotationMasterRefferedback->salesPersonID !!}</td>
            <td>{!! $quotationMasterRefferedback->versionNo !!}</td>
            <td>{!! $quotationMasterRefferedback->referenceNo !!}</td>
            <td>{!! $quotationMasterRefferedback->narration !!}</td>
            <td>{!! $quotationMasterRefferedback->Note !!}</td>
            <td>{!! $quotationMasterRefferedback->contactPersonName !!}</td>
            <td>{!! $quotationMasterRefferedback->contactPersonNumber !!}</td>
            <td>{!! $quotationMasterRefferedback->customerSystemCode !!}</td>
            <td>{!! $quotationMasterRefferedback->customerCode !!}</td>
            <td>{!! $quotationMasterRefferedback->customerName !!}</td>
            <td>{!! $quotationMasterRefferedback->customerAddress !!}</td>
            <td>{!! $quotationMasterRefferedback->customerTelephone !!}</td>
            <td>{!! $quotationMasterRefferedback->customerFax !!}</td>
            <td>{!! $quotationMasterRefferedback->customerEmail !!}</td>
            <td>{!! $quotationMasterRefferedback->customerReceivableAutoID !!}</td>
            <td>{!! $quotationMasterRefferedback->customerReceivableSystemGLCode !!}</td>
            <td>{!! $quotationMasterRefferedback->customerReceivableGLAccount !!}</td>
            <td>{!! $quotationMasterRefferedback->customerReceivableDescription !!}</td>
            <td>{!! $quotationMasterRefferedback->customerReceivableType !!}</td>
            <td>{!! $quotationMasterRefferedback->transactionCurrencyID !!}</td>
            <td>{!! $quotationMasterRefferedback->transactionCurrency !!}</td>
            <td>{!! $quotationMasterRefferedback->transactionExchangeRate !!}</td>
            <td>{!! $quotationMasterRefferedback->transactionAmount !!}</td>
            <td>{!! $quotationMasterRefferedback->transactionCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMasterRefferedback->companyLocalCurrencyID !!}</td>
            <td>{!! $quotationMasterRefferedback->companyLocalCurrency !!}</td>
            <td>{!! $quotationMasterRefferedback->companyLocalExchangeRate !!}</td>
            <td>{!! $quotationMasterRefferedback->companyLocalAmount !!}</td>
            <td>{!! $quotationMasterRefferedback->companyLocalCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMasterRefferedback->companyReportingCurrencyID !!}</td>
            <td>{!! $quotationMasterRefferedback->companyReportingCurrency !!}</td>
            <td>{!! $quotationMasterRefferedback->companyReportingExchangeRate !!}</td>
            <td>{!! $quotationMasterRefferedback->companyReportingAmount !!}</td>
            <td>{!! $quotationMasterRefferedback->companyReportingCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMasterRefferedback->customerCurrencyID !!}</td>
            <td>{!! $quotationMasterRefferedback->customerCurrency !!}</td>
            <td>{!! $quotationMasterRefferedback->customerCurrencyExchangeRate !!}</td>
            <td>{!! $quotationMasterRefferedback->customerCurrencyAmount !!}</td>
            <td>{!! $quotationMasterRefferedback->customerCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMasterRefferedback->isDeleted !!}</td>
            <td>{!! $quotationMasterRefferedback->deletedEmpID !!}</td>
            <td>{!! $quotationMasterRefferedback->deletedDate !!}</td>
            <td>{!! $quotationMasterRefferedback->confirmedYN !!}</td>
            <td>{!! $quotationMasterRefferedback->confirmedByEmpSystemID !!}</td>
            <td>{!! $quotationMasterRefferedback->confirmedByEmpID !!}</td>
            <td>{!! $quotationMasterRefferedback->confirmedByName !!}</td>
            <td>{!! $quotationMasterRefferedback->confirmedDate !!}</td>
            <td>{!! $quotationMasterRefferedback->approvedYN !!}</td>
            <td>{!! $quotationMasterRefferedback->approvedDate !!}</td>
            <td>{!! $quotationMasterRefferedback->approvedEmpSystemID !!}</td>
            <td>{!! $quotationMasterRefferedback->approvedbyEmpID !!}</td>
            <td>{!! $quotationMasterRefferedback->approvedbyEmpName !!}</td>
            <td>{!! $quotationMasterRefferedback->refferedBackYN !!}</td>
            <td>{!! $quotationMasterRefferedback->timesReferred !!}</td>
            <td>{!! $quotationMasterRefferedback->RollLevForApp_curr !!}</td>
            <td>{!! $quotationMasterRefferedback->closedYN !!}</td>
            <td>{!! $quotationMasterRefferedback->closedDate !!}</td>
            <td>{!! $quotationMasterRefferedback->closedReason !!}</td>
            <td>{!! $quotationMasterRefferedback->companySystemID !!}</td>
            <td>{!! $quotationMasterRefferedback->companyID !!}</td>
            <td>{!! $quotationMasterRefferedback->createdUserSystemID !!}</td>
            <td>{!! $quotationMasterRefferedback->createdUserGroup !!}</td>
            <td>{!! $quotationMasterRefferedback->createdPCID !!}</td>
            <td>{!! $quotationMasterRefferedback->createdUserID !!}</td>
            <td>{!! $quotationMasterRefferedback->createdDateTime !!}</td>
            <td>{!! $quotationMasterRefferedback->createdUserName !!}</td>
            <td>{!! $quotationMasterRefferedback->modifiedUserSystemID !!}</td>
            <td>{!! $quotationMasterRefferedback->modifiedPCID !!}</td>
            <td>{!! $quotationMasterRefferedback->modifiedUserID !!}</td>
            <td>{!! $quotationMasterRefferedback->modifiedDateTime !!}</td>
            <td>{!! $quotationMasterRefferedback->modifiedUserName !!}</td>
            <td>{!! $quotationMasterRefferedback->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['quotationMasterRefferedbacks.destroy', $quotationMasterRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('quotationMasterRefferedbacks.show', [$quotationMasterRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('quotationMasterRefferedbacks.edit', [$quotationMasterRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>