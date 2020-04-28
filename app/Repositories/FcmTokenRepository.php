<?php

namespace App\Repositories;

use App\Models\FcmToken;
use InfyOm\Generator\Common\BaseRepository;

/**
 * Class FcmTokenRepository
 * @package App\Repositories
 * @version April 28, 2020, 8:00 am +04
 *
 * @method FcmToken findWithoutFail($id, $columns = ['*'])
 * @method FcmToken find($id, $columns = ['*'])
 * @method FcmToken first($columns = ['*'])
*/
class FcmTokenRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'userID',
        'fcm_token'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return FcmToken::class;
    }
}
