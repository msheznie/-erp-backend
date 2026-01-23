<?php

namespace App\Repositories;

use App\Models\MobileBillSummary;
use App\Repositories\BaseRepository;

/**
 * Class MobileBillSummaryRepository
 * @package App\Repositories
 * @version July 12, 2020, 12:38 pm +04
 *
 * @method MobileBillSummary findWithoutFail($id, $columns = ['*'])
 * @method MobileBillSummary find($id, $columns = ['*'])
 * @method MobileBillSummary first($columns = ['*'])
*/
class MobileBillSummaryRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'mobileMasterID',
        'mobileNumber',
        'rental',
        'setUpFee',
        'localCharges',
        'internationalCallCharges',
        'domesticSMS',
        'internationalSMS',
        'domesticMMS',
        'internationalMMS',
        'discounts',
        'otherCharges',
        'blackberryCharges',
        'roamingCharges',
        'GPRSPayG',
        'GPRSPKG',
        'totalCurrentCharges',
        'billDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MobileBillSummary::class;
    }
}
