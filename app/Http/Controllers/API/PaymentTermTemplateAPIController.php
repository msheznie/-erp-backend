<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentTermTemplateAPIRequest;
use App\Http\Requests\API\UpdatePaymentTermTemplateAPIRequest;
use App\Models\PaymentTermTemplate;
use App\Models\PaymentTermConfig;
use App\Models\ProcumentOrder;
use App\Repositories\PaymentTermTemplateRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use DB;

/**
 * Class PaymentTermTemplateController
 * @package App\Http\Controllers\API
 */

class PaymentTermTemplateAPIController extends AppBaseController
{
    /** @var  PaymentTermTemplateRepository */
    private $paymentTermTemplateRepository;

    public function __construct(PaymentTermTemplateRepository $paymentTermTemplateRepo)
    {
        $this->paymentTermTemplateRepository = $paymentTermTemplateRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/paymentTermTemplates",
     *      summary="getPaymentTermTemplateList",
     *      tags={"PaymentTermTemplate"},
     *      description="Get all PaymentTermTemplates",
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
     *                  @OA\Items(ref="#/definitions/PaymentTermTemplate")
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
        $this->paymentTermTemplateRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentTermTemplateRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentTermTemplates = $this->paymentTermTemplateRepository->all();

        return $this->sendResponse($paymentTermTemplates->toArray(), 'Payment Term Templates retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/paymentTermTemplates",
     *      summary="createPaymentTermTemplate",
     *      tags={"PaymentTermTemplate"},
     *      description="Create PaymentTermTemplate",
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
     *                  ref="#/definitions/PaymentTermTemplate"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentTermTemplateAPIRequest $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'templateName' => 'required|string|max:25',
            'description' => 'required|string|max:25',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $checkTemplate = PaymentTermTemplate::where('templateName', $input['templateName'])->first();

        if ($checkTemplate) {
            return $this->sendError('Template name already exists.');
        }

        $paymentTermTemplate = $this->paymentTermTemplateRepository->create($input);

        $this->insertPreDefinedConfigTerms($paymentTermTemplate->id);

        return $this->sendResponse($paymentTermTemplate->toArray(), 'Payment term Template saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/paymentTermTemplates/{id}",
     *      summary="getPaymentTermTemplateItem",
     *      tags={"PaymentTermTemplate"},
     *      description="Get PaymentTermTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermTemplate",
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
     *                  ref="#/definitions/PaymentTermTemplate"
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
        /** @var PaymentTermTemplate $paymentTermTemplate */
        $paymentTermTemplate = $this->paymentTermTemplateRepository->findWithoutFail($id);

        if (empty($paymentTermTemplate)) {
            return $this->sendError('Payment Term Template not found');
        }

        return $this->sendResponse($paymentTermTemplate->toArray(), 'Payment Term Template retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/paymentTermTemplates/{id}",
     *      summary="updatePaymentTermTemplate",
     *      tags={"PaymentTermTemplate"},
     *      description="Update PaymentTermTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermTemplate",
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
     *                  ref="#/definitions/PaymentTermTemplate"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentTermTemplateAPIRequest $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'templateName' => 'required|string|max:25',
            'description' => 'required|string|max:25',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $checkTemplate = PaymentTermTemplate::where('templateName', $input['templateName'])->where('id', '!=', $id)->first();

        if ($checkTemplate) {
            return $this->sendError('Template name already exists.');
        }

        $paymentTermTemplate = $this->paymentTermTemplateRepository->findWithoutFail($id);

        if (empty($paymentTermTemplate)) {
            return $this->sendError('Payment Term Template not found');
        }

        $paymentTermTemplate = $this->paymentTermTemplateRepository->update($input, $id);

        return $this->sendResponse($paymentTermTemplate->toArray(), 'PaymentTermTemplate updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/paymentTermTemplates/{id}",
     *      summary="deletePaymentTermTemplate",
     *      tags={"PaymentTermTemplate"},
     *      description="Delete PaymentTermTemplate",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermTemplate",
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
        /** @var PaymentTermTemplate $paymentTermTemplate */
        $paymentTermTemplate = $this->paymentTermTemplateRepository->findWithoutFail($id);

        if (empty($paymentTermTemplate)) {
            return $this->sendError('Payment Term Template not found');
        }

        $templatePulledPO = \DB::table('po_wise_payment_term_config')->where('templateID', $id)
            ->pluck('purchaseOrderID')->unique()->values()->all();

        $pendingApprovalCount = ProcumentOrder::whereIn('purchaseOrderID', $templatePulledPO)
            ->where('poConfirmedYN', 1)
            ->where('approved', 0)
            ->where('refferedBackYN', 0)
            ->count();

        if ($pendingApprovalCount > 0) {
            return $this->sendError('The template has already been applied to certain purchase orders that are pending for approval.', 500);
        }

        $paymentTermTemplate->delete();

        return $this->sendResponse($id, 'Payment Term Template deleted successfully');
    }


    public function getAllPaymentTerms(Request $request) {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $paymentTermTemplates =  DB::table('payment_term_templates');

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $paymentTermTemplates = $paymentTermTemplates->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($paymentTermTemplates)
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

    public function paymentTermDefaultTemplateUpdate($id, UpdatePaymentTermTemplateAPIRequest $request){

        $input = $request->all();

        $paymentTermTemplate = $this->paymentTermTemplateRepository->findWithoutFail($id);

        if (empty($paymentTermTemplate)) {
            return $this->sendError('Payment Term Template not found');
        }

        $defaultTemplate = PaymentTermTemplate::where('isDefault', true)->first();
        if($defaultTemplate) {
            $defaultTempPulledPO = DB::table('po_wise_payment_term_config')->where('templateID', $defaultTemplate->id)
                ->pluck('purchaseOrderID')->unique()->values()->all();

            $pendingApprovalCount = ProcumentOrder::whereIn('purchaseOrderID', $defaultTempPulledPO)
                ->where('poConfirmedYN', 1)
                ->where('approved', 0)
                ->where('refferedBackYN', 0)
                ->count();

            if ($pendingApprovalCount > 0) {
                return $this->sendError('The default template has already been applied to certain purchase orders that are pending for approval.', 500);
            }
        }

        PaymentTermTemplate::where('id', '!=', $id)->update(['isDefault' => 0]);

        $input['isActive'] = true;
        $paymentTermTemplate = $this->paymentTermTemplateRepository->update($input, $id);

        return $this->sendResponse($paymentTermTemplate->toArray(), 'Payment Term Template updated successfully');

    }

    public function insertPreDefinedConfigTerms($templateId) {
        $predefinedTerms = [
            'Payment Terms',
            'Delivery Terms',
            'Delivery & shipping',
            'Penalty Terms',
            'Product/Service Specifications',
            'Price and Currency',
            'Taxes and Fees',
            'Warranties and Guarantees',
            'Cancellation and Returns',
            'Limitation of Liability',
            'Confidentiality',
            'Governing Law',
            'Dispute Resolution',
            'Termination',
            'Insurance',
            'Indemnity'
        ];

        $order = 1;
        foreach ($predefinedTerms as $term) {
            $config = new PaymentTermConfig();
            $config->templateId = $templateId;
            $config->term = $term;
            $config->sortOrder = $order;
            $config->save();

            $order++;
        }
    }

}
