<?php

namespace App\Repositories;

use App\Models\ERPLanguageMaster;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ERPLanguageMasterRepository
 * @package App\Repositories
 * @version June 19, 2023, 11:21 am +04
 *
 * @method ERPLanguageMaster findWithoutFail($id, $columns = ['*'])
 * @method ERPLanguageMaster find($id, $columns = ['*'])
 * @method ERPLanguageMaster first($columns = ['*'])
*/
class ERPLanguageMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'systemDescription',
        'description',
        'languageShortCode',
        'languageSecShortCode',
        'isActive',
        'icon'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ERPLanguageMaster::class;
    }
}
