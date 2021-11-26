<?php

namespace App\Repositories;

use App\Models\ExpenseAssetAllocation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ExpenseAssetAllocationRepository
 * @package App\Repositories
 * @version November 15, 2021, 1:17 pm +04
 *
 * @method ExpenseAssetAllocation findWithoutFail($id, $columns = ['*'])
 * @method ExpenseAssetAllocation find($id, $columns = ['*'])
 * @method ExpenseAssetAllocation first($columns = ['*'])
*/
class ExpenseAssetAllocationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'assetID',
        'documentSystemID',
        'documentSystemCode',
        'amount'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ExpenseAssetAllocation::class;
    }

    public function deleteExpenseAssetAllocation($documentSystemCode, $documentSystemID)
    {
        $res = ExpenseAssetAllocation::where('documentSystemCode', $documentSystemCode)
                                     ->where('documentSystemID', $documentSystemID)
                                     ->delete();

        return ['status' => true, 'message' => "Asset allocation deleted succssfully"];
    }
}
