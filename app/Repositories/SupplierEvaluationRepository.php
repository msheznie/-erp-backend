<?php

namespace App\Repositories;

use App\Models\SupplierEvaluation;
use InfyOm\Generator\Common\BaseRepository;
use Illuminate\Support\Facades\DB;

class SupplierEvaluationRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'evaluationSerialNo',
        'evaluationCode',
        'evaluationType',
        'evaluationTemplate',
        'documentCode',
        'documentId',
        'documentSystemCode',
        'supplierId',
        'supplierCode',
        'supplierName',
        'companySystemID',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return SupplierEvaluation::class;
    }
    public function supplierEvaluationListQuery($request, $input, $search = '', $supplier, $evaluationTemplate)
    {
        $supplierEvaluation = SupplierEvaluation::with(['createdBy', 'templateMaster'])->where('companySystemID',$input['companyID']);

        if (array_key_exists('evaluationType', $input)) {
            $supplierEvaluation->where('evaluationType', $input['evaluationType']);
        }

        if (array_key_exists('evaluationDate', $input)) {
            if (!is_null($input['evaluationDate'])) {
                $supplierEvaluation->where(DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d")'), "{$input['evaluationDate']}");
            }
        }

        if (array_key_exists('supplierID', $input)) {
            if ($input['supplierID'] && !is_null($supplier)) {
                $supplierEvaluation->where('supplierId', $supplier);
            }
        }

        if (array_key_exists('evaluationTemplate', $input)) {
            if ($input['evaluationTemplate'] && !is_null($evaluationTemplate)) {
                $supplierEvaluation->where('evaluationTemplate', $evaluationTemplate);
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $supplierEvaluation = $supplierEvaluation->where(function ($query) use ($search) {
                $query->where('evaluationCode', 'LIKE', "%{$search}%")
                    ->orWhere('documentSystemCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }
        return $supplierEvaluation;
    }
}
