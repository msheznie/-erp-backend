<?php

namespace App\Repositories;

use App\Models\BankMaster;
use App\Repositories\BaseRepository;
use Illuminate\Support\Facades\DB;

/**
 * Class BankMasterRepository
 * @package App\Repositories
 * @version March 21, 2018, 5:24 am UTC
 *
 * @method BankMaster findWithoutFail($id, $columns = ['*'])
 * @method BankMaster find($id, $columns = ['*'])
 * @method BankMaster first($columns = ['*'])
*/
class BankMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'bankShortCode',
        'bankName',
        'createdDateTime',
        'createdByEmpID',
        'TimeStamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return BankMaster::class;
    }


    public function findExistingBankShortCodes(array $bankShortCodes)
    {
        if (empty($bankShortCodes)) {
            return collect();
        }

        $normalized = array_values(array_unique(array_map(function ($code) {
            return strtolower(trim((string) $code));
        }, $bankShortCodes)));

        $placeholders = implode(',', array_fill(0, count($normalized), '?'));

        return BankMaster::whereRaw('LOWER(bankShortCode) IN (' . $placeholders . ')', $normalized)
            ->pluck('bankShortCode');
    }
}
