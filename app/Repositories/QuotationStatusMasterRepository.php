<?php

namespace App\Repositories;

use App\Models\QuotationStatusMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class QuotationStatusMasterRepository
 * @package App\Repositories
 * @version July 14, 2020, 3:57 pm +04
 *
 * @method QuotationStatusMaster findWithoutFail($id, $columns = ['*'])
 * @method QuotationStatusMaster find($id, $columns = ['*'])
 * @method QuotationStatusMaster first($columns = ['*'])
*/
class QuotationStatusMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'quotationStatus',
        'isAdmin'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return QuotationStatusMaster::class;
    }
}
