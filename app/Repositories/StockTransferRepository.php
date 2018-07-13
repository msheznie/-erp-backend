<?php

namespace App\Repositories;

use App\Models\StockTransfer;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class StockTransferRepository
 * @package App\Repositories
 * @version July 13, 2018, 5:27 am UTC
 *
 * @method StockTransfer findWithoutFail($id, $columns = ['*'])
 * @method StockTransfer find($id, $columns = ['*'])
 * @method StockTransfer first($columns = ['*'])
*/
class StockTransferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companyID',
        'serviceLineCode',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'documentID',
        'serialNo',
        'stockTransferCode',
        'refNo',
        'tranferDate',
        'comment',
        'companyFrom',
        'companyTo',
        'locationTo',
        'locationFrom',
        'confirmedYN',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'postedDate',
        'fullyReceived',
        'timesReferred',
        'interCompanyTransferYN',
        'RollLevForApp_curr',
        'createdDateTime',
        'createdUserGroup',
        'createdPCID',
        'createdUserID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return StockTransfer::class;
    }
}
