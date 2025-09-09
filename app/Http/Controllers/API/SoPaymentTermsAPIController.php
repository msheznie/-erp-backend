<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateSoPaymentTermsAPIRequest;
use App\Http\Requests\API\UpdateSoPaymentTermsAPIRequest;
use App\Models\PoPaymentTerms;
use App\Models\ProcumentOrder;
use App\Models\PurchaseOrderDetails;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\SalesOrderAdvPayment;
use App\Models\SoPaymentTerms;
use App\Repositories\SoPaymentTermsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class SoPaymentTermsController
 * @package App\Http\Controllers\API
 */

class SoPaymentTermsAPIController extends AppBaseController
{
    /** @var  SoPaymentTermsRepository */
    private $soPaymentTermsRepository;

    public function __construct(SoPaymentTermsRepository $soPaymentTermsRepo)
    {
        $this->soPaymentTermsRepository = $soPaymentTermsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/soPaymentTerms",
     *      summary="Get a listing of the SoPaymentTerms.",
     *      tags={"SoPaymentTerms"},
     *      description="Get all SoPaymentTerms",
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
     *                  @SWG\Items(ref="#/definitions/SoPaymentTerms")
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
        $this->soPaymentTermsRepository->pushCriteria(new RequestCriteria($request));
        $this->soPaymentTermsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $soPaymentTerms = $this->soPaymentTermsRepository->all();

        return $this->sendResponse($soPaymentTerms->toArray(), trans('custom.so_payment_terms_retrieved_successfully'));
    }

    /**
     * @param CreateSoPaymentTermsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/soPaymentTerms",
     *      summary="Store a newly created SoPaymentTerms in storage",
     *      tags={"SoPaymentTerms"},
     *      description="Store SoPaymentTerms",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SoPaymentTerms that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SoPaymentTerms")
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
     *                  ref="#/definitions/SoPaymentTerms"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateSoPaymentTermsAPIRequest $request)
    {
        $input = $request->all();
        $salesOrderID = isset($input['soID'])?$input['soID']:0;

        if (isset($input['comDate'])) {
            if ($input['comDate']) {
                $input['comDate'] = new Carbon($input['comDate']);
            }
        }

        if (isset($input['LCPaymentYNR'])) {
            $input['LCPaymentYN'] = $input['LCPaymentYNR'];
        }

        $detailExist = QuotationDetails::where('quotationMasterID', $salesOrderID)->count();

        if (!$detailExist) {
            return $this->sendError('At least one item should added to create payment term');
        }

        $salesOrder = QuotationMaster::with(['customer'])->find($salesOrderID);

        if (empty($salesOrder)) {
            return $this->sendError(trans('custom.sales_order_not_found'));
        }

        $input['inDays'] = (isset($salesOrder->customer->creditDays) && $salesOrder->customer->creditDays > 0) ? $salesOrder->customer->creditDays : 0;

        /*if (!empty($purchaseOrder->createdDateTime) && !empty($purchaseOrder->creditPeriod)) {
            $addedDate = strtotime("+$purchaseOrder->creditPeriod day", strtotime($purchaseOrder->createdDateTime));
            $input['comDate'] = date("Y-m-d", $addedDate);
        } else {
            $input['comDate'] = $purchaseOrder->createdDateTime;
        }*/

        $input['comDate'] = $salesOrder->createdDateTime;

        if ($input['LCPaymentYN'] == 1) {
            $input['paymentTemDes'] = 'Payment In';
        } else if ($input['LCPaymentYN'] == 2) {
            $input['paymentTemDes'] = 'Advance Payment';
        }

        $soPaymentTerms = $this->soPaymentTermsRepository->create($input);

        return $this->sendResponse($soPaymentTerms->toArray(), trans('custom.payment_terms_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/soPaymentTerms/{id}",
     *      summary="Display the specified SoPaymentTerms",
     *      tags={"SoPaymentTerms"},
     *      description="Get SoPaymentTerms",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SoPaymentTerms",
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
     *                  ref="#/definitions/SoPaymentTerms"
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
        /** @var SoPaymentTerms $soPaymentTerms */
        $soPaymentTerms = $this->soPaymentTermsRepository->findWithoutFail($id);

        if (empty($soPaymentTerms)) {
            return $this->sendError(trans('custom.so_payment_terms_not_found'));
        }

        return $this->sendResponse($soPaymentTerms->toArray(), trans('custom.so_payment_terms_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateSoPaymentTermsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/soPaymentTerms/{id}",
     *      summary="Update the specified SoPaymentTerms in storage",
     *      tags={"SoPaymentTerms"},
     *      description="Update SoPaymentTerms",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SoPaymentTerms",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="SoPaymentTerms that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/SoPaymentTerms")
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
     *                  ref="#/definitions/SoPaymentTerms"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateSoPaymentTermsAPIRequest $request)
    {
        $input = $request->all();

        /** @var SoPaymentTerms $soPaymentTerms */
        $soPaymentTerms = $this->soPaymentTermsRepository->findWithoutFail($id);

        if (empty($soPaymentTerms)) {
            return $this->sendError(trans('custom.payment_terms_not_found'));
        }

        $input = $this->convertArrayToValue($input);

        $salesOrderID = isset($input['soID']) ? $input['soID'] : 0;

        $salesOrder = QuotationMaster::find($salesOrderID);

        if (empty($salesOrder)) {
            return $this->sendError(trans('custom.purchase_order_not_found'));
        }

        $daysIn = $input['inDays'];

        if (!empty($salesOrder->createdDateTime) && $daysIn != 0) {
            $addedDate = strtotime("+$daysIn day", strtotime($salesOrder->createdDateTime));
            $input['comDate'] = date("Y-m-d", $addedDate);
        }

        if (!empty($salesOrder->createdDateTime) && $daysIn == 0) {
            $input['comDate'] = $salesOrder->createdDateTime;
        }


        $soPaymentTerms = $this->soPaymentTermsRepository->update($input, $id);

        return $this->sendResponse($soPaymentTerms->toArray(), trans('custom.paymentterms_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/soPaymentTerms/{id}",
     *      summary="Remove the specified SoPaymentTerms from storage",
     *      tags={"SoPaymentTerms"},
     *      description="Delete SoPaymentTerms",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of SoPaymentTerms",
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
        /** @var SoPaymentTerms $soPaymentTerms */
        $soPaymentTerms = $this->soPaymentTermsRepository->findWithoutFail($id);

        if (empty($soPaymentTerms)) {
            return $this->sendError(trans('custom.payment_terms_not_found'));
        }

        $soPaymentTerms->delete();

        SalesOrderAdvPayment::where('soTermID', $id)->delete();

        return $this->sendResponse([],trans('custom.payment_terms_deleted_successfully'));
    }

    public function getSalesOrderPaymentTerms(Request $request)
    {
        $input = $request->all();
        $input['soID'] = isset($input['soID']) ? $input['soID'] : 0;

        $soAdvancePaymentType = SoPaymentTerms::where('soID', $input['soID'])
            ->orderBy('paymentTermID', 'ASC')
            ->get();

        return $this->sendResponse($soAdvancePaymentType->toArray(), trans('custom.data_retrieved_successfully'));
    }
}
