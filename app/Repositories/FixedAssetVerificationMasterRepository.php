<?php

namespace App\Repositories;

use App\Models\FixedAssetVerification;
use App\Repositories\BaseRepository;

/**
 * Class FixedAssetVerificationMasterRepository
 *
 * @package \App\Repositories
 */
class FixedAssetVerificationMasterRepository extends BaseRepository
{

    public function model()
    {
        return FixedAssetVerification::class;
    }


}
