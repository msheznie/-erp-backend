<?php

namespace App\Repositories;

use App\Models\SrmTenderPo;
use Illuminate\Support\Facades\Log;

class SrmTenderPoRepository
{

    protected $model;

    public function __construct(SrmTenderPo $model)
    {
        $this->model = $model;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $updated = SrmTenderPo::where('po_id', $id)->update($data);

        if ($updated) {
            return true;
        }

        return null;
    }

}
