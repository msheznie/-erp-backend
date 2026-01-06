<?php

namespace App\Repositories;

use App\Models\SrmPOAcknowledgement;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrmPOAcknowledgementRepository
 * @package App\Repositories
 * @version December 5, 2025, 12:43 pm +04
 *
 * @method SrmPOAcknowledgement findWithoutFail($id, $columns = ['*'])
 * @method SrmPOAcknowledgement find($id, $columns = ['*'])
 * @method SrmPOAcknowledgement first($columns = ['*'])
*/
class SrmPOAcknowledgementRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'comment',
        'created_by',
        'po_id',
        'supplier_id',
        'updated_by'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrmPOAcknowledgement::class;
    }
}
