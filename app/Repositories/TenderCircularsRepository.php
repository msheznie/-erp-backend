<?php

namespace App\Repositories;

use App\Models\TenderCirculars;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderCircularsRepository
 * @package App\Repositories
 * @version July 6, 2022, 12:39 pm +04
 *
 * @method TenderCirculars findWithoutFail($id, $columns = ['*'])
 * @method TenderCirculars find($id, $columns = ['*'])
 * @method TenderCirculars first($columns = ['*'])
*/
class TenderCircularsRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'circular_name',
        'description',
        'attachment_id',
        'status',
        'created_by',
        'updated_by',
        'deleted_by',
        'company_id'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderCirculars::class;
    }
}
