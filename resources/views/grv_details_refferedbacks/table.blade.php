<table class="table table-responsive" id="grvDetailsRefferedbacks-table">
    <thead>
        <tr>
            <th>Grvdetailsid</th>
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
    @foreach($grvDetailsRefferedbacks as $grvDetailsRefferedback)
        <tr>
            <td>{!! $grvDetailsRefferedback->grvDetailsID !!}</td>
            <td>{!! $grvDetailsRefferedback->grvAutoID !!}</td>
            <td>{!! $grvDetailsRefferedback->companySystemID !!}</td>
            <td>{!! $grvDetailsRefferedback->companyID !!}</td>
            <td>{!! $grvDetailsRefferedback->serviceLineCode !!}</td>
            <td>{!! $grvDetailsRefferedback->purchaseOrderMastertID !!}</td>
            <td>{!! $grvDetailsRefferedback->purchaseOrderDetailsID !!}</td>
            <td>{!! $grvDetailsRefferedback->itemCode !!}</td>
            <td>{!! $grvDetailsRefferedback->itemPrimaryCode !!}</td>
            <td>{!! $grvDetailsRefferedback->itemDescription !!}</td>
            <td>{!! $grvDetailsRefferedback->itemFinanceCategoryID !!}</td>
            <td>{!! $grvDetailsRefferedback->itemFinanceCategorySubID !!}</td>
            <td>{!! $grvDetailsRefferedback->financeGLcodebBSSystemID !!}</td>
            <td>{!! $grvDetailsRefferedback->financeGLcodebBS !!}</td>
            <td>{!! $grvDetailsRefferedback->financeGLcodePLSystemID !!}</td>
            <td>{!! $grvDetailsRefferedback->financeGLcodePL !!}</td>
            <td>{!! $grvDetailsRefferedback->includePLForGRVYN !!}</td>
            <td>{!! $grvDetailsRefferedback->supplierPartNumber !!}</td>
            <td>{!! $grvDetailsRefferedback->unitOfMeasure !!}</td>
            <td>{!! $grvDetailsRefferedback->noQty !!}</td>
            <td>{!! $grvDetailsRefferedback->prvRecievedQty !!}</td>
            <td>{!! $grvDetailsRefferedback->poQty !!}</td>
            <td>{!! $grvDetailsRefferedback->unitCost !!}</td>
            <td>{!! $grvDetailsRefferedback->discountPercentage !!}</td>
            <td>{!! $grvDetailsRefferedback->discountAmount !!}</td>
            <td>{!! $grvDetailsRefferedback->netAmount !!}</td>
            <td>{!! $grvDetailsRefferedback->comment !!}</td>
            <td>{!! $grvDetailsRefferedback->supplierDefaultCurrencyID !!}</td>
            <td>{!! $grvDetailsRefferedback->supplierDefaultER !!}</td>
            <td>{!! $grvDetailsRefferedback->supplierItemCurrencyID !!}</td>
            <td>{!! $grvDetailsRefferedback->foreignToLocalER !!}</td>
            <td>{!! $grvDetailsRefferedback->companyReportingCurrencyID !!}</td>
            <td>{!! $grvDetailsRefferedback->companyReportingER !!}</td>
            <td>{!! $grvDetailsRefferedback->localCurrencyID !!}</td>
            <td>{!! $grvDetailsRefferedback->localCurrencyER !!}</td>
            <td>{!! $grvDetailsRefferedback->addonDistCost !!}</td>
            <td>{!! $grvDetailsRefferedback->GRVcostPerUnitLocalCur !!}</td>
            <td>{!! $grvDetailsRefferedback->GRVcostPerUnitSupDefaultCur !!}</td>
            <td>{!! $grvDetailsRefferedback->GRVcostPerUnitSupTransCur !!}</td>
            <td>{!! $grvDetailsRefferedback->GRVcostPerUnitComRptCur !!}</td>
            <td>{!! $grvDetailsRefferedback->landingCost_TransCur !!}</td>
            <td>{!! $grvDetailsRefferedback->landingCost_LocalCur !!}</td>
            <td>{!! $grvDetailsRefferedback->landingCost_RptCur !!}</td>
            <td>{!! $grvDetailsRefferedback->logisticsCharges_TransCur !!}</td>
            <td>{!! $grvDetailsRefferedback->logisticsCharges_LocalCur !!}</td>
            <td>{!! $grvDetailsRefferedback->logisticsChargest_RptCur !!}</td>
            <td>{!! $grvDetailsRefferedback->assetAllocationDoneYN !!}</td>
            <td>{!! $grvDetailsRefferedback->isContract !!}</td>
            <td>{!! $grvDetailsRefferedback->timesReferred !!}</td>
            <td>{!! $grvDetailsRefferedback->totalWHTAmount !!}</td>
            <td>{!! $grvDetailsRefferedback->WHTBearedBySupplier !!}</td>
            <td>{!! $grvDetailsRefferedback->WHTBearedByCompany !!}</td>
            <td>{!! $grvDetailsRefferedback->extraComment !!}</td>
            <td>{!! $grvDetailsRefferedback->vatRegisteredYN !!}</td>
            <td>{!! $grvDetailsRefferedback->supplierVATEligible !!}</td>
            <td>{!! $grvDetailsRefferedback->VATPercentage !!}</td>
            <td>{!! $grvDetailsRefferedback->VATAmount !!}</td>
            <td>{!! $grvDetailsRefferedback->VATAmountLocal !!}</td>
            <td>{!! $grvDetailsRefferedback->VATAmountRpt !!}</td>
            <td>{!! $grvDetailsRefferedback->logisticsAvailable !!}</td>
            <td>{!! $grvDetailsRefferedback->createdUserGroup !!}</td>
            <td>{!! $grvDetailsRefferedback->createdPcID !!}</td>
            <td>{!! $grvDetailsRefferedback->createdUserID !!}</td>
            <td>{!! $grvDetailsRefferedback->modifiedPc !!}</td>
            <td>{!! $grvDetailsRefferedback->modifiedUser !!}</td>
            <td>{!! $grvDetailsRefferedback->createdDateTime !!}</td>
            <td>{!! $grvDetailsRefferedback->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['grvDetailsRefferedbacks.destroy', $grvDetailsRefferedback->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('grvDetailsRefferedbacks.show', [$grvDetailsRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('grvDetailsRefferedbacks.edit', [$grvDetailsRefferedback->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>