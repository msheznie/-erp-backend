<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaymentTermConfigAPIRequest;
use App\Http\Requests\API\UpdatePaymentTermConfigAPIRequest;
use App\Models\PaymentTermConfig;
use App\Models\ProcumentOrder;
use App\Repositories\PaymentTermConfigRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaymentTermConfigController
 * @package App\Http\Controllers\API
 */

class PaymentTermConfigAPIController extends AppBaseController
{
    /** @var  PaymentTermConfigRepository */
    private $paymentTermConfigRepository;

    public function __construct(PaymentTermConfigRepository $paymentTermConfigRepo)
    {
        $this->paymentTermConfigRepository = $paymentTermConfigRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/paymentTermConfigs",
     *      summary="getPaymentTermConfigList",
     *      tags={"PaymentTermConfig"},
     *      description="Get all PaymentTermConfigs",
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
     *                  @OA\Items(ref="#/definitions/PaymentTermConfig")
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
        $this->paymentTermConfigRepository->pushCriteria(new RequestCriteria($request));
        $this->paymentTermConfigRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paymentTermConfigs = $this->paymentTermConfigRepository->all();

        return $this->sendResponse($paymentTermConfigs->toArray(), 'Payment Term Configs retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/paymentTermConfigs",
     *      summary="createPaymentTermConfig",
     *      tags={"PaymentTermConfig"},
     *      description="Create PaymentTermConfig",
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
     *                  ref="#/definitions/PaymentTermConfig"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaymentTermConfigAPIRequest $request)
    {
        $input = $request->all();

        $paymentTermConfig = $this->paymentTermConfigRepository->create($input);

        return $this->sendResponse($paymentTermConfig->toArray(), 'Payment Term Config saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/paymentTermConfigs/{id}",
     *      summary="getPaymentTermConfigItem",
     *      tags={"PaymentTermConfig"},
     *      description="Get PaymentTermConfig",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermConfig",
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
     *                  ref="#/definitions/PaymentTermConfig"
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
        /** @var PaymentTermConfig $paymentTermConfig */
        $paymentTermConfig = $this->paymentTermConfigRepository->findWithoutFail($id);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        return $this->sendResponse($paymentTermConfig->toArray(), 'Payment Term Config retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/paymentTermConfigs/{id}",
     *      summary="updatePaymentTermConfig",
     *      tags={"PaymentTermConfig"},
     *      description="Update PaymentTermConfig",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermConfig",
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
     *                  ref="#/definitions/PaymentTermConfig"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaymentTermConfigAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaymentTermConfig $paymentTermConfig */
        $paymentTermConfig = $this->paymentTermConfigRepository->findWithoutFail($id);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        $paymentTermConfig = $this->paymentTermConfigRepository->update($input, $id);

        return $this->sendResponse($paymentTermConfig->toArray(), 'PaymentTermConfig updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/paymentTermConfigs/{id}",
     *      summary="deletePaymentTermConfig",
     *      tags={"PaymentTermConfig"},
     *      description="Delete PaymentTermConfig",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of PaymentTermConfig",
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
        /** @var PaymentTermConfig $paymentTermConfig */
        $paymentTermConfig = $this->paymentTermConfigRepository->findWithoutFail($id);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        $paymentTermConfig->delete();

        return $this->sendSuccess('Payment Term Config deleted successfully');
    }

    public function getAllPaymentTermConfigs(Request $request) {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $paymentTermTemplateConfigs =  PaymentTermConfig::where('templateId', $input['templateId']);

        return \DataTables::of($paymentTermTemplateConfigs)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('sortOrder', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function configDescriptionUpdate($id, Request $request){

        $input = $request->all();

        $paymentTermConfig = $this->paymentTermConfigRepository->findWithoutFail($id);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        $paymentTermConfig = $this->paymentTermConfigRepository->update($input, $id);

        return $this->sendResponse($paymentTermConfig->toArray(), 'Description updated successfully');

    }

    public function deleteConfigDescription(Request $request){

        $configId = $request->configId;

        $paymentTermConfig = $this->paymentTermConfigRepository->findWithoutFail($configId);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        $templateID = $paymentTermConfig['templateId'];
        $templatePulledPO = \DB::table('po_wise_payment_term_config')->where('templateID', $templateID)
            ->pluck('purchaseOrderID')->unique()->values()->all();

        $pendingApprovalCount = ProcumentOrder::whereIn('purchaseOrderID', $templatePulledPO)
            ->where('approved', 0)
            ->count();

        if ($pendingApprovalCount > 0) {
            return $this->sendError('The template has already been applied to certain purchase orders.', 500);
        }
        $paymentTermConfig = PaymentTermConfig::where('id', $configId)->update(['description' => '']);

        return $this->sendResponse($paymentTermConfig, 'Description removed successfully');

    }

    public function updateConfigSelection(Request $request){

        $input = $request->all();

        $paymentTermConfig = $this->paymentTermConfigRepository->findWithoutFail($input['id']);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        $paymentTermConfig = PaymentTermConfig::where('id', $input['id'])->update(['isSelected' => $input['isSelected']]);

        return $this->sendResponse($paymentTermConfig, 'Payment term config updated successfully');

    }

    public function updateSortOrder(Request $request){

        $input = $request->all();

        $paymentTermConfig = $this->paymentTermConfigRepository->findWithoutFail($input['id']);

        if (empty($paymentTermConfig)) {
            return $this->sendError('Payment Term Config not found');
        }

        $paymentTermConfig = PaymentTermConfig::where('id', $input['id'])->update(['sortOrder' => $input['sortOrder']]);

        return $this->sendResponse($paymentTermConfig, 'Payment term config sort order updated successfully');

    }
}
