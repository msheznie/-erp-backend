<table class="table table-responsive" id="freeBillings-table">
    <thead>
        <tr>
            <th>Billprocessno</th>
        <th>Ticketno</th>
        <th>Motid</th>
        <th>Mitid</th>
        <th>Assetunitid</th>
        <th>Assetserialno</th>
        <th>Unitid</th>
        <th>Ratecurrencyid</th>
        <th>Standardtimeonloc</th>
        <th>Standardtimeonlocinitial</th>
        <th>Standardrate</th>
        <th>Operationtimeonloc</th>
        <th>Operationtimeonlocinitial</th>
        <th>Operationrate</th>
        <th>Usagetimeonloc</th>
        <th>Usagetimeonlocinitial</th>
        <th>Usagerate</th>
        <th>Lostinholeyn</th>
        <th>Lostinholeyninitial</th>
        <th>Lostinholerate</th>
        <th>Lihdate</th>
        <th>Dbryn</th>
        <th>Dbryninitial</th>
        <th>Dbrrate</th>
        <th>Performainvoiceno</th>
        <th>Invoiceno</th>
        <th>Usedyn</th>
        <th>Usedyninitial</th>
        <th>Contractdetailid</th>
        <th>Lihinspectionstartedyn</th>
        <th>Dbrinspectionstartedyn</th>
        <th>Mitqty</th>
        <th>Assetdescription</th>
        <th>Motdate</th>
        <th>Mitdate</th>
        <th>Rentalstartdate</th>
        <th>Rentalenddate</th>
        <th>Assetdescriptionamend</th>
        <th>Amendhistory</th>
        <th>Stdglcode</th>
        <th>Operatingglcode</th>
        <th>Usageglcode</th>
        <th>Lihglcode</th>
        <th>Dbrglcode</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Qtyserviceproduct</th>
        <th>Opperformacaptionlink</th>
        <th>Timestamp</th>
        <th>Unitop</th>
        <th>Unitusage</th>
        <th>Unitlih</th>
        <th>Unitdbr</th>
        <th>Companyid</th>
        <th>Serviceline</th>
        <th>Usagelinkid</th>
        <th>Subcontdetid</th>
        <th>Subcontdetails</th>
        <th>Usagetype</th>
        <th>Usagetypedes</th>
        <th>Ticketdetdes</th>
        <th>Grouponrptyn</th>
        <th>Isconsumable</th>
        <th>Motdetailid</th>
        <th>Freebillingcomment</th>
        <th>Stbhrrate</th>
        <th>Ophrrate</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($freeBillings as $freeBilling)
        <tr>
            <td>{!! $freeBilling->billProcessNo !!}</td>
            <td>{!! $freeBilling->TicketNo !!}</td>
            <td>{!! $freeBilling->motID !!}</td>
            <td>{!! $freeBilling->mitID !!}</td>
            <td>{!! $freeBilling->AssetUnitID !!}</td>
            <td>{!! $freeBilling->assetSerialNo !!}</td>
            <td>{!! $freeBilling->unitID !!}</td>
            <td>{!! $freeBilling->rateCurrencyID !!}</td>
            <td>{!! $freeBilling->StandardTimeOnLoc !!}</td>
            <td>{!! $freeBilling->StandardTimeOnLocInitial !!}</td>
            <td>{!! $freeBilling->standardRate !!}</td>
            <td>{!! $freeBilling->operationTimeOnLoc !!}</td>
            <td>{!! $freeBilling->operationTimeOnLocInitial !!}</td>
            <td>{!! $freeBilling->operationRate !!}</td>
            <td>{!! $freeBilling->UsageTimeOnLoc !!}</td>
            <td>{!! $freeBilling->UsageTimeOnLocInitial !!}</td>
            <td>{!! $freeBilling->usageRate !!}</td>
            <td>{!! $freeBilling->lostInHoleYN !!}</td>
            <td>{!! $freeBilling->lostInHoleYNinitial !!}</td>
            <td>{!! $freeBilling->lostInHoleRate !!}</td>
            <td>{!! $freeBilling->lihDate !!}</td>
            <td>{!! $freeBilling->dbrYN !!}</td>
            <td>{!! $freeBilling->dbrYNinitial !!}</td>
            <td>{!! $freeBilling->dbrRate !!}</td>
            <td>{!! $freeBilling->performaInvoiceNo !!}</td>
            <td>{!! $freeBilling->InvoiceNo !!}</td>
            <td>{!! $freeBilling->usedYN !!}</td>
            <td>{!! $freeBilling->usedYNinitial !!}</td>
            <td>{!! $freeBilling->ContractDetailID !!}</td>
            <td>{!! $freeBilling->lihInspectionStartedYN !!}</td>
            <td>{!! $freeBilling->dbrInspectionStartedYN !!}</td>
            <td>{!! $freeBilling->mitQty !!}</td>
            <td>{!! $freeBilling->assetDescription !!}</td>
            <td>{!! $freeBilling->motDate !!}</td>
            <td>{!! $freeBilling->mitDate !!}</td>
            <td>{!! $freeBilling->rentalStartDate !!}</td>
            <td>{!! $freeBilling->rentalEndDate !!}</td>
            <td>{!! $freeBilling->assetDescriptionAmend !!}</td>
            <td>{!! $freeBilling->amendHistory !!}</td>
            <td>{!! $freeBilling->stdGLcode !!}</td>
            <td>{!! $freeBilling->operatingGLcode !!}</td>
            <td>{!! $freeBilling->usageGLcode !!}</td>
            <td>{!! $freeBilling->lihGLcode !!}</td>
            <td>{!! $freeBilling->dbrGLcode !!}</td>
            <td>{!! $freeBilling->createdUserGroup !!}</td>
            <td>{!! $freeBilling->createdPcID !!}</td>
            <td>{!! $freeBilling->createdUserID !!}</td>
            <td>{!! $freeBilling->modifiedPc !!}</td>
            <td>{!! $freeBilling->modifiedUser !!}</td>
            <td>{!! $freeBilling->createdDateTime !!}</td>
            <td>{!! $freeBilling->qtyServiceProduct !!}</td>
            <td>{!! $freeBilling->opPerformaCaptionLink !!}</td>
            <td>{!! $freeBilling->timeStamp !!}</td>
            <td>{!! $freeBilling->unitOP !!}</td>
            <td>{!! $freeBilling->unitUsage !!}</td>
            <td>{!! $freeBilling->unitLIH !!}</td>
            <td>{!! $freeBilling->unitDBR !!}</td>
            <td>{!! $freeBilling->companyID !!}</td>
            <td>{!! $freeBilling->serviceLine !!}</td>
            <td>{!! $freeBilling->UsageLinkID !!}</td>
            <td>{!! $freeBilling->subContDetID !!}</td>
            <td>{!! $freeBilling->subContDetails !!}</td>
            <td>{!! $freeBilling->usageType !!}</td>
            <td>{!! $freeBilling->usageTypeDes !!}</td>
            <td>{!! $freeBilling->ticketDetDes !!}</td>
            <td>{!! $freeBilling->groupOnRptYN !!}</td>
            <td>{!! $freeBilling->isConsumable !!}</td>
            <td>{!! $freeBilling->motDetailID !!}</td>
            <td>{!! $freeBilling->freeBillingComment !!}</td>
            <td>{!! $freeBilling->StbHrRate !!}</td>
            <td>{!! $freeBilling->OpHrRate !!}</td>
            <td>
                {!! Form::open(['route' => ['freeBillings.destroy', $freeBilling->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('freeBillings.show', [$freeBilling->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('freeBillings.edit', [$freeBilling->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>