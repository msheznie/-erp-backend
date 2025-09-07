<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePurchaseReturnMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdatePurchaseReturnMasterRefferedBackAPIRequest;
use App\Models\PurchaseReturnMasterRefferedBack;
use App\Repositories\PurchaseReturnMasterRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class PurchaseReturnMasterRefferedBackController
 * @package App\Http\Controllers\API
 */

class PurchaseReturnMasterRefferedBackAPIController extends AppBaseController
{
    /** @var  PurchaseReturnMasterRefferedBackRepository */
    private $purchaseReturnMasterRefferedBackRepository;

    public function __construct(PurchaseReturnMasterRefferedBackRepository $purchaseReturnMasterRefferedBackRepo)
    {
        $this->purchaseReturnMasterRefferedBackRepository = $purchaseReturnMasterRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnMasterRefferedBacks",
     *      summary="Get a listing of the PurchaseReturnMasterRefferedBacks.",
     *      tags={"PurchaseReturnMasterRefferedBack"},
     *      description="Get all PurchaseReturnMasterRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/PurchaseReturnMasterRefferedBack")
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
        $this->purchaseReturnMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->purchaseReturnMasterRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $purchaseReturnMasterRefferedBacks = $this->purchaseReturnMasterRefferedBackRepository->all();

        return $this->sendResponse($purchaseReturnMasterRefferedBacks->toArray(), trans('custom.purchase_return_master_reffered_backs_retrieved_su'));
    }

    /**
     * @param CreatePurchaseReturnMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/purchaseReturnMasterRefferedBacks",
     *      summary="Store a newly created PurchaseReturnMasterRefferedBack in storage",
     *      tags={"PurchaseReturnMasterRefferedBack"},
     *      description="Store PurchaseReturnMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnMasterRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnMasterRefferedBack")
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
     *                  ref="#/definitions/PurchaseReturnMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreatePurchaseReturnMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $purchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepository->create($input);

        return $this->sendResponse($purchaseReturnMasterRefferedBack->toArray(), trans('custom.purchase_return_master_reffered_back_saved_success'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/purchaseReturnMasterRefferedBacks/{id}",
     *      summary="Display the specified PurchaseReturnMasterRefferedBack",
     *      tags={"PurchaseReturnMasterRefferedBack"},
     *      description="Get PurchaseReturnMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnMasterRefferedBack",
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
     *                  ref="#/definitions/PurchaseReturnMasterRefferedBack"
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
        /** @var PurchaseReturnMasterRefferedBack $purchaseReturnMasterRefferedBack */
        $purchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepository->with(['created_by', 'confirmed_by', 'segment_by', 'location_by', 'currency_by', 'supplier_by', 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }, 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }])->findWithoutFail($id);

        if (empty($purchaseReturnMasterRefferedBack)) {
            return $this->sendError(trans('custom.purchase_return_master_reffered_back_not_found'));
        }

        return $this->sendResponse($purchaseReturnMasterRefferedBack->toArray(), trans('custom.purchase_return_master_reffered_back_retrieved_suc'));
    }

    /**
     * @param int $id
     * @param UpdatePurchaseReturnMasterRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/purchaseReturnMasterRefferedBacks/{id}",
     *      summary="Update the specified PurchaseReturnMasterRefferedBack in storage",
     *      tags={"PurchaseReturnMasterRefferedBack"},
     *      description="Update PurchaseReturnMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnMasterRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="PurchaseReturnMasterRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/PurchaseReturnMasterRefferedBack")
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
     *                  ref="#/definitions/PurchaseReturnMasterRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdatePurchaseReturnMasterRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var PurchaseReturnMasterRefferedBack $purchaseReturnMasterRefferedBack */
        $purchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($purchaseReturnMasterRefferedBack)) {
            return $this->sendError(trans('custom.purchase_return_master_reffered_back_not_found'));
        }

        $purchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepository->update($input, $id);

        return $this->sendResponse($purchaseReturnMasterRefferedBack->toArray(), trans('custom.purchasereturnmasterrefferedback_updated_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/purchaseReturnMasterRefferedBacks/{id}",
     *      summary="Remove the specified PurchaseReturnMasterRefferedBack from storage",
     *      tags={"PurchaseReturnMasterRefferedBack"},
     *      description="Delete PurchaseReturnMasterRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of PurchaseReturnMasterRefferedBack",
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
        /** @var PurchaseReturnMasterRefferedBack $purchaseReturnMasterRefferedBack */
        $purchaseReturnMasterRefferedBack = $this->purchaseReturnMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($purchaseReturnMasterRefferedBack)) {
            return $this->sendError(trans('custom.purchase_return_master_reffered_back_not_found'));
        }

        $purchaseReturnMasterRefferedBack->delete();

        return $this->sendSuccess('Purchase Return Master Reffered Back deleted successfully');
    }


    public function getPurchaseReturnAmendHistory(Request $request)
    {
        $input = $request->all();

        $prHistory = PurchaseReturnMasterRefferedBack::where('purhaseReturnAutoID', $input['purhaseReturnAutoID'])
                        ->with(['created_by','confirmed_by','modified_by','supplier_by','segment_by', 'currency_by', 'location_by'])
                        ->get();

        return $this->sendResponse($prHistory, trans('custom.purchase_return_reffered_back_data_retrieved_succe'));
    }
}
