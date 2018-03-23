<table class="table table-responsive" id="purchaseOrderDetails-table">
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
    @foreach($purchaseOrderDetails as $purchaseOrderDetails)
        <tr>
            <td>{!! $purchaseOrderDetails->companyID !!}</td>
            <td>{!! $purchaseOrderDetails->departmentID !!}</td>
            <td>{!! $purchaseOrderDetails->serviceLineCode !!}</td>
            <td>{!! $purchaseOrderDetails->purchaseOrderMasterID !!}</td>
            <td>{!! $purchaseOrderDetails->POProcessMasterID !!}</td>
            <td>{!! $purchaseOrderDetails->WO_purchaseOrderMasterID !!}</td>
            <td>{!! $purchaseOrderDetails->WP_purchaseOrderDetailsID !!}</td>
            <td>{!! $purchaseOrderDetails->itemCode !!}</td>
            <td>{!! $purchaseOrderDetails->itemPrimaryCode !!}</td>
            <td>{!! $purchaseOrderDetails->itemDescription !!}</td>
            <td>{!! $purchaseOrderDetails->itemFinanceCategoryID !!}</td>
            <td>{!! $purchaseOrderDetails->itemFinanceCategorySubID !!}</td>
            <td>{!! $purchaseOrderDetails->financeGLcodebBSSystemID !!}</td>
            <td>{!! $purchaseOrderDetails->financeGLcodebBS !!}</td>
            <td>{!! $purchaseOrderDetails->financeGLcodePLSystemID !!}</td>
            <td>{!! $purchaseOrderDetails->financeGLcodePL !!}</td>
            <td>{!! $purchaseOrderDetails->includePLForGRVYN !!}</td>
            <td>{!! $purchaseOrderDetails->supplierPartNumber !!}</td>
            <td>{!! $purchaseOrderDetails->unitOfMeasure !!}</td>
            <td>{!! $purchaseOrderDetails->itemClientReferenceNumberMasterID !!}</td>
            <td>{!! $purchaseOrderDetails->clientReferenceNumber !!}</td>
            <td>{!! $purchaseOrderDetails->noQty !!}</td>
            <td>{!! $purchaseOrderDetails->noOfDays !!}</td>
            <td>{!! $purchaseOrderDetails->unitCost !!}</td>
            <td>{!! $purchaseOrderDetails->discountPercentage !!}</td>
            <td>{!! $purchaseOrderDetails->discountAmount !!}</td>
            <td>{!! $purchaseOrderDetails->netAmount !!}</td>
            <td>{!! $purchaseOrderDetails->budgetYear !!}</td>
            <td>{!! $purchaseOrderDetails->prBelongsYear !!}</td>
            <td>{!! $purchaseOrderDetails->isAccrued !!}</td>
            <td>{!! $purchaseOrderDetails->budjetAmtLocal !!}</td>
            <td>{!! $purchaseOrderDetails->budjetAmtRpt !!}</td>
            <td>{!! $purchaseOrderDetails->comment !!}</td>
            <td>{!! $purchaseOrderDetails->supplierDefaultCurrencyID !!}</td>
            <td>{!! $purchaseOrderDetails->supplierDefaultER !!}</td>
            <td>{!! $purchaseOrderDetails->supplierItemCurrencyID !!}</td>
            <td>{!! $purchaseOrderDetails->foreignToLocalER !!}</td>
            <td>{!! $purchaseOrderDetails->companyReportingCurrencyID !!}</td>
            <td>{!! $purchaseOrderDetails->companyReportingER !!}</td>
            <td>{!! $purchaseOrderDetails->localCurrencyID !!}</td>
            <td>{!! $purchaseOrderDetails->localCurrencyER !!}</td>
            <td>{!! $purchaseOrderDetails->addonDistCost !!}</td>
            <td>{!! $purchaseOrderDetails->GRVcostPerUnitLocalCur !!}</td>
            <td>{!! $purchaseOrderDetails->GRVcostPerUnitSupDefaultCur !!}</td>
            <td>{!! $purchaseOrderDetails->GRVcostPerUnitSupTransCur !!}</td>
            <td>{!! $purchaseOrderDetails->GRVcostPerUnitComRptCur !!}</td>
            <td>{!! $purchaseOrderDetails->addonPurchaseReturnCost !!}</td>
            <td>{!! $purchaseOrderDetails->purchaseRetcostPerUnitLocalCur !!}</td>
            <td>{!! $purchaseOrderDetails->purchaseRetcostPerUniSupDefaultCur !!}</td>
            <td>{!! $purchaseOrderDetails->purchaseRetcostPerUnitTranCur !!}</td>
            <td>{!! $purchaseOrderDetails->purchaseRetcostPerUnitRptCur !!}</td>
            <td>{!! $purchaseOrderDetails->GRVSelectedYN !!}</td>
            <td>{!! $purchaseOrderDetails->goodsRecievedYN !!}</td>
            <td>{!! $purchaseOrderDetails->logisticSelectedYN !!}</td>
            <td>{!! $purchaseOrderDetails->logisticRecievedYN !!}</td>
            <td>{!! $purchaseOrderDetails->isAccruedYN !!}</td>
            <td>{!! $purchaseOrderDetails->accrualJVID !!}</td>
            <td>{!! $purchaseOrderDetails->timesReferred !!}</td>
            <td>{!! $purchaseOrderDetails->totalWHTAmount !!}</td>
            <td>{!! $purchaseOrderDetails->WHTBearedBySupplier !!}</td>
            <td>{!! $purchaseOrderDetails->WHTBearedByCompany !!}</td>
            <td>{!! $purchaseOrderDetails->VATPercentage !!}</td>
            <td>{!! $purchaseOrderDetails->VATAmount !!}</td>
            <td>{!! $purchaseOrderDetails->VATAmountLocal !!}</td>
            <td>{!! $purchaseOrderDetails->VATAmountRpt !!}</td>
            <td>{!! $purchaseOrderDetails->createdUserGroup !!}</td>
            <td>{!! $purchaseOrderDetails->createdPcID !!}</td>
            <td>{!! $purchaseOrderDetails->createdUserID !!}</td>
            <td>{!! $purchaseOrderDetails->modifiedPc !!}</td>
            <td>{!! $purchaseOrderDetails->modifiedUser !!}</td>
            <td>{!! $purchaseOrderDetails->createdDateTime !!}</td>
            <td>{!! $purchaseOrderDetails->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseOrderDetails.destroy', $purchaseOrderDetails->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseOrderDetails.show', [$purchaseOrderDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseOrderDetails.edit', [$purchaseOrderDetails->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>