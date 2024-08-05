<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierEvaluationTableDetailsAPIRequest;
use App\Http\Requests\API\UpdateSupplierEvaluationTableDetailsAPIRequest;
use App\Models\SupplierEvaluationMasterDetails;
use App\Models\SupplierEvaluationTableDetails;
use App\Repositories\SupplierEvaluationTableDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierEvaluationTableDetailsController
 * @package App\Http\Controllers\API
 */

class SupplierEvaluationTableDetailsAPIController extends AppBaseController
{
    /** @var  SupplierEvaluationTableDetailsRepository */
    private $supplierEvaluationTableDetailsRepository;

    public function __construct(SupplierEvaluationTableDetailsRepository $supplierEvaluationTableDetailsRepo)
    {
        $this->supplierEvaluationTableDetailsRepository = $supplierEvaluationTableDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTableDetails",
     *      summary="getSupplierEvaluationTableDetailsList",
     *      tags={"SupplierEvaluationTableDetails"},
     *      description="Get all SupplierEvaluationTableDetails",
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
     *                  @OA\Items(ref="#/definitions/SupplierEvaluationTableDetails")
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
        $this->supplierEvaluationTableDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierEvaluationTableDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierEvaluationTableDetails = $this->supplierEvaluationTableDetailsRepository->all();

        return $this->sendResponse($supplierEvaluationTableDetails->toArray(), 'Supplier Evaluation Table Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierEvaluationTableDetails",
     *      summary="createSupplierEvaluationTableDetails",
     *      tags={"SupplierEvaluationTableDetails"},
     *      description="Create SupplierEvaluationTableDetails",
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
     *                  ref="#/definitions/SupplierEvaluationTableDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierEvaluationTableDetailsAPIRequest $request)
    {
        $input = $request->all();

        $supplierEvaluationTableDetails = $this->supplierEvaluationTableDetailsRepository->create($input);

        return $this->sendResponse($supplierEvaluationTableDetails->toArray(), 'Supplier Evaluation Table Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTableDetails/{id}",
     *      summary="getSupplierEvaluationTableDetailsItem",
     *      tags={"SupplierEvaluationTableDetails"},
     *      description="Get SupplierEvaluationTableDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTableDetails",
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
     *                  ref="#/definitions/SupplierEvaluationTableDetails"
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
        /** @var SupplierEvaluationTableDetails $supplierEvaluationTableDetails */
        $supplierEvaluationTableDetails = $this->supplierEvaluationTableDetailsRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTableDetails)) {
            return $this->sendError('Supplier Evaluation Table Details not found');
        }

        return $this->sendResponse($supplierEvaluationTableDetails->toArray(), 'Supplier Evaluation Table Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/supplierEvaluationTableDetails/{id}",
     *      summary="updateSupplierEvaluationTableDetails",
     *      tags={"SupplierEvaluationTableDetails"},
     *      description="Update SupplierEvaluationTableDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTableDetails",
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
     *                  ref="#/definitions/SupplierEvaluationTableDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierEvaluationTableDetailsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SupplierEvaluationTableDetails $supplierEvaluationTableDetails */
        $supplierEvaluationTableDetails = $this->supplierEvaluationTableDetailsRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTableDetails)) {
            return $this->sendError('Supplier Evaluation Table Details not found');
        }

        $supplierEvaluationTableDetails = $this->supplierEvaluationTableDetailsRepository->update($input, $id);

        return $this->sendResponse($supplierEvaluationTableDetails->toArray(), 'SupplierEvaluationTableDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierEvaluationTableDetails/{id}",
     *      summary="deleteSupplierEvaluationTableDetails",
     *      tags={"SupplierEvaluationTableDetails"},
     *      description="Delete SupplierEvaluationTableDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTableDetails",
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
        /** @var SupplierEvaluationTableDetails $supplierEvaluationTableDetails */
        $supplierEvaluationTableDetails = $this->supplierEvaluationTableDetailsRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTableDetails)) {
            return $this->sendError('Supplier Evaluation Table Details not found');
        }

        $supplierEvaluationTableDetails->delete();

        return $this->sendSuccess('Supplier Evaluation Table Details deleted successfully');
    }

    public function updateEvaluationDetails(Request $request) {
        $input = $request->all();
        $array = [];
        if($input) {
            foreach ($input as $key => $value) {
                if($value) {
                    $detailIDs = explode('_', $key);
                    $row = SupplierEvaluationTableDetails::find($detailIDs[1]);
                    $rowArray = json_decode($row->rowData, true);
                    foreach ($rowArray[$detailIDs[3]] as $key2 => $det) {
                        $rowArray[$detailIDs[3]][$key2] = $value;
                    }

                    $evaluationMasterDetails = SupplierEvaluationMasterDetails::find($value);
                    if(isset($evaluationMasterDetails->score)) {
                        foreach ($rowArray as &$item) {
                            if (array_key_exists('Score(Number)', $item)) {
                                $item['Score(Number)'] = $evaluationMasterDetails->score;
                            }
                        }
                    } else if (isset($evaluationMasterDetails->rating)) {
                        foreach ($rowArray as &$item) {
                            if (array_key_exists('Score(Rating)', $item)) {
                                $item['Score(Rating)'] = $evaluationMasterDetails->rating;
                            }
                        }
                    }
                    $rowArray = json_encode($rowArray);
                    $row->rowData = $rowArray;
                    $row->save();
                    $array[] = $row;
                }
            }
        }
        return $this->sendResponse($array, 'Supplier Evaluation Details Updated Successfully');
    }
}
