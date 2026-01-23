<?php

namespace App\Repositories;

use App\Models\AssetDisposalType;
use App\Repositories\BaseRepository;

/**
 * Class AssetDisposalTypeRepository
 * @package App\Repositories
 * @version October 19, 2018, 4:15 am UTC
 *
 * @method AssetDisposalType findWithoutFail($id, $columns = ['*'])
 * @method AssetDisposalType find($id, $columns = ['*'])
 * @method AssetDisposalType first($columns = ['*'])
*/
class AssetDisposalTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'typeDescription'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return AssetDisposalType::class;
    }

    public function fetch_data($search){
        $data = $this->model
            ->with('chartofaccount:chartOfAccountSystemID,AccountCode,AccountDescription')
            ->where('activeYN', 1);

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $data = $data->where(function ($query) use ($search) {
                $query->where('typeDescription', 'LIKE', "%{$search}%");
                $query->orWhereHas('chartofaccount',function ($q) use($search){
                    $q->where('AccountCode', 'LIKE', "%{$search}%")
                        ->orWhere('AccountDescription', 'LIKE', "%{$search}%");
                });
            });
        }

        return $data;
    }
}
