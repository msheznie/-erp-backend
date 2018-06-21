<table class="table table-responsive" id="itemIssueDetails-table">
    <thead>
        <tr>
            <th>Itemissueautoid</th>
        <th>Itemissuecode</th>
        <th>Itemcodesystem</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Itemunitofmeasure</th>
        <th>Unitofmeasureissued</th>
        <th>Clientreferencenumber</th>
        <th>Qtyrequested</th>
        <th>Qtyissued</th>
        <th>Comments</th>
        <th>Convertionmeasureval</th>
        <th>Qtyissueddefaultmeasure</th>
        <th>Localcurrencyid</th>
        <th>Issuecostlocal</th>
        <th>Issuecostlocaltotal</th>
        <th>Reportingcurrencyid</th>
        <th>Issuecostrpt</th>
        <th>Issuecostrpttotal</th>
        <th>Currentstockqty</th>
        <th>Currentwarehousestockqty</th>
        <th>Currentstockqtyindamagereturn</th>
        <th>Maxqty</th>
        <th>Minqty</th>
        <th>Selectedforbillingop</th>
        <th>Selectedforbillingoptemp</th>
        <th>Opticketno</th>
        <th>Del</th>
        <th>Backload</th>
        <th>Used</th>
        <th>Grvdocumentno</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>P1</th>
        <th>P2</th>
        <th>P3</th>
        <th>P4</th>
        <th>P5</th>
        <th>P6</th>
        <th>P7</th>
        <th>P8</th>
        <th>P9</th>
        <th>P10</th>
        <th>P11</th>
        <th>P12</th>
        <th>P13</th>
        <th>Pl10</th>
        <th>Pl3</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($itemIssueDetails as $itemIssueDetails)
        <tr>
            <td>{!! $itemIssueDetails->itemIssueAutoID !!}</td>
            <td>{!! $itemIssueDetails->itemIssueCode !!}</td>
            <td>{!! $itemIssueDetails->itemCodeSystem !!}</td>
            <td>{!! $itemIssueDetails->itemPrimaryCode !!}</td>
            <td>{!! $itemIssueDetails->itemDescription !!}</td>
            <td>{!! $itemIssueDetails->itemUnitOfMeasure !!}</td>
            <td>{!! $itemIssueDetails->unitOfMeasureIssued !!}</td>
            <td>{!! $itemIssueDetails->clientReferenceNumber !!}</td>
            <td>{!! $itemIssueDetails->qtyRequested !!}</td>
            <td>{!! $itemIssueDetails->qtyIssued !!}</td>
            <td>{!! $itemIssueDetails->comments !!}</td>
            <td>{!! $itemIssueDetails->convertionMeasureVal !!}</td>
            <td>{!! $itemIssueDetails->qtyIssuedDefaultMeasure !!}</td>
            <td>{!! $itemIssueDetails->localCurrencyID !!}</td>
            <td>{!! $itemIssueDetails->issueCostLocal !!}</td>
            <td>{!! $itemIssueDetails->issueCostLocalTotal !!}</td>
            <td>{!! $itemIssueDetails->reportingCurrencyID !!}</td>
            <td>{!! $itemIssueDetails->issueCostRpt !!}</td>
            <td>{!! $itemIssueDetails->issueCostRptTotal !!}</td>
            <td>{!! $itemIssueDetails->currentStockQty !!}</td>
            <td>{!! $itemIssueDetails->currentWareHouseStockQty !!}</td>
            <td>{!! $itemIssueDetails->currentStockQtyInDamageReturn !!}</td>
            <td>{!! $itemIssueDetails->maxQty !!}</td>
            <td>{!! $itemIssueDetails->minQty !!}</td>
            <td>{!! $itemIssueDetails->selectedForBillingOP !!}</td>
            <td>{!! $itemIssueDetails->selectedForBillingOPtemp !!}</td>
            <td>{!! $itemIssueDetails->opTicketNo !!}</td>
            <td>{!! $itemIssueDetails->del !!}</td>
            <td>{!! $itemIssueDetails->backLoad !!}</td>
            <td>{!! $itemIssueDetails->used !!}</td>
            <td>{!! $itemIssueDetails->grvDocumentNO !!}</td>
            <td>{!! $itemIssueDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $itemIssueDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $itemIssueDetails->financeGLcodebBSSystemID !!}</td>
            <td>{!! $itemIssueDetails->financeGLcodebBS !!}</td>
            <td>{!! $itemIssueDetails->financeGLcodePLSystemID !!}</td>
            <td>{!! $itemIssueDetails->financeGLcodePL !!}</td>
            <td>{!! $itemIssueDetails->includePLForGRVYN !!}</td>
            <td>{!! $itemIssueDetails->p1 !!}</td>
            <td>{!! $itemIssueDetails->p2 !!}</td>
            <td>{!! $itemIssueDetails->p3 !!}</td>
            <td>{!! $itemIssueDetails->p4 !!}</td>
            <td>{!! $itemIssueDetails->p5 !!}</td>
            <td>{!! $itemIssueDetails->p6 !!}</td>
            <td>{!! $itemIssueDetails->p7 !!}</td>
            <td>{!! $itemIssueDetails->p8 !!}</td>
            <td>{!! $itemIssueDetails->p9 !!}</td>
            <td>{!! $itemIssueDetails->p10 !!}</td>
            <td>{!! $itemIssueDetails->p11 !!}</td>
            <td>{!! $itemIssueDetails->p12 !!}</td>
            <td>{!! $itemIssueDetails->p13 !!}</td>
            <td>{!! $itemIssueDetails->pl10 !!}</td>
            <td>{!! $itemIssueDetails->pl3 !!}</td>
            <td>{!! $itemIssueDetails->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemIssueDetails.destroy', $itemIssueDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemIssueDetails.show', [$itemIssueDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemIssueDetails.edit', [$itemIssueDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>