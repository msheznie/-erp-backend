<?php

namespace App\Repositories;

use App\Models\InterCompanyAssetDisposal;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class InterCompanyAssetDisposalRepository
 * @package App\Repositories
 * @version February 15, 2022, 11:01 am +04
 *
 * @method InterCompanyAssetDisposal findWithoutFail($id, $columns = ['*'])
 * @method InterCompanyAssetDisposal find($id, $columns = ['*'])
 * @method InterCompanyAssetDisposal first($columns = ['*'])
*/
class InterCompanyAssetDisposalRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'assetDisposalID',
        'customerInvoiceID',
        'grvID'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return InterCompanyAssetDisposal::class;
    }
}
