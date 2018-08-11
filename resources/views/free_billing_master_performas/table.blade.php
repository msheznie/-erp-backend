<table class="table table-responsive" id="freeBillingMasterPerformas-table">
    <thead>
        <tr>
            <th>Billprocessno</th>
        <th>Performainvoiceno</th>
        <th>Performainvoicetext</th>
        <th>Ticketno</th>
        <th>Clientid</th>
        <th>Contractid</th>
        <th>Performadate</th>
        <th>Performastatus</th>
        <th>Billprocessdate</th>
        <th>Selectedforperformayn</th>
        <th>Invoiceno</th>
        <th>Performaopconfirmed</th>
        <th>Performafinanceconfirmed</th>
        <th>Performaopconfirmedby</th>
        <th>Performaopconfirmeddate</th>
        <th>Performafinanceconfirmedby</th>
        <th>Performafinanceconfirmeddate</th>
        <th>Confirmedyn</th>
        <th>Confirmedby</th>
        <th>Confirmeddate</th>
        <th>Confirmedbyname</th>
        <th>Approvedyn</th>
        <th>Approvedby</th>
        <th>Approveddate</th>
        <th>Documentid</th>
        <th>Companyid</th>
        <th>Servicelinecode</th>
        <th>Serialno</th>
        <th>Billingcode</th>
        <th>Performaserialno</th>
        <th>Performacode</th>
        <th>Rentalstartdate</th>
        <th>Rentalenddate</th>
        <th>Rentaltype</th>
        <th>Createduserid</th>
        <th>Modifieduserid</th>
        <th>Timestamp</th>
        <th>Performamasterid</th>
        <th>Istrasportrental</th>
        <th>Disablerental</th>
        <th>Isopstbdaysfrommit</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($freeBillingMasterPerformas as $freeBillingMasterPerforma)
        <tr>
            <td>{!! $freeBillingMasterPerforma->BillProcessNO !!}</td>
            <td>{!! $freeBillingMasterPerforma->PerformaInvoiceNo !!}</td>
            <td>{!! $freeBillingMasterPerforma->PerformaInvoiceText !!}</td>
            <td>{!! $freeBillingMasterPerforma->Ticketno !!}</td>
            <td>{!! $freeBillingMasterPerforma->clientID !!}</td>
            <td>{!! $freeBillingMasterPerforma->contractID !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaDate !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaStatus !!}</td>
            <td>{!! $freeBillingMasterPerforma->BillProcessDate !!}</td>
            <td>{!! $freeBillingMasterPerforma->SelectedForPerformaYN !!}</td>
            <td>{!! $freeBillingMasterPerforma->InvoiceNo !!}</td>
            <td>{!! $freeBillingMasterPerforma->PerformaOpConfirmed !!}</td>
            <td>{!! $freeBillingMasterPerforma->PerformaFinanceConfirmed !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaOpConfirmedBy !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaOpConfirmedDate !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaFinanceConfirmedBy !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaFinanceConfirmedDate !!}</td>
            <td>{!! $freeBillingMasterPerforma->confirmedYN !!}</td>
            <td>{!! $freeBillingMasterPerforma->confirmedBy !!}</td>
            <td>{!! $freeBillingMasterPerforma->confirmedDate !!}</td>
            <td>{!! $freeBillingMasterPerforma->confirmedByName !!}</td>
            <td>{!! $freeBillingMasterPerforma->approvedYN !!}</td>
            <td>{!! $freeBillingMasterPerforma->approvedBy !!}</td>
            <td>{!! $freeBillingMasterPerforma->approvedDate !!}</td>
            <td>{!! $freeBillingMasterPerforma->documentID !!}</td>
            <td>{!! $freeBillingMasterPerforma->companyID !!}</td>
            <td>{!! $freeBillingMasterPerforma->serviceLineCode !!}</td>
            <td>{!! $freeBillingMasterPerforma->serialNo !!}</td>
            <td>{!! $freeBillingMasterPerforma->billingCode !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaSerialNo !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaCode !!}</td>
            <td>{!! $freeBillingMasterPerforma->rentalStartDate !!}</td>
            <td>{!! $freeBillingMasterPerforma->rentalEndDate !!}</td>
            <td>{!! $freeBillingMasterPerforma->rentalType !!}</td>
            <td>{!! $freeBillingMasterPerforma->createdUserID !!}</td>
            <td>{!! $freeBillingMasterPerforma->modifiedUserID !!}</td>
            <td>{!! $freeBillingMasterPerforma->timeStamp !!}</td>
            <td>{!! $freeBillingMasterPerforma->performaMasterID !!}</td>
            <td>{!! $freeBillingMasterPerforma->isTrasportRental !!}</td>
            <td>{!! $freeBillingMasterPerforma->disableRental !!}</td>
            <td>{!! $freeBillingMasterPerforma->IsOpStbDaysFromMIT !!}</td>
            <td>
                {!! Form::open(['route' => ['freeBillingMasterPerformas.destroy', $freeBillingMasterPerforma->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('freeBillingMasterPerformas.show', [$freeBillingMasterPerforma->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('freeBillingMasterPerformas.edit', [$freeBillingMasterPerforma->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>