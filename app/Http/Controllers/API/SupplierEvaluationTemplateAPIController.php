<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSupplierEvaluationTemplateAPIRequest;
use App\Http\Requests\API\UpdateSupplierEvaluationTemplateAPIRequest;
use App\Models\SupplierEvaluationTemplate;
use App\Repositories\SupplierEvaluationTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SupplierEvaluationTemplateController
 * @package App\Http\Controllers\API
 */

class SupplierEvaluationTemplateAPIController extends AppBaseController
{
    /** @var  SupplierEvaluationTemplateRepository */
    private $supplierEvaluationTemplateRepository;

    public function __construct(SupplierEvaluationTemplateRepository $supplierEvaluationTemplateRepo)
    {
        $this->supplierEvaluationTemplateRepository = $supplierEvaluationTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTemplates",
     *      summary="getSupplierEvaluationTemplateList",
     *      tags={"SupplierEvaluationTemplate"},
     *      description="Get all SupplierEvaluationTemplates",
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
     *                  @OA\Items(ref="#/definitions/SupplierEvaluationTemplate")
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
        $this->supplierEvaluationTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->supplierEvaluationTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $supplierEvaluationTemplates = $this->supplierEvaluationTemplateRepository->all();

        return $this->sendResponse($supplierEvaluationTemplates->toArray(), 'Supplier Evaluation Templates retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/supplierEvaluationTemplates",
     *      summary="createSupplierEvaluationTemplate",
     *      tags={"SupplierEvaluationTemplate"},
     *      description="Create SupplierEvaluationTemplate",
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
     *                  ref="#/definitions/SupplierEvaluationTemplate"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSupplierEvaluationTemplateAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $supplierEvaluationTemplate = $this->supplierEvaluationTemplateRepository->create($input);

        return $this->sendResponse($supplierEvaluationTemplate->toArray(), 'Supplier Evaluation Template saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/supplierEvaluationTemplates/{id}",
     *      summary="getSupplierEvaluationTemplateItem",
     *      tags={"SupplierEvaluationTemplate"},
     *      description="Get SupplierEvaluationTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplate",
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
     *                  ref="#/definitions/SupplierEvaluationTemplate"
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
        /** @var SupplierEvaluationTemplate $supplierEvaluationTemplate */
        $supplierEvaluationTemplate = $this->supplierEvaluationTemplateRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplate)) {
            return $this->sendError('Supplier Evaluation Template not found');
        }

        return $this->sendResponse($supplierEvaluationTemplate->toArray(), 'Supplier Evaluation Template retrieved successfully');
    }

    public function getAllSupplierEvaluationTemplates(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $companyID = $input['companyID'];


        $supplierEvaluationMasters = SupplierEvaluationTemplate::where('companySystemID',$companyID);



        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $supplierEvaluationMasters = $supplierEvaluationMasters->where(function ($query) use ($search) {
                $query->where('template_name', 'LIKE', "%{$search}%");
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
     *      path="/supplierEvaluationTemplates/{id}",
     *      summary="updateSupplierEvaluationTemplate",
     *      tags={"SupplierEvaluationTemplate"},
     *      description="Update SupplierEvaluationTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplate",
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
     *                  ref="#/definitions/SupplierEvaluationTemplate"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSupplierEvaluationTemplateAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        /** @var SupplierEvaluationTemplate $supplierEvaluationTemplate */
        $supplierEvaluationTemplate = $this->supplierEvaluationTemplateRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplate)) {
            return $this->sendError('Supplier Evaluation Template not found');
        }

        $supplierEvaluationTemplate = $this->supplierEvaluationTemplateRepository->update($input, $id);

        return $this->sendResponse($supplierEvaluationTemplate->toArray(), 'SupplierEvaluationTemplate updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/supplierEvaluationTemplates/{id}",
     *      summary="deleteSupplierEvaluationTemplate",
     *      tags={"SupplierEvaluationTemplate"},
     *      description="Delete SupplierEvaluationTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of SupplierEvaluationTemplate",
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
        /** @var SupplierEvaluationTemplate $supplierEvaluationTemplate */
        $supplierEvaluationTemplate = $this->supplierEvaluationTemplateRepository->findWithoutFail($id);

        if (empty($supplierEvaluationTemplate)) {
            return $this->sendError('Supplier Evaluation Template not found');
        }

        $supplierEvaluationTemplate->delete();

        return $this->sendResponse($id,'Supplier Evaluation Template deleted successfully');
    }
}
