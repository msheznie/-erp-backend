<?php

namespace App\Repositories;

use App\Models\ERPAssetTransfer;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ERPAssetTransferRepository
 * @package App\Repositories
 * @version July 15, 2021, 5:24 am +04
 *
 * @method ERPAssetTransfer findWithoutFail($id, $columns = ['*'])
 * @method ERPAssetTransfer find($id, $columns = ['*'])
 * @method ERPAssetTransfer first($columns = ['*'])
*/
class ERPAssetTransferRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_id',
        'document_code',
        'type',
        'reference_no',
        'document_date',
        'approval_comments',
        'serial_no',
        'emp_id',
        'narration',
        'company_id',
        'confirmed_yn',
        'confirmed_by_emp_id',
        'confirmed_by_name',
        'confirmed_date',
        'approved_yn',
        'approved_date',
        'approved_by_emp_name',
        'approved_by_emp_id',
        'current_level_no',
        'created_user_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ERPAssetTransfer::class;
    }
}
