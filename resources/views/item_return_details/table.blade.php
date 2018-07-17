<table class="table table-responsive" id="itemReturnDetails-table">
    <thead>
        <tr>
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
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemReturnDetails as $itemReturnDetails)
        <tr>
            <td>{!! $itemReturnDetails->itemReturnAutoID !!}</td>
            <td>{!! $itemReturnDetails->itemReturnCode !!}</td>
            <td>{!! $itemReturnDetails->issueCodeSystem !!}</td>
            <td>{!! $itemReturnDetails->itemCodeSystem !!}</td>
            <td>{!! $itemReturnDetails->itemPrimaryCode !!}</td>
            <td>{!! $itemReturnDetails->itemDescription !!}</td>
            <td>{!! $itemReturnDetails->itemUnitOfMeasure !!}</td>
            <td>{!! $itemReturnDetails->unitOfMeasureIssued !!}</td>
            <td>{!! $itemReturnDetails->qtyIssued !!}</td>
            <td>{!! $itemReturnDetails->convertionMeasureVal !!}</td>
            <td>{!! $itemReturnDetails->qtyIssuedDefaultMeasure !!}</td>
            <td>{!! $itemReturnDetails->comments !!}</td>
            <td>{!! $itemReturnDetails->localCurrencyID !!}</td>
            <td>{!! $itemReturnDetails->unitCostLocal !!}</td>
            <td>{!! $itemReturnDetails->reportingCurrencyID !!}</td>
            <td>{!! $itemReturnDetails->unitCostRpt !!}</td>
            <td>{!! $itemReturnDetails->qtyFromIssue !!}</td>
            <td>{!! $itemReturnDetails->selectedForBillingOP !!}</td>
            <td>{!! $itemReturnDetails->selectedForBillingOPtemp !!}</td>
            <td>{!! $itemReturnDetails->opTicketNo !!}</td>
            <td>{!! $itemReturnDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $itemReturnDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $itemReturnDetails->financeGLcodebBSSystemID !!}</td>
            <td>{!! $itemReturnDetails->financeGLcodebBS !!}</td>
            <td>{!! $itemReturnDetails->financeGLcodePLSystemID !!}</td>
            <td>{!! $itemReturnDetails->financeGLcodePL !!}</td>
            <td>{!! $itemReturnDetails->includePLForGRVYN !!}</td>
            <td>{!! $itemReturnDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemReturnDetails.destroy', $itemReturnDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemReturnDetails.show', [$itemReturnDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemReturnDetails.edit', [$itemReturnDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>