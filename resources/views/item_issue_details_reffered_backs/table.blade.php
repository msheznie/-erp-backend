<table class="table table-responsive" id="itemIssueDetailsRefferedBacks-table">
    <thead>
        <tr>
            <th>Itemissuedetailid</th>
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
        <th>Timesreferred</th>
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
    @foreach($itemIssueDetailsRefferedBacks as $itemIssueDetailsRefferedBack)
        <tr>
            <td>{!! $itemIssueDetailsRefferedBack->itemIssueDetailID !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->itemIssueAutoID !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->itemIssueCode !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->itemCodeSystem !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->itemPrimaryCode !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->itemDescription !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->itemUnitOfMeasure !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->unitOfMeasureIssued !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->clientReferenceNumber !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->qtyRequested !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->qtyIssued !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->comments !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->convertionMeasureVal !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->qtyIssuedDefaultMeasure !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->localCurrencyID !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->issueCostLocal !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->issueCostLocalTotal !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->reportingCurrencyID !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->issueCostRpt !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->issueCostRptTotal !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->currentStockQty !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->currentWareHouseStockQty !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->currentStockQtyInDamageReturn !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->maxQty !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->minQty !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->selectedForBillingOP !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->selectedForBillingOPtemp !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->opTicketNo !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->del !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->backLoad !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->used !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->grvDocumentNO !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->itemFinanceCategoryID !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->itemFinanceCategorySubID !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->financeGLcodebBSSystemID !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->financeGLcodebBS !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->financeGLcodePLSystemID !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->financeGLcodePL !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->includePLForGRVYN !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->timesReferred !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p1 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p2 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p3 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p4 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p5 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p6 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p7 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p8 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p9 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p10 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p11 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p12 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->p13 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->pl10 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->pl3 !!}</td>
            <td>{!! $itemIssueDetailsRefferedBack->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['itemIssueDetailsRefferedBacks.destroy', $itemIssueDetailsRefferedBack->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('itemIssueDetailsRefferedBacks.show', [$itemIssueDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('itemIssueDetailsRefferedBacks.edit', [$itemIssueDetailsRefferedBack->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>