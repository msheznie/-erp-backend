<?php

namespace App\Repositories;

use App\Models\TemplatesGLCode;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class TemplatesGLCodeRepository
 * @package App\Repositories
 * @version October 17, 2018, 5:24 am UTC
 *
 * @method TemplatesGLCode findWithoutFail($id, $columns = ['*'])
 * @method TemplatesGLCode find($id, $columns = ['*'])
 * @method TemplatesGLCode first($columns = ['*'])
*/
class TemplatesGLCodeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'templateMasterID',
        'templatesDetailsAutoID',
        'chartOfAccountSystemID',
        'glCode',
        'glDescription',
        'timestamp',
        'erp_templatesglcodecol'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TemplatesGLCode::class;
    }
}
