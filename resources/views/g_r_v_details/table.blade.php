<table class="table table-responsive" id="gRVDetails-table">
    <thead>
        <tr>
            <th>Grvautoid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Servicelinecode</th>
        <th>Purchaseordermastertid</th>
        <th>Purchaseorderdetailsid</th>
        <th>Itemcode</th>
        <th>Itemprimarycode</th>
        <th>Itemdescription</th>
        <th>Itemfinancecategoryid</th>
        <th>Itemfinancecategorysubid</th>
        <th>Financeglcodebbssystemid</th>
        <th>Financeglcodebbs</th>
        <th>Financeglcodeplsystemid</th>
        <th>Financeglcodepl</th>
        <th>Includeplforgrvyn</th>
        <th>Supplierpartnumber</th>
        <th>Unitofmeasure</th>
        <th>Noqty</th>
        <th>Prvrecievedqty</th>
        <th>Poqty</th>
        <th>Unitcost</th>
        <th>Discountpercentage</th>
        <th>Discountamount</th>
        <th>Netamount</th>
        <th>Comment</th>
        <th>Supplierdefaultcurrencyid</th>
        <th>Supplierdefaulter</th>
        <th>Supplieritemcurrencyid</th>
        <th>Foreigntolocaler</th>
        <th>Companyreportingcurrencyid</th>
        <th>Companyreportinger</th>
        <th>Localcurrencyid</th>
        <th>Localcurrencyer</th>
        <th>Addondistcost</th>
        <th>Grvcostperunitlocalcur</th>
        <th>Grvcostperunitsupdefaultcur</th>
        <th>Grvcostperunitsuptranscur</th>
        <th>Grvcostperunitcomrptcur</th>
        <th>Landingcost Transcur</th>
        <th>Landingcost Localcur</th>
        <th>Landingcost Rptcur</th>
        <th>Logisticscharges Transcur</th>
        <th>Logisticscharges Localcur</th>
        <th>Logisticschargest Rptcur</th>
        <th>Assetallocationdoneyn</th>
        <th>Iscontract</th>
        <th>Timesreferred</th>
        <th>Totalwhtamount</th>
        <th>Whtbearedbysupplier</th>
        <th>Whtbearedbycompany</th>
        <th>Extracomment</th>
        <th>Vatregisteredyn</th>
        <th>Suppliervateligible</th>
        <th>Vatpercentage</th>
        <th>Vatamount</th>
        <th>Vatamountlocal</th>
        <th>Vatamountrpt</th>
        <th>Logisticsavailable</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($gRVDetails as $gRVDetails)
        <tr>
            <td>{!! $gRVDetails->grvAutoID !!}</td>
            <td>{!! $gRVDetails->companySystemID !!}</td>
            <td>{!! $gRVDetails->companyID !!}</td>
            <td>{!! $gRVDetails->serviceLineCode !!}</td>
            <td>{!! $gRVDetails->purchaseOrderMastertID !!}</td>
            <td>{!! $gRVDetails->purchaseOrderDetailsID !!}</td>
            <td>{!! $gRVDetails->itemCode !!}</td>
            <td>{!! $gRVDetails->itemPrimaryCode !!}</td>
            <td>{!! $gRVDetails->itemDescription !!}</td>
            <td>{!! $gRVDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $gRVDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $gRVDetails->financeGLcodebBSSystemID !!}</td>
            <td>{!! $gRVDetails->financeGLcodebBS !!}</td>
            <td>{!! $gRVDetails->financeGLcodePLSystemID !!}</td>
            <td>{!! $gRVDetails->financeGLcodePL !!}</td>
            <td>{!! $gRVDetails->includePLForGRVYN !!}</td>
            <td>{!! $gRVDetails->supplierPartNumber !!}</td>
            <td>{!! $gRVDetails->unitOfMeasure !!}</td>
            <td>{!! $gRVDetails->noQty !!}</td>
            <td>{!! $gRVDetails->prvRecievedQty !!}</td>
            <td>{!! $gRVDetails->poQty !!}</td>
            <td>{!! $gRVDetails->unitCost !!}</td>
            <td>{!! $gRVDetails->discountPercentage !!}</td>
            <td>{!! $gRVDetails->discountAmount !!}</td>
            <td>{!! $gRVDetails->netAmount !!}</td>
            <td>{!! $gRVDetails->comment !!}</td>
            <td>{!! $gRVDetails->supplierDefaultCurrencyID !!}</td>
            <td>{!! $gRVDetails->supplierDefaultER !!}</td>
            <td>{!! $gRVDetails->supplierItemCurrencyID !!}</td>
            <td>{!! $gRVDetails->foreignToLocalER !!}</td>
            <td>{!! $gRVDetails->companyReportingCurrencyID !!}</td>
            <td>{!! $gRVDetails->companyReportingER !!}</td>
            <td>{!! $gRVDetails->localCurrencyID !!}</td>
            <td>{!! $gRVDetails->localCurrencyER !!}</td>
            <td>{!! $gRVDetails->addonDistCost !!}</td>
            <td>{!! $gRVDetails->GRVcostPerUnitLocalCur !!}</td>
            <td>{!! $gRVDetails->GRVcostPerUnitSupDefaultCur !!}</td>
            <td>{!! $gRVDetails->GRVcostPerUnitSupTransCur !!}</td>
            <td>{!! $gRVDetails->GRVcostPerUnitComRptCur !!}</td>
            <td>{!! $gRVDetails->landingCost_TransCur !!}</td>
            <td>{!! $gRVDetails->landingCost_LocalCur !!}</td>
            <td>{!! $gRVDetails->landingCost_RptCur !!}</td>
            <td>{!! $gRVDetails->logisticsCharges_TransCur !!}</td>
            <td>{!! $gRVDetails->logisticsCharges_LocalCur !!}</td>
            <td>{!! $gRVDetails->logisticsChargest_RptCur !!}</td>
            <td>{!! $gRVDetails->assetAllocationDoneYN !!}</td>
            <td>{!! $gRVDetails->isContract !!}</td>
            <td>{!! $gRVDetails->timesReferred !!}</td>
            <td>{!! $gRVDetails->totalWHTAmount !!}</td>
            <td>{!! $gRVDetails->WHTBearedBySupplier !!}</td>
            <td>{!! $gRVDetails->WHTBearedByCompany !!}</td>
            <td>{!! $gRVDetails->extraComment !!}</td>
            <td>{!! $gRVDetails->vatRegisteredYN !!}</td>
            <td>{!! $gRVDetails->supplierVATEligible !!}</td>
            <td>{!! $gRVDetails->VATPercentage !!}</td>
            <td>{!! $gRVDetails->VATAmount !!}</td>
            <td>{!! $gRVDetails->VATAmountLocal !!}</td>
            <td>{!! $gRVDetails->VATAmountRpt !!}</td>
            <td>{!! $gRVDetails->logisticsAvailable !!}</td>
            <td>{!! $gRVDetails->createdUserGroup !!}</td>
            <td>{!! $gRVDetails->createdPcID !!}</td>
            <td>{!! $gRVDetails->createdUserID !!}</td>
            <td>{!! $gRVDetails->modifiedPc !!}</td>
            <td>{!! $gRVDetails->modifiedUser !!}</td>
            <td>{!! $gRVDetails->createdDateTime !!}</td>
            <td>{!! $gRVDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['gRVDetails.destroy', $gRVDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('gRVDetails.show', [$gRVDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('gRVDetails.edit', [$gRVDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>