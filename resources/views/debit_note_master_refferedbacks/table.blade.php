<table class="table table-responsive" id="debitNoteMasterRefferedbacks-table">
    <thead>
        <tr>
            <th>Debitnoteautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Companyfinanceyearid</th>
        <th>Fybiggin</th>
        <th>Fyend</th>
        <th>Companyfinanceperiodid</th>
        <th>Fyperioddatefrom</th>
        <th>Fyperioddateto</th>
        <th>Debitnotecode</th>
        <th>Debitnotedate</th>
        <th>Comments</th>
        <th>Referencenumber</th>
        <th>Invoicenumber</th>
        <th>Supplierid</th>
        <th>Supplierglcodesystemid</th>
        <th>Supplierglcode</th>
        <th>Liabilityaccountsysemid</th>
        <th>Liabilityaccount</th>
        <th>Unbilledgrvaccountsystemid</th>
        <th>Unbilledgrvaccount</th>
        <th>Suppliertransactioncurrencyid</th>
        <th>Suppliertransactioncurrencyer</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Debitamounttrans</th>
        <th>Debitamountlocal</th>
        <th>Debitamountrpt</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Posteddate</th>
        <th>Documenttype</th>
        <th>Refferedbackyn</th>
        <th>Timesreferred</th>
        <th>Rolllevforapp Curr</th>
        <th>Matchinvoice</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempsystemid</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Createddateandtime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($debitNoteMasterRefferedbacks as $debitNoteMasterRefferedback)
        <tr>
            <td>{!! $debitNoteMasterRefferedback->debitNoteAutoID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->companySystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->companyID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->documentSystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->documentID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->serialNo !!}</td>
            <td>{!! $debitNoteMasterRefferedback->companyFinanceYearID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->FYBiggin !!}</td>
            <td>{!! $debitNoteMasterRefferedback->FYEnd !!}</td>
            <td>{!! $debitNoteMasterRefferedback->companyFinancePeriodID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->FYPeriodDateFrom !!}</td>
            <td>{!! $debitNoteMasterRefferedback->FYPeriodDateTo !!}</td>
            <td>{!! $debitNoteMasterRefferedback->debitNoteCode !!}</td>
            <td>{!! $debitNoteMasterRefferedback->debitNoteDate !!}</td>
            <td>{!! $debitNoteMasterRefferedback->comments !!}</td>
            <td>{!! $debitNoteMasterRefferedback->referenceNumber !!}</td>
            <td>{!! $debitNoteMasterRefferedback->invoiceNumber !!}</td>
            <td>{!! $debitNoteMasterRefferedback->supplierID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->supplierGLCodeSystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->supplierGLCode !!}</td>
            <td>{!! $debitNoteMasterRefferedback->liabilityAccountSysemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->liabilityAccount !!}</td>
            <td>{!! $debitNoteMasterRefferedback->UnbilledGRVAccountSystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->UnbilledGRVAccount !!}</td>
            <td>{!! $debitNoteMasterRefferedback->supplierTransactionCurrencyID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->supplierTransactionCurrencyER !!}</td>
            <td>{!! $debitNoteMasterRefferedback->companyReportingCurrencyID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->companyReportingER !!}</td>
            <td>{!! $debitNoteMasterRefferedback->localCurrencyID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->localCurrencyER !!}</td>
            <td>{!! $debitNoteMasterRefferedback->debitAmountTrans !!}</td>
            <td>{!! $debitNoteMasterRefferedback->debitAmountLocal !!}</td>
            <td>{!! $debitNoteMasterRefferedback->debitAmountRpt !!}</td>
            <td>{!! $debitNoteMasterRefferedback->confirmedYN !!}</td>
            <td>{!! $debitNoteMasterRefferedback->confirmedByEmpSystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->confirmedByEmpID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->confirmedByName !!}</td>
            <td>{!! $debitNoteMasterRefferedback->confirmedDate !!}</td>
            <td>{!! $debitNoteMasterRefferedback->approved !!}</td>
            <td>{!! $debitNoteMasterRefferedback->approvedDate !!}</td>
            <td>{!! $debitNoteMasterRefferedback->approvedByUserID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->approvedByUserSystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->postedDate !!}</td>
            <td>{!! $debitNoteMasterRefferedback->documentType !!}</td>
            <td>{!! $debitNoteMasterRefferedback->refferedBackYN !!}</td>
            <td>{!! $debitNoteMasterRefferedback->timesReferred !!}</td>
            <td>{!! $debitNoteMasterRefferedback->RollLevForApp_curr !!}</td>
            <td>{!! $debitNoteMasterRefferedback->matchInvoice !!}</td>
            <td>{!! $debitNoteMasterRefferedback->matchingConfirmedYN !!}</td>
            <td>{!! $debitNoteMasterRefferedback->matchingConfirmedByEmpSystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->matchingConfirmedByEmpID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->matchingConfirmedByName !!}</td>
            <td>{!! $debitNoteMasterRefferedback->matchingConfirmedDate !!}</td>
            <td>{!! $debitNoteMasterRefferedback->createdUserGroup !!}</td>
            <td>{!! $debitNoteMasterRefferedback->createdUserSystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->createdUserID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->createdPcID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->modifiedUserSystemID !!}</td>
            <td>{!! $debitNoteMasterRefferedback->modifiedUser !!}</td>
            <td>{!! $debitNoteMasterRefferedback->modifiedPc !!}</td>
            <td>{!! $debitNoteMasterRefferedback->createdDateTime !!}</td>
            <td>{!! $debitNoteMasterRefferedback->createdDateAndTime !!}</td>
            <td>{!! $debitNoteMasterRefferedback->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['debitNoteMasterRefferedbacks.destroy', $debitNoteMasterRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('debitNoteMasterRefferedbacks.show', [$debitNoteMasterRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('debitNoteMasterRefferedbacks.edit', [$debitNoteMasterRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>