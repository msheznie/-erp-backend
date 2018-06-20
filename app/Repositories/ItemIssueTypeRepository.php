<?php

namespace App\Repositories;

use App\Models\ItemIssueType;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class ItemIssueTypeRepository
 * @package App\Repositories
 * @version June 20, 2018, 4:24 am UTC
 *
 * @method ItemIssueType findWithoutFail($id, $columns = ['*'])
 * @method ItemIssueType find($id, $columns = ['*'])
 * @method ItemIssueType first($columns = ['*'])
*/
class ItemIssueTypeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'issueTypeDes'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return ItemIssueType::class;
    }
}
