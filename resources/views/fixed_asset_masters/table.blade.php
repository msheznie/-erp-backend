<table class="table table-responsive" id="fixedAssetMasters-table">
    <thead>
        <tr>
            <th>Departmentsystemid</th>
        <th>Departmentid</th>
        <th>Servicelinesystemid</th>
        <th>Servicelinecode</th>
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
        <th>Costglcode</th>
        <th>Costglcodedes</th>
        <th>Accdepglcode</th>
        <th>Accdepglcodedes</th>
        <th>Depglcode</th>
        <th>Depglcodedes</th>
        <th>Dispoglcode</th>
        <th>Dispoglcodedes</th>
        <th>Confirmedyn</th>
        <th>Confirmedbyempsystemid</th>
        <th>Confirmedbyempid</th>
        <th>Confirmeddate</th>
        <th>Approved</th>
        <th>Approveddate</th>
        <th>Lastverifieddate</th>
        <th>Createdusergroup</th>
        <th>Createdusersystemid</th>
        <th>Createduserid</th>
        <th>Createdpcid</th>
        <th>Modifieduser</th>
        <th>Modifiedpc</th>
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
    @foreach($fixedAssetMasters as $fixedAssetMaster)
        <tr>
            <td>{!! $fixedAssetMaster->departmentSystemID !!}</td>
            <td>{!! $fixedAssetMaster->departmentID !!}</td>
            <td>{!! $fixedAssetMaster->serviceLineSystemID !!}</td>
            <td>{!! $fixedAssetMaster->serviceLineCode !!}</td>
            <td>{!! $fixedAssetMaster->docOriginSystemCode !!}</td>
            <td>{!! $fixedAssetMaster->docOrigin !!}</td>
            <td>{!! $fixedAssetMaster->docOriginDetailID !!}</td>
            <td>{!! $fixedAssetMaster->companySystemID !!}</td>
            <td>{!! $fixedAssetMaster->companyID !!}</td>
            <td>{!! $fixedAssetMaster->documentSystemID !!}</td>
            <td>{!! $fixedAssetMaster->documentID !!}</td>
            <td>{!! $fixedAssetMaster->faAssetDept !!}</td>
            <td>{!! $fixedAssetMaster->serialNo !!}</td>
            <td>{!! $fixedAssetMaster->itemCode !!}</td>
            <td>{!! $fixedAssetMaster->faCode !!}</td>
            <td>{!! $fixedAssetMaster->assetCodeS !!}</td>
            <td>{!! $fixedAssetMaster->faUnitSerialNo !!}</td>
            <td>{!! $fixedAssetMaster->assetDescription !!}</td>
            <td>{!! $fixedAssetMaster->COMMENTS !!}</td>
            <td>{!! $fixedAssetMaster->groupTO !!}</td>
            <td>{!! $fixedAssetMaster->dateAQ !!}</td>
            <td>{!! $fixedAssetMaster->dateDEP !!}</td>
            <td>{!! $fixedAssetMaster->depMonth !!}</td>
            <td>{!! $fixedAssetMaster->DEPpercentage !!}</td>
            <td>{!! $fixedAssetMaster->faCatID !!}</td>
            <td>{!! $fixedAssetMaster->faSubCatID !!}</td>
            <td>{!! $fixedAssetMaster->faSubCatID2 !!}</td>
            <td>{!! $fixedAssetMaster->faSubCatID3 !!}</td>
            <td>{!! $fixedAssetMaster->COSTUNIT !!}</td>
            <td>{!! $fixedAssetMaster->costUnitRpt !!}</td>
            <td>{!! $fixedAssetMaster->AUDITCATOGARY !!}</td>
            <td>{!! $fixedAssetMaster->PARTNUMBER !!}</td>
            <td>{!! $fixedAssetMaster->MANUFACTURE !!}</td>
            <td>{!! $fixedAssetMaster->IMAGE !!}</td>
            <td>{!! $fixedAssetMaster->UNITASSIGN !!}</td>
            <td>{!! $fixedAssetMaster->UHITASSHISTORY !!}</td>
            <td>{!! $fixedAssetMaster->USEDBY !!}</td>
            <td>{!! $fixedAssetMaster->USEBYHISTRY !!}</td>
            <td>{!! $fixedAssetMaster->LOCATION !!}</td>
            <td>{!! $fixedAssetMaster->currentLocation !!}</td>
            <td>{!! $fixedAssetMaster->LOCATIONHISTORY !!}</td>
            <td>{!! $fixedAssetMaster->selectedForDisposal !!}</td>
            <td>{!! $fixedAssetMaster->DIPOSED !!}</td>
            <td>{!! $fixedAssetMaster->disposedDate !!}</td>
            <td>{!! $fixedAssetMaster->assetdisposalMasterAutoID !!}</td>
            <td>{!! $fixedAssetMaster->RESONDISPO !!}</td>
            <td>{!! $fixedAssetMaster->CASHDISPOSAL !!}</td>
            <td>{!! $fixedAssetMaster->COSTATDISP !!}</td>
            <td>{!! $fixedAssetMaster->ACCDEPDIP !!}</td>
            <td>{!! $fixedAssetMaster->PROFITLOSSDIS !!}</td>
            <td>{!! $fixedAssetMaster->TECHNICAL_HISTORY !!}</td>
            <td>{!! $fixedAssetMaster->COSTGLCODE !!}</td>
            <td>{!! $fixedAssetMaster->COSTGLCODEdes !!}</td>
            <td>{!! $fixedAssetMaster->ACCDEPGLCODE !!}</td>
            <td>{!! $fixedAssetMaster->ACCDEPGLCODEdes !!}</td>
            <td>{!! $fixedAssetMaster->DEPGLCODE !!}</td>
            <td>{!! $fixedAssetMaster->DEPGLCODEdes !!}</td>
            <td>{!! $fixedAssetMaster->DISPOGLCODE !!}</td>
            <td>{!! $fixedAssetMaster->DISPOGLCODEdes !!}</td>
            <td>{!! $fixedAssetMaster->confirmedYN !!}</td>
            <td>{!! $fixedAssetMaster->confirmedByEmpSystemID !!}</td>
            <td>{!! $fixedAssetMaster->confirmedByEmpID !!}</td>
            <td>{!! $fixedAssetMaster->confirmedDate !!}</td>
            <td>{!! $fixedAssetMaster->approved !!}</td>
            <td>{!! $fixedAssetMaster->approvedDate !!}</td>
            <td>{!! $fixedAssetMaster->lastVerifiedDate !!}</td>
            <td>{!! $fixedAssetMaster->createdUserGroup !!}</td>
            <td>{!! $fixedAssetMaster->createdUserSystemID !!}</td>
            <td>{!! $fixedAssetMaster->createdUserID !!}</td>
            <td>{!! $fixedAssetMaster->createdPcID !!}</td>
            <td>{!! $fixedAssetMaster->modifiedUser !!}</td>
            <td>{!! $fixedAssetMaster->modifiedPc !!}</td>
            <td>{!! $fixedAssetMaster->createdDateTime !!}</td>
            <td>{!! $fixedAssetMaster->selectedYN !!}</td>
            <td>{!! $fixedAssetMaster->assetType !!}</td>
            <td>{!! $fixedAssetMaster->supplierIDRentedAsset !!}</td>
            <td>{!! $fixedAssetMaster->tempRecord !!}</td>
            <td>{!! $fixedAssetMaster->toolsCondition !!}</td>
            <td>{!! $fixedAssetMaster->selectedforJobYN !!}</td>
            <td>{!! $fixedAssetMaster->timestamp !!}</td>
            <td>
                {!! Form::open(['route' => ['fixedAssetMasters.destroy', $fixedAssetMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('fixedAssetMasters.show', [$fixedAssetMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('fixedAssetMasters.edit', [$fixedAssetMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>