<table class="table table-responsive" id="purchaseOrderProcessDetails-table">
    <thead>
        <tr>
            <th>Poprocessmasterid</th>
        <th>Purchaserequestid</th>
        <th>Purchaserequestdetailsid</th>
        <th>Podeliverylocation</th>
        <th>Itemcode</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Unitofmeasure</th>
        <th>Comments</th>
        <th>Quantityrequested</th>
        <th>Orderedqty</th>
        <th>Supplierpoqty</th>
        <th>Suppliercost</th>
        <th>Selectedsupplier</th>
        <th>Cataloguemasterid</th>
        <th>Cataloguedetailid</th>
        <th>Partnumber</th>
        <th>Itemclientreferencenumbermasterid</th>
        <th>Clientreferencenumber</th>
        <th>Localcurrencyid</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Selectedforpo</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Isaccrued</th>
        <th>Budgetyear</th>
        <th>Prbelongsyear</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($purchaseOrderProcessDetails as $purchaseOrderProcessDetails)
        <tr>
            <td>{!! $purchaseOrderProcessDetails->POProcessMasterID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->purchaseRequestID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->purchaseRequestDetailsID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->poDeliveryLocation !!}</td>
            <td>{!! $purchaseOrderProcessDetails->itemCode !!}</td>
            <td>{!! $purchaseOrderProcessDetails->itemPrimaryCode !!}</td>
            <td>{!! $purchaseOrderProcessDetails->itemDescription !!}</td>
            <td>{!! $purchaseOrderProcessDetails->unitOfMeasure !!}</td>
            <td>{!! $purchaseOrderProcessDetails->comments !!}</td>
            <td>{!! $purchaseOrderProcessDetails->quantityRequested !!}</td>
            <td>{!! $purchaseOrderProcessDetails->orderedQty !!}</td>
            <td>{!! $purchaseOrderProcessDetails->supplierPOqty !!}</td>
            <td>{!! $purchaseOrderProcessDetails->supplierCost !!}</td>
            <td>{!! $purchaseOrderProcessDetails->selectedSupplier !!}</td>
            <td>{!! $purchaseOrderProcessDetails->catalogueMasterID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->catalogueDetailID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->partNumber !!}</td>
            <td>{!! $purchaseOrderProcessDetails->itemClientReferenceNumberMasterID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->clientReferenceNumber !!}</td>
            <td>{!! $purchaseOrderProcessDetails->localCurrencyID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->companyReportingCurrencyID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->companyReportingER !!}</td>
            <td>{!! $purchaseOrderProcessDetails->selectedForPO !!}</td>
            <td>{!! $purchaseOrderProcessDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->financeGLcodebBSSystemID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->financeGLcodebBS !!}</td>
            <td>{!! $purchaseOrderProcessDetails->financeGLcodePLSystemID !!}</td>
            <td>{!! $purchaseOrderProcessDetails->financeGLcodePL !!}</td>
            <td>{!! $purchaseOrderProcessDetails->includePLForGRVYN !!}</td>
            <td>{!! $purchaseOrderProcessDetails->isAccrued !!}</td>
            <td>{!! $purchaseOrderProcessDetails->budgetYear !!}</td>
            <td>{!! $purchaseOrderProcessDetails->prBelongsYear !!}</td>
            <td>{!! $purchaseOrderProcessDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseOrderProcessDetails.destroy', $purchaseOrderProcessDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseOrderProcessDetails.show', [$purchaseOrderProcessDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseOrderProcessDetails.edit', [$purchaseOrderProcessDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>