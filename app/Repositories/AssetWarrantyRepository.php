<?php

namespace App\Repositories;

use App\Models\AssetWarranty;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class AssetWarrantyRepository
 * @package App\Repositories
 * @version July 12, 2023, 11:06 am +04
 *
 * @method AssetWarranty findWithoutFail($id, $columns = ['*'])
 * @method AssetWarranty find($id, $columns = ['*'])
 * @method AssetWarranty first($columns = ['*'])
*/
class AssetWarrantyRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'documentSystemCode',
        'warranty_provider',
        'start_date',
        'duration',
        'end_date',
        'warranty_coverage',
        'claim_process',
        'extended_warranty',
        'createdUserID',
        'createdUserSystemID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetWarranty::class;
    }
}
