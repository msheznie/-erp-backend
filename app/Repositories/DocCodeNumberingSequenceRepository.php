<?php

namespace App\Repositories;

use App\Models\DocCodeNumberingSequence;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class DocCodeNumberingSequenceRepository
 * @package App\Repositories
 * @version January 30, 2025, 10:16 am +04
 *
 * @method DocCodeNumberingSequence findWithoutFail($id, $columns = ['*'])
 * @method DocCodeNumberingSequence find($id, $columns = ['*'])
 * @method DocCodeNumberingSequence first($columns = ['*'])
*/
class DocCodeNumberingSequenceRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'description',
        'is_active'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return DocCodeNumberingSequence::class;
    }
}
