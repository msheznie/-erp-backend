<?php

namespace App\Repositories;

use App\Models\TicketMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TicketMasterRepository
 * @package App\Repositories
 * @version August 10, 2018, 8:32 am UTC
 *
 * @method TicketMaster findWithoutFail($id, $columns = ['*'])
 * @method TicketMaster find($id, $columns = ['*'])
 * @method TicketMaster first($columns = ['*'])
*/
class TicketMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ticketNo',
        'ticketMonth',
        'ticketYear',
        'contractRefNo',
        'regName',
        'regNo',
        'companyID',
        'clientID',
        'ticketCategory',
        'serviceLine',
        'fieldName',
        'fieldType',
        'wellNo',
        'wellType',
        'comments',
        'createdUserGroup',
        'createdPcID',
        'createdUserID',
        'modifiedPc',
        'modifiedUser',
        'createdDateTime',
        'timeStamp',
        'ticketStatus',
        'ticketStatusEmpID',
        'ticketStatusDate',
        'ticketStatusComment',
        'BillingStatus',
        'confirmedYN',
        'confirmedBy',
        'confrmedDate',
        'confirmedComment',
        'JobAcheived',
        'jobNetworkNo',
        'documentID',
        'serialNo',
        'primaryUnitAssetID',
        'jobSupervisor',
        'Temperature',
        'Depth',
        'timeBaseLeftLocation',
        'TimeDateArrive',
        'TimedateRigup',
        'Timedatejobstra',
        'Timedatejobend',
        'Timedateleaveloc',
        'Totalhourloac',
        'TotalOperatingHours',
        'jobScheduledYNBM',
        'jobScheduledEmpIDBM',
        'jobScheduledDateBM',
        'jobScheduledCommentBM',
        'jobStartedYNBM',
        'jobStartedEmpIDBM',
        'jobStartedDateBM',
        'jobStartedCommentBM',
        'jobEndYNSup',
        'jobEndEmpIDSup',
        'jobEndDateSup',
        'jobEndCommentSup',
        'ticketTypeMaster',
        'ticketType',
        'selectedBillingYN',
        'processSelectTemp',
        'estimatedServiceValue',
        'estimatedProductValue',
        'revenueYear',
        'revenueMonth',
        'ticketServiceValue',
        'ticketProductValue',
        'ticketNature',
        'ticketClientSerial',
        'companyComment',
        'clientComment',
        'opDept',
        'poNumber',
        'tempPerformaMasID',
        'tempPerformaCode',
        'cancelledYN',
        'ticketCancelledDesc',
        'EngID',
        'ticketManulNo',
        'contractUID',
        'oldNoUpdate',
        'JobFailure',
        'isFail',
        'customerRep',
        'companyRep',
        'customerRepContact',
        'country',
        'isWeb',
        'assginBaseManager',
        'assginSuperviser',
        'callout',
        'rigClosedDate',
        'serviceEntry',
        'submissionDate',
        'batchNo',
        'callOutDate',
        'sqauditCategoryID',
        'querySentYN',
        'querySentDate',
        'querySentBy',
        'financeApprovedYN',
        'financeApprovedDate',
        'financeApprovedBy',
        'isDeleted',
        'deletedBy',
        'deletedDate',
        'deletedComment',
        'jobDescID',
        'secondComments'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TicketMaster::class;
    }
}
