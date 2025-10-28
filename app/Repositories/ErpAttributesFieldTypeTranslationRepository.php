<?php

namespace App\Repositories;

use App\Models\ErpAttributesFieldTypeTranslation;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ErpAttributesFieldTypeTranslationRepository
 * @package App\Repositories
 * @version October 16, 2025, 9:21 pm +04
 *
 * @method ErpAttributesFieldTypeTranslation findWithoutFail($id, $columns = ['*'])
 * @method ErpAttributesFieldTypeTranslation find($id, $columns = ['*'])
 * @method ErpAttributesFieldTypeTranslation first($columns = ['*'])
*/
class ErpAttributesFieldTypeTranslationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'fieldTypeId',
        'languageCode',
        'description'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ErpAttributesFieldTypeTranslation::class;
    }
}
