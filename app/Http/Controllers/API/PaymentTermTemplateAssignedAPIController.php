<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentTermTemplateAssignedAPIRequest;
use App\Http\Requests\API\UpdatePaymentTermTemplateAssignedAPIRequest;
use App\Models\PaymentTermTemplateAssigned;
use App\Repositories\PaymentTermTemplateAssignedRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\SupplierCategory;
use App\Models\SupplierMaster;
use App\Models\Company;
use App\Models\PaymentTermTemplate;

/**
 * Class PaymentTermTemplateAssignedController
 * @package App\Http\Controllers\API
 */

class PaymentTermTemplateAssignedAPIController extends AppBaseController
{
    /** @var  PaymentTermTemplateAssignedRepository */
    private $paymentTermTemplateAssignedRepository;

    public function __construct(PaymentTermTemplateAssignedRepository $paymentTermTemplateAssignedRepo)
    {
        $this->paymentTermTemplateAssignedRepository = $paymentTermTemplateAssignedRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/paymentTermTemplateAssigneds",
     *      summary="getPaymentTermTemplateAssignedList",
     *      tags={"PaymentTermTemplateAssigned"},
     *      description="Get all PaymentTermTemplateAssigneds",
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
     *                  @OA\Items(ref="#/definitions/PaymentTermTemplateAssigned")
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
        $this->paymentTermTemplateAssignedRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentTermTemplateAssignedRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentTermTemplateAssigneds = $this->paymentTermTemplateAssignedRepository->all();

        return $this->sendResponse($paymentTermTemplateAssigneds->toArray(), 'Payment Term Template Assigneds retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/paymentTermTemplateAssigneds",
     *      summary="createPaymentTermTemplateAssigned",
     *      tags={"PaymentTermTemplateAssigned"},
     *      description="Create PaymentTermTemplateAssigned",
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
     *                  ref="#/definitions/PaymentTermTemplateAssigned"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentTermTemplateAssignedAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('templateID', 'companySystemID', 'supplierCategoryID'));

        $errorMsg = [];
        $assignSuppliers = [];
        foreach ($input['supplierID'] as $supplier) {
            // Check if the supplier has already been assigned to a template
            $existingAssignment = PaymentTermTemplateAssigned::where('supplierID', $supplier['id'])->first();

            if ($existingAssignment) {
                $errorMsg[] = $existingAssignment->supplier->supplierName . ' has already been assigned to ' . $existingAssignment->template->templateName;
            } else {
                $paymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepository->create([
                    'templateID' => $input['templateID'],
                    'companySystemID' => $input['companySystemID'],
                    'supplierCategoryID' => $input['supplierCategoryID'],
                    'supplierID' => $supplier['id'],
                ]);
                $assignSuppliers[] = $supplier['itemName'];
            }
        }

        if (!empty($errorMsg)) {
            return $this->sendError("The following suppliers have already been assigned to a template.", 500, $errorMsg);
        }

        return $this->sendResponse($assignSuppliers, 'Payment Term Template Assigned successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/paymentTermTemplateAssigneds/{id}",
     *      summary="getPaymentTermTemplateAssignedItem",
     *      tags={"PaymentTermTemplateAssigned"},
     *      description="Get PaymentTermTemplateAssigned",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermTemplateAssigned",
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
     *                  ref="#/definitions/PaymentTermTemplateAssigned"
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
        /** @var PaymentTermTemplateAssigned $paymentTermTemplateAssigned */
        $paymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepository->findWithoutFail($id);

        if (empty($paymentTermTemplateAssigned)) {
            return $this->sendError('Payment Term Template Assigned not found');
        }

        return $this->sendResponse($paymentTermTemplateAssigned->toArray(), 'Payment Term Template Assigned retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/paymentTermTemplateAssigneds/{id}",
     *      summary="updatePaymentTermTemplateAssigned",
     *      tags={"PaymentTermTemplateAssigned"},
     *      description="Update PaymentTermTemplateAssigned",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermTemplateAssigned",
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
     *                  ref="#/definitions/PaymentTermTemplateAssigned"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentTermTemplateAssignedAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentTermTemplateAssigned $paymentTermTemplateAssigned */
        $paymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepository->findWithoutFail($id);

        if (empty($paymentTermTemplateAssigned)) {
            return $this->sendError('Payment Term Template Assigned not found');
        }

        $paymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepository->update($input, $id);

        return $this->sendResponse($paymentTermTemplateAssigned->toArray(), 'PaymentTermTemplateAssigned updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/paymentTermTemplateAssigneds/{id}",
     *      summary="deletePaymentTermTemplateAssigned",
     *      tags={"PaymentTermTemplateAssigned"},
     *      description="Delete PaymentTermTemplateAssigned",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermTemplateAssigned",
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
        /** @var PaymentTermTemplateAssigned $paymentTermTemplateAssigned */
        $paymentTermTemplateAssigned = $this->paymentTermTemplateAssignedRepository->findWithoutFail($id);

        if (empty($paymentTermTemplateAssigned)) {
            return $this->sendError('Payment Term Template Assigned not found');
        }

        $paymentTermTemplateAssigned->delete();

        return $this->sendResponse($id, 'Payment Term Template Assigned deleted successfully');
    }

    public function getSupplierAssignFormData(Request $request)
    {
        $selectedCompanyId = $request['companySystemID'];

        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $companies = Company::whereIn('companySystemID', $subCompanies)
            ->selectRaw('companySystemID as value, CONCAT(CompanyID, " - " ,CompanyName) as label')
            ->get();

        $supplierCategories = SupplierCategory::where('is_active',true)->where('is_deleted',false)
            ->selectRaw('id as value, category as label')
            ->get();

        $output = array(
            'supplierCategories' => $supplierCategories,
            'companies' => $companies,
        );

        return $this->sendResponse($output, 'Records retrieved successfully');
    }

    public function getSupplierList(Request $request)
    {
        $supplierCategoryID = $request['supplierCategoryID'];
        $companySystemID = $request['companySystemID'];

        $suppliers = SupplierMaster::where('supplier_category_id', $supplierCategoryID)
            ->where('primaryCompanySystemID', $companySystemID)
            ->where('isActive', true)
            ->where('isBlocked', false)
            ->get();

        return $this->sendResponse($suppliers, 'Record retrieved successfully');
    }

    public function getAllAssignedSuppliers(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $templateAssignedSuppliers =  PaymentTermTemplateAssigned::with(['company', 'supplierCategory', 'supplier'])->where('templateID', $input['templateID']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $templateAssignedSuppliers = $templateAssignedSuppliers->where(function ($query) use ($search) {
                $query->whereHas('supplier', function ($query) use ($search) {
                    $query->where('supplierName', 'LIKE', "%{$search}%");
                });
            });
        }

        return \DataTables::of($templateAssignedSuppliers)
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

}
