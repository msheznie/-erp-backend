<table class="table table-responsive" id="prDetailsReferedHistories-table">
    <thead>
        <tr>
            <th>Purchaserequestid</th>
        <th>Itemcode</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Quantityrequested</th>
        <th>Estimatedcost</th>
        <th>Quantityonorder</th>
        <th>Comments</th>
        <th>Unitofmeasure</th>
        <th>Quantityinhand</th>
        <th>Timesreffered</th>
        <th>Timestamp</th>
        <th>Partnumber</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($prDetailsReferedHistories as $prDetailsReferedHistory)
        <tr>
            <td>{!! $prDetailsReferedHistory->purchaseRequestID !!}</td>
            <td>{!! $prDetailsReferedHistory->itemCode !!}</td>
            <td>{!! $prDetailsReferedHistory->itemPrimaryCode !!}</td>
            <td>{!! $prDetailsReferedHistory->itemDescription !!}</td>
            <td>{!! $prDetailsReferedHistory->itemFinanceCategoryID !!}</td>
            <td>{!! $prDetailsReferedHistory->itemFinanceCategorySubID !!}</td>
            <td>{!! $prDetailsReferedHistory->financeGLcodebBS !!}</td>
            <td>{!! $prDetailsReferedHistory->financeGLcodePL !!}</td>
            <td>{!! $prDetailsReferedHistory->includePLForGRVYN !!}</td>
            <td>{!! $prDetailsReferedHistory->quantityRequested !!}</td>
            <td>{!! $prDetailsReferedHistory->estimatedCost !!}</td>
            <td>{!! $prDetailsReferedHistory->quantityOnOrder !!}</td>
            <td>{!! $prDetailsReferedHistory->comments !!}</td>
            <td>{!! $prDetailsReferedHistory->unitOfMeasure !!}</td>
            <td>{!! $prDetailsReferedHistory->quantityInHand !!}</td>
            <td>{!! $prDetailsReferedHistory->timesReffered !!}</td>
            <td>{!! $prDetailsReferedHistory->timeStamp !!}</td>
            <td>{!! $prDetailsReferedHistory->partNumber !!}</td>
            <td>
                {!! Form::open(['route' => ['prDetailsReferedHistories.destroy', $prDetailsReferedHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('prDetailsReferedHistories.show', [$prDetailsReferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('prDetailsReferedHistories.edit', [$prDetailsReferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>