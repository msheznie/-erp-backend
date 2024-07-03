<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateSupplierEvaluationMastersAPIRequest;
use App\Http\Requests\API\UpdateSupplierEvaluationMastersAPIRequest;
use App\Models\SupplierEvaluationMasters;
use App\Repositories\SupplierEvaluationMastersRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierEvaluationMastersController
 * @package App\Http\Controllers\API
 */

class SupplierEvaluationMastersAPIController extends AppBaseController
{
    /** @var  SupplierEvaluationMastersRepository */
    private $supplierEvaluationMastersRepository;

    public function __construct(SupplierEvaluationMastersRepository $supplierEvaluationMastersRepo)
    {
        $this->supplierEvaluationMastersRepository = $supplierEvaluationMastersRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationMasters",
     *      summary="getSupplierEvaluationMastersList",
     *      tags={"SupplierEvaluationMasters"},
     *      description="Get all SupplierEvaluationMasters",
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
     *                  @OA\Items(ref="#/definitions/SupplierEvaluationMasters")
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
        $this->supplierEvaluationMastersRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierEvaluationMastersRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierEvaluationMasters = $this->supplierEvaluationMastersRepository->all();

        return $this->sendResponse($supplierEvaluationMasters->toArray(), 'Supplier Evaluation Masters retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierEvaluationMasters",
     *      summary="createSupplierEvaluationMasters",
     *      tags={"SupplierEvaluationMasters"},
     *      description="Create SupplierEvaluationMasters",
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
     *                  ref="#/definitions/SupplierEvaluationMasters"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierEvaluationMastersAPIRequest $request)
    {
        $currentUserID = Helper::getEmployeeSystemID();
        $input = $request->all();
        $input['created_by'] = $currentUserID;
        $supplierEvaluationMasters = $this->supplierEvaluationMastersRepository->create($input);

        return $this->sendResponse($supplierEvaluationMasters->toArray(), 'Supplier Evaluation Masters saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationMasters/{id}",
     *      summary="getSupplierEvaluationMastersItem",
     *      tags={"SupplierEvaluationMasters"},
     *      description="Get SupplierEvaluationMasters",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationMasters",
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
     *                  ref="#/definitions/SupplierEvaluationMasters"
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
        /** @var SupplierEvaluationMasters $supplierEvaluationMasters */
        $supplierEvaluationMasters = $this->supplierEvaluationMastersRepository->findWithoutFail($id);

        if (empty($supplierEvaluationMasters)) {
            return $this->sendError('Supplier Evaluation Masters not found');
        }

        return $this->sendResponse($supplierEvaluationMasters->toArray(), 'Supplier Evaluation Masters retrieved successfully');
    }

    public function getAllSupplierEvaluationMasters(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyID = $input['companyID'];


        $supplierEvaluationMasters = SupplierEvaluationMasters::with(['createdBy'])->where('companySystemID',$companyID);



        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $supplierEvaluationMasters = $supplierEvaluationMasters->where(function ($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($supplierEvaluationMasters)
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
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/supplierEvaluationMasters/{id}",
     *      summary="updateSupplierEvaluationMasters",
     *      tags={"SupplierEvaluationMasters"},
     *      description="Update SupplierEvaluationMasters",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationMasters",
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
     *                  ref="#/definitions/SupplierEvaluationMasters"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierEvaluationMastersAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var SupplierEvaluationMasters $supplierEvaluationMasters */
        $supplierEvaluationMasters = $this->supplierEvaluationMastersRepository->findWithoutFail($id);

        if (empty($supplierEvaluationMasters)) {
            return $this->sendError('Supplier Evaluation Masters not found');
        }

        $supplierEvaluationMasters = $this->supplierEvaluationMastersRepository->update($input, $id);

        return $this->sendResponse($supplierEvaluationMasters->toArray(), 'Supplier Evaluation Masters updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierEvaluationMasters/{id}",
     *      summary="deleteSupplierEvaluationMasters",
     *      tags={"SupplierEvaluationMasters"},
     *      description="Delete SupplierEvaluationMasters",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationMasters",
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
        /** @var SupplierEvaluationMasters $supplierEvaluationMasters */
        $supplierEvaluationMasters = $this->supplierEvaluationMastersRepository->findWithoutFail($id);

        if (empty($supplierEvaluationMasters)) {
            return $this->sendError('Supplier Evaluation Masters not found');
        }

        $supplierEvaluationMasters->delete();

        return $this->sendResponse($id,'Supplier Evaluation Masters deleted successfully');
    }
}
