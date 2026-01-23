<?php

namespace App\Repositories;

use App\Models\QuotationStatus;
use App\Repositories\BaseRepository;

/**
 * Class QuotationStatusRepository
 * @package App\Repositories
 * @version July 15, 2020, 8:11 am +04
 *
 * @method QuotationStatus findWithoutFail($id, $columns = ['*'])
 * @method QuotationStatus find($id, $columns = ['*'])
 * @method QuotationStatus first($columns = ['*'])
*/
class QuotationStatusRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'quotationID',
        'quotationStatusMasterID',
        'companySystemID',
        'quotationStatusDate',
        'comments',
        'createdDateTime',
        'createdUserSystemID',
        'modifiedUserSystemID',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return QuotationStatus::class;
    }
}
