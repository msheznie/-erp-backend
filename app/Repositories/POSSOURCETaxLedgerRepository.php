<?php

namespace App\Repositories;

use App\Models\POSSOURCETaxLedger;
use App\Repositories\BaseRepository;

/**
 * Class POSSOURCETaxLedgerRepository
 * @package App\Repositories
 * @version August 9, 2022, 3:32 pm +04
 *
 * @method POSSOURCETaxLedger findWithoutFail($id, $columns = ['*'])
 * @method POSSOURCETaxLedger find($id, $columns = ['*'])
 * @method POSSOURCETaxLedger first($columns = ['*'])
*/
class POSSOURCETaxLedgerRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'amount',
        'companyCode',
        'companyID',
        'countryID',
        'createdDateTime',
        'createdPCID',
        'createdUserGroup',
        'createdUserID',
        'createdUserName',
        'documentDetailAutoID',
        'documentID',
        'documentMasterAutoID',
        'formula',
        'isClaimable',
        'isClaimed',
        'ismanuallychanged',
        'isSync',
        'locationID',
        'locationType',
        'modifiedDateTime',
        'modifiedPCID',
        'modifiedUserID',
        'modifiedUserName',
        'outputVatGL',
        'outputVatTransferGL',
        'partyID',
        'partyVATEligibleYN',
        'taxDetailAutoID',
        'taxFormulaDetailID',
        'taxFormulaMasterID',
        'taxGlAutoID',
        'taxMasterID',
        'taxPercentage',
        'timestamp',
        'transaction_log_id',
        'transferGLAutoID',
        'vatTypeID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return POSSOURCETaxLedger::class;
    }
}
