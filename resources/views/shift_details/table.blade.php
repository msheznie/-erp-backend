<table class="table table-responsive" id="shiftDetails-table">
    <thead>
        <tr>
            <th>Warehouseid</th>
        <th>Empid</th>
        <th>Counterid</th>
        <th>Starttime</th>
        <th>Endtime</th>
        <th>Isclosed</th>
        <th>Cashsales</th>
        <th>Giftcardtopup</th>
        <th>Startingbalance Transaction</th>
        <th>Endingbalance Transaction</th>
        <th>Different Transaction</th>
        <th>Cashsales Local</th>
        <th>Giftcardtopup Local</th>
        <th>Startingbalance Local</th>
        <th>Endingbalance Local</th>
        <th>Different Local</th>
        <th>Cashsales Reporting</th>
        <th>Giftcardtopup Reporting</th>
        <th>Closingcashbalance Transaction</th>
        <th>Closingcashbalance Local</th>
        <th>Startingbalance Reporting</th>
        <th>Endingbalance Reporting</th>
        <th>Different Local Reporting</th>
        <th>Closingcashbalance Reporting</th>
        <th>Transactioncurrencyid</th>
        <th>Transactioncurrency</th>
        <th>Transactionexchangerate</th>
        <th>Transactioncurrencydecimalplaces</th>
        <th>Companylocalcurrencyid</th>
        <th>Companylocalcurrency</th>
        <th>Companylocalexchangerate</th>
        <th>Companylocalcurrencydecimalplaces</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportingcurrency</th>
        <th>Companyreportingexchangerate</th>
        <th>Companyreportingcurrencydecimalplaces</th>
        <th>Companyid</th>
        <th>Companycode</th>
        <th>Segmentid</th>
        <th>Segmentcode</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createddatetime</th>
        <th>Createdusername</th>
        <th>Modifiedpcid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifieduserid</th>
        <th>Modifieddatetime</th>
        <th>Modifiedusername</th>
        <th>Timestamp</th>
        <th>Id Store</th>
        <th>Is Sync</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($shiftDetails as $shiftDetails)
        <tr>
            <td>{!! $shiftDetails->wareHouseID !!}</td>
            <td>{!! $shiftDetails->empID !!}</td>
            <td>{!! $shiftDetails->counterID !!}</td>
            <td>{!! $shiftDetails->startTime !!}</td>
            <td>{!! $shiftDetails->endTime !!}</td>
            <td>{!! $shiftDetails->isClosed !!}</td>
            <td>{!! $shiftDetails->cashSales !!}</td>
            <td>{!! $shiftDetails->giftCardTopUp !!}</td>
            <td>{!! $shiftDetails->startingBalance_transaction !!}</td>
            <td>{!! $shiftDetails->endingBalance_transaction !!}</td>
            <td>{!! $shiftDetails->different_transaction !!}</td>
            <td>{!! $shiftDetails->cashSales_local !!}</td>
            <td>{!! $shiftDetails->giftCardTopUp_local !!}</td>
            <td>{!! $shiftDetails->startingBalance_local !!}</td>
            <td>{!! $shiftDetails->endingBalance_local !!}</td>
            <td>{!! $shiftDetails->different_local !!}</td>
            <td>{!! $shiftDetails->cashSales_reporting !!}</td>
            <td>{!! $shiftDetails->giftCardTopUp_reporting !!}</td>
            <td>{!! $shiftDetails->closingCashBalance_transaction !!}</td>
            <td>{!! $shiftDetails->closingCashBalance_local !!}</td>
            <td>{!! $shiftDetails->startingBalance_reporting !!}</td>
            <td>{!! $shiftDetails->endingBalance_reporting !!}</td>
            <td>{!! $shiftDetails->different_local_reporting !!}</td>
            <td>{!! $shiftDetails->closingCashBalance_reporting !!}</td>
            <td>{!! $shiftDetails->transactionCurrencyID !!}</td>
            <td>{!! $shiftDetails->transactionCurrency !!}</td>
            <td>{!! $shiftDetails->transactionExchangeRate !!}</td>
            <td>{!! $shiftDetails->transactionCurrencyDecimalPlaces !!}</td>
            <td>{!! $shiftDetails->companyLocalCurrencyID !!}</td>
            <td>{!! $shiftDetails->companyLocalCurrency !!}</td>
            <td>{!! $shiftDetails->companyLocalExchangeRate !!}</td>
            <td>{!! $shiftDetails->companyLocalCurrencyDecimalPlaces !!}</td>
            <td>{!! $shiftDetails->companyReportingCurrencyID !!}</td>
            <td>{!! $shiftDetails->companyReportingCurrency !!}</td>
            <td>{!! $shiftDetails->companyReportingExchangeRate !!}</td>
            <td>{!! $shiftDetails->companyReportingCurrencyDecimalPlaces !!}</td>
            <td>{!! $shiftDetails->companyID !!}</td>
            <td>{!! $shiftDetails->companyCode !!}</td>
            <td>{!! $shiftDetails->segmentID !!}</td>
            <td>{!! $shiftDetails->segmentCode !!}</td>
            <td>{!! $shiftDetails->createdUserGroup !!}</td>
            <td>{!! $shiftDetails->createdPCID !!}</td>
            <td>{!! $shiftDetails->createdUserSystemID !!}</td>
            <td>{!! $shiftDetails->createdUserID !!}</td>
            <td>{!! $shiftDetails->createdDateTime !!}</td>
            <td>{!! $shiftDetails->createdUserName !!}</td>
            <td>{!! $shiftDetails->modifiedPCID !!}</td>
            <td>{!! $shiftDetails->modifiedUserSystemID !!}</td>
            <td>{!! $shiftDetails->modifiedUserID !!}</td>
            <td>{!! $shiftDetails->modifiedDateTime !!}</td>
            <td>{!! $shiftDetails->modifiedUserName !!}</td>
            <td>{!! $shiftDetails->timestamp !!}</td>
            <td>{!! $shiftDetails->id_store !!}</td>
            <td>{!! $shiftDetails->is_sync !!}</td>
            <td>
                {!! Form::open(['route' => ['shiftDetails.destroy', $shiftDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('shiftDetails.show', [$shiftDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('shiftDetails.edit', [$shiftDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>