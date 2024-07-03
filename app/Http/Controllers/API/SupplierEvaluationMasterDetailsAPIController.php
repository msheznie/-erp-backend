<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierEvaluationMasterDetailsAPIRequest;
use App\Http\Requests\API\UpdateSupplierEvaluationMasterDetailsAPIRequest;
use App\Models\SupplierEvaluationMasterDetails;
use App\Repositories\SupplierEvaluationMasterDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierEvaluationMasterDetailsController
 * @package App\Http\Controllers\API
 */

class SupplierEvaluationMasterDetailsAPIController extends AppBaseController
{
    /** @var  SupplierEvaluationMasterDetailsRepository */
    private $supplierEvaluationMasterDetailsRepository;

    public function __construct(SupplierEvaluationMasterDetailsRepository $supplierEvaluationMasterDetailsRepo)
    {
        $this->supplierEvaluationMasterDetailsRepository = $supplierEvaluationMasterDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationMasterDetails",
     *      summary="getSupplierEvaluationMasterDetailsList",
     *      tags={"SupplierEvaluationMasterDetails"},
     *      description="Get all SupplierEvaluationMasterDetails",
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
     *                  @OA\Items(ref="#/definitions/SupplierEvaluationMasterDetails")
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
        $this->supplierEvaluationMasterDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierEvaluationMasterDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepository->all();

        return $this->sendResponse($supplierEvaluationMasterDetails->toArray(), 'Supplier Evaluation Master Details retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierEvaluationMasterDetails",
     *      summary="createSupplierEvaluationMasterDetails",
     *      tags={"SupplierEvaluationMasterDetails"},
     *      description="Create SupplierEvaluationMasterDetails",
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
     *                  ref="#/definitions/SupplierEvaluationMasterDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierEvaluationMasterDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $supplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepository->create($input);

        return $this->sendResponse($supplierEvaluationMasterDetails->toArray(), 'Supplier Evaluation Master Details saved successfully');
    }

    public function getAllSupplierEvaluationDetails(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $masterID = $input['master_id'];


        $supplierEvaluationDetails = SupplierEvaluationMasterDetails::where('master_id',$masterID);



        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $supplierEvaluationDetails = $supplierEvaluationDetails->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($supplierEvaluationDetails)
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

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationMasterDetails/{id}",
     *      summary="getSupplierEvaluationMasterDetailsItem",
     *      tags={"SupplierEvaluationMasterDetails"},
     *      description="Get SupplierEvaluationMasterDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationMasterDetails",
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
     *                  ref="#/definitions/SupplierEvaluationMasterDetails"
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
        /** @var SupplierEvaluationMasterDetails $supplierEvaluationMasterDetails */
        $supplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepository->findWithoutFail($id);

        if (empty($supplierEvaluationMasterDetails)) {
            return $this->sendError('Supplier Evaluation Master Details not found');
        }

        return $this->sendResponse($supplierEvaluationMasterDetails->toArray(), 'Supplier Evaluation Master Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/supplierEvaluationMasterDetails/{id}",
     *      summary="updateSupplierEvaluationMasterDetails",
     *      tags={"SupplierEvaluationMasterDetails"},
     *      description="Update SupplierEvaluationMasterDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationMasterDetails",
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
     *                  ref="#/definitions/SupplierEvaluationMasterDetails"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierEvaluationMasterDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var SupplierEvaluationMasterDetails $supplierEvaluationMasterDetails */
        $supplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepository->findWithoutFail($id);

        if (empty($supplierEvaluationMasterDetails)) {
            return $this->sendError('Supplier Evaluation Master Details not found');
        }

        $supplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepository->update($input, $id);

        return $this->sendResponse($supplierEvaluationMasterDetails->toArray(), 'SupplierEvaluationMasterDetails updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierEvaluationMasterDetails/{id}",
     *      summary="deleteSupplierEvaluationMasterDetails",
     *      tags={"SupplierEvaluationMasterDetails"},
     *      description="Delete SupplierEvaluationMasterDetails",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationMasterDetails",
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
        /** @var SupplierEvaluationMasterDetails $supplierEvaluationMasterDetails */
        $supplierEvaluationMasterDetails = $this->supplierEvaluationMasterDetailsRepository->findWithoutFail($id);

        if (empty($supplierEvaluationMasterDetails)) {
            return $this->sendError('Supplier Evaluation Master Details not found');
        }

        $supplierEvaluationMasterDetails->delete();

        return $this->sendResponse($id,'Supplier Evaluation Master Details deleted successfully');
    }
}
