<?php

namespace App\Repositories;

use App\Models\TenderBidFormatMaster;
use App\Repositories\BaseRepository;

/**
 * Class TenderBidFormatMasterRepository
 * @package App\Repositories
 * @version March 4, 2022, 10:32 am +04
 *
 * @method TenderBidFormatMaster findWithoutFail($id, $columns = ['*'])
 * @method TenderBidFormatMaster find($id, $columns = ['*'])
 * @method TenderBidFormatMaster first($columns = ['*'])
*/
class TenderBidFormatMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_name',
        'boq_applicable',
        'company_id',
        'created_by',
        'updated_by',
        'deleted_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderBidFormatMaster::class;
    }
}
