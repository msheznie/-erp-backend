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
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
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

        return $this->sendResponse($currencyConversions->toArray(), 'Currency Conversions retrieved successfully');
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

        return $this->sendResponse($currencyConversions->toArray(), 'Currency Conversion saved successfully');
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
            return $this->sendError('Currency Conversion not found');
        }

        return $this->sendResponse($currencyConversion->toArray(), 'Currency Conversion retrieved successfully');
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
            return $this->sendError('Base currency Conversion not found', 500);
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
                return $this->sendError('Sub currency Conversion not found', 500);
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
        return $this->sendResponse($currencyConversion->toArray(), 'Currency conversion updated successfully');
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
            return $this->sendError('Currency Conversion not found');
        }

        $currencyConversion->delete();

        return $this->sendResponse($id, 'Currency Conversion deleted successfully');
    }

    public function updateCrossExchange(Request $request)
    {

        $input = $request->all();
        $currency = isset($input['currency']) ? $input['currency'] : 0;

        $currencyMaster = CurrencyMaster::find($currency);

        if (empty($currencyMaster)) {
            return $this->sendError('Currency Master not found');
        }

        $checkExchanges = $this->currencyConversionRepository->findWhere(['masterCurrencyID' => $currency, ['conversion', '<=', 0]]);

        if ($checkExchanges->count() > 0) {
            return $this->sendError('This currency has pending currency exchange rate to update. please contact IT Team to update.', 500);
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
            return $this->sendResponse($arrayNew, 'Cross exchange updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError($e->getMessage(), 500);
        }
    }

    public function currencyConvert(Request $request)
    {
        $input = $request->all();
        $currencyConversion = Helper::currencyConversion(null, $input['transactionCurrencyID'], $input['documentCurrencyID'], $input['transactionAmount']);

        return $this->sendResponse($currencyConversion, 'Cross exchange updated successfully');
    }
}
