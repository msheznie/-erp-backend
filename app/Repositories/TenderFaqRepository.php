<?php

namespace App\Repositories;

use App\Models\TenderFaq;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TenderFaqRepository
 * @package App\Repositories
 * @version April 11, 2022, 11:12 am +04
 *
 * @method TenderFaq findWithoutFail($id, $columns = ['*'])
 * @method TenderFaq find($id, $columns = ['*'])
 * @method TenderFaq first($columns = ['*'])
*/
class TenderFaqRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'answer',
        'company_id',
        'created_by',
        'question',
        'tender_master_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderFaq::class;
    }
}
