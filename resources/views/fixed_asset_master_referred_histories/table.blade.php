<table class="table table-responsive" id="fixedAssetMasterReferredHistories-table">
    <thead>
        <tr>
            <th>Faid</th>
        <th>Departmentsystemid</th>
        <th>Departmentid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
        <th>Docorigindocumentsystemid</th>
        <th>Docorigindocumentid</th>
        <th>Docoriginsystemcode</th>
        <th>Docorigin</th>
        <th>Docorigindetailid</th>
        <th>Companysystemid</th>
        <th>Companyid</th>
        <th>Documentsystemid</th>
        <th>Documentid</th>
        <th>Faassetdept</th>
        <th>Serialno</th>
        <th>Itemcode</th>
        <th>Facode</th>
        <th>Assetcodes</th>
        <th>Faunitserialno</th>
        <th>Assetdescription</th>
        <th>Comments</th>
        <th>Groupto</th>
        <th>Dateaq</th>
        <th>Datedep</th>
        <th>Depmonth</th>
        <th>Deppercentage</th>
        <th>Facatid</th>
        <th>Fasubcatid</th>
        <th>Fasubcatid2</th>
        <th>Fasubcatid3</th>
        <th>Costunit</th>
        <th>Costunitrpt</th>
        <th>Auditcatogary</th>
        <th>Partnumber</th>
        <th>Manufacture</th>
        <th>Itempath</th>
        <th>Itempicture</th>
        <th>Image</th>
        <th>Unitassign</th>
        <th>Uhitasshistory</th>
        <th>Usedby</th>
        <th>Usebyhistry</th>
        <th>Location</th>
        <th>Currentlocation</th>
        <th>Locationhistory</th>
        <th>Selectedfordisposal</th>
        <th>Diposed</th>
        <th>Disposeddate</th>
        <th>Assetdisposalmasterautoid</th>
        <th>Resondispo</th>
        <th>Cashdisposal</th>
        <th>Costatdisp</th>
        <th>Accdepdip</th>
        <th>Profitlossdis</th>
        <th>Technical History</th>
        <th>Costglcodesystemid</th>
        <th>Costglcode</th>
        <th>Costglcodedes</th>
        <th>Accdepglcodesystemid</th>
        <th>Accdepglcode</th>
        <th>Accdepglcodedes</th>
        <th>Depglcodesystemid</th>
        <th>Depglcode</th>
        <th>Depglcodedes</th>
        <th>Dispglcodesystemid</th>
        <th>Dispoglcode</th>
        <th>Dispoglcodedes</th>
        <th>Rolllevforapp Curr</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Approvedbyuserid</th>
        <th>Approvedbyusersystemid</th>
        <th>Lastverifieddate</th>
        <th>Timesreferred</th>
        <th>Refferedbackyn</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifieduser</th>
        <th>Modifiedusersystemid</th>
        <th>Modifiedpc</th>
        <th>Createddateandtime</th>
        <th>Createddatetime</th>
        <th>Selectedyn</th>
        <th>Assettype</th>
        <th>Supplieridrentedasset</th>
        <th>Temprecord</th>
        <th>Toolscondition</th>
        <th>Selectedforjobyn</th>
        <th>Timestamp</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($fixedAssetMasterReferredHistories as $fixedAssetMasterReferredHistory)
        <tr>
            <td>{!! $fixedAssetMasterReferredHistory->faID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->departmentSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->departmentID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->serviceLineSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->serviceLineCode !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->docOriginDocumentSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->docOriginDocumentID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->docOriginSystemCode !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->docOrigin !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->docOriginDetailID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->companySystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->companyID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->documentSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->documentID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->faAssetDept !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->serialNo !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->itemCode !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->faCode !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->assetCodeS !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->faUnitSerialNo !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->assetDescription !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->COMMENTS !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->groupTO !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->dateAQ !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->dateDEP !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->depMonth !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->DEPpercentage !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->faCatID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->faSubCatID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->faSubCatID2 !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->faSubCatID3 !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->COSTUNIT !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->costUnitRpt !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->AUDITCATOGARY !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->PARTNUMBER !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->MANUFACTURE !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->itemPath !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->itemPicture !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->IMAGE !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->UNITASSIGN !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->UHITASSHISTORY !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->USEDBY !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->USEBYHISTRY !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->LOCATION !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->currentLocation !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->LOCATIONHISTORY !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->selectedForDisposal !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->DIPOSED !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->disposedDate !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->assetdisposalMasterAutoID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->RESONDISPO !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->CASHDISPOSAL !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->COSTATDISP !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->ACCDEPDIP !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->PROFITLOSSDIS !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->TECHNICAL_HISTORY !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->costglCodeSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->COSTGLCODE !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->COSTGLCODEdes !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->accdepglCodeSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->ACCDEPGLCODE !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->ACCDEPGLCODEdes !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->depglCodeSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->DEPGLCODE !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->DEPGLCODEdes !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->dispglCodeSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->DISPOGLCODE !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->DISPOGLCODEdes !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->RollLevForApp_curr !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->confirmedYN !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->confirmedByEmpSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->confirmedByEmpID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->confirmedDate !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->approved !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->approvedDate !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->approvedByUserID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->approvedByUserSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->lastVerifiedDate !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->timesReferred !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->refferedBackYN !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->createdUserGroup !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->createdUserSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->createdUserID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->createdPcID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->modifiedUser !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->modifiedUserSystemID !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->modifiedPc !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->createdDateAndTime !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->createdDateTime !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->selectedYN !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->assetType !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->supplierIDRentedAsset !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->tempRecord !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->toolsCondition !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->selectedforJobYN !!}</td>
            <td>{!! $fixedAssetMasterReferredHistory->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['fixedAssetMasterReferredHistories.destroy', $fixedAssetMasterReferredHistory->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fixedAssetMasterReferredHistories.show', [$fixedAssetMasterReferredHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fixedAssetMasterReferredHistories.edit', [$fixedAssetMasterReferredHistory->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>