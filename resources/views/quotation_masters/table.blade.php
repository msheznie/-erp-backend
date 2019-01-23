<table class="table table-responsive" id="quotationMasters-table">
    <thead>
        <tr>
            <th>Documentsystemid</th>
        <th>Documentid</th>
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
        <th>Rolllevforapp Curr</th>
        <th>Closedyn</th>
        <th>Closeddate</th>
        <th>Closedreason</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($quotationMasters as $quotationMaster)
        <tr>
            <td>{!! $quotationMaster->documentSystemID !!}</td>
            <td>{!! $quotationMaster->documentID !!}</td>
            <td>{!! $quotationMaster->documentDate !!}</td>
            <td>{!! $quotationMaster->documentExpDate !!}</td>
            <td>{!! $quotationMaster->salesPersonID !!}</td>
            <td>{!! $quotationMaster->versionNo !!}</td>
            <td>{!! $quotationMaster->referenceNo !!}</td>
            <td>{!! $quotationMaster->narration !!}</td>
            <td>{!! $quotationMaster->Note !!}</td>
            <td>{!! $quotationMaster->contactPersonName !!}</td>
            <td>{!! $quotationMaster->contactPersonNumber !!}</td>
            <td>{!! $quotationMaster->customerSystemCode !!}</td>
            <td>{!! $quotationMaster->customerCode !!}</td>
            <td>{!! $quotationMaster->customerName !!}</td>
            <td>{!! $quotationMaster->customerAddress !!}</td>
            <td>{!! $quotationMaster->customerTelephone !!}</td>
            <td>{!! $quotationMaster->customerFax !!}</td>
            <td>{!! $quotationMaster->customerEmail !!}</td>
            <td>{!! $quotationMaster->customerReceivableAutoID !!}</td>
            <td>{!! $quotationMaster->customerReceivableSystemGLCode !!}</td>
            <td>{!! $quotationMaster->customerReceivableGLAccount !!}</td>
            <td>{!! $quotationMaster->customerReceivableDescription !!}</td>
            <td>{!! $quotationMaster->customerReceivableType !!}</td>
            <td>{!! $quotationMaster->transactionCurrencyID !!}</td>
            <td>{!! $quotationMaster->transactionCurrency !!}</td>
            <td>{!! $quotationMaster->transactionExchangeRate !!}</td>
            <td>{!! $quotationMaster->transactionAmount !!}</td>
            <td>{!! $quotationMaster->transactionCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMaster->companyLocalCurrencyID !!}</td>
            <td>{!! $quotationMaster->companyLocalCurrency !!}</td>
            <td>{!! $quotationMaster->companyLocalExchangeRate !!}</td>
            <td>{!! $quotationMaster->companyLocalAmount !!}</td>
            <td>{!! $quotationMaster->companyLocalCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMaster->companyReportingCurrencyID !!}</td>
            <td>{!! $quotationMaster->companyReportingCurrency !!}</td>
            <td>{!! $quotationMaster->companyReportingExchangeRate !!}</td>
            <td>{!! $quotationMaster->companyReportingAmount !!}</td>
            <td>{!! $quotationMaster->companyReportingCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMaster->customerCurrencyID !!}</td>
            <td>{!! $quotationMaster->customerCurrency !!}</td>
            <td>{!! $quotationMaster->customerCurrencyExchangeRate !!}</td>
            <td>{!! $quotationMaster->customerCurrencyAmount !!}</td>
            <td>{!! $quotationMaster->customerCurrencyDecimalPlaces !!}</td>
            <td>{!! $quotationMaster->isDeleted !!}</td>
            <td>{!! $quotationMaster->deletedEmpID !!}</td>
            <td>{!! $quotationMaster->deletedDate !!}</td>
            <td>{!! $quotationMaster->confirmedYN !!}</td>
            <td>{!! $quotationMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $quotationMaster->confirmedByEmpID !!}</td>
            <td>{!! $quotationMaster->confirmedByName !!}</td>
            <td>{!! $quotationMaster->confirmedDate !!}</td>
            <td>{!! $quotationMaster->approvedYN !!}</td>
            <td>{!! $quotationMaster->approvedDate !!}</td>
            <td>{!! $quotationMaster->approvedEmpSystemID !!}</td>
            <td>{!! $quotationMaster->approvedbyEmpID !!}</td>
            <td>{!! $quotationMaster->approvedbyEmpName !!}</td>
            <td>{!! $quotationMaster->RollLevForApp_curr !!}</td>
            <td>{!! $quotationMaster->closedYN !!}</td>
            <td>{!! $quotationMaster->closedDate !!}</td>
            <td>{!! $quotationMaster->closedReason !!}</td>
            <td>{!! $quotationMaster->companySystemID !!}</td>
            <td>{!! $quotationMaster->companyID !!}</td>
            <td>{!! $quotationMaster->createdUserGroup !!}</td>
            <td>{!! $quotationMaster->createdPCID !!}</td>
            <td>{!! $quotationMaster->createdUserID !!}</td>
            <td>{!! $quotationMaster->createdDateTime !!}</td>
            <td>{!! $quotationMaster->createdUserName !!}</td>
            <td>{!! $quotationMaster->modifiedPCID !!}</td>
            <td>{!! $quotationMaster->modifiedUserID !!}</td>
            <td>{!! $quotationMaster->modifiedDateTime !!}</td>
            <td>{!! $quotationMaster->modifiedUserName !!}</td>
            <td>{!! $quotationMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['quotationMasters.destroy', $quotationMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('quotationMasters.show', [$quotationMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('quotationMasters.edit', [$quotationMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>