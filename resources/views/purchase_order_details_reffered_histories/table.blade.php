<table class="table table-responsive" id="purchaseOrderDetailsRefferedHistories-table">
    <thead>
        <tr>
            <th>Purchaseorderdetailsid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Departmentid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Purchaseordermasterid</th>
        <th>Purchaseprocessdetailid</th>
        <th>Poprocessmasterid</th>
        <th>Wo Purchaseordermasterid</th>
        <th>Wp Purchaseorderdetailsid</th>
        <th>Purchaserequestdetailsid</th>
        <th>Purchaserequestid</th>
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
        <th>Requestedqty</th>
        <th>Noqty</th>
        <th>Balanceqty</th>
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
        <th>Receivedqty</th>
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
        <th>Manuallyclosed</th>
        <th>Manuallyclosedbyempsystemid</th>
        <th>Manuallyclosedbyempid</th>
        <th>Manuallyclosedbyempname</th>
        <th>Manuallycloseddate</th>
        <th>Manuallyclosedcomment</th>
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
    @foreach($purchaseOrderDetailsRefferedHistories as $purchaseOrderDetailsRefferedHistory)
        <tr>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseOrderDetailsID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->companySystemID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->companyID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->departmentID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->serviceLineSystemID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->serviceLineCode !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseOrderMasterID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseProcessDetailID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->POProcessMasterID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->WO_purchaseOrderMasterID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->WP_purchaseOrderDetailsID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseRequestDetailsID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseRequestID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->itemCode !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->itemPrimaryCode !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->itemDescription !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->itemFinanceCategoryID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->itemFinanceCategorySubID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->financeGLcodebBSSystemID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->financeGLcodebBS !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->financeGLcodePLSystemID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->financeGLcodePL !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->includePLForGRVYN !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->supplierPartNumber !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->unitOfMeasure !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->itemClientReferenceNumberMasterID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->clientReferenceNumber !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->requestedQty !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->noQty !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->balanceQty !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->noOfDays !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->unitCost !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->discountPercentage !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->discountAmount !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->netAmount !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->budgetYear !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->prBelongsYear !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->isAccrued !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->budjetAmtLocal !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->budjetAmtRpt !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->comment !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->supplierDefaultCurrencyID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->supplierDefaultER !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->supplierItemCurrencyID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->foreignToLocalER !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->companyReportingCurrencyID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->companyReportingER !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->localCurrencyID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->localCurrencyER !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->addonDistCost !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->GRVcostPerUnitLocalCur !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->GRVcostPerUnitSupDefaultCur !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->GRVcostPerUnitSupTransCur !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->GRVcostPerUnitComRptCur !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->addonPurchaseReturnCost !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseRetcostPerUnitLocalCur !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseRetcostPerUniSupDefaultCur !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseRetcostPerUnitTranCur !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->purchaseRetcostPerUnitRptCur !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->receivedQty !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->GRVSelectedYN !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->goodsRecievedYN !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->logisticSelectedYN !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->logisticRecievedYN !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->isAccruedYN !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->accrualJVID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->timesReferred !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->totalWHTAmount !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->WHTBearedBySupplier !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->WHTBearedByCompany !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->VATPercentage !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->VATAmount !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->VATAmountLocal !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->VATAmountRpt !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->manuallyClosed !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->manuallyClosedByEmpSystemID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->manuallyClosedByEmpID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->manuallyClosedByEmpName !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->manuallyClosedDate !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->manuallyClosedComment !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->createdUserGroup !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->createdPcID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->createdUserID !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->modifiedPc !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->modifiedUser !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->createdDateTime !!}</td>
            <td>{!! $purchaseOrderDetailsRefferedHistory->timeStamp !!}</td>
            <td>
                {!! Form::open(['route' => ['purchaseOrderDetailsRefferedHistories.destroy', $purchaseOrderDetailsRefferedHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('purchaseOrderDetailsRefferedHistories.show', [$purchaseOrderDetailsRefferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('purchaseOrderDetailsRefferedHistories.edit', [$purchaseOrderDetailsRefferedHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>