<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierEvaluationTemplateSectionTableAPIRequest;
use App\Http\Requests\API\UpdateSupplierEvaluationTemplateSectionTableAPIRequest;
use App\Models\SupplierEvaluationTemplateSectionTable;
use App\Repositories\SupplierEvaluationTemplateSectionTableRepository;
use App\Repositories\EvaluationTemplateSectionRepository;
use App\Repositories\EvaluationTemplateSectionLabelRepository;
use App\Repositories\EvaluationTemplateSectionFormulaRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\EvaluationTemplateSection;
use App\Models\EvaluationTemplateSectionFormula;
use App\Models\SupplierEvaluationMasters;
use App\Models\SupplierEvaluationTemplate;
use App\Models\SupplierEvaluationTemplateSectionTableColumn;
use App\Models\TemplateSectionTableRow;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierEvaluationTemplateSectionTableController
 * @package App\Http\Controllers\API
 */

class SupplierEvaluationTemplateSectionTableAPIController extends AppBaseController
{
    /** @var  SupplierEvaluationTemplateSectionTableRepository */
    /** @var  EvaluationTemplateSectionRepository */
    /** @var  EvaluationTemplateSectionLabelRepository */
    /** @var  EvaluationTemplateSectionFormulaRepository */
    private $supplierEvaluationTemplateSectionTableRepository;
    private $evaluationTemplateSectionRepository;
    private $evaluationTemplateSectionLabelRepository;
    private $evaluationTemplateSectionFormulaRepository;

    public function __construct(SupplierEvaluationTemplateSectionTableRepository $supplierEvaluationTemplateSectionTableRepo, 
                                EvaluationTemplateSectionRepository $evaluationTemplateSectionRepo,
                                EvaluationTemplateSectionLabelRepository $evaluationTemplateSectionLabelRepo,
                                EvaluationTemplateSectionFormulaRepository $evaluationTemplateSectionFormulaRepo
        )
    {
        $this->supplierEvaluationTemplateSectionTableRepository = $supplierEvaluationTemplateSectionTableRepo;
        $this->evaluationTemplateSectionRepository = $evaluationTemplateSectionRepo;
        $this->evaluationTemplateSectionLabelRepository = $evaluationTemplateSectionLabelRepo;
        $this->evaluationTemplateSectionFormulaRepository = $evaluationTemplateSectionFormulaRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTemplateSectionTables",
     *      summary="getSupplierEvaluationTemplateSectionTableList",
     *      tags={"SupplierEvaluationTemplateSectionTable"},
     *      description="Get all SupplierEvaluationTemplateSectionTables",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/SupplierEvaluationTemplateSectionTable")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->supplierEvaluationTemplateSectionTableRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierEvaluationTemplateSectionTableRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierEvaluationTemplateSectionTables = $this->supplierEvaluationTemplateSectionTableRepository->all();

        return $this->sendResponse($supplierEvaluationTemplateSectionTables->toArray(), trans('custom.supplier_evaluation_template_section_tables_retrie'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierEvaluationTemplateSectionTables",
     *      summary="createSupplierEvaluationTemplateSectionTable",
     *      tags={"SupplierEvaluationTemplateSectionTable"},
     *      description="Create SupplierEvaluationTemplateSectionTable",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SupplierEvaluationTemplateSectionTable"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierEvaluationTemplateSectionTableAPIRequest $request)
    {
        DB::beginTransaction();
        try {
            $input = $request->all();
            $data= [];

            $validator = \Validator::make($input, [
                'supplier_evaluation_template_id' => 'required',
                'table_name' => 'required',
                'section_type' => 'required'
            ]);
    
            if ($validator->fails()) {
                return $this->sendError($validator->messages(), 422);
            }

            $sectionInput = [
                'supplier_evaluation_template_id' => $input['supplier_evaluation_template_id'],
                'section_name' => $input['table_name'],
                'section_type' => $input['section_type'],
            ];

            // Create the evaluation template section
            $evaluationTemplateSection = $this->evaluationTemplateSectionRepository->create($sectionInput);
            $data = [
                'sectionData' => $evaluationTemplateSection
            ];

            if($evaluationTemplateSection && $evaluationTemplateSection->section_type == 1){
                $sectionTableInput = $input;
                unset($sectionTableInput['section_type']);
                $sectionTableInput['evaluation_template_section_id'] = $evaluationTemplateSection->id;
                // Create the supplier evaluation template section table
                $supplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepository->create($sectionTableInput);
    
                if ($supplierEvaluationTemplateSectionTable) {
                    $tableColumns = $supplierEvaluationTemplateSectionTable->table_column;
                    $tableRows = $supplierEvaluationTemplateSectionTable->table_row;
                    $tableId = ['table_id' => $supplierEvaluationTemplateSectionTable->id];
        
                    // Create table columns
                    $tableColumnsCreate = [];
                    for ($i = 0; $i < $tableColumns; $i++) {
                        $tableColumnsCreate[] = SupplierEvaluationTemplateSectionTableColumn::create($tableId);
                    }
        
                    // Prepare row data in JSON format
                    $row_data = [];
                    foreach ($tableColumnsCreate as $column) {
                        $column_header = $column->column_header; // Use column_header as the key
                        $row_data[] = [$column_header => null]; // Replace with actual data as needed
                    }
                    $row_data_json = json_encode($row_data);
        
                    // Create a new row in the template_section_table_row table
                    $row = [
                        'table_id' => $supplierEvaluationTemplateSectionTable->id,
                        'rowData' => $row_data_json
                    ];
                    for ($i = 0; $i < $tableRows; $i++) {
                        TemplateSectionTableRow::create($row);
                    }
                    

                    $tableData = $supplierEvaluationTemplateSectionTable;
                    $tableColumnsData = SupplierEvaluationTemplateSectionTableColumn::where('table_id', $tableId)->get();
                    $tableRowData = TemplateSectionTableRow::where('table_id', $tableId)->get();
                    if(isset($tableRowData->row_data)){
                        $tableRowData->row_data = json_decode($tableRowData->row_data);
                    }

                    $data = [
                        'tableData' => $tableData,
                        'tableColumnsData' => $tableColumnsData,
                        'tableRowData' => $tableRowData,
                        'sectionData' => $evaluationTemplateSection
                    ];
                }
            }
            
            if($evaluationTemplateSection && $evaluationTemplateSection->section_type == 2){
                $sectionLabelInput = $input;
                unset($sectionLabelInput['section_type']);
                unset($sectionLabelInput['table_name']);
                unset($sectionLabelInput['table_column']);
                unset($sectionLabelInput['table_row']);

                $sectionLabelInput['evaluation_template_section_id'] = $evaluationTemplateSection->id;
                $sectionLabelInput['labelName'] = $input['table_name'];
                // Create the supplier evaluation template section label
                $evaluationTemplateSectionLabel = $this->evaluationTemplateSectionLabelRepository->create($sectionLabelInput);

                
                $data = [
                    'sectionData' => $evaluationTemplateSection,
                    'sectionLabelData' => $evaluationTemplateSectionLabel
                ];
            }

            if($evaluationTemplateSection && $evaluationTemplateSection->section_type == 3){
                $sectionformulaInput = $input;
                unset($sectionformulaInput['section_type']);
                unset($sectionformulaInput['table_name']);
                unset($sectionformulaInput['table_column']);
                unset($sectionformulaInput['table_row']);

                $sectionformulaInput['evaluation_template_section_id'] = $evaluationTemplateSection->id;
                $sectionformulaInput['formulaName'] = $input['table_name'];
                $inputData = $sectionformulaInput;
                unset($sectionformulaInput['table_id']);
                $formulas = [];
                foreach($inputData['table_id'] as $tableID) {
                    $sectionformulaInput['table_id'] = $tableID['id'];

                    $duplicateCheckCount = EvaluationTemplateSectionFormula::where('table_id' ,$tableID['id'])
                                                                        ->where('formulaType' ,$sectionformulaInput['formulaType'])
                                                                        ->count();
                    if($duplicateCheckCount > 0){
                        return $this->sendError(trans('custom.same_formula_type_cannot_be_multiple_times'), 500);
                    }
                    // Create the supplier evaluation template section formula
                    $evaluationTemplateSectionFormula = $this->evaluationTemplateSectionFormulaRepository->create($sectionformulaInput);
                    $formulas[] = $evaluationTemplateSectionFormula;

                }

                
                $data = [
                    'sectionData' => $evaluationTemplateSection,
                    'sectionFormulaData' => $formulas
                ];
            }

            DB::commit();
            return $this->sendResponse($data, trans('custom.supplier_evaluation_template_section_saved_success'));
    
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage() . $exception->getLine());
        }
    }
    
    public function confirmTable(Request $request)
    {
        $input = $request->all();
        $validateTableColumn = SupplierEvaluationTemplateSectionTableColumn::where('table_id', $input['id'])
                                                                                    ->where(function($query) {
                                                                                        $query->whereNull('column_type')
                                                                                            ->orWhereNull('column_header')
                                                                                            ->orWhere('column_type', 0);
                                                                                    })
                                                                                    ->count();
    
        if($validateTableColumn > 0){
            return $this->sendError(trans('custom.column_type_or_header_cannot_be_empty'), 500);                                                
        } else {
            $updateData = [
                'isConfirmed' => 1
            ];
            $confirmTable = SupplierEvaluationTemplateSectionTable::where('id', $input['id'])->update($updateData);
             return $this->sendResponse($confirmTable, trans('custom.confirmed_table_successfully'));
        }

    }

    public function addMasterColumns(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {

            $evaluationMaster = SupplierEvaluationMasters::where('id',$input['evaluationMasterId'] )->first();
            if(!$evaluationMaster){
                return $this->sendError(trans('custom.evaluation_master_not_found'), 500);                                                
            }
            $input['is_disabled'] = 1;
            $input['evaluationMasterType'] = $evaluationMaster->type;

            $updateColumn = SupplierEvaluationTemplateSectionTableColumn::where('id', $input['id'])->update($input);

            if($input['column_type'] == 3){

                if(isset($evaluationMaster->type) && ($evaluationMaster->type == 1 || $evaluationMaster->type == 2)){
                    $scoreColumnCount = SupplierEvaluationTemplateSectionTableColumn::where('table_id', $input['table_id'])
                    ->where('id', '!=', $input['id'])
                    ->where('column_type', 3)
                    ->whereNotNull('evaluationMasterType')
                    ->where('evaluationMasterType', '!=', 3)
                    ->count();


                    if($scoreColumnCount > 0){
                        return $this->sendError(trans('custom.only_general_master_evaluation_type_multiple_columns'), 500);                                                
                    }

                    if ($evaluationMaster->type == 1 ){
                        $columnHeader = trans('custom.score_number');
                        $columnType = 4;
                    }
                    if ($evaluationMaster->type == 2 ){
                        $columnHeader = trans('custom.score_rating');
                        $columnType = 5;
                    }

                    SupplierEvaluationTemplateSectionTableColumn::create(['table_id' => $input['table_id'] , 'column_type' => $columnType ,'column_header' => $columnHeader, 'is_disabled' => 1 ,'evaluationMasterType' => $evaluationMaster->type ,'evaluationMasterColumn' => $input['id'], 'evaluationMasterId' => $evaluationMaster->id]);
                    $table = SupplierEvaluationTemplateSectionTable::where('id', $input['table_id'])->first();

                    $tableColumnCount = $table->table_column + 1;
                    $updateData = ['table_column' => $tableColumnCount];
                    $updateTable = SupplierEvaluationTemplateSectionTable::where('id', $input['table_id'])->update($updateData);
                } 
            }
            

            

            $tableColumnsCreate = SupplierEvaluationTemplateSectionTableColumn::where('table_id' ,$input['table_id'])->get();

            $deleteTableRowData = TemplateSectionTableRow::where('table_id', $input['table_id'])->delete();

            // Prepare row data in JSON format
            $supplierEvaluationTemplateSectionTable = SupplierEvaluationTemplateSectionTable::where('id' ,$input['table_id'])->first();
            $tableRows = $supplierEvaluationTemplateSectionTable['table_row'];
            
            $row_data = [];
            foreach ($tableColumnsCreate as $column) {
                $column_header = $column->column_header; // Use column_header as the key
                $row_data[] = [$column_header => null]; // Replace with actual data as needed
            }
            $row_data_json = json_encode($row_data);

            // Create a new row in the template_section_table_row table
            $row = [
                'table_id' => $input['table_id'],
                'rowData' => $row_data_json
            ];
            for ($i = 0; $i < $tableRows; $i++) {
                TemplateSectionTableRow::create($row);
            }
            

            $tableData = $supplierEvaluationTemplateSectionTable;
            $tableColumnsData = SupplierEvaluationTemplateSectionTableColumn::where('table_id', $input['table_id'])->get();
            $tableRowData = TemplateSectionTableRow::where('table_id', $input['table_id'])->get();
            if(isset($tableRowData->row_data)){
                $tableRowData->row_data = json_decode($tableRowData->row_data);
            }

            $data = [
                'tableData' => $tableData,
                'tableColumnsData' => $tableColumnsData,
                'tableRowData' => $tableRowData,
            ];

            DB::commit();
            return $this->sendResponse($data, trans('custom.master_columns_created_successfully'));
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getMessage() . $exception->getLine());
        }
    }


    public function sectionTableData(Request $request)  {
        $input = $request->all();
        $id = $input['id'];
        $evaluationTemplateSection = EvaluationTemplateSection::with([
                                        'table' => function($query) {
                                            $query->with(['column' => function($columnQuery) {
                                                $columnQuery->with('evaluation_master','evaluation_master_detail');
                                            }, 'row', 'formula' => function($formulaQuery) {
                                                $formulaQuery->with('label');
                                            }]);
                                        }
                                    ])->whereHas('table')->where('supplier_evaluation_template_id', $id)->get();

        if(!empty($evaluationTemplateSection))
        {
            $evaluationTemplateSection->each(function($section) {
                if(isset($section['table']) && !empty($section['table']['row'])){
                    collect($section['table']['row'])->each(function($row){
                        $row['rowData'] = $row['rowData'];
                    });
                }
            });
        }

        return $this->sendResponse($evaluationTemplateSection, trans('custom.evaluation_template_section_retrieved_successfully_1'));

    }
    
    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTemplateSectionTables/{id}",
     *      summary="getSupplierEvaluationTemplateSectionTableItem",
     *      tags={"SupplierEvaluationTemplateSectionTable"},
     *      description="Get SupplierEvaluationTemplateSectionTable",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateSectionTable",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SupplierEvaluationTemplateSectionTable"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var SupplierEvaluationTemplateSectionTable $supplierEvaluationTemplateSectionTable */
        $supplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplateSectionTable)) {
            return $this->sendError(trans('custom.supplier_evaluation_template_section_table_not_fou'));
        }

        return $this->sendResponse($supplierEvaluationTemplateSectionTable->toArray(), trans('custom.supplier_evaluation_template_section_table_retriev'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/supplierEvaluationTemplateSectionTables/{id}",
     *      summary="updateSupplierEvaluationTemplateSectionTable",
     *      tags={"SupplierEvaluationTemplateSectionTable"},
     *      description="Update SupplierEvaluationTemplateSectionTable",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateSectionTable",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/SupplierEvaluationTemplateSectionTable"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierEvaluationTemplateSectionTableAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var SupplierEvaluationTemplateSectionTable $supplierEvaluationTemplateSectionTable */
        $supplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplateSectionTable)) {
            return $this->sendError(trans('custom.supplier_evaluation_template_section_table_not_fou'));
        }

        $supplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepository->update($input, $id);

        
        return $this->sendResponse($supplierEvaluationTemplateSectionTable->toArray(), trans('custom.supplierevaluationtemplatesectiontable_updated_suc'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierEvaluationTemplateSectionTables/{id}",
     *      summary="deleteSupplierEvaluationTemplateSectionTable",
     *      tags={"SupplierEvaluationTemplateSectionTable"},
     *      description="Delete SupplierEvaluationTemplateSectionTable",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateSectionTable",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var SupplierEvaluationTemplateSectionTable $supplierEvaluationTemplateSectionTable */
        $supplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplateSectionTable)) {
            return $this->sendError(trans('custom.supplier_evaluation_template_section_table_not_fou'));
        }

        $supplierEvaluationTemplateSectionTable->delete();

        $tableFormula = EvaluationTemplateSectionFormula::where('table_id', $id)->delete();

        return $this->sendResponse($id,trans('custom.supplier_evaluation_template_section_table_deleted'));

    }
}
