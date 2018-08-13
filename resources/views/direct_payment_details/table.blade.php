<table class="table table-responsive" id="directPaymentDetails-table">
    <thead>
        <tr>
            <th>Directpaymentautoid</th>
        <th>Companyid</th>
        <th>Servicelinecode</th>
        <th>Supplierid</th>
        <th>Expenseclaimmasterautoid</th>
        <th>Glcode</th>
        <th>Glcodedes</th>
        <th>Glcodeisbank</th>
        <th>Comments</th>
        <th>Suppliertranscurrencyid</th>
        <th>Suppliertranser</th>
        <th>Dpamountcurrency</th>
        <th>Dpamountcurrencyer</th>
        <th>Dpamount</th>
        <th>Bankamount</th>
        <th>Bankcurrencyid</th>
        <th>Bankcurrencyer</th>
        <th>Localcurrency</th>
        <th>Localcurrencyer</th>
        <th>Localamount</th>
        <th>Comrptcurrency</th>
        <th>Comrptcurrencyer</th>
        <th>Comrptamount</th>
        <th>Budgetyear</th>
        <th>Timesreferred</th>
        <th>Relatedpartyyn</th>
        <th>Pettycashyn</th>
        <th>Glcompanysystemid</th>
        <th>Glcompanyid</th>
        <th>Tobankid</th>
        <th>Tobankaccountid</th>
        <th>Tobankcurrencyid</th>
        <th>Tobankcurrencyer</th>
        <th>Tobankamount</th>
        <th>Tobankglcode</th>
        <th>Tobankgldescription</th>
        <th>Tocompanylocalcurrencyid</th>
        <th>Tocompanylocalcurrencyer</th>
        <th>Tocompanylocalcurrencyamount</th>
        <th>Tocompanyrptcurrencyid</th>
        <th>Tocompanyrptcurrencyer</th>
        <th>Tocompanyrptcurrencyamount</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($directPaymentDetails as $directPaymentDetails)
        <tr>
            <td>{!! $directPaymentDetails->directPaymentAutoID !!}</td>
            <td>{!! $directPaymentDetails->companyID !!}</td>
            <td>{!! $directPaymentDetails->serviceLineCode !!}</td>
            <td>{!! $directPaymentDetails->supplierID !!}</td>
            <td>{!! $directPaymentDetails->expenseClaimMasterAutoID !!}</td>
            <td>{!! $directPaymentDetails->glCode !!}</td>
            <td>{!! $directPaymentDetails->glCodeDes !!}</td>
            <td>{!! $directPaymentDetails->glCodeIsBank !!}</td>
            <td>{!! $directPaymentDetails->comments !!}</td>
            <td>{!! $directPaymentDetails->supplierTransCurrencyID !!}</td>
            <td>{!! $directPaymentDetails->supplierTransER !!}</td>
            <td>{!! $directPaymentDetails->DPAmountCurrency !!}</td>
            <td>{!! $directPaymentDetails->DPAmountCurrencyER !!}</td>
            <td>{!! $directPaymentDetails->DPAmount !!}</td>
            <td>{!! $directPaymentDetails->bankAmount !!}</td>
            <td>{!! $directPaymentDetails->bankCurrencyID !!}</td>
            <td>{!! $directPaymentDetails->bankCurrencyER !!}</td>
            <td>{!! $directPaymentDetails->localCurrency !!}</td>
            <td>{!! $directPaymentDetails->localCurrencyER !!}</td>
            <td>{!! $directPaymentDetails->localAmount !!}</td>
            <td>{!! $directPaymentDetails->comRptCurrency !!}</td>
            <td>{!! $directPaymentDetails->comRptCurrencyER !!}</td>
            <td>{!! $directPaymentDetails->comRptAmount !!}</td>
            <td>{!! $directPaymentDetails->budgetYear !!}</td>
            <td>{!! $directPaymentDetails->timesReferred !!}</td>
            <td>{!! $directPaymentDetails->relatedPartyYN !!}</td>
            <td>{!! $directPaymentDetails->pettyCashYN !!}</td>
            <td>{!! $directPaymentDetails->glCompanySystemID !!}</td>
            <td>{!! $directPaymentDetails->glCompanyID !!}</td>
            <td>{!! $directPaymentDetails->toBankID !!}</td>
            <td>{!! $directPaymentDetails->toBankAccountID !!}</td>
            <td>{!! $directPaymentDetails->toBankCurrencyID !!}</td>
            <td>{!! $directPaymentDetails->toBankCurrencyER !!}</td>
            <td>{!! $directPaymentDetails->toBankAmount !!}</td>
            <td>{!! $directPaymentDetails->toBankGlCode !!}</td>
            <td>{!! $directPaymentDetails->toBankGLDescription !!}</td>
            <td>{!! $directPaymentDetails->toCompanyLocalCurrencyID !!}</td>
            <td>{!! $directPaymentDetails->toCompanyLocalCurrencyER !!}</td>
            <td>{!! $directPaymentDetails->toCompanyLocalCurrencyAmount !!}</td>
            <td>{!! $directPaymentDetails->toCompanyRptCurrencyID !!}</td>
            <td>{!! $directPaymentDetails->toCompanyRptCurrencyER !!}</td>
            <td>{!! $directPaymentDetails->toCompanyRptCurrencyAmount !!}</td>
            <td>{!! $directPaymentDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['directPaymentDetails.destroy', $directPaymentDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('directPaymentDetails.show', [$directPaymentDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('directPaymentDetails.edit', [$directPaymentDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>