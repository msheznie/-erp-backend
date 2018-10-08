<table class="table table-responsive" id="fixedAssetCosts-table">
    <thead>
        <tr>
            <th>Origindocumentsystemcode</th>
        <th>Origindocumentid</th>
        <th>Itemcode</th>
        <th>Faid</th>
        <th>Assetid</th>
        <th>Assetdescription</th>
        <th>Costdate</th>
        <th>Localcurrencyid</th>
        <th>Localamount</th>
        <th>Rptcurrencyid</th>
        <th>Rptamount</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fixedAssetCosts as $fixedAssetCost)
        <tr>
            <td>{!! $fixedAssetCost->originDocumentSystemCode !!}</td>
            <td>{!! $fixedAssetCost->originDocumentID !!}</td>
            <td>{!! $fixedAssetCost->itemCode !!}</td>
            <td>{!! $fixedAssetCost->faID !!}</td>
            <td>{!! $fixedAssetCost->assetID !!}</td>
            <td>{!! $fixedAssetCost->assetDescription !!}</td>
            <td>{!! $fixedAssetCost->costDate !!}</td>
            <td>{!! $fixedAssetCost->localCurrencyID !!}</td>
            <td>{!! $fixedAssetCost->localAmount !!}</td>
            <td>{!! $fixedAssetCost->rptCurrencyID !!}</td>
            <td>{!! $fixedAssetCost->rptAmount !!}</td>
            <td>{!! $fixedAssetCost->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['fixedAssetCosts.destroy', $fixedAssetCost->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fixedAssetCosts.show', [$fixedAssetCost->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fixedAssetCosts.edit', [$fixedAssetCost->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>