<?php

namespace App\Repositories;

use App\Models\MolContribution;
use App\Repositories\BaseRepository;

/**
 * Class MolContributionRepository
 * @package App\Repositories
 * @version November 24, 2025, 8:03 am +04
 *
 * @method MolContribution findWithoutFail($id, $columns = ['*'])
 * @method MolContribution find($id, $columns = ['*'])
 * @method MolContribution first($columns = ['*'])
*/
class MolContributionRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'authority_id',
        'company_id',
        'contribution_type',
        'description',
        'mol_calculation_type_id',
        'mol_expense_gl_account_id',
        'mol_percentage',
        'status'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return MolContribution::class;
    }
}
