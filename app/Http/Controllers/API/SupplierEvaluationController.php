<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\EvaluationTemplateSection;
use App\Models\GRVMaster;
use App\Models\SupplierEvaluationTableDetails;
use App\Models\SupplierEvaluationTemplate;
use App\Models\SupplierEvaluationTemplateComment;
use App\Models\SupplierEvaluationTemplateSectionTable;
use App\Models\TemplateSectionTableRow;
use Illuminate\Http\Request;
use App\Repositories\SupplierEvaluationRepository;
use App\Http\Requests\API\CreateSupplierEvaluationAPIRequest;
use App\Models\SupplierEvaluation;

class SupplierEvaluationController extends AppBaseController
{
    private $SupplierEvaluationRepository;

    public function __construct(SupplierEvaluationRepository $SupplierEvaluationRepository)
    {
        $this->SupplierEvaluationRepository = $SupplierEvaluationRepository;
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($request)
    {
        $this->SupplierEvaluationRepository->pushCriteria(new RequestCriteria($request));
        $this->SupplierEvaluationRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierEvaluation = $this->SupplierEvaluationRepository->all();

        return $this->sendResponse($supplierEvaluation->toArray(), 'Supplier Evaluation retrieved successfully');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreateSupplierEvaluationAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $input['created_by'] = Helper::getEmployeeSystemID();

        $grvMaster = GRVMaster::where('grvAutoID', $input['documentId'])->first();
        if ($grvMaster) {
            $input['documentSystemCode'] = $grvMaster->grvPrimaryCode;
        }

        $lastSerial = SupplierEvaluation::where('companySystemID', $input['companySystemID'])
                            ->orderBy('evaluationSerialNo', 'desc')
                            ->first();
        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->evaluationSerialNo) + 1;
        }
        $input['evaluationSerialNo'] = $lastSerialNumber;
        $input['evaluationCode'] = 'SE' . '\\' . $input['supplierCode'] . '\\' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT);

        $supplierEvaluation = $this->SupplierEvaluationRepository->create($input);
        /** Create detail table row */
        $tableIds = SupplierEvaluationTemplateSectionTable::where('supplier_evaluation_template_id', $supplierEvaluation->evaluationTemplate)
            ->where('isConfirmed', 1)
            ->pluck('id')->toArray();

        if($tableIds) {
            $tableDetails = TemplateSectionTableRow::whereIn('table_id', $tableIds)->get();
            $insertTableRow = [];
            foreach ($tableDetails as $tableDetail) {
                $insertTableRow[] = [
                    'evaluationId' => $supplierEvaluation->id,
                    'tableId' => $tableDetail->table_id,
                    'rowData' =>  json_encode($tableDetail->rowData),
                    'createdBy' => $input['created_by']
                ];
            }
            if (!empty($insertTableRow)) {
                SupplierEvaluationTableDetails::insert($insertTableRow);
            }
        }

        return $this->sendResponse($supplierEvaluation->toArray(), 'Supplier evaluation created successfully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $supplierEvaluation = SupplierEvaluation::where('id', $id)->first();

        if (empty($supplierEvaluation)) {
            return $this->sendError('Supplier Evaluation not found');
        }

        $supplierEvaluation['templateMaster'] = SupplierEvaluationTemplate::with(['company'])->where('id', $supplierEvaluation['evaluationTemplate'])->first();
        $supplierEvaluation['templateSections'] = EvaluationTemplateSection::with([
            'table' => function($query) use ($id) {
                $query->where('isConfirmed', 1)->with(['column' => function($columnQuery) {
                    $columnQuery->with('evaluation_master_detail');
                }, 'evaluationDetailRow' => function($evaluationDetailRowQuery)use ($id) {
                    $evaluationDetailRowQuery->where('evaluationId', $id);
                }, 'formula' => function($formulaQuery) {
                    $formulaQuery->with('label');
                }]);
            }
        ])->where('supplier_evaluation_template_id', $supplierEvaluation['evaluationTemplate'])->get();

        $supplierEvaluation['templateComments'] = SupplierEvaluationTemplateComment::where('supplier_evaluation_template_id',$supplierEvaluation['evaluationTemplate'])
            ->orderBy('created_at', 'desc')
            ->get();

        return $this->sendResponse($supplierEvaluation->toArray(), 'Supplier Evaluation retrieved successfully');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
