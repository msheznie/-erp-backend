<table class="table table-responsive" id="logistics-table">
    <thead>
        <tr>
            <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelineid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Logisticdoccode</th>
        <th>Comments</th>
        <th>Supplierid</th>
        <th>Logisticshippingmodeid</th>
        <th>Modeofimportid</th>
        <th>Nextcustomdocrenewaldate</th>
        <th>Customdocrenewalhistory</th>
        <th>Custominvoiceno</th>
        <th>Custominvoicedate</th>
        <th>Custominvoicecurrencyid</th>
        <th>Custominvoiceamount</th>
        <th>Custominvoicelocalcurrencyid</th>
        <th>Custominvoicelocaler</th>
        <th>Custominvoicelocalamount</th>
        <th>Custominvoicerptcurrencyid</th>
        <th>Custominvoicerpter</th>
        <th>Custominvoicerptamount</th>
        <th>Airwaybillno</th>
        <th>Totalweight</th>
        <th>Totalweightuom</th>
        <th>Totalvolume</th>
        <th>Totalvolumeuom</th>
        <th>Customearrivaldate</th>
        <th>Deliverydate</th>
        <th>Billofentrydate</th>
        <th>Billofentryno</th>
        <th>Agentdeliverylocationid</th>
        <th>Agentdonumber</th>
        <th>Agentdodate</th>
        <th>Agentid</th>
        <th>Agentfeecurrencyid</th>
        <th>Agentfee</th>
        <th>Agentfeelocalamount</th>
        <th>Agenfeerptamount</th>
        <th>Customdutyfeecurrencyid</th>
        <th>Customdutyfeeamount</th>
        <th>Customdutyfeelocalamount</th>
        <th>Customdutyfeerptamount</th>
        <th>Customdutytotalamount</th>
        <th>Shippingoriginport</th>
        <th>Shippingorigincountry</th>
        <th>Shippingorigindate</th>
        <th>Shippingdestinationport</th>
        <th>Shippingdestinationcountry</th>
        <th>Shippingdestinationdate</th>
        <th>Ftaordf</th>
        <th>Createduserid</th>
        <th>Createdusersystemid</th>
        <th>Createdpcid</th>
        <th>Createddatetime</th>
        <th>Modifieduserid</th>
        <th>Modifiedusersystemid</th>
        <th>Modifiedpcid</th>
        <th>Modifieddate</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($logistics as $logistic)
        <tr>
            <td>{!! $logistic->companySystemID !!}</td>
            <td>{!! $logistic->companyID !!}</td>
            <td>{!! $logistic->serviceLineSystemID !!}</td>
            <td>{!! $logistic->serviceLineID !!}</td>
            <td>{!! $logistic->documentSystemID !!}</td>
            <td>{!! $logistic->documentID !!}</td>
            <td>{!! $logistic->serialNo !!}</td>
            <td>{!! $logistic->logisticDocCode !!}</td>
            <td>{!! $logistic->comments !!}</td>
            <td>{!! $logistic->supplierID !!}</td>
            <td>{!! $logistic->logisticShippingModeID !!}</td>
            <td>{!! $logistic->modeOfImportID !!}</td>
            <td>{!! $logistic->nextCustomDocRenewalDate !!}</td>
            <td>{!! $logistic->customDocRenewalHistory !!}</td>
            <td>{!! $logistic->customInvoiceNo !!}</td>
            <td>{!! $logistic->customInvoiceDate !!}</td>
            <td>{!! $logistic->customInvoiceCurrencyID !!}</td>
            <td>{!! $logistic->customInvoiceAmount !!}</td>
            <td>{!! $logistic->customInvoiceLocalCurrencyID !!}</td>
            <td>{!! $logistic->customInvoiceLocalER !!}</td>
            <td>{!! $logistic->customInvoiceLocalAmount !!}</td>
            <td>{!! $logistic->customInvoiceRptCurrencyID !!}</td>
            <td>{!! $logistic->customInvoiceRptER !!}</td>
            <td>{!! $logistic->customInvoiceRptAmount !!}</td>
            <td>{!! $logistic->airwayBillNo !!}</td>
            <td>{!! $logistic->totalWeight !!}</td>
            <td>{!! $logistic->totalWeightUOM !!}</td>
            <td>{!! $logistic->totalVolume !!}</td>
            <td>{!! $logistic->totalVolumeUOM !!}</td>
            <td>{!! $logistic->customeArrivalDate !!}</td>
            <td>{!! $logistic->deliveryDate !!}</td>
            <td>{!! $logistic->billofEntryDate !!}</td>
            <td>{!! $logistic->billofEntryNo !!}</td>
            <td>{!! $logistic->agentDeliveryLocationID !!}</td>
            <td>{!! $logistic->agentDOnumber !!}</td>
            <td>{!! $logistic->agentDOdate !!}</td>
            <td>{!! $logistic->agentID !!}</td>
            <td>{!! $logistic->agentFeeCurrencyID !!}</td>
            <td>{!! $logistic->agentFee !!}</td>
            <td>{!! $logistic->agentFeeLocalAmount !!}</td>
            <td>{!! $logistic->agenFeeRptAmount !!}</td>
            <td>{!! $logistic->customDutyFeeCurrencyID !!}</td>
            <td>{!! $logistic->customDutyFeeAmount !!}</td>
            <td>{!! $logistic->customDutyFeeLocalAmount !!}</td>
            <td>{!! $logistic->customDutyFeeRptAmount !!}</td>
            <td>{!! $logistic->customDutyTotalAmount !!}</td>
            <td>{!! $logistic->shippingOriginPort !!}</td>
            <td>{!! $logistic->shippingOriginCountry !!}</td>
            <td>{!! $logistic->shippingOriginDate !!}</td>
            <td>{!! $logistic->shippingDestinationPort !!}</td>
            <td>{!! $logistic->shippingDestinationCountry !!}</td>
            <td>{!! $logistic->shippingDestinationDate !!}</td>
            <td>{!! $logistic->ftaOrDF !!}</td>
            <td>{!! $logistic->createdUserID !!}</td>
            <td>{!! $logistic->createdUserSystemID !!}</td>
            <td>{!! $logistic->createdPCid !!}</td>
            <td>{!! $logistic->createdDateTime !!}</td>
            <td>{!! $logistic->modifiedUserID !!}</td>
            <td>{!! $logistic->modifiedUserSystemID !!}</td>
            <td>{!! $logistic->modifiedPCID !!}</td>
            <td>{!! $logistic->modifiedDate !!}</td>
            <td>{!! $logistic->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['logistics.destroy', $logistic->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('logistics.show', [$logistic->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('logistics.edit', [$logistic->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>