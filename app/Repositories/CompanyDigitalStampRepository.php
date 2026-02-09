<?php

namespace App\Repositories;

use App\Models\CompanyDigitalStamp;
use App\Repositories\BaseRepository;

/**
 * Class CompanyDigitalStampRepository
 * @package App\Repositories
 * @version November 16, 2021, 10:42 am +04
 *
 * @method CompanyDigitalStamp findWithoutFail($id, $columns = ['*'])
 * @method CompanyDigitalStamp find($id, $columns = ['*'])
 * @method CompanyDigitalStamp first($columns = ['*'])
*/
class CompanyDigitalStampRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'path',
        'company_system_id',
        'is_default',
        'created_by',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return CompanyDigitalStamp::class;
    }
}
