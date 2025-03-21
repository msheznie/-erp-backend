<?php

namespace App\Repositories;

use App\Models\BankStatementDetail;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class BankStatementDetailRepository
 * @package App\Repositories
 * @version February 4, 2025, 8:35 am +04
 *
 * @method BankStatementDetail findWithoutFail($id, $columns = ['*'])
 * @method BankStatementDetail find($id, $columns = ['*'])
 * @method BankStatementDetail first($columns = ['*'])
*/
class BankStatementDetailRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'statementId',
        'transactionNumber',
        'transactionDate',
        'debit',
        'credit',
        'description',
        'category',
        'createdDateTime',
        'timeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankStatementDetail::class;
    }
}
