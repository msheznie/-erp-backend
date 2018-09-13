<table class="table table-responsive" id="matchDocumentMasters-table">
    <thead>
        <tr>
            <th>Paymasterautoid</th>
        <th>Documentsystemid</th>
        <th>Companyid</th>
        <th>Companysystemid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Matchingdoccode</th>
        <th>Matchingdocdate</th>
        <th>Bpvcode</th>
        <th>Bpvdate</th>
        <th>Bpvnarration</th>
        <th>Directpaymentpayee</th>
        <th>Directpayeecurrency</th>
        <th>Bpvsupplierid</th>
        <th>Supplierglcode</th>
        <th>Suppliertranscurrencyid</th>
        <th>Suppliertranscurrencyer</th>
        <th>Supplierdefcurrencyid</th>
        <th>Supplierdefcurrencyer</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Companyrptcurrencyid</th>
        <th>Companyrptcurrencyer</th>
        <th>Payamountbank</th>
        <th>Payamountsupptrans</th>
        <th>Payamountsuppdef</th>
        <th>Suppamountdoctotal</th>
        <th>Payamountcomplocal</th>
        <th>Payamountcomprpt</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempid</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyname</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Invoicetype</th>
        <th>Matchinvoice</th>
        <th>Matchingconfirmedyn</th>
        <th>Matchingconfirmedbyempsystemid</th>
        <th>Matchingconfirmedbyempid</th>
        <th>Matchingconfirmedbyname</th>
        <th>Matchingconfirmeddate</th>
        <th>Matchingamount</th>
        <th>Matchbalanceamount</th>
        <th>Matchedamount</th>
        <th>Matchlocalamount</th>
        <th>Matchrptamount</th>
        <th>Matchingtype</th>
        <th>Isexchangematch</th>
        <th>Createdusergroup</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($matchDocumentMasters as $matchDocumentMaster)
        <tr>
            <td>{!! $matchDocumentMaster->PayMasterAutoId !!}</td>
            <td>{!! $matchDocumentMaster->documentSystemID !!}</td>
            <td>{!! $matchDocumentMaster->companyID !!}</td>
            <td>{!! $matchDocumentMaster->companySystemID !!}</td>
            <td>{!! $matchDocumentMaster->documentID !!}</td>
            <td>{!! $matchDocumentMaster->serialNo !!}</td>
            <td>{!! $matchDocumentMaster->matchingDocCode !!}</td>
            <td>{!! $matchDocumentMaster->matchingDocdate !!}</td>
            <td>{!! $matchDocumentMaster->BPVcode !!}</td>
            <td>{!! $matchDocumentMaster->BPVdate !!}</td>
            <td>{!! $matchDocumentMaster->BPVNarration !!}</td>
            <td>{!! $matchDocumentMaster->directPaymentPayee !!}</td>
            <td>{!! $matchDocumentMaster->directPayeeCurrency !!}</td>
            <td>{!! $matchDocumentMaster->BPVsupplierID !!}</td>
            <td>{!! $matchDocumentMaster->supplierGLCode !!}</td>
            <td>{!! $matchDocumentMaster->supplierTransCurrencyID !!}</td>
            <td>{!! $matchDocumentMaster->supplierTransCurrencyER !!}</td>
            <td>{!! $matchDocumentMaster->supplierDefCurrencyID !!}</td>
            <td>{!! $matchDocumentMaster->supplierDefCurrencyER !!}</td>
            <td>{!! $matchDocumentMaster->localCurrencyID !!}</td>
            <td>{!! $matchDocumentMaster->localCurrencyER !!}</td>
            <td>{!! $matchDocumentMaster->companyRptCurrencyID !!}</td>
            <td>{!! $matchDocumentMaster->companyRptCurrencyER !!}</td>
            <td>{!! $matchDocumentMaster->payAmountBank !!}</td>
            <td>{!! $matchDocumentMaster->payAmountSuppTrans !!}</td>
            <td>{!! $matchDocumentMaster->payAmountSuppDef !!}</td>
            <td>{!! $matchDocumentMaster->suppAmountDocTotal !!}</td>
            <td>{!! $matchDocumentMaster->payAmountCompLocal !!}</td>
            <td>{!! $matchDocumentMaster->payAmountCompRpt !!}</td>
            <td>{!! $matchDocumentMaster->confirmedYN !!}</td>
            <td>{!! $matchDocumentMaster->confirmedByEmpID !!}</td>
            <td>{!! $matchDocumentMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $matchDocumentMaster->confirmedByName !!}</td>
            <td>{!! $matchDocumentMaster->confirmedDate !!}</td>
            <td>{!! $matchDocumentMaster->approved !!}</td>
            <td>{!! $matchDocumentMaster->approvedDate !!}</td>
            <td>{!! $matchDocumentMaster->invoiceType !!}</td>
            <td>{!! $matchDocumentMaster->matchInvoice !!}</td>
            <td>{!! $matchDocumentMaster->matchingConfirmedYN !!}</td>
            <td>{!! $matchDocumentMaster->matchingConfirmedByEmpSystemID !!}</td>
            <td>{!! $matchDocumentMaster->matchingConfirmedByEmpID !!}</td>
            <td>{!! $matchDocumentMaster->matchingConfirmedByName !!}</td>
            <td>{!! $matchDocumentMaster->matchingConfirmedDate !!}</td>
            <td>{!! $matchDocumentMaster->matchingAmount !!}</td>
            <td>{!! $matchDocumentMaster->matchBalanceAmount !!}</td>
            <td>{!! $matchDocumentMaster->matchedAmount !!}</td>
            <td>{!! $matchDocumentMaster->matchLocalAmount !!}</td>
            <td>{!! $matchDocumentMaster->matchRptAmount !!}</td>
            <td>{!! $matchDocumentMaster->matchingType !!}</td>
            <td>{!! $matchDocumentMaster->isExchangematch !!}</td>
            <td>{!! $matchDocumentMaster->createdUserGroup !!}</td>
            <td>{!! $matchDocumentMaster->createdUserID !!}</td>
            <td>{!! $matchDocumentMaster->createdPcID !!}</td>
            <td>{!! $matchDocumentMaster->modifiedUser !!}</td>
            <td>{!! $matchDocumentMaster->modifiedPc !!}</td>
            <td>{!! $matchDocumentMaster->createdDateTime !!}</td>
            <td>{!! $matchDocumentMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['matchDocumentMasters.destroy', $matchDocumentMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('matchDocumentMasters.show', [$matchDocumentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('matchDocumentMasters.edit', [$matchDocumentMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>