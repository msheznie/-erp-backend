<table class="table table-responsive" id="purchaseRequests-table">
    <thead>
        <tr>
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
        <th>Prconfirmedbysystemid</th>
        <th>Prconfirmeddate</th>
        <th>Isactive</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Timesreferred</th>
        <th>Prclosedyn</th>
        <th>Prclosedcomments</th>
        <th>Prclosedbyempid</th>
        <th>Prcloseddate</th>
        <th>Cancelledyn</th>
        <th>Cancelledbyempid</th>
        <th>Cancelledbyempname</th>
        <th>Cancelledcomments</th>
        <th>Cancelleddate</th>
        <th>Selectedforpo</th>
        <th>Selectedforpobyempid</th>
        <th>Supplychainongoing</th>
        <th>Potrackid</th>
        <th>Rolllevforapp Curr</th>
        <th>Hidepoyn</th>
        <th>Hidebyempid</th>
        <th>Hidebyempname</th>
        <th>Hidedate</th>
        <th>Hidecomments</th>
        <th>Previousbuyerempid</th>
        <th>Delegateddate</th>
        <th>Delegatedcomments</th>
        <th>Fromweb</th>
        <th>Wo Status</th>
        <th>Doc Type</th>
        <th>Refferedbackyn</th>
        <th>Isaccrued</th>
        <th>Budgetyear</th>
        <th>Prbelongsyear</th>
        <th>Budgetblockyn</th>
        <th>Budgetblockbyempid</th>
        <th>Budgetblockbyempemailid</th>
        <th>Checkbudgetyn</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($purchaseRequests as $purchaseRequest)
        <tr>
            <td>{!! $purchaseRequest->companySystemID !!}</td>
            <td>{!! $purchaseRequest->companyID !!}</td>
            <td>{!! $purchaseRequest->departmentID !!}</td>
            <td>{!! $purchaseRequest->serviceLineSystemID !!}</td>
            <td>{!! $purchaseRequest->serviceLineCode !!}</td>
            <td>{!! $purchaseRequest->documentSystemID !!}</td>
            <td>{!! $purchaseRequest->documentID !!}</td>
            <td>{!! $purchaseRequest->companyJobID !!}</td>
            <td>{!! $purchaseRequest->serialNumber !!}</td>
            <td>{!! $purchaseRequest->purchaseRequestCode !!}</td>
            <td>{!! $purchaseRequest->comments !!}</td>
            <td>{!! $purchaseRequest->location !!}</td>
            <td>{!! $purchaseRequest->priority !!}</td>
            <td>{!! $purchaseRequest->deliveryLocation !!}</td>
            <td>{!! $purchaseRequest->PRRequestedDate !!}</td>
            <td>{!! $purchaseRequest->docRefNo !!}</td>
            <td>{!! $purchaseRequest->invoiceNumber !!}</td>
            <td>{!! $purchaseRequest->currency !!}</td>
            <td>{!! $purchaseRequest->buyerEmpID !!}</td>
            <td>{!! $purchaseRequest->buyerEmpSystemID !!}</td>
            <td>{!! $purchaseRequest->buyerEmpName !!}</td>
            <td>{!! $purchaseRequest->buyerEmpEmail !!}</td>
            <td>{!! $purchaseRequest->supplierCodeSystem !!}</td>
            <td>{!! $purchaseRequest->supplierName !!}</td>
            <td>{!! $purchaseRequest->supplierAddress !!}</td>
            <td>{!! $purchaseRequest->supplierTransactionCurrencyID !!}</td>
            <td>{!! $purchaseRequest->supplierCountryID !!}</td>
            <td>{!! $purchaseRequest->financeCategory !!}</td>
            <td>{!! $purchaseRequest->PRConfirmedYN !!}</td>
            <td>{!! $purchaseRequest->PRConfirmedBy !!}</td>
            <td>{!! $purchaseRequest->PRConfirmedBySystemID !!}</td>
            <td>{!! $purchaseRequest->PRConfirmedDate !!}</td>
            <td>{!! $purchaseRequest->isActive !!}</td>
            <td>{!! $purchaseRequest->approved !!}</td>
            <td>{!! $purchaseRequest->approvedDate !!}</td>
            <td>{!! $purchaseRequest->timesReferred !!}</td>
            <td>{!! $purchaseRequest->prClosedYN !!}</td>
            <td>{!! $purchaseRequest->prClosedComments !!}</td>
            <td>{!! $purchaseRequest->prClosedByEmpID !!}</td>
            <td>{!! $purchaseRequest->prClosedDate !!}</td>
            <td>{!! $purchaseRequest->cancelledYN !!}</td>
            <td>{!! $purchaseRequest->cancelledByEmpID !!}</td>
            <td>{!! $purchaseRequest->cancelledByEmpName !!}</td>
            <td>{!! $purchaseRequest->cancelledComments !!}</td>
            <td>{!! $purchaseRequest->cancelledDate !!}</td>
            <td>{!! $purchaseRequest->selectedForPO !!}</td>
            <td>{!! $purchaseRequest->selectedForPOByEmpID !!}</td>
            <td>{!! $purchaseRequest->supplyChainOnGoing !!}</td>
            <td>{!! $purchaseRequest->poTrackID !!}</td>
            <td>{!! $purchaseRequest->RollLevForApp_curr !!}</td>
            <td>{!! $purchaseRequest->hidePOYN !!}</td>
            <td>{!! $purchaseRequest->hideByEmpID !!}</td>
            <td>{!! $purchaseRequest->hideByEmpName !!}</td>
            <td>{!! $purchaseRequest->hideDate !!}</td>
            <td>{!! $purchaseRequest->hideComments !!}</td>
            <td>{!! $purchaseRequest->PreviousBuyerEmpID !!}</td>
            <td>{!! $purchaseRequest->delegatedDate !!}</td>
            <td>{!! $purchaseRequest->delegatedComments !!}</td>
            <td>{!! $purchaseRequest->fromWeb !!}</td>
            <td>{!! $purchaseRequest->wo_status !!}</td>
            <td>{!! $purchaseRequest->doc_type !!}</td>
            <td>{!! $purchaseRequest->refferedBackYN !!}</td>
            <td>{!! $purchaseRequest->isAccrued !!}</td>
            <td>{!! $purchaseRequest->budgetYear !!}</td>
            <td>{!! $purchaseRequest->prBelongsYear !!}</td>
            <td>{!! $purchaseRequest->budgetBlockYN !!}</td>
            <td>{!! $purchaseRequest->budgetBlockByEmpID !!}</td>
            <td>{!! $purchaseRequest->budgetBlockByEmpEmailID !!}</td>
            <td>{!! $purchaseRequest->checkBudgetYN !!}</td>
            <td>{!! $purchaseRequest->createdUserGroup !!}</td>
            <td>{!! $purchaseRequest->createdPcID !!}</td>
            <td>{!! $purchaseRequest->createdUserID !!}</td>
            <td>{!! $purchaseRequest->modifiedPc !!}</td>
            <td>{!! $purchaseRequest->modifiedUser !!}</td>
            <td>{!! $purchaseRequest->createdDateTime !!}</td>
            <td>{!! $purchaseRequest->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseRequests.destroy', $purchaseRequest->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseRequests.show', [$purchaseRequest->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseRequests.edit', [$purchaseRequest->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>