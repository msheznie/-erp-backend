<table class="table table-responsive" id="itemReturnDetailsRefferedBacks-table">
    <thead>
        <tr>
            <th>Itemreturndetailid</th>
        <th>Itemreturnautoid</th>
        <th>Itemreturncode</th>
        <th>Issuecodesystem</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Itemunitofmeasure</th>
        <th>Unitofmeasureissued</th>
        <th>Qtyissued</th>
        <th>Convertionmeasureval</th>
        <th>Qtyissueddefaultmeasure</th>
        <th>Comments</th>
        <th>Localcurrencyid</th>
        <th>Unitcostlocal</th>
        <th>Reportingcurrencyid</th>
        <th>Unitcostrpt</th>
        <th>Qtyfromissue</th>
        <th>Selectedforbillingop</th>
        <th>Selectedforbillingoptemp</th>
        <th>Opticketno</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Timesreferred</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemReturnDetailsRefferedBacks as $itemReturnDetailsRefferedBack)
        <tr>
            <td>{!! $itemReturnDetailsRefferedBack->itemReturnDetailID !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->itemReturnAutoID !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->itemReturnCode !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->issueCodeSystem !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->itemCodeSystem !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->itemPrimaryCode !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->itemDescription !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->itemUnitOfMeasure !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->unitOfMeasureIssued !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->qtyIssued !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->convertionMeasureVal !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->qtyIssuedDefaultMeasure !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->comments !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->localCurrencyID !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->unitCostLocal !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->reportingCurrencyID !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->unitCostRpt !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->qtyFromIssue !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->selectedForBillingOP !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->selectedForBillingOPtemp !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->opTicketNo !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->itemFinanceCategoryID !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->itemFinanceCategorySubID !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->financeGLcodebBSSystemID !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->financeGLcodebBS !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->financeGLcodePLSystemID !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->financeGLcodePL !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->includePLForGRVYN !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->timesReferred !!}</td>
            <td>{!! $itemReturnDetailsRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemReturnDetailsRefferedBacks.destroy', $itemReturnDetailsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemReturnDetailsRefferedBacks.show', [$itemReturnDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemReturnDetailsRefferedBacks.edit', [$itemReturnDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>