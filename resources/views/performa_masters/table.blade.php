<table class="table table-responsive" id="performaMasters-table">
    <thead>
        <tr>
            <th>Performainvoiceno</th>
        <th>Performaserialno</th>
        <th>Performacode</th>
        <th>Companyid</th>
        <th>Serviceline</th>
        <th>Clientid</th>
        <th>Contractid</th>
        <th>Performadate</th>
        <th>Createduserid</th>
        <th>Modifieduserid</th>
        <th>Performastatus</th>
        <th>Performaopconfirmed</th>
        <th>Performaopconfirmedby</th>
        <th>Performaopconfirmeddate</th>
        <th>Performafinanceconfirmed</th>
        <th>Performafinanceconfirmedby</th>
        <th>Performafinanceconfirmeddate</th>
        <th>Performavalue</th>
        <th>Ticketno</th>
        <th>Bankid</th>
        <th>Accountid</th>
        <th>Paymentindaysforjob</th>
        <th>Custinvnomodified</th>
        <th>Isperformaoneditrental</th>
        <th>Isrefbackbillingyn</th>
        <th>Refbackbillingby</th>
        <th>Refbackbillingdate</th>
        <th>Isrefbackopyn</th>
        <th>Refbackopby</th>
        <th>Refbackopdate</th>
        <th>Refbillingcomment</th>
        <th>Refopcomment</th>
        <th>Clientappperformatype</th>
        <th>Clientapproveddate</th>
        <th>Clientapprovedby</th>
        <th>Performasenttoho</th>
        <th>Performasenttohodate</th>
        <th>Performasenttohoempid</th>
        <th>Lotsystemautoid</th>
        <th>Lotnumber</th>
        <th>Performareceivedbyempid</th>
        <th>Performareceivedbydate</th>
        <th>Submittedtoclientdate</th>
        <th>Submittedtoclientbyempid</th>
        <th>Receivedfromclientdate</th>
        <th>Resubmitteddate</th>
        <th>Approvedbyclientdate</th>
        <th>Timestamp</th>
        <th>Isaccrualyn</th>
        <th>Iscanceledyn</th>
        <th>Servicecompanyid</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($performaMasters as $performaMaster)
        <tr>
            <td>{!! $performaMaster->PerformaInvoiceNo !!}</td>
            <td>{!! $performaMaster->performaSerialNO !!}</td>
            <td>{!! $performaMaster->PerformaCode !!}</td>
            <td>{!! $performaMaster->companyID !!}</td>
            <td>{!! $performaMaster->serviceLine !!}</td>
            <td>{!! $performaMaster->clientID !!}</td>
            <td>{!! $performaMaster->contractID !!}</td>
            <td>{!! $performaMaster->performaDate !!}</td>
            <td>{!! $performaMaster->createdUserID !!}</td>
            <td>{!! $performaMaster->modifiedUserID !!}</td>
            <td>{!! $performaMaster->performaStatus !!}</td>
            <td>{!! $performaMaster->PerformaOpConfirmed !!}</td>
            <td>{!! $performaMaster->performaOpConfirmedBy !!}</td>
            <td>{!! $performaMaster->performaOpConfirmedDate !!}</td>
            <td>{!! $performaMaster->PerformaFinanceConfirmed !!}</td>
            <td>{!! $performaMaster->performaFinanceConfirmedBy !!}</td>
            <td>{!! $performaMaster->performaFinanceConfirmedDate !!}</td>
            <td>{!! $performaMaster->performaValue !!}</td>
            <td>{!! $performaMaster->ticketNo !!}</td>
            <td>{!! $performaMaster->bankID !!}</td>
            <td>{!! $performaMaster->accountID !!}</td>
            <td>{!! $performaMaster->paymentInDaysForJob !!}</td>
            <td>{!! $performaMaster->custInvNoModified !!}</td>
            <td>{!! $performaMaster->isPerformaOnEditRental !!}</td>
            <td>{!! $performaMaster->isRefBackBillingYN !!}</td>
            <td>{!! $performaMaster->refBackBillingBy !!}</td>
            <td>{!! $performaMaster->refBackBillingDate !!}</td>
            <td>{!! $performaMaster->isRefBackOPYN !!}</td>
            <td>{!! $performaMaster->refBackOPby !!}</td>
            <td>{!! $performaMaster->refBackOpDate !!}</td>
            <td>{!! $performaMaster->refBillingComment !!}</td>
            <td>{!! $performaMaster->refOpComment !!}</td>
            <td>{!! $performaMaster->clientAppPerformaType !!}</td>
            <td>{!! $performaMaster->clientapprovedDate !!}</td>
            <td>{!! $performaMaster->clientapprovedBy !!}</td>
            <td>{!! $performaMaster->performaSentToHO !!}</td>
            <td>{!! $performaMaster->performaSentToHODate !!}</td>
            <td>{!! $performaMaster->performaSentToHOEmpID !!}</td>
            <td>{!! $performaMaster->lotSystemAutoID !!}</td>
            <td>{!! $performaMaster->lotNumber !!}</td>
            <td>{!! $performaMaster->performaReceivedByEmpID !!}</td>
            <td>{!! $performaMaster->performaReceivedByDate !!}</td>
            <td>{!! $performaMaster->submittedToClientDate !!}</td>
            <td>{!! $performaMaster->submittedToClientByEmpID !!}</td>
            <td>{!! $performaMaster->receivedFromClientDate !!}</td>
            <td>{!! $performaMaster->reSubmittedDate !!}</td>
            <td>{!! $performaMaster->approvedByClientDate !!}</td>
            <td>{!! $performaMaster->timeStamp !!}</td>
            <td>{!! $performaMaster->isAccrualYN !!}</td>
            <td>{!! $performaMaster->isCanceledYN !!}</td>
            <td>{!! $performaMaster->serviceCompanyID !!}</td>
            <td>
                {!! Form::open(['route' => ['performaMasters.destroy', $performaMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('performaMasters.show', [$performaMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('performaMasters.edit', [$performaMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>