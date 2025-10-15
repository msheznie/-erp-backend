<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCurrencyConversionDetailAPIRequest;
use App\Http\Requests\API\UpdateCurrencyConversionDetailAPIRequest;
use App\Models\CurrencyConversionDetail;
use App\Models\CurrencyMaster;
use App\Repositories\CurrencyConversionDetailRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;

/**
 * Class CurrencyConversionDetailController
 * @package App\Http\Controllers\API
 */

class CurrencyConversionDetailAPIController extends AppBaseController
{
    /** @var  CurrencyConversionDetailRepository */
    private $currencyConversionDetailRepository;

    public function __construct(CurrencyConversionDetailRepository $currencyConversionDetailRepo)
    {
        $this->currencyConversionDetailRepository = $currencyConversionDetailRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/currencyConversionDetails",
     *      summary="Get a listing of the CurrencyConversionDetails.",
     *      tags={"CurrencyConversionDetail"},
     *      description="Get all CurrencyConversionDetails",
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
     *                  @SWG\Items(ref="#/definitions/CurrencyConversionDetail")
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
        $this->currencyConversionDetailRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyConversionDetailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyConversionDetails = $this->currencyConversionDetailRepository->all();

        return $this->sendResponse($currencyConversionDetails->toArray(), trans('custom.currency_conversion_details_retrieved_successfully'));
    }

    /**
     * @param CreateCurrencyConversionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/currencyConversionDetails",
     *      summary="Store a newly created CurrencyConversionDetail in storage",
     *      tags={"CurrencyConversionDetail"},
     *      description="Store CurrencyConversionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CurrencyConversionDetail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CurrencyConversionDetail")
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
     *                  ref="#/definitions/CurrencyConversionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCurrencyConversionDetailAPIRequest $request)
    {
        $input = $request->all();

        $currencyConversionDetail = $this->currencyConversionDetailRepository->create($input);

        return $this->sendResponse($currencyConversionDetail->toArray(), trans('custom.currency_conversion_detail_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/currencyConversionDetails/{id}",
     *      summary="Display the specified CurrencyConversionDetail",
     *      tags={"CurrencyConversionDetail"},
     *      description="Get CurrencyConversionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionDetail",
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
     *                  ref="#/definitions/CurrencyConversionDetail"
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
        /** @var CurrencyConversionDetail $currencyConversionDetail */
        $currencyConversionDetail = $this->currencyConversionDetailRepository->findWithoutFail($id);

        if (empty($currencyConversionDetail)) {
            return $this->sendError(trans('custom.currency_conversion_detail_not_found'));
        }

        return $this->sendResponse($currencyConversionDetail->toArray(), trans('custom.currency_conversion_detail_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateCurrencyConversionDetailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/currencyConversionDetails/{id}",
     *      summary="Update the specified CurrencyConversionDetail in storage",
     *      tags={"CurrencyConversionDetail"},
     *      description="Update CurrencyConversionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionDetail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CurrencyConversionDetail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CurrencyConversionDetail")
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
     *                  ref="#/definitions/CurrencyConversionDetail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCurrencyConversionDetailAPIRequest $request)
    {
        $input = $request->all();

        /** @var CurrencyConversionDetail $currencyConversionDetail */
        $currencyConversionDetail = $this->currencyConversionDetailRepository->findWithoutFail($id);

        if (empty($currencyConversionDetail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.base_currency_conversion')]), 500);
        }
        $validator = \Validator::make($input, [
            'conversion' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        DB::beginTransaction();
        try {
            if ($input['conversion'] != 0) {
                $subConversion = round(1 / $input['conversion'], 8);
            } else {
                $subConversion = 0;
            }

            $input['conversion'] = round($input['conversion'], 8);
            $this->currencyConversionDetailRepository->update(array_only($input, ['conversion']), $id);

            $subCurrency = $this->currencyConversionDetailRepository
                ->findWhere(['masterCurrencyID' => $currencyConversionDetail->subCurrencyID,
                    'subCurrencyID' => $currencyConversionDetail->masterCurrencyID, 'currencyConversioMasterID' => $currencyConversionDetail->currencyConversioMasterID])
                ->first();

            if (empty($subCurrency)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.sub_currency_conversion')]), 500);
            }

            $this->currencyConversionDetailRepository->update(['conversion' => $subConversion], $subCurrency->currencyConversionDetailAutoID);

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
        return $this->sendResponse($currencyConversionDetail->toArray(), trans('custom.update', ['attribute' => trans('custom.currency_conversions')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/currencyConversionDetails/{id}",
     *      summary="Remove the specified CurrencyConversionDetail from storage",
     *      tags={"CurrencyConversionDetail"},
     *      description="Delete CurrencyConversionDetail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CurrencyConversionDetail",
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
        /** @var CurrencyConversionDetail $currencyConversionDetail */
        $currencyConversionDetail = $this->currencyConversionDetailRepository->findWithoutFail($id);

        if (empty($currencyConversionDetail)) {
            return $this->sendError(trans('custom.currency_conversion_detail_not_found'));
        }

        $currencyConversionDetail->delete();

        return $this->sendSuccess('Currency Conversion Detail deleted successfully');
    }

    public function updateTempCrossExchange(Request $request)
    {

        $input = $request->all();
        $currency = isset($input['currency']) ? $input['currency'] : 0;
        $currencyConversionMasterID = isset($input['currencyConversionMasterID']) ? $input['currencyConversionMasterID'] : 0;

        $currencyMaster = CurrencyMaster::find($currency);

        if (empty($currencyMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_master')]));
        }

        $checkExchanges = $this->currencyConversionDetailRepository->where('masterCurrencyID', $currency)
                                                                   ->where('currencyConversioMasterID', $currencyConversionMasterID)
                                                                   ->where('conversion', '<=',0);

        if ($checkExchanges->count() > 0) {
            return $this->sendError(trans('custom.this_currency_has_pending_currency_exchange_rate_to_update_please_contact_it_team_to_update'), 500);
        }
        DB::beginTransaction();
        try {
            $master = $this->currencyConversionDetailRepository->where('masterCurrencyID', $currency)
                                                               ->where('currencyConversioMasterID', $currencyConversionMasterID)
                                                               ->where('subCurrencyID', '!=',$currency)
                                                               ->get();

            $createdBy = Helper::getEmployeeName();
            $createdUserID = Helper::getEmployeeID();
            $createdPc = gethostname();
            $globalArray = array();
            foreach ($master as $value) {
                for ($i = 0; $i < count($master); $i++) {
                    $exchangeRate = 0;
                    if ($value['conversion'] != 0) {
                        $exchangeRate = $master[$i]['conversion'] / $value['conversion'];
                        $exchangeRate = round($exchangeRate, 8);
                    }
                    $newConversion = array('masterCurrencyID' => $value['subCurrencyID'],
                        'subCurrencyID' => $master[$i]['subCurrencyID'],
                        'conversion' => $exchangeRate,
                        'currencyConversioMasterID' => $currencyConversionMasterID
                    );
                    array_push($globalArray, $newConversion);
                }
            }

            $historyData = CurrencyConversionDetail::where('currencyConversioMasterID', $currencyConversionMasterID)
                                                   ->where('masterCurrencyID', '!=',$currency)
                                                   ->where('subCurrencyID', '!=',$currency)
                                                   ->delete();

            $arrayNew = array();
            foreach ($globalArray as $value) {
               $new = $this->currencyConversionDetailRepository->create($value);
               array_push($arrayNew,$new);
            }
            DB::commit();
            return $this->sendResponse($arrayNew, trans('custom.update', ['attribute' => trans('custom.cross_exchange')]));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }
}
