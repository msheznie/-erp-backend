<table class="table table-responsive" id="materielRequestDetails-table">
    <thead>
        <tr>
            <th>Requestid</th>
        <th>Itemcode</th>
        <th>Itemdescription</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Partnumber</th>
        <th>Unitofmeasure</th>
        <th>Unitofmeasureissued</th>
        <th>Quantityrequested</th>
        <th>Qtyissueddefaultmeasure</th>
        <th>Convertionmeasureval</th>
        <th>Comments</th>
        <th>Quantityonorder</th>
        <th>Quantityinhand</th>
        <th>Estimatedcost</th>
        <th>Minqty</th>
        <th>Maxqty</th>
        <th>Selectedforissue</th>
        <th>Closedyn</th>
        <th>Allowcreatepr</th>
        <th>Selectedtocreatepr</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($materielRequestDetails as $materielRequestDetails)
        <tr>
            <td>{!! $materielRequestDetails->RequestID !!}</td>
            <td>{!! $materielRequestDetails->itemCode !!}</td>
            <td>{!! $materielRequestDetails->itemDescription !!}</td>
            <td>{!! $materielRequestDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $materielRequestDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $materielRequestDetails->financeGLcodebBS !!}</td>
            <td>{!! $materielRequestDetails->financeGLcodePL !!}</td>
            <td>{!! $materielRequestDetails->includePLForGRVYN !!}</td>
            <td>{!! $materielRequestDetails->partNumber !!}</td>
            <td>{!! $materielRequestDetails->unitOfMeasure !!}</td>
            <td>{!! $materielRequestDetails->unitOfMeasureIssued !!}</td>
            <td>{!! $materielRequestDetails->quantityRequested !!}</td>
            <td>{!! $materielRequestDetails->qtyIssuedDefaultMeasure !!}</td>
            <td>{!! $materielRequestDetails->convertionMeasureVal !!}</td>
            <td>{!! $materielRequestDetails->comments !!}</td>
            <td>{!! $materielRequestDetails->quantityOnOrder !!}</td>
            <td>{!! $materielRequestDetails->quantityInHand !!}</td>
            <td>{!! $materielRequestDetails->estimatedCost !!}</td>
            <td>{!! $materielRequestDetails->minQty !!}</td>
            <td>{!! $materielRequestDetails->maxQty !!}</td>
            <td>{!! $materielRequestDetails->selectedForIssue !!}</td>
            <td>{!! $materielRequestDetails->ClosedYN !!}</td>
            <td>{!! $materielRequestDetails->allowCreatePR !!}</td>
            <td>{!! $materielRequestDetails->selectedToCreatePR !!}</td>
            <td>{!! $materielRequestDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['materielRequestDetails.destroy', $materielRequestDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('materielRequestDetails.show', [$materielRequestDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('materielRequestDetails.edit', [$materielRequestDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>