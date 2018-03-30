<table class="table table-responsive" id="purchaseRequestDetails-table">
    <thead>
        <tr>
            <th>Purchaserequestid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Itemcategoryid</th>
        <th>Itemcode</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Partnumber</th>
        <th>Quantityrequested</th>
        <th>Estimatedcost</th>
        <th>Totalcost</th>
        <th>Budgetyear</th>
        <th>Budjetamtlocal</th>
        <th>Budjetamtrpt</th>
        <th>Quantityonorder</th>
        <th>Comments</th>
        <th>Unitofmeasure</th>
        <th>Itemclientreferencenumbermasterid</th>
        <th>Clientreferencenumber</th>
        <th>Quantityinhand</th>
        <th>Maxqty</th>
        <th>Minqty</th>
        <th>Poquantity</th>
        <th>Specificationgrade</th>
        <th>Jobno</th>
        <th>Technicaldatasheetattachment</th>
        <th>Selectedforpo</th>
        <th>Prclosedyn</th>
        <th>Fullyordered</th>
        <th>Potrackingid</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($purchaseRequestDetails as $purchaseRequestDetails)
        <tr>
            <td>{!! $purchaseRequestDetails->purchaseRequestID !!}</td>
            <td>{!! $purchaseRequestDetails->companySystemID !!}</td>
            <td>{!! $purchaseRequestDetails->companyID !!}</td>
            <td>{!! $purchaseRequestDetails->itemCategoryID !!}</td>
            <td>{!! $purchaseRequestDetails->itemCode !!}</td>
            <td>{!! $purchaseRequestDetails->itemPrimaryCode !!}</td>
            <td>{!! $purchaseRequestDetails->itemDescription !!}</td>
            <td>{!! $purchaseRequestDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $purchaseRequestDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $purchaseRequestDetails->financeGLcodebBSSystemID !!}</td>
            <td>{!! $purchaseRequestDetails->financeGLcodebBS !!}</td>
            <td>{!! $purchaseRequestDetails->financeGLcodePLSystemID !!}</td>
            <td>{!! $purchaseRequestDetails->financeGLcodePL !!}</td>
            <td>{!! $purchaseRequestDetails->includePLForGRVYN !!}</td>
            <td>{!! $purchaseRequestDetails->partNumber !!}</td>
            <td>{!! $purchaseRequestDetails->quantityRequested !!}</td>
            <td>{!! $purchaseRequestDetails->estimatedCost !!}</td>
            <td>{!! $purchaseRequestDetails->totalCost !!}</td>
            <td>{!! $purchaseRequestDetails->budgetYear !!}</td>
            <td>{!! $purchaseRequestDetails->budjetAmtLocal !!}</td>
            <td>{!! $purchaseRequestDetails->budjetAmtRpt !!}</td>
            <td>{!! $purchaseRequestDetails->quantityOnOrder !!}</td>
            <td>{!! $purchaseRequestDetails->comments !!}</td>
            <td>{!! $purchaseRequestDetails->unitOfMeasure !!}</td>
            <td>{!! $purchaseRequestDetails->itemClientReferenceNumberMasterID !!}</td>
            <td>{!! $purchaseRequestDetails->clientReferenceNumber !!}</td>
            <td>{!! $purchaseRequestDetails->quantityInHand !!}</td>
            <td>{!! $purchaseRequestDetails->maxQty !!}</td>
            <td>{!! $purchaseRequestDetails->minQty !!}</td>
            <td>{!! $purchaseRequestDetails->poQuantity !!}</td>
            <td>{!! $purchaseRequestDetails->specificationGrade !!}</td>
            <td>{!! $purchaseRequestDetails->jobNo !!}</td>
            <td>{!! $purchaseRequestDetails->technicalDataSheetAttachment !!}</td>
            <td>{!! $purchaseRequestDetails->selectedForPO !!}</td>
            <td>{!! $purchaseRequestDetails->prClosedYN !!}</td>
            <td>{!! $purchaseRequestDetails->fullyOrdered !!}</td>
            <td>{!! $purchaseRequestDetails->poTrackingID !!}</td>
            <td>{!! $purchaseRequestDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseRequestDetails.destroy', $purchaseRequestDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseRequestDetails.show', [$purchaseRequestDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseRequestDetails.edit', [$purchaseRequestDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>