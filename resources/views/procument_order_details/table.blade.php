<table class="table table-responsive" id="procumentOrderDetails-table">
    <thead>
        <tr>
            <th>Companyid</th>
        <th>Departmentid</th>
        <th>Servicelinecode</th>
        <th>Purchaseordermasterid</th>
        <th>Poprocessmasterid</th>
        <th>Wo Purchaseordermasterid</th>
        <th>Wp Purchaseorderdetailsid</th>
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
        <th>Itemclientreferencenumbermasterid</th>
        <th>Clientreferencenumber</th>
        <th>Noqty</th>
        <th>Noofdays</th>
        <th>Unitcost</th>
        <th>Discountpercentage</th>
        <th>Discountamount</th>
        <th>Netamount</th>
        <th>Budgetyear</th>
        <th>Prbelongsyear</th>
        <th>Isaccrued</th>
        <th>Budjetamtlocal</th>
        <th>Budjetamtrpt</th>
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
        <th>Addonpurchasereturncost</th>
        <th>Purchaseretcostperunitlocalcur</th>
        <th>Purchaseretcostperunisupdefaultcur</th>
        <th>Purchaseretcostperunittrancur</th>
        <th>Purchaseretcostperunitrptcur</th>
        <th>Grvselectedyn</th>
        <th>Goodsrecievedyn</th>
        <th>Logisticselectedyn</th>
        <th>Logisticrecievedyn</th>
        <th>Isaccruedyn</th>
        <th>Accrualjvid</th>
        <th>Timesreferred</th>
        <th>Totalwhtamount</th>
        <th>Whtbearedbysupplier</th>
        <th>Whtbearedbycompany</th>
        <th>Vatpercentage</th>
        <th>Vatamount</th>
        <th>Vatamountlocal</th>
        <th>Vatamountrpt</th>
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
    @foreach($procumentOrderDetails as $procumentOrderDetail)
        <tr>
            <td>{!! $procumentOrderDetail->companyID !!}</td>
            <td>{!! $procumentOrderDetail->departmentID !!}</td>
            <td>{!! $procumentOrderDetail->serviceLineCode !!}</td>
            <td>{!! $procumentOrderDetail->purchaseOrderMasterID !!}</td>
            <td>{!! $procumentOrderDetail->POProcessMasterID !!}</td>
            <td>{!! $procumentOrderDetail->WO_purchaseOrderMasterID !!}</td>
            <td>{!! $procumentOrderDetail->WP_purchaseOrderDetailsID !!}</td>
            <td>{!! $procumentOrderDetail->itemCode !!}</td>
            <td>{!! $procumentOrderDetail->itemPrimaryCode !!}</td>
            <td>{!! $procumentOrderDetail->itemDescription !!}</td>
            <td>{!! $procumentOrderDetail->itemFinanceCategoryID !!}</td>
            <td>{!! $procumentOrderDetail->itemFinanceCategorySubID !!}</td>
            <td>{!! $procumentOrderDetail->financeGLcodebBSSystemID !!}</td>
            <td>{!! $procumentOrderDetail->financeGLcodebBS !!}</td>
            <td>{!! $procumentOrderDetail->financeGLcodePLSystemID !!}</td>
            <td>{!! $procumentOrderDetail->financeGLcodePL !!}</td>
            <td>{!! $procumentOrderDetail->includePLForGRVYN !!}</td>
            <td>{!! $procumentOrderDetail->supplierPartNumber !!}</td>
            <td>{!! $procumentOrderDetail->unitOfMeasure !!}</td>
            <td>{!! $procumentOrderDetail->itemClientReferenceNumberMasterID !!}</td>
            <td>{!! $procumentOrderDetail->clientReferenceNumber !!}</td>
            <td>{!! $procumentOrderDetail->noQty !!}</td>
            <td>{!! $procumentOrderDetail->noOfDays !!}</td>
            <td>{!! $procumentOrderDetail->unitCost !!}</td>
            <td>{!! $procumentOrderDetail->discountPercentage !!}</td>
            <td>{!! $procumentOrderDetail->discountAmount !!}</td>
            <td>{!! $procumentOrderDetail->netAmount !!}</td>
            <td>{!! $procumentOrderDetail->budgetYear !!}</td>
            <td>{!! $procumentOrderDetail->prBelongsYear !!}</td>
            <td>{!! $procumentOrderDetail->isAccrued !!}</td>
            <td>{!! $procumentOrderDetail->budjetAmtLocal !!}</td>
            <td>{!! $procumentOrderDetail->budjetAmtRpt !!}</td>
            <td>{!! $procumentOrderDetail->comment !!}</td>
            <td>{!! $procumentOrderDetail->supplierDefaultCurrencyID !!}</td>
            <td>{!! $procumentOrderDetail->supplierDefaultER !!}</td>
            <td>{!! $procumentOrderDetail->supplierItemCurrencyID !!}</td>
            <td>{!! $procumentOrderDetail->foreignToLocalER !!}</td>
            <td>{!! $procumentOrderDetail->companyReportingCurrencyID !!}</td>
            <td>{!! $procumentOrderDetail->companyReportingER !!}</td>
            <td>{!! $procumentOrderDetail->localCurrencyID !!}</td>
            <td>{!! $procumentOrderDetail->localCurrencyER !!}</td>
            <td>{!! $procumentOrderDetail->addonDistCost !!}</td>
            <td>{!! $procumentOrderDetail->GRVcostPerUnitLocalCur !!}</td>
            <td>{!! $procumentOrderDetail->GRVcostPerUnitSupDefaultCur !!}</td>
            <td>{!! $procumentOrderDetail->GRVcostPerUnitSupTransCur !!}</td>
            <td>{!! $procumentOrderDetail->GRVcostPerUnitComRptCur !!}</td>
            <td>{!! $procumentOrderDetail->addonPurchaseReturnCost !!}</td>
            <td>{!! $procumentOrderDetail->purchaseRetcostPerUnitLocalCur !!}</td>
            <td>{!! $procumentOrderDetail->purchaseRetcostPerUniSupDefaultCur !!}</td>
            <td>{!! $procumentOrderDetail->purchaseRetcostPerUnitTranCur !!}</td>
            <td>{!! $procumentOrderDetail->purchaseRetcostPerUnitRptCur !!}</td>
            <td>{!! $procumentOrderDetail->GRVSelectedYN !!}</td>
            <td>{!! $procumentOrderDetail->goodsRecievedYN !!}</td>
            <td>{!! $procumentOrderDetail->logisticSelectedYN !!}</td>
            <td>{!! $procumentOrderDetail->logisticRecievedYN !!}</td>
            <td>{!! $procumentOrderDetail->isAccruedYN !!}</td>
            <td>{!! $procumentOrderDetail->accrualJVID !!}</td>
            <td>{!! $procumentOrderDetail->timesReferred !!}</td>
            <td>{!! $procumentOrderDetail->totalWHTAmount !!}</td>
            <td>{!! $procumentOrderDetail->WHTBearedBySupplier !!}</td>
            <td>{!! $procumentOrderDetail->WHTBearedByCompany !!}</td>
            <td>{!! $procumentOrderDetail->VATPercentage !!}</td>
            <td>{!! $procumentOrderDetail->VATAmount !!}</td>
            <td>{!! $procumentOrderDetail->VATAmountLocal !!}</td>
            <td>{!! $procumentOrderDetail->VATAmountRpt !!}</td>
            <td>{!! $procumentOrderDetail->createdUserGroup !!}</td>
            <td>{!! $procumentOrderDetail->createdPcID !!}</td>
            <td>{!! $procumentOrderDetail->createdUserID !!}</td>
            <td>{!! $procumentOrderDetail->modifiedPc !!}</td>
            <td>{!! $procumentOrderDetail->modifiedUser !!}</td>
            <td>{!! $procumentOrderDetail->createdDateTime !!}</td>
            <td>{!! $procumentOrderDetail->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['procumentOrderDetails.destroy', $procumentOrderDetail->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('procumentOrderDetails.show', [$procumentOrderDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('procumentOrderDetails.edit', [$procumentOrderDetail->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>