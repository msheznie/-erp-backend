<?php
/**
 * =============================================
 * -- File Name : PaySupplierInvoiceMasterReferbackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  PaySupplierInvoiceMaster
 * -- Author : Mohamed Mubashir
 * -- Create date : 26 - Novemeber 2018
 * -- Description : This file contains the all CRUD for Pay Supplier Invoice Master Referback
 * -- REVISION HISTORY
 * -- Date: 26 - Novemeber 2018 By:Mubashir Description: Added new functions named as getAllPaymentVoucherAmendHistory(),paymentVoucherHistoryByPVID()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePaySupplierInvoiceMasterReferbackAPIRequest;
use App\Http\Requests\API\UpdatePaySupplierInvoiceMasterReferbackAPIRequest;
use App\Models\PaySupplierInvoiceMasterReferback;
use App\Repositories\PaySupplierInvoiceMasterReferbackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PaySupplierInvoiceMasterReferbackController
 * @package App\Http\Controllers\API
 */

class PaySupplierInvoiceMasterReferbackAPIController extends AppBaseController
{
    /** @var  PaySupplierInvoiceMasterReferbackRepository */
    private $paySupplierInvoiceMasterReferbackRepository;

    public function __construct(PaySupplierInvoiceMasterReferbackRepository $paySupplierInvoiceMasterReferbackRepo)
    {
        $this->paySupplierInvoiceMasterReferbackRepository = $paySupplierInvoiceMasterReferbackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceMasterReferbacks",
     *      summary="Get a listing of the PaySupplierInvoiceMasterReferbacks.",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Get all PaySupplierInvoiceMasterReferbacks",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/PaySupplierInvoiceMasterReferback")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->paySupplierInvoiceMasterReferbackRepository->pushCriteria(new RequestCriteria($request));
        $this->paySupplierInvoiceMasterReferbackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $paySupplierInvoiceMasterReferbacks = $this->paySupplierInvoiceMasterReferbackRepository->all();

        return $this->sendResponse($paySupplierInvoiceMasterReferbacks->toArray(), trans('custom.pay_supplier_invoice_master_referbacks_retrieved_s'));
    }

    /**
     * @param CreatePaySupplierInvoiceMasterReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/paySupplierInvoiceMasterReferbacks",
     *      summary="Store a newly created PaySupplierInvoiceMasterReferback in storage",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Store PaySupplierInvoiceMasterReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceMasterReferback that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceMasterReferback")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PaySupplierInvoiceMasterReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePaySupplierInvoiceMasterReferbackAPIRequest $request)
    {
        $input = $request->all();

        $paySupplierInvoiceMasterReferbacks = $this->paySupplierInvoiceMasterReferbackRepository->create($input);

        return $this->sendResponse($paySupplierInvoiceMasterReferbacks->toArray(), trans('custom.pay_supplier_invoice_master_referback_saved_succes'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/paySupplierInvoiceMasterReferbacks/{id}",
     *      summary="Display the specified PaySupplierInvoiceMasterReferback",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Get PaySupplierInvoiceMasterReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMasterReferback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PaySupplierInvoiceMasterReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var PaySupplierInvoiceMasterReferback $paySupplierInvoiceMasterReferback */
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            return $this->sendError(trans('custom.pay_supplier_invoice_master_referback_not_found'));
        }

        return $this->sendResponse($paySupplierInvoiceMasterReferback->toArray(), trans('custom.pay_supplier_invoice_master_referback_retrieved_su'));
    }

    /**
     * @param int $id
     * @param UpdatePaySupplierInvoiceMasterReferbackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/paySupplierInvoiceMasterReferbacks/{id}",
     *      summary="Update the specified PaySupplierInvoiceMasterReferback in storage",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Update PaySupplierInvoiceMasterReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMasterReferback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PaySupplierInvoiceMasterReferback that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PaySupplierInvoiceMasterReferback")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/PaySupplierInvoiceMasterReferback"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePaySupplierInvoiceMasterReferbackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PaySupplierInvoiceMasterReferback $paySupplierInvoiceMasterReferback */
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            return $this->sendError(trans('custom.pay_supplier_invoice_master_referback_not_found'));
        }

        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->update($input, $id);

        return $this->sendResponse($paySupplierInvoiceMasterReferback->toArray(), trans('custom.paysupplierinvoicemasterreferback_updated_successf'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/paySupplierInvoiceMasterReferbacks/{id}",
     *      summary="Remove the specified PaySupplierInvoiceMasterReferback from storage",
     *      tags={"PaySupplierInvoiceMasterReferback"},
     *      description="Delete PaySupplierInvoiceMasterReferback",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PaySupplierInvoiceMasterReferback",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var PaySupplierInvoiceMasterReferback $paySupplierInvoiceMasterReferback */
        $paySupplierInvoiceMasterReferback = $this->paySupplierInvoiceMasterReferbackRepository->findWithoutFail($id);

        if (empty($paySupplierInvoiceMasterReferback)) {
            return $this->sendError(trans('custom.pay_supplier_invoice_master_referback_not_found'));
        }

        $paySupplierInvoiceMasterReferback->delete();

        return $this->sendResponse($id, trans('custom.pay_supplier_invoice_master_referback_deleted_succ'));
    }

    public function getAllPaymentVoucherAmendHistory(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyID'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $paymentVoucher = PaySupplierInvoiceMasterReferback::with(['supplier', 'created_by', 'suppliercurrency', 'bankcurrency'])->wherepaymasterautoid($input['PayMasterAutoId']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $paymentVoucher = $paymentVoucher->where(function ($query) use ($search) {
                $query->where('BPVcode', 'LIKE', "%{$search}%")
                    ->orWhere('BPVNarration', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($paymentVoucher)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('PayMasterAutoRefferedBackID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }


    public function paymentVoucherHistoryByPVID(Request $request){

        $paySupplierInvoiceMaster = PaySupplierInvoiceMasterReferback::with(['confirmed_by', 'bankaccount','supplier','suppliercurrency', 'financeperiod_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'financeyear_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->find($request->PayMasterAutoId);

        if (empty($paySupplierInvoiceMaster)) {
            return $this->sendError(trans('custom.pay_supplier_invoice_master_not_found'));
        }

        return $this->sendResponse($paySupplierInvoiceMaster->toArray(), trans('custom.pay_supplier_invoice_master_retrieved_successfully'));
    }



}
