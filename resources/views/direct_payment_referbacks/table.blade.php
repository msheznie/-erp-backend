<table class="table table-responsive" id="directPaymentReferbacks-table">
    <thead>
        <tr>
            <th>Directpaymentdetailsid</th>
        <th>Directpaymentautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Supplierid</th>
        <th>Expenseclaimmasterautoid</th>
        <th>Chartofaccountsystemid</th>
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
        <th>Tobankglcodesystemid</th>
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
    @foreach($directPaymentReferbacks as $directPaymentReferback)
        <tr>
            <td>{!! $directPaymentReferback->directPaymentDetailsID !!}</td>
            <td>{!! $directPaymentReferback->directPaymentAutoID !!}</td>
            <td>{!! $directPaymentReferback->companySystemID !!}</td>
            <td>{!! $directPaymentReferback->companyID !!}</td>
            <td>{!! $directPaymentReferback->serviceLineSystemID !!}</td>
            <td>{!! $directPaymentReferback->serviceLineCode !!}</td>
            <td>{!! $directPaymentReferback->supplierID !!}</td>
            <td>{!! $directPaymentReferback->expenseClaimMasterAutoID !!}</td>
            <td>{!! $directPaymentReferback->chartOfAccountSystemID !!}</td>
            <td>{!! $directPaymentReferback->glCode !!}</td>
            <td>{!! $directPaymentReferback->glCodeDes !!}</td>
            <td>{!! $directPaymentReferback->glCodeIsBank !!}</td>
            <td>{!! $directPaymentReferback->comments !!}</td>
            <td>{!! $directPaymentReferback->supplierTransCurrencyID !!}</td>
            <td>{!! $directPaymentReferback->supplierTransER !!}</td>
            <td>{!! $directPaymentReferback->DPAmountCurrency !!}</td>
            <td>{!! $directPaymentReferback->DPAmountCurrencyER !!}</td>
            <td>{!! $directPaymentReferback->DPAmount !!}</td>
            <td>{!! $directPaymentReferback->bankAmount !!}</td>
            <td>{!! $directPaymentReferback->bankCurrencyID !!}</td>
            <td>{!! $directPaymentReferback->bankCurrencyER !!}</td>
            <td>{!! $directPaymentReferback->localCurrency !!}</td>
            <td>{!! $directPaymentReferback->localCurrencyER !!}</td>
            <td>{!! $directPaymentReferback->localAmount !!}</td>
            <td>{!! $directPaymentReferback->comRptCurrency !!}</td>
            <td>{!! $directPaymentReferback->comRptCurrencyER !!}</td>
            <td>{!! $directPaymentReferback->comRptAmount !!}</td>
            <td>{!! $directPaymentReferback->budgetYear !!}</td>
            <td>{!! $directPaymentReferback->timesReferred !!}</td>
            <td>{!! $directPaymentReferback->relatedPartyYN !!}</td>
            <td>{!! $directPaymentReferback->pettyCashYN !!}</td>
            <td>{!! $directPaymentReferback->glCompanySystemID !!}</td>
            <td>{!! $directPaymentReferback->glCompanyID !!}</td>
            <td>{!! $directPaymentReferback->toBankID !!}</td>
            <td>{!! $directPaymentReferback->toBankAccountID !!}</td>
            <td>{!! $directPaymentReferback->toBankCurrencyID !!}</td>
            <td>{!! $directPaymentReferback->toBankCurrencyER !!}</td>
            <td>{!! $directPaymentReferback->toBankAmount !!}</td>
            <td>{!! $directPaymentReferback->toBankGlCodeSystemID !!}</td>
            <td>{!! $directPaymentReferback->toBankGlCode !!}</td>
            <td>{!! $directPaymentReferback->toBankGLDescription !!}</td>
            <td>{!! $directPaymentReferback->toCompanyLocalCurrencyID !!}</td>
            <td>{!! $directPaymentReferback->toCompanyLocalCurrencyER !!}</td>
            <td>{!! $directPaymentReferback->toCompanyLocalCurrencyAmount !!}</td>
            <td>{!! $directPaymentReferback->toCompanyRptCurrencyID !!}</td>
            <td>{!! $directPaymentReferback->toCompanyRptCurrencyER !!}</td>
            <td>{!! $directPaymentReferback->toCompanyRptCurrencyAmount !!}</td>
            <td>{!! $directPaymentReferback->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['directPaymentReferbacks.destroy', $directPaymentReferback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('directPaymentReferbacks.show', [$directPaymentReferback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('directPaymentReferbacks.edit', [$directPaymentReferback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>