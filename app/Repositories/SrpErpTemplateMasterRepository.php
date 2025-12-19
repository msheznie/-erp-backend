<?php

namespace App\Repositories;

use App\Models\SrpErpTemplateMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class SrpErpTemplateMasterRepository
 * @package App\Repositories
 * @version September 3, 2021, 2:39 pm +04
 *
 * @method SrpErpTemplateMaster findWithoutFail($id, $columns = ['*'])
 * @method SrpErpTemplateMaster find($id, $columns = ['*'])
 * @method SrpErpTemplateMaster first($columns = ['*'])
*/
class SrpErpTemplateMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'TempDes',
        'TempPageName',
        'TempPageNameLink',
        'createPageLink',
        'FormCatID',
        'isReport',
        'isDefault',
        'documentCode',
        'templateKey'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SrpErpTemplateMaster::class;
    }
}
