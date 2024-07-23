<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierEvaluationTemplateSectionTableColumnAPIRequest;
use App\Http\Requests\API\UpdateSupplierEvaluationTemplateSectionTableColumnAPIRequest;
use App\Models\SupplierEvaluationTemplateSectionTableColumn;
use App\Repositories\SupplierEvaluationTemplateSectionTableColumnRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\SupplierEvaluationTemplateSectionTable;
use App\Models\TemplateSectionTableRow;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierEvaluationTemplateSectionTableColumnController
 * @package App\Http\Controllers\API
 */

class SupplierEvaluationTemplateSectionTableColumnAPIController extends AppBaseController
{
    /** @var  SupplierEvaluationTemplateSectionTableColumnRepository */
    private $supplierEvaluationTemplateSectionTableColumnRepository;

    public function __construct(SupplierEvaluationTemplateSectionTableColumnRepository $supplierEvaluationTemplateSectionTableColumnRepo)
    {
        $this->supplierEvaluationTemplateSectionTableColumnRepository = $supplierEvaluationTemplateSectionTableColumnRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTemplateSectionTableColumns",
     *      summary="getSupplierEvaluationTemplateSectionTableColumnList",
     *      tags={"SupplierEvaluationTemplateSectionTableColumn"},
     *      description="Get all SupplierEvaluationTemplateSectionTableColumns",
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
     *                  @OA\Items(ref="#/definitions/SupplierEvaluationTemplateSectionTableColumn")
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
        $this->supplierEvaluationTemplateSectionTableColumnRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierEvaluationTemplateSectionTableColumnRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierEvaluationTemplateSectionTableColumns = $this->supplierEvaluationTemplateSectionTableColumnRepository->all();

        return $this->sendResponse($supplierEvaluationTemplateSectionTableColumns->toArray(), 'Supplier Evaluation Template Section Table Columns retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierEvaluationTemplateSectionTableColumns",
     *      summary="createSupplierEvaluationTemplateSectionTableColumn",
     *      tags={"SupplierEvaluationTemplateSectionTableColumn"},
     *      description="Create SupplierEvaluationTemplateSectionTableColumn",
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
     *                  ref="#/definitions/SupplierEvaluationTemplateSectionTableColumn"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierEvaluationTemplateSectionTableColumnAPIRequest $request)
    {
        $input = $request->all();

        $supplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepository->create($input);

        return $this->sendResponse($supplierEvaluationTemplateSectionTableColumn->toArray(), 'Supplier Evaluation Template Section Table Column saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTemplateSectionTableColumns/{id}",
     *      summary="getSupplierEvaluationTemplateSectionTableColumnItem",
     *      tags={"SupplierEvaluationTemplateSectionTableColumn"},
     *      description="Get SupplierEvaluationTemplateSectionTableColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateSectionTableColumn",
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
     *                  ref="#/definitions/SupplierEvaluationTemplateSectionTableColumn"
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
        /** @var SupplierEvaluationTemplateSectionTableColumn $supplierEvaluationTemplateSectionTableColumn */
        $supplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplateSectionTableColumn)) {
            return $this->sendError('Supplier Evaluation Template Section Table Column not found');
        }

        return $this->sendResponse($supplierEvaluationTemplateSectionTableColumn->toArray(), 'Supplier Evaluation Template Section Table Column retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/supplierEvaluationTemplateSectionTableColumns/{id}",
     *      summary="updateSupplierEvaluationTemplateSectionTableColumn",
     *      tags={"SupplierEvaluationTemplateSectionTableColumn"},
     *      description="Update SupplierEvaluationTemplateSectionTableColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateSectionTableColumn",
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
     *                  ref="#/definitions/SupplierEvaluationTemplateSectionTableColumn"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierEvaluationTemplateSectionTableColumnAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);



        /** @var SupplierEvaluationTemplateSectionTableColumn $supplierEvaluationTemplateSectionTableColumn */
        $supplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepository->findWithoutFail($id);

    
        if (empty($supplierEvaluationTemplateSectionTableColumn)) {
            return $this->sendError('Supplier Evaluation Template Section Table Column not found');
        }

        if(isset($input['column_type']) && $input['column_type'] == 3){
            $input['is_disabled'] = 1; 
        }

        if(isset($input['column_type']) && $input['column_type'] == 1){
            $input['is_disabled'] = 1; 
            $scoreColumnCount = SupplierEvaluationTemplateSectionTableColumn::where('table_id', $input['table_id'])
                                                                            ->where('column_type', 1)
                                                                            ->where('id', '!=', $input['id']) 
                                                                            ->count();

            if($supplierEvaluationTemplateSectionTableColumn['column_type'] == 1 && isset($input['autoIncrementStart']) && $input['autoIncrementStart'] != null){

            } else {
                if($scoreColumnCount > 0){
                    return $this->sendError('Auto increment column can not be multiple', 500);                                                
                }
            }

        }

        $supplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepository->update($input, $id);

        $tableColumnsCreate = SupplierEvaluationTemplateSectionTableColumn::where('table_id' ,$supplierEvaluationTemplateSectionTableColumn['table_id'])->get();

        $deleteTableRowData = TemplateSectionTableRow::where('table_id', $supplierEvaluationTemplateSectionTableColumn['table_id'])->delete();

        // Prepare row data in JSON format
        $supplierEvaluationTemplateSectionTable = SupplierEvaluationTemplateSectionTable::where('id' ,$supplierEvaluationTemplateSectionTableColumn['table_id'])->first();
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

        $tableColumnData = SupplierEvaluationTemplateSectionTableColumn::where('table_id', $supplierEvaluationTemplateSectionTableColumn['table_id'])->get();
        $data = [
            'supplierEvaluationTemplateSectionTableColumn'=> $supplierEvaluationTemplateSectionTableColumn,
            'tableColumnData'=> $tableColumnData,
        ];
        return $this->sendResponse($data, 'SupplierEvaluationTemplateSectionTableColumn updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierEvaluationTemplateSectionTableColumns/{id}",
     *      summary="deleteSupplierEvaluationTemplateSectionTableColumn",
     *      tags={"SupplierEvaluationTemplateSectionTableColumn"},
     *      description="Delete SupplierEvaluationTemplateSectionTableColumn",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplateSectionTableColumn",
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
        /** @var SupplierEvaluationTemplateSectionTableColumn $supplierEvaluationTemplateSectionTableColumn */
        $supplierEvaluationTemplateSectionTableColumn = $this->supplierEvaluationTemplateSectionTableColumnRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplateSectionTableColumn)) {
            return $this->sendError('Supplier Evaluation Template Section Table Column not found');
        }

        $supplierEvaluationTemplateSectionTableColumn->delete();

        $tableColumnsCreate = SupplierEvaluationTemplateSectionTableColumn::where('table_id' ,$supplierEvaluationTemplateSectionTableColumn['table_id'])->get();

        $deleteTableRowData = TemplateSectionTableRow::where('table_id', $supplierEvaluationTemplateSectionTableColumn['table_id'])->delete();

        // Prepare row data in JSON format
        $supplierEvaluationTemplateSectionTable = SupplierEvaluationTemplateSectionTable::where('id' ,$supplierEvaluationTemplateSectionTableColumn['table_id'])->first();
        $tableRows = $supplierEvaluationTemplateSectionTable['table_row'];
        
        $row_data = [];
        foreach ($tableColumnsCreate as $column) {
            $column_header = $column->column_header; // Use column_header as the key
            $row_data[] = [$column_header => null]; // Replace with actual data as needed
        }
        $row_data_json = json_encode($row_data);
                    // Create a new row in the template_section_table_row table
        $row = [
            'table_id' => $supplierEvaluationTemplateSectionTableColumn['table_id'],
            'rowData' => $row_data_json
        ];
        for ($i = 0; $i < $tableRows; $i++) {
            TemplateSectionTableRow::create($row);
        }

        $tableColumnData = SupplierEvaluationTemplateSectionTableColumn::where('table_id', $supplierEvaluationTemplateSectionTableColumn['table_id'])->get();

        return $this->sendResponse($tableColumnData,'Supplier Evaluation Template Section Table Column deleted successfully');
    }
}
