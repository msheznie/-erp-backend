<table class="table table-responsive" id="quotationMasterVersions-table">
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
    @foreach($quotationMasterVersions as $quotationMasterVersion)
        <tr>
            <td>{!! $quotationMasterVersion->quotationMasterID !!}</td>
            <td>{!! $quotationMasterVersion->documentSystemID !!}</td>
            <td>{!! $quotationMasterVersion->documentID !!}</td>
            <td>{!! $quotationMasterVersion->quotationCode !!}</td>
            <td>{!! $quotationMasterVersion->serialNumber !!}</td>
            <td>{!! $quotationMasterVersion->documentDate !!}</td>
            <td>{!! $quotationMasterVersion->documentExpDate !!}</td>
            <td>{!! $quotationMasterVersion->salesPersonID !!}</td>
            <td>{!! $quotationMasterVersion->versionNo !!}</td>
            <td>{!! $quotationMasterVersion->referenceNo !!}</td>
            <td>{!! $quotationMasterVersion->narration !!}</td>
            <td>{!! $quotationMasterVersion->Note !!}</td>
            <td>{!! $quotationMasterVersion->contactPersonName !!}</td>
            <td>{!! $quotationMasterVersion->contactPersonNumber !!}</td>
            <td>{!! $quotationMasterVersion->customerSystemCode !!}</td>
            <td>{!! $quotationMasterVersion->customerCode !!}</td>
            <td>{!! $quotationMasterVersion->customerName !!}</td>
            <td>{!! $quotationMasterVersion->customerAddress !!}</td>
            <td>{!! $quotationMasterVersion->customerTelephone !!}</td>
            <td>{!! $quotationMasterVersion->customerFax !!}</td>
            <td>{!! $quotationMasterVersion->customerEmail !!}</td>
            <td>{!! $quotationMasterVersion->customerReceivableAutoID !!}</td>
            <td>{!! $quotationMasterVersion->customerReceivableSystemGLCode !!}</td>
            <td>{!! $quotationMasterVersion->customerReceivableGLAccount !!}</td>
            <td>{!! $quotationMasterVersion->customerReceivableDescription !!}</td>
            <td>{!! $quotationMasterVersion->customerReceivableType !!}</td>
            <td>{!! $quotationMasterVersion->transactionCurrencyID !!}</td>
            <td>{!! $quotationMasterVersion->transactionCurrency !!}</td>
            <td>{!! $quotationMasterVersion->transactionExchangeRate !!}</td>
            <td>{!! $quotationMasterVersion->transactionAmount !!}</td>
            <td>{!! $quotationMasterVersion->transactionCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMasterVersion->companyLocalCurrencyID !!}</td>
            <td>{!! $quotationMasterVersion->companyLocalCurrency !!}</td>
            <td>{!! $quotationMasterVersion->companyLocalExchangeRate !!}</td>
            <td>{!! $quotationMasterVersion->companyLocalAmount !!}</td>
            <td>{!! $quotationMasterVersion->companyLocalCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMasterVersion->companyReportingCurrencyID !!}</td>
            <td>{!! $quotationMasterVersion->companyReportingCurrency !!}</td>
            <td>{!! $quotationMasterVersion->companyReportingExchangeRate !!}</td>
            <td>{!! $quotationMasterVersion->companyReportingAmount !!}</td>
            <td>{!! $quotationMasterVersion->companyReportingCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMasterVersion->customerCurrencyID !!}</td>
            <td>{!! $quotationMasterVersion->customerCurrency !!}</td>
            <td>{!! $quotationMasterVersion->customerCurrencyExchangeRate !!}</td>
            <td>{!! $quotationMasterVersion->customerCurrencyAmount !!}</td>
            <td>{!! $quotationMasterVersion->customerCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMasterVersion->isDeleted !!}</td>
            <td>{!! $quotationMasterVersion->deletedEmpID !!}</td>
            <td>{!! $quotationMasterVersion->deletedDate !!}</td>
            <td>{!! $quotationMasterVersion->confirmedYN !!}</td>
            <td>{!! $quotationMasterVersion->confirmedByEmpSystemID !!}</td>
            <td>{!! $quotationMasterVersion->confirmedByEmpID !!}</td>
            <td>{!! $quotationMasterVersion->confirmedByName !!}</td>
            <td>{!! $quotationMasterVersion->confirmedDate !!}</td>
            <td>{!! $quotationMasterVersion->approvedYN !!}</td>
            <td>{!! $quotationMasterVersion->approvedDate !!}</td>
            <td>{!! $quotationMasterVersion->approvedEmpSystemID !!}</td>
            <td>{!! $quotationMasterVersion->approvedbyEmpID !!}</td>
            <td>{!! $quotationMasterVersion->approvedbyEmpName !!}</td>
            <td>{!! $quotationMasterVersion->refferedBackYN !!}</td>
            <td>{!! $quotationMasterVersion->timesReferred !!}</td>
            <td>{!! $quotationMasterVersion->RollLevForApp_curr !!}</td>
            <td>{!! $quotationMasterVersion->closedYN !!}</td>
            <td>{!! $quotationMasterVersion->closedDate !!}</td>
            <td>{!! $quotationMasterVersion->closedReason !!}</td>
            <td>{!! $quotationMasterVersion->companySystemID !!}</td>
            <td>{!! $quotationMasterVersion->companyID !!}</td>
            <td>{!! $quotationMasterVersion->createdUserSystemID !!}</td>
            <td>{!! $quotationMasterVersion->createdUserGroup !!}</td>
            <td>{!! $quotationMasterVersion->createdPCID !!}</td>
            <td>{!! $quotationMasterVersion->createdUserID !!}</td>
            <td>{!! $quotationMasterVersion->createdDateTime !!}</td>
            <td>{!! $quotationMasterVersion->createdUserName !!}</td>
            <td>{!! $quotationMasterVersion->modifiedUserSystemID !!}</td>
            <td>{!! $quotationMasterVersion->modifiedPCID !!}</td>
            <td>{!! $quotationMasterVersion->modifiedUserID !!}</td>
            <td>{!! $quotationMasterVersion->modifiedDateTime !!}</td>
            <td>{!! $quotationMasterVersion->modifiedUserName !!}</td>
            <td>{!! $quotationMasterVersion->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['quotationMasterVersions.destroy', $quotationMasterVersion->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('quotationMasterVersions.show', [$quotationMasterVersion->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('quotationMasterVersions.edit', [$quotationMasterVersion->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>