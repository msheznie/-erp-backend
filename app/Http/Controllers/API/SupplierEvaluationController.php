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
use App\Models\SupplierAssigned;
use Illuminate\Support\Facades\DB;
use App\Models\SupplierEvaluationMasterDetails;



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

    function getAllSupplierEvaluations(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $input = $this->convertArrayToSelectedValue($input, array('supplierID', 'evaluationTemplate', 'evaluationType'));

        $search = $request->input('search.value');

        $supplier = $request['supplierID'];
        $supplier = (array)$supplier;
        $supplier = collect($supplier)->pluck('id');

        $evaluationTemplate = $request['evaluationTemplate'];
        $evaluationTemplate = (array)$evaluationTemplate;
        $evaluationTemplate = collect($evaluationTemplate)->pluck('id');

        $supplierEvalations = $this->SupplierEvaluationRepository->supplierEvaluationListQuery($request, $input, $search, $supplier, $evaluationTemplate);

        return \DataTables::eloquent($supplierEvalations)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function getSupplierEvaluationFormData(Request $request)
    {
        $companyId = $request['companyId'];
        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $supplierEvaluation = SupplierEvaluationTemplate::where('companySystemID', $companyId)
            ->where('is_confirmed', 1)
            ->get();

        $output = [
            'suppliers' => $supplier,
            'evaluationTemplates' => $supplierEvaluation
        ];

        return $this->sendResponse($output, '');
    }

    function printSupplierEvaluation(Request $request)
    {
        $id = $request->get('id');
        $supplierEvaluation = SupplierEvaluation::where('id', $id)->first();

        $templateMaster = SupplierEvaluationTemplate::with(['company'])->where('id', $supplierEvaluation['evaluationTemplate'])->first();
        $templateSections = EvaluationTemplateSection::with([
            'table' => function($query) use ($id) {
                $query->where('isConfirmed', 1)->with(['column' => function($columnQuery) {},
                    'evaluationDetailRow' => function($evaluationDetailRowQuery)use ($id) {
                    $evaluationDetailRowQuery->where('evaluationId', $id);
                }, 'formula' => function($formulaQuery) {
                    $formulaQuery->with('label');
                }]);
            }
        ])->where('supplier_evaluation_template_id', $supplierEvaluation['evaluationTemplate'])->get();

        foreach ($templateSections as &$tables) {
            if ($tables['table'] !== null) {
                $a = 0;
                $tableSelectedScore = 0;
                foreach ($tables['table']['column'] as &$columns) {
                    if ($columns['column_type'] == 3) {
                        foreach ($tables['table']['evaluationDetailRow'] as &$evaluationDetails) {
                            $b = 0;
                            $row = json_decode($evaluationDetails['rowData'], true);
                            foreach ($row as &$det) {
                                if ($a === $b) {
                                    $evaluationMasterDetail = SupplierEvaluationMasterDetails::where('id', $det)->first();
                                    if ($evaluationMasterDetail) {
                                        $det = [
                                            $columns['column_header'] => $evaluationMasterDetail->description
                                        ];
                                        if ($evaluationMasterDetail->score !== null) {
                                           $tableSelectedScore += $evaluationMasterDetail->score;
                                        }
                                    } else {
                                        $det = [
                                            $columns['column_header'] => ''
                                        ];
                                    }
                                }
                                $b++;
                            }
                            $evaluationDetails['rowData'] = json_encode($row);
                            $evaluationDetails['rowDetails'] = $row;
                        }
                        $maxEvaluationScore = SupplierEvaluationMasterDetails::where('master_id', $columns['evaluationMasterId'])
                            ->whereNotNull('score')
                            ->max('score');
                        if($maxEvaluationScore !== null) {
                            $maxScore = $maxEvaluationScore;
                        }
                    }
                    $a++;
                }
                $tables['table']['totalRowCount'] = count($tables['table']['evaluationDetailRow']);
                $tables['table']['totalSelectedScore'] = $tableSelectedScore;
                $tables['table']['selectedAverage'] = ($tableSelectedScore / $tables['table']['totalRowCount']);
                if(isset($maxScore) && $maxScore > 0) {
                    $tables['table']['selectedPercentage'] = ($tableSelectedScore / ($maxScore * $tables['table']['totalRowCount'])) * 100;
                }
            }
        }

        $templateComments = SupplierEvaluationTemplateComment::where('supplier_evaluation_template_id',$supplierEvaluation['evaluationTemplate'])
            ->orderBy('created_at', 'desc')
            ->get();

        $array = [
            'evaluationMaster' => $supplierEvaluation,
            'templateMaster' => $templateMaster,
            'templateSections' => $templateSections,
            'templateComments' => $templateComments
        ];

        $time = strtotime("now");
        $fileName = 'supplier_evaluation_' . $id . '_' . $time . '.pdf';
        $html = view('print.supplier_evaluation', $array);
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('tmp'), 'mode' => 'utf-8', 'format' => 'A4-P', 'setAutoTopMargin' => 'stretch', 'autoMarginPadding' => -10]);
        $mpdf->AddPage('P');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }
}
