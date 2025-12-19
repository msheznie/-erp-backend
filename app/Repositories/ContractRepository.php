<?php

namespace App\Repositories;

use App\Models\Contract;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ContractRepository
 * @package App\Repositories
 * @version August 1, 2018, 6:32 am UTC
 *
 * @method Contract findWithoutFail($id, $columns = ['*'])
 * @method Contract find($id, $columns = ['*'])
 * @method Contract first($columns = ['*'])
*/
class ContractRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'ContractNumber',
        'companySystemID',
        'CompanyID',
        'clientID',
        'CutomerCode',
        'ServiceLineCode',
        'contractDescription',
        'ContStartDate',
        'ContEndDate',
        'ContCurrencyID',
        'contValue',
        'isInitialExtCont',
        'ContExtUpto',
        'LineTechnicalIncharge',
        'LineFinanceIncharge',
        'LineOthersIncharge',
        'ContractCreatedON',
        'createdPcID',
        'createdUserGroup',
        'createdUserID',
        'createdDateTime',
        'modifiedPc',
        'modifiedUser',
        'allowMultipleBillingYN',
        'isContract',
        'allowRentalWithoutMITyn',
        'allowEditRentalDes',
        'defaultRateInRental',
        'allowEditUOM',
        'rentalTemplate',
        'contractType',
        'contractSubType',
        'bankID',
        'accountID',
        'vendonCode',
        'paymentInDaysForJob',
        'ticketClientSerialStart',
        'secondaryLogoComp',
        'secondaryLogName',
        'secondaryLogoActive',
        'estRevServiceGLcode',
        'estRevProductGLcode',
        'isFormulaApplicable',
        'opHrsRounding',
        'formulaOphrsFromField',
        'formulaOphrsToField',
        'formulaStandbyField',
        'isStandByApplicable',
        'customerRepName',
        'customerRepEmail',
        'showContDetInMOT',
        'showContDetInMIT',
        'performaTempID',
        'timeStamp',
        'contInvTemplate',
        'isAllowGenerateTransRental',
        'isAllowServiceEntryInPerforma',
        'isAllowedDefauldUsage',
        'actionTrackerEnabled',
        'webTemplate',
        'isRequiredStamp',
        'showSystemNo',
        'isAllowedToolsWithoutMOT',
        'isDispacthAvailable',
        'isRequireAppNewWell',
        'isMorningReportAvailable',
        'isContractActive',
        'allowMutipleTicketsInProforma',
        'isServiceEntryApplicable',
        'isTicketKPIApplicable',
        'isTicketTotalApplicable',
        'isMotAssetDescEditable',
        'motTemplate',
        'mitTemplate',
        'rentalDates',
        'invoiceTemplate',
        'rentalSheetTemplate',
        'isRequiredNetworkRefNo',
        'formulaLocHrsFromField',
        'formulaLocHrsToField',
        'isServiceApplicable',
        'isAllowToEditHours',
        'contractStatus',
        'ticketTemplates',
        'allowOpStdyDaysinMIT',
        'motprintTemplate'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Contract::class;
    }
}
