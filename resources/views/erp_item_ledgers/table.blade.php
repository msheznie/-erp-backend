<table class="table table-responsive" id="erpItemLedgers-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Documentsystemcode</th>
        <th>Documentcode</th>
        <th>Referencenumber</th>
        <th>Warehousesystemcode</th>
        <th>Itemsystemcode</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Unitofmeasure</th>
        <th>Inoutqty</th>
        <th>Waclocalcurrencyid</th>
        <th>Waclocal</th>
        <th>Wacrptcurrencyid</th>
        <th>Wacrpt</th>
        <th>Comments</th>
        <th>Transactiondate</th>
        <th>Fromdamagedtransactionyn</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($erpItemLedgers as $erpItemLedger)
        <tr>
            <td>{!! $erpItemLedger->companySystemID !!}</td>
            <td>{!! $erpItemLedger->companyID !!}</td>
            <td>{!! $erpItemLedger->serviceLineSystemID !!}</td>
            <td>{!! $erpItemLedger->serviceLineCode !!}</td>
            <td>{!! $erpItemLedger->documentSystemID !!}</td>
            <td>{!! $erpItemLedger->documentID !!}</td>
            <td>{!! $erpItemLedger->documentSystemCode !!}</td>
            <td>{!! $erpItemLedger->documentCode !!}</td>
            <td>{!! $erpItemLedger->referenceNumber !!}</td>
            <td>{!! $erpItemLedger->wareHouseSystemCode !!}</td>
            <td>{!! $erpItemLedger->itemSystemCode !!}</td>
            <td>{!! $erpItemLedger->itemPrimaryCode !!}</td>
            <td>{!! $erpItemLedger->itemDescription !!}</td>
            <td>{!! $erpItemLedger->unitOfMeasure !!}</td>
            <td>{!! $erpItemLedger->inOutQty !!}</td>
            <td>{!! $erpItemLedger->wacLocalCurrencyID !!}</td>
            <td>{!! $erpItemLedger->wacLocal !!}</td>
            <td>{!! $erpItemLedger->wacRptCurrencyID !!}</td>
            <td>{!! $erpItemLedger->wacRpt !!}</td>
            <td>{!! $erpItemLedger->comments !!}</td>
            <td>{!! $erpItemLedger->transactionDate !!}</td>
            <td>{!! $erpItemLedger->fromDamagedTransactionYN !!}</td>
            <td>{!! $erpItemLedger->createdUserSystemID !!}</td>
            <td>{!! $erpItemLedger->createdUserID !!}</td>
            <td>{!! $erpItemLedger->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['erpItemLedgers.destroy', $erpItemLedger->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('erpItemLedgers.show', [$erpItemLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('erpItemLedgers.edit', [$erpItemLedger->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>