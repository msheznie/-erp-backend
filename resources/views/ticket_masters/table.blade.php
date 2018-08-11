<table class="table table-responsive" id="ticketMasters-table">
    <thead>
        <tr>
            <th>Ticketno</th>
        <th>Ticketmonth</th>
        <th>Ticketyear</th>
        <th>Contractrefno</th>
        <th>Regname</th>
        <th>Regno</th>
        <th>Companyid</th>
        <th>Clientid</th>
        <th>Ticketcategory</th>
        <th>Serviceline</th>
        <th>Fieldname</th>
        <th>Fieldtype</th>
        <th>Wellno</th>
        <th>Welltype</th>
        <th>Comments</th>
        <th>Createdusergroup</th>
        <th>Createdpcid</th>
        <th>Createduserid</th>
        <th>Modifiedpc</th>
        <th>Modifieduser</th>
        <th>Createddatetime</th>
        <th>Timestamp</th>
        <th>Ticketstatus</th>
        <th>Ticketstatusempid</th>
        <th>Ticketstatusdate</th>
        <th>Ticketstatuscomment</th>
        <th>Billingstatus</th>
        <th>Confirmedyn</th>
        <th>Confirmedby</th>
        <th>Confrmeddate</th>
        <th>Confirmedcomment</th>
        <th>Jobacheived</th>
        <th>Jobnetworkno</th>
        <th>Documentid</th>
        <th>Serialno</th>
        <th>Primaryunitassetid</th>
        <th>Jobsupervisor</th>
        <th>Temperature</th>
        <th>Depth</th>
        <th>Timebaseleftlocation</th>
        <th>Timedatearrive</th>
        <th>Timedaterigup</th>
        <th>Timedatejobstra</th>
        <th>Timedatejobend</th>
        <th>Timedateleaveloc</th>
        <th>Totalhourloac</th>
        <th>Totaloperatinghours</th>
        <th>Jobscheduledynbm</th>
        <th>Jobscheduledempidbm</th>
        <th>Jobscheduleddatebm</th>
        <th>Jobscheduledcommentbm</th>
        <th>Jobstartedynbm</th>
        <th>Jobstartedempidbm</th>
        <th>Jobstarteddatebm</th>
        <th>Jobstartedcommentbm</th>
        <th>Jobendynsup</th>
        <th>Jobendempidsup</th>
        <th>Jobenddatesup</th>
        <th>Jobendcommentsup</th>
        <th>Tickettypemaster</th>
        <th>Tickettype</th>
        <th>Selectedbillingyn</th>
        <th>Processselecttemp</th>
        <th>Estimatedservicevalue</th>
        <th>Estimatedproductvalue</th>
        <th>Revenueyear</th>
        <th>Revenuemonth</th>
        <th>Ticketservicevalue</th>
        <th>Ticketproductvalue</th>
        <th>Ticketnature</th>
        <th>Ticketclientserial</th>
        <th>Companycomment</th>
        <th>Clientcomment</th>
        <th>Opdept</th>
        <th>Ponumber</th>
        <th>Tempperformamasid</th>
        <th>Tempperformacode</th>
        <th>Cancelledyn</th>
        <th>Ticketcancelleddesc</th>
        <th>Engid</th>
        <th>Ticketmanulno</th>
        <th>Contractuid</th>
        <th>Oldnoupdate</th>
        <th>Jobfailure</th>
        <th>Isfail</th>
        <th>Customerrep</th>
        <th>Companyrep</th>
        <th>Customerrepcontact</th>
        <th>Country</th>
        <th>Isweb</th>
        <th>Assginbasemanager</th>
        <th>Assginsuperviser</th>
        <th>Callout</th>
        <th>Rigcloseddate</th>
        <th>Serviceentry</th>
        <th>Submissiondate</th>
        <th>Batchno</th>
        <th>Calloutdate</th>
        <th>Sqauditcategoryid</th>
        <th>Querysentyn</th>
        <th>Querysentdate</th>
        <th>Querysentby</th>
        <th>Financeapprovedyn</th>
        <th>Financeapproveddate</th>
        <th>Financeapprovedby</th>
        <th>Isdeleted</th>
        <th>Deletedby</th>
        <th>Deleteddate</th>
        <th>Deletedcomment</th>
        <th>Jobdescid</th>
        <th>Secondcomments</th>
            <th colspan="3">Action</th>
        </tr>
    </thead>
    <tbody>
    @foreach($ticketMasters as $ticketMaster)
        <tr>
            <td>{!! $ticketMaster->ticketNo !!}</td>
            <td>{!! $ticketMaster->ticketMonth !!}</td>
            <td>{!! $ticketMaster->ticketYear !!}</td>
            <td>{!! $ticketMaster->contractRefNo !!}</td>
            <td>{!! $ticketMaster->regName !!}</td>
            <td>{!! $ticketMaster->regNo !!}</td>
            <td>{!! $ticketMaster->companyID !!}</td>
            <td>{!! $ticketMaster->clientID !!}</td>
            <td>{!! $ticketMaster->ticketCategory !!}</td>
            <td>{!! $ticketMaster->serviceLine !!}</td>
            <td>{!! $ticketMaster->fieldName !!}</td>
            <td>{!! $ticketMaster->fieldType !!}</td>
            <td>{!! $ticketMaster->wellNo !!}</td>
            <td>{!! $ticketMaster->wellType !!}</td>
            <td>{!! $ticketMaster->comments !!}</td>
            <td>{!! $ticketMaster->createdUserGroup !!}</td>
            <td>{!! $ticketMaster->createdPcID !!}</td>
            <td>{!! $ticketMaster->createdUserID !!}</td>
            <td>{!! $ticketMaster->modifiedPc !!}</td>
            <td>{!! $ticketMaster->modifiedUser !!}</td>
            <td>{!! $ticketMaster->createdDateTime !!}</td>
            <td>{!! $ticketMaster->timeStamp !!}</td>
            <td>{!! $ticketMaster->ticketStatus !!}</td>
            <td>{!! $ticketMaster->ticketStatusEmpID !!}</td>
            <td>{!! $ticketMaster->ticketStatusDate !!}</td>
            <td>{!! $ticketMaster->ticketStatusComment !!}</td>
            <td>{!! $ticketMaster->BillingStatus !!}</td>
            <td>{!! $ticketMaster->confirmedYN !!}</td>
            <td>{!! $ticketMaster->confirmedBy !!}</td>
            <td>{!! $ticketMaster->confrmedDate !!}</td>
            <td>{!! $ticketMaster->confirmedComment !!}</td>
            <td>{!! $ticketMaster->JobAcheived !!}</td>
            <td>{!! $ticketMaster->jobNetworkNo !!}</td>
            <td>{!! $ticketMaster->documentID !!}</td>
            <td>{!! $ticketMaster->serialNo !!}</td>
            <td>{!! $ticketMaster->primaryUnitAssetID !!}</td>
            <td>{!! $ticketMaster->jobSupervisor !!}</td>
            <td>{!! $ticketMaster->Temperature !!}</td>
            <td>{!! $ticketMaster->Depth !!}</td>
            <td>{!! $ticketMaster->timeBaseLeftLocation !!}</td>
            <td>{!! $ticketMaster->TimeDateArrive !!}</td>
            <td>{!! $ticketMaster->TimedateRigup !!}</td>
            <td>{!! $ticketMaster->Timedatejobstra !!}</td>
            <td>{!! $ticketMaster->Timedatejobend !!}</td>
            <td>{!! $ticketMaster->Timedateleaveloc !!}</td>
            <td>{!! $ticketMaster->Totalhourloac !!}</td>
            <td>{!! $ticketMaster->TotalOperatingHours !!}</td>
            <td>{!! $ticketMaster->jobScheduledYNBM !!}</td>
            <td>{!! $ticketMaster->jobScheduledEmpIDBM !!}</td>
            <td>{!! $ticketMaster->jobScheduledDateBM !!}</td>
            <td>{!! $ticketMaster->jobScheduledCommentBM !!}</td>
            <td>{!! $ticketMaster->jobStartedYNBM !!}</td>
            <td>{!! $ticketMaster->jobStartedEmpIDBM !!}</td>
            <td>{!! $ticketMaster->jobStartedDateBM !!}</td>
            <td>{!! $ticketMaster->jobStartedCommentBM !!}</td>
            <td>{!! $ticketMaster->jobEndYNSup !!}</td>
            <td>{!! $ticketMaster->jobEndEmpIDSup !!}</td>
            <td>{!! $ticketMaster->jobEndDateSup !!}</td>
            <td>{!! $ticketMaster->jobEndCommentSup !!}</td>
            <td>{!! $ticketMaster->ticketTypeMaster !!}</td>
            <td>{!! $ticketMaster->ticketType !!}</td>
            <td>{!! $ticketMaster->selectedBillingYN !!}</td>
            <td>{!! $ticketMaster->processSelectTemp !!}</td>
            <td>{!! $ticketMaster->estimatedServiceValue !!}</td>
            <td>{!! $ticketMaster->estimatedProductValue !!}</td>
            <td>{!! $ticketMaster->revenueYear !!}</td>
            <td>{!! $ticketMaster->revenueMonth !!}</td>
            <td>{!! $ticketMaster->ticketServiceValue !!}</td>
            <td>{!! $ticketMaster->ticketProductValue !!}</td>
            <td>{!! $ticketMaster->ticketNature !!}</td>
            <td>{!! $ticketMaster->ticketClientSerial !!}</td>
            <td>{!! $ticketMaster->companyComment !!}</td>
            <td>{!! $ticketMaster->clientComment !!}</td>
            <td>{!! $ticketMaster->opDept !!}</td>
            <td>{!! $ticketMaster->poNumber !!}</td>
            <td>{!! $ticketMaster->tempPerformaMasID !!}</td>
            <td>{!! $ticketMaster->tempPerformaCode !!}</td>
            <td>{!! $ticketMaster->cancelledYN !!}</td>
            <td>{!! $ticketMaster->ticketCancelledDesc !!}</td>
            <td>{!! $ticketMaster->EngID !!}</td>
            <td>{!! $ticketMaster->ticketManulNo !!}</td>
            <td>{!! $ticketMaster->contractUID !!}</td>
            <td>{!! $ticketMaster->oldNoUpdate !!}</td>
            <td>{!! $ticketMaster->JobFailure !!}</td>
            <td>{!! $ticketMaster->isFail !!}</td>
            <td>{!! $ticketMaster->customerRep !!}</td>
            <td>{!! $ticketMaster->companyRep !!}</td>
            <td>{!! $ticketMaster->customerRepContact !!}</td>
            <td>{!! $ticketMaster->country !!}</td>
            <td>{!! $ticketMaster->isWeb !!}</td>
            <td>{!! $ticketMaster->assginBaseManager !!}</td>
            <td>{!! $ticketMaster->assginSuperviser !!}</td>
            <td>{!! $ticketMaster->callout !!}</td>
            <td>{!! $ticketMaster->rigClosedDate !!}</td>
            <td>{!! $ticketMaster->serviceEntry !!}</td>
            <td>{!! $ticketMaster->submissionDate !!}</td>
            <td>{!! $ticketMaster->batchNo !!}</td>
            <td>{!! $ticketMaster->callOutDate !!}</td>
            <td>{!! $ticketMaster->sqauditCategoryID !!}</td>
            <td>{!! $ticketMaster->querySentYN !!}</td>
            <td>{!! $ticketMaster->querySentDate !!}</td>
            <td>{!! $ticketMaster->querySentBy !!}</td>
            <td>{!! $ticketMaster->financeApprovedYN !!}</td>
            <td>{!! $ticketMaster->financeApprovedDate !!}</td>
            <td>{!! $ticketMaster->financeApprovedBy !!}</td>
            <td>{!! $ticketMaster->isDeleted !!}</td>
            <td>{!! $ticketMaster->deletedBy !!}</td>
            <td>{!! $ticketMaster->deletedDate !!}</td>
            <td>{!! $ticketMaster->deletedComment !!}</td>
            <td>{!! $ticketMaster->jobDescID !!}</td>
            <td>{!! $ticketMaster->secondComments !!}</td>
            <td>
                {!! Form::open(['route' => ['ticketMasters.destroy', $ticketMaster->id], 'method' => 'delete']) !!}
                <div class='btn-group'>
                    <a href="{!! route('ticketMasters.show', [$ticketMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-eye-open"></i></a>
                    <a href="{!! route('ticketMasters.edit', [$ticketMaster->id]) !!}" class='btn btn-default btn-xs'><i class="glyphicon glyphicon-edit"></i></a>
                    {!! Form::button('<i class="glyphicon glyphicon-trash"></i>', ['type' => 'submit', 'class' => 'btn btn-danger btn-xs', 'onclick' => "return confirm('Are you sure?')"]) !!}
                </div>
                {!! Form::close() !!}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>