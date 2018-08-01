<table class="table table-responsive" id="purchaseRequestReferreds-table">
    <thead>
        <tr>
            <th>Purchaserequestid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Departmentid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Companyjobid</th>
        <th>Serialnumber</th>
        <th>Purchaserequestcode</th>
        <th>Comments</th>
        <th>Location</th>
        <th>Priority</th>
        <th>Deliverylocation</th>
        <th>Prrequesteddate</th>
        <th>Docrefno</th>
        <th>Invoicenumber</th>
        <th>Currency</th>
        <th>Buyerempid</th>
        <th>Buyerempsystemid</th>
        <th>Buyerempname</th>
        <th>Buyerempemail</th>
        <th>Suppliercodesystem</th>
        <th>Suppliername</th>
        <th>Supplieraddress</th>
        <th>Suppliertransactioncurrencyid</th>
        <th>Suppliercountryid</th>
        <th>Financecategory</th>
        <th>Prconfirmedyn</th>
        <th>Prconfirmedby</th>
        <th>Prconfirmeddate</th>
        <th>Isactive</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
        <th>Selectedforpo</th>
        <th>Approved</th>
        <th>Timesreferred</th>
        <th>Prclosedyn</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($purchaseRequestReferreds as $purchaseRequestReferred)
        <tr>
            <td>{!! $purchaseRequestReferred->purchaseRequestID !!}</td>
            <td>{!! $purchaseRequestReferred->companySystemID !!}</td>
            <td>{!! $purchaseRequestReferred->companyID !!}</td>
            <td>{!! $purchaseRequestReferred->departmentID !!}</td>
            <td>{!! $purchaseRequestReferred->serviceLineSystemID !!}</td>
            <td>{!! $purchaseRequestReferred->serviceLineCode !!}</td>
            <td>{!! $purchaseRequestReferred->documentSystemID !!}</td>
            <td>{!! $purchaseRequestReferred->documentID !!}</td>
            <td>{!! $purchaseRequestReferred->companyJobID !!}</td>
            <td>{!! $purchaseRequestReferred->serialNumber !!}</td>
            <td>{!! $purchaseRequestReferred->purchaseRequestCode !!}</td>
            <td>{!! $purchaseRequestReferred->comments !!}</td>
            <td>{!! $purchaseRequestReferred->location !!}</td>
            <td>{!! $purchaseRequestReferred->priority !!}</td>
            <td>{!! $purchaseRequestReferred->deliveryLocation !!}</td>
            <td>{!! $purchaseRequestReferred->PRRequestedDate !!}</td>
            <td>{!! $purchaseRequestReferred->docRefNo !!}</td>
            <td>{!! $purchaseRequestReferred->invoiceNumber !!}</td>
            <td>{!! $purchaseRequestReferred->currency !!}</td>
            <td>{!! $purchaseRequestReferred->buyerEmpID !!}</td>
            <td>{!! $purchaseRequestReferred->buyerEmpSystemID !!}</td>
            <td>{!! $purchaseRequestReferred->buyerEmpName !!}</td>
            <td>{!! $purchaseRequestReferred->buyerEmpEmail !!}</td>
            <td>{!! $purchaseRequestReferred->supplierCodeSystem !!}</td>
            <td>{!! $purchaseRequestReferred->supplierName !!}</td>
            <td>{!! $purchaseRequestReferred->supplierAddress !!}</td>
            <td>{!! $purchaseRequestReferred->supplierTransactionCurrencyID !!}</td>
            <td>{!! $purchaseRequestReferred->supplierCountryID !!}</td>
            <td>{!! $purchaseRequestReferred->financeCategory !!}</td>
            <td>{!! $purchaseRequestReferred->PRConfirmedYN !!}</td>
            <td>{!! $purchaseRequestReferred->PRConfirmedBy !!}</td>
            <td>{!! $purchaseRequestReferred->PRConfirmedDate !!}</td>
            <td>{!! $purchaseRequestReferred->isActive !!}</td>
            <td>{!! $purchaseRequestReferred->createdUserGroup !!}</td>
            <td>{!! $purchaseRequestReferred->createdPcID !!}</td>
            <td>{!! $purchaseRequestReferred->createdUserID !!}</td>
            <td>{!! $purchaseRequestReferred->modifiedPc !!}</td>
            <td>{!! $purchaseRequestReferred->modifiedUser !!}</td>
            <td>{!! $purchaseRequestReferred->createdDateTime !!}</td>
            <td>{!! $purchaseRequestReferred->timeStamp !!}</td>
            <td>{!! $purchaseRequestReferred->selectedForPO !!}</td>
            <td>{!! $purchaseRequestReferred->approved !!}</td>
            <td>{!! $purchaseRequestReferred->timesReferred !!}</td>
            <td>{!! $purchaseRequestReferred->prClosedYN !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseRequestReferreds.destroy', $purchaseRequestReferred->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseRequestReferreds.show', [$purchaseRequestReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseRequestReferreds.edit', [$purchaseRequestReferred->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>