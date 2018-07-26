<?php

namespace App\Repositories;

use App\Models\Designation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DesignationRepository
 * @package App\Repositories
 * @version July 26, 2018, 8:40 am UTC
 *
 * @method Designation findWithoutFail($id, $columns = ['*'])
 * @method Designation find($id, $columns = ['*'])
 * @method Designation first($columns = ['*'])
*/
class DesignationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'designation',
        'designation_O',
        'localName',
        'jobCode',
        'jobDecipline',
        'businessFunction',
        'appraisalTemplateID',
        'createdPCid',
        'createdUserID',
        'modifiedUser',
        'modifiedPc',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return Designation::class;
    }
}
