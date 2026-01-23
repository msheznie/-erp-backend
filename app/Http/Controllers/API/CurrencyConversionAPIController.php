<?php
/**
 * =============================================
 * -- File Name : CurrencyConversionAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Finance
 * -- Author : Mubashir
 * -- Create date : 14 - March 2018
 * -- Description : This file contains the all CRUD for Currency Conversion.
 * -- REVISION HISTORY
 * --
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateCurrencyConversionAPIRequest;
use App\Http\Requests\API\UpdateCurrencyConversionAPIRequest;
use App\Models\CurrencyConversion;
use App\Models\CurrencyConversionHistory;
use App\Models\CurrencyMaster;
use App\Repositories\CurrencyConversionHistoryRepository;
use App\Repositories\CurrencyConversionRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CurrencyConversionController
 * @package App\Http\Controllers\API
 */
class CurrencyConversionAPIController extends AppBaseController
{
    /** @var  CurrencyConversionRepository */
    private $currencyConversionRepository;
    private $currencyConversionHistoryRepository;

    public function __construct(CurrencyConversionRepository $currencyConversionRepo, CurrencyConversionHistoryRepository $currencyConversionHistoryRepo)
    {
        $this->currencyConversionRepository = $currencyConversionRepo;
        $this->currencyConversionHistoryRepository = $currencyConversionHistoryRepo;
    }

    /**
     * Display a listing of the CurrencyConversion.
     * GET|HEAD /currencyConversions
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->currencyConversionRepository->pushCriteria(new RequestCriteria($request));
        $this->currencyConversionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $currencyConversions = $this->currencyConversionRepository->all();

        return $this->sendResponse($currencyConversions->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.currency_conversions')]));
    }

    /**
     * Store a newly created CurrencyConversion in storage.
     * POST /currencyConversions
     *
     * @param CreateCurrencyConversionAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCurrencyConversionAPIRequest $request)
    {
        $input = $request->all();

        $currencyConversions = $this->currencyConversionRepository->create($input);

        return $this->sendResponse($currencyConversions->toArray(), trans('custom.save', ['attribute' => trans('custom.currency_conversions')]));
    }

    /**
     * Display the specified CurrencyConversion.
     * GET|HEAD /currencyConversions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CurrencyConversion $currencyConversion */
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_conversions')]));
        }

        return $this->sendResponse($currencyConversion->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.currency_conversions')]));
    }

    /**
     * Update the specified CurrencyConversion in storage.
     * PUT/PATCH /currencyConversions/{id}
     *
     * @param int $id
     * @param UpdateCurrencyConversionAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCurrencyConversionAPIRequest $request)
    {
        $input = $request->all();

        /** @var CurrencyConversion $currencyConversion */
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
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
            $this->currencyConversionRepository->update(array_only($input, ['conversion']), $id);

            $subCurrency = $this->currencyConversionRepository
                ->findWhere(['masterCurrencyID' => $currencyConversion->subCurrencyID,
                    'subCurrencyID' => $currencyConversion->masterCurrencyID])
                ->first();

            if (empty($subCurrency)) {
                return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.sub_currency_conversion')]), 500);
            }

            $this->currencyConversionRepository->update(['conversion' => $subConversion], $subCurrency->currencyConversionAutoID);


            if ($currencyConversion->conversion != $input['conversion']) {
                $serialNo = CurrencyConversionHistory::max('serialNo') + 1;
                $temData = array(
                    'serialNo' => $serialNo,
                    'masterCurrencyID' => $currencyConversion->masterCurrencyID,
                    'subCurrencyID' => $currencyConversion->subCurrencyID,
                    'conversion' => $currencyConversion->conversion,
                    'createdBy' => Helper::getEmployeeName(),
                    'createdUserID' => Helper::getEmployeeID(),
                    'createdpc' => gethostname()
                );
                $this->currencyConversionHistoryRepository->create($temData);
                $temData1 = array(
                    'serialNo' => $serialNo,
                    'masterCurrencyID' => $subCurrency->masterCurrencyID,
                    'subCurrencyID' => $subCurrency->subCurrencyID,
                    'conversion' => $subCurrency->conversion,
                    'createdBy' => Helper::getEmployeeName(),
                    'createdUserID' => Helper::getEmployeeID(),
                    'createdpc' => gethostname()
                );
                $this->currencyConversionHistoryRepository->create($temData1);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
        return $this->sendResponse($currencyConversion->toArray(), trans('custom.update', ['attribute' => trans('custom.currency_conversions')]));
    }

    /**
     * Remove the specified CurrencyConversion from storage.
     * DELETE /currencyConversions/{id}
     *
     * @param int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CurrencyConversion $currencyConversion */
        $currencyConversion = $this->currencyConversionRepository->findWithoutFail($id);

        if (empty($currencyConversion)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_conversions')]));
        }

        $currencyConversion->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.currency_conversions')]));
    }

    public function updateCrossExchange(Request $request)
    {

        $input = $request->all();
        $currency = isset($input['currency']) ? $input['currency'] : 0;

        $currencyMaster = CurrencyMaster::find($currency);

        if (empty($currencyMaster)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.currency_master')]));
        }

        $checkExchanges = $this->currencyConversionRepository->findWhere(['masterCurrencyID' => $currency, ['conversion', '<=', 0]]);

        if ($checkExchanges->count() > 0) {
            return $this->sendError(trans('custom.this_currency_has_pending_currency_exchange_rate_to_update_please_contact_it_team_to_update'), 500);
        }
        DB::beginTransaction();
        try {
            $master = $this->currencyConversionRepository->findWhere(['masterCurrencyID' => $currency, ['subCurrencyID', '!=', $currency]]);

            $serialNo = CurrencyConversionHistory::max('serialNo') + 1;
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
                        'conversion' => $exchangeRate);
                    array_push($globalArray, $newConversion);
                }
            }

            $historyData = $this->currencyConversionRepository->findWhere([['masterCurrencyID' ,'!=', $currency], ['subCurrencyID', '!=', $currency]]);
            foreach ($historyData as $value) {
                $temData = array(
                    'serialNo' => $serialNo,
                    'masterCurrencyID' => $value->masterCurrencyID,
                    'subCurrencyID' => $value->subCurrencyID,
                    'conversion' => $value->conversion,
                    'createdBy' => $createdBy,
                    'createdUserID' => $createdUserID,
                    'createdpc' => $createdPc
                );
                $this->currencyConversionHistoryRepository->create($temData);
                $this->currencyConversionRepository->delete($value->currencyConversionAutoID);
            }
            $arrayNew = array();
            foreach ($globalArray as $value) {
               $new = $this->currencyConversionRepository->create($value);
               array_push($arrayNew,$new);
            }
            DB::commit();
            return $this->sendResponse($arrayNew, trans('custom.update', ['attribute' => trans('custom.cross_exchange')]));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function currencyConvert(Request $request)
    {
        $input = $request->all();
        $currencyConversion = Helper::currencyConversion(null, $input['transactionCurrencyID'], $input['documentCurrencyID'], $input['transactionAmount']);

        $currencyConversion['DecimalPlaces'] = Helper::getCurrencyDecimalPlace($input['documentCurrencyID']);

        return $this->sendResponse($currencyConversion, trans('custom.update', ['attribute' => trans('custom.cross_exchange')]));
    }
}
