<?php

namespace App\Repositories;

use App\Models\AssetRequest;
use App\Repositories\BaseRepository;

/**
 * Class AssetRequestRepository
 * @package App\Repositories
 * @version July 5, 2021, 2:51 pm +04
 *
 * @method AssetRequest findWithoutFail($id, $columns = ['*'])
 * @method AssetRequest find($id, $columns = ['*'])
 * @method AssetRequest first($columns = ['*'])
*/
class AssetRequestRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'document_id',
        'document_code',
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
        'created_user_id',  
        'departmentSystemID',
        'type'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetRequest::class;
    }
}
