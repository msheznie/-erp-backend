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

        return $this->sendResponse($supplierEvaluationTemplateSectionTables->toArray(), 'Supplier Evaluation Template Section Tables retrieved successfully');
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
            return $this->sendResponse($data, 'Supplier Evaluation Template Section saved successfully');
    
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
                                                                                            ->orWhereNull('column_header');
                                                                                    })
                                                                                    ->count();
    
        if($validateTableColumn > 0){
            return $this->sendError('Column type or column header can not be empty', 500);                                                
        } else {
            $updateData = [
                'isConfirmed' => 1
            ];
            $confirmTable = SupplierEvaluationTemplateSectionTable::where('id', $input['id'])->update($updateData);
             return $this->sendResponse($confirmTable, 'Confirmed table successfully');
        }

    }

    public function addMasterColumns(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {

            $evaluationMaster = SupplierEvaluationMasters::where('id',$input['evaluationMasterId'] )->first();
            if(!$evaluationMaster){
                return $this->sendError('Evaluation master not found', 500);                                                
            }
            $input['is_disabled'] = 1;
            $input['evaluationMasterType'] = $evaluationMaster->type;

            $updateColumn = SupplierEvaluationTemplateSectionTableColumn::where('id', $input['id'])->update($input);

            if($input['column_type'] == 3){

                if(isset($evaluationMaster->type) && ($evaluationMaster->type == 1 || $evaluationMaster->type == 2)){
                    $scoreColumnCount = SupplierEvaluationTemplateSectionTableColumn::where('table_id', $input['table_id'])
                    ->where('column_type', 3)
                    ->whereNotNull('evaluationMasterType')
                    ->where('evaluationMasterType', '!=', 3)
                    ->whereNotNull('evaluationMasterColumn')
                    ->count();


                    if($scoreColumnCount > 0){
                    return $this->sendError('Only General Master evaluation type can be multiple columns', 500);                                                
                    }

                    if ($evaluationMaster->type == 1 ){
                        $columnHeader = 'Score(Number)';
                        $columnType = 4;
                    }
                    if ($evaluationMaster->type == 2 ){
                        $columnHeader = 'Score(Rating)';
                        $columnType = 5;
                    }

                    SupplierEvaluationTemplateSectionTableColumn::create(['table_id' => $input['table_id'] , 'column_type' => $columnType ,'column_header' => $columnHeader, 'is_disabled' => 1 ,'evaluationMasterType' => $evaluationMaster->type ,'evaluationMasterColumn' => $input['id'], 'evaluationMasterId' => $evaluationMaster->id]);
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
            return $this->sendResponse($data, 'Master columns created successfully');
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
                                    ])->where('supplier_evaluation_template_id', $id)->get();
        

        return $this->sendResponse($evaluationTemplateSection, 'Evaluation template section retrieved successfully');

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
            return $this->sendError('Supplier Evaluation Template Section Table not found');
        }

        return $this->sendResponse($supplierEvaluationTemplateSectionTable->toArray(), 'Supplier Evaluation Template Section Table retrieved successfully');
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
            return $this->sendError('Supplier Evaluation Template Section Table not found');
        }

        $supplierEvaluationTemplateSectionTable = $this->supplierEvaluationTemplateSectionTableRepository->update($input, $id);

        
        return $this->sendResponse($supplierEvaluationTemplateSectionTable->toArray(), 'SupplierEvaluationTemplateSectionTable updated successfully');
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
            return $this->sendError('Supplier Evaluation Template Section Table not found');
        }

        $supplierEvaluationTemplateSectionTable->delete();

        return $this->sendResponse($id,'Supplier Evaluation Template Section Table deleted successfully');

    }
}
