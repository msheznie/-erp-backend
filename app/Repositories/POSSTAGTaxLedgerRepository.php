<?php

namespace App\Repositories;

use App\Models\POSSTAGTaxLedger;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class POSSTAGTaxLedgerRepository
 * @package App\Repositories
 * @version August 9, 2022, 3:32 pm +04
 *
 * @method POSSTAGTaxLedger findWithoutFail($id, $columns = ['*'])
 * @method POSSTAGTaxLedger find($id, $columns = ['*'])
 * @method POSSTAGTaxLedger first($columns = ['*'])
*/
class POSSTAGTaxLedgerRepository extends BaseRepository
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
        return POSSTAGTaxLedger::class;
    }
}
