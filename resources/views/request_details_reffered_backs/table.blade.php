<table class="table table-responsive" id="requestDetailsRefferedBacks-table">
    <thead>
        <tr>
            <th>Requestdetailsid</th>
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
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($requestDetailsRefferedBacks as $requestDetailsRefferedBack)
        <tr>
            <td>{!! $requestDetailsRefferedBack->RequestDetailsID !!}</td>
            <td>{!! $requestDetailsRefferedBack->RequestID !!}</td>
            <td>{!! $requestDetailsRefferedBack->itemCode !!}</td>
            <td>{!! $requestDetailsRefferedBack->itemDescription !!}</td>
            <td>{!! $requestDetailsRefferedBack->itemFinanceCategoryID !!}</td>
            <td>{!! $requestDetailsRefferedBack->itemFinanceCategorySubID !!}</td>
            <td>{!! $requestDetailsRefferedBack->financeGLcodebBS !!}</td>
            <td>{!! $requestDetailsRefferedBack->financeGLcodePL !!}</td>
            <td>{!! $requestDetailsRefferedBack->includePLForGRVYN !!}</td>
            <td>{!! $requestDetailsRefferedBack->partNumber !!}</td>
            <td>{!! $requestDetailsRefferedBack->unitOfMeasure !!}</td>
            <td>{!! $requestDetailsRefferedBack->unitOfMeasureIssued !!}</td>
            <td>{!! $requestDetailsRefferedBack->quantityRequested !!}</td>
            <td>{!! $requestDetailsRefferedBack->qtyIssuedDefaultMeasure !!}</td>
            <td>{!! $requestDetailsRefferedBack->convertionMeasureVal !!}</td>
            <td>{!! $requestDetailsRefferedBack->comments !!}</td>
            <td>{!! $requestDetailsRefferedBack->quantityOnOrder !!}</td>
            <td>{!! $requestDetailsRefferedBack->quantityInHand !!}</td>
            <td>{!! $requestDetailsRefferedBack->estimatedCost !!}</td>
            <td>{!! $requestDetailsRefferedBack->minQty !!}</td>
            <td>{!! $requestDetailsRefferedBack->maxQty !!}</td>
            <td>{!! $requestDetailsRefferedBack->selectedForIssue !!}</td>
            <td>{!! $requestDetailsRefferedBack->ClosedYN !!}</td>
            <td>{!! $requestDetailsRefferedBack->allowCreatePR !!}</td>
            <td>{!! $requestDetailsRefferedBack->selectedToCreatePR !!}</td>
            <td>{!! $requestDetailsRefferedBack->timesReferred !!}</td>
            <td>{!! $requestDetailsRefferedBack->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['requestDetailsRefferedBacks.destroy', $requestDetailsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('requestDetailsRefferedBacks.show', [$requestDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('requestDetailsRefferedBacks.edit', [$requestDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>