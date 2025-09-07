<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBudgetConsumedDataAPIRequest;
use App\Http\Requests\API\UpdateBudgetConsumedDataAPIRequest;
use App\Models\BudgetConsumedData;
use App\Models\GRVDetails;
use App\Models\CompanyFinanceYear;
use App\Models\Months;
use App\Models\BudgetMaster;
use App\Models\SegmentMaster;
use App\Repositories\BudgetConsumedDataRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/**
 * Class BudgetConsumedDataController
 * @package App\Http\Controllers\API
 */

class BudgetConsumedDataAPIController extends AppBaseController
{
    /** @var  BudgetConsumedDataRepository */
    private $budgetConsumedDataRepository;

    public function __construct(BudgetConsumedDataRepository $budgetConsumedDataRepo)
    {
        $this->budgetConsumedDataRepository = $budgetConsumedDataRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetConsumedDatas",
     *      summary="Get a listing of the BudgetConsumedDatas.",
     *      tags={"BudgetConsumedData"},
     *      description="Get all BudgetConsumedDatas",
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
     *                  @SWG\Items(ref="#/definitions/BudgetConsumedData")
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
        $this->budgetConsumedDataRepository->pushCriteria(new RequestCriteria($request));
        $this->budgetConsumedDataRepository->pushCriteria(new LimitOffsetCriteria($request));
        $budgetConsumedDatas = $this->budgetConsumedDataRepository->all();

        return $this->sendResponse($budgetConsumedDatas->toArray(), trans('custom.not_found', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    /**
     * @param CreateBudgetConsumedDataAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/budgetConsumedDatas",
     *      summary="Store a newly created BudgetConsumedData in storage",
     *      tags={"BudgetConsumedData"},
     *      description="Store BudgetConsumedData",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetConsumedData that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetConsumedData")
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
     *                  ref="#/definitions/BudgetConsumedData"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBudgetConsumedDataAPIRequest $request)
    {
        $input = $request->all();

        $budgetConsumedDatas = $this->budgetConsumedDataRepository->create($input);

        return $this->sendResponse($budgetConsumedDatas->toArray(), trans('custom.save', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/budgetConsumedDatas/{id}",
     *      summary="Display the specified BudgetConsumedData",
     *      tags={"BudgetConsumedData"},
     *      description="Get BudgetConsumedData",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetConsumedData",
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
     *                  ref="#/definitions/BudgetConsumedData"
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
        /** @var BudgetConsumedData $budgetConsumedData */
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_consumed_data')]));
        }

        return $this->sendResponse($budgetConsumedData->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    /**
     * @param int $id
     * @param UpdateBudgetConsumedDataAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/budgetConsumedDatas/{id}",
     *      summary="Update the specified BudgetConsumedData in storage",
     *      tags={"BudgetConsumedData"},
     *      description="Update BudgetConsumedData",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetConsumedData",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BudgetConsumedData that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BudgetConsumedData")
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
     *                  ref="#/definitions/BudgetConsumedData"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBudgetConsumedDataAPIRequest $request)
    {
        $input = $request->all();

        /** @var BudgetConsumedData $budgetConsumedData */
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_consumed_data')]));
        }

        $budgetConsumedData = $this->budgetConsumedDataRepository->update($input, $id);

        return $this->sendResponse($budgetConsumedData->toArray(), trans('custom.update', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/budgetConsumedDatas/{id}",
     *      summary="Remove the specified BudgetConsumedData from storage",
     *      tags={"BudgetConsumedData"},
     *      description="Delete BudgetConsumedData",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BudgetConsumedData",
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
        /** @var BudgetConsumedData $budgetConsumedData */
        $budgetConsumedData = $this->budgetConsumedDataRepository->findWithoutFail($id);

        if (empty($budgetConsumedData)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.budget_consumed_data')]));
        }

        $budgetConsumedData->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.budget_consumed_data')]));
    }

    public function getBudgetConsumptionForReview(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'grvRecieved', 'month', 'year', 'invoicedBooked', 'supplierID', 'sentToSupplier', 'logisticsAvailable', 'financeCategory', 'poTypeID'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $supplierID = $request['supplierID'];
        $supplierID = (array)$supplierID;
        $supplierID = collect($supplierID)->pluck('id');

        $serviceLineSystemID = $request['serviceLineSystemID'];
        $serviceLineSystemID = (array)$serviceLineSystemID;
        $serviceLineSystemID = collect($serviceLineSystemID)->pluck('id');

        $consumedData = BudgetConsumedData::where('documentSystemID', 2)
                                          ->where('companySystemID', $input['companyId'])
                                          ->with(['purchase_order' => function($query) {
                                            $query->with(['created_by' => function ($query) {
                                            }, 'category' => function ($query) {
                                            }, 'location' => function ($query) {
                                            }, 'supplier' => function ($query) {
                                            }, 'currency' => function ($query) {
                                            }, 'fcategory' => function ($query) {
                                            }, 'segment' => function ($query) {
                                            },'reportingcurrency']);
                                          },'budget_master'])
                                          ->whereHas('purchase_order', function($procumentOrders) use ($input, $supplierID, $serviceLineSystemID) {
                                                if (array_key_exists('companyId', $input)) {
                                                    if ($input['companyId'] && !is_null($input['companyId'])) {
                                                        $procumentOrders->where('companySystemID', $input['companyId']);
                                                    }
                                                }

                                                if (array_key_exists('serviceLineSystemID', $input)) {
                                                    if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                                                        $procumentOrders->whereIn('serviceLineSystemID', $serviceLineSystemID);
                                                    }
                                                }

                                                if (array_key_exists('grvRecieved', $input)) {
                                                    if (($input['grvRecieved'] == 0 || $input['grvRecieved'] == 1 || $input['grvRecieved'] == 2) && !is_null($input['grvRecieved'])) {
                                                        $procumentOrders->where('grvRecieved', $input['grvRecieved']);
                                                    }
                                                }

                                                if (array_key_exists('invoicedBooked', $input)) {
                                                    if (($input['invoicedBooked'] == 0 || $input['invoicedBooked'] == 1 || $input['invoicedBooked'] == 2) && !is_null($input['invoicedBooked'])) {
                                                        $procumentOrders->where('invoicedBooked', $input['invoicedBooked']);
                                                    }
                                                }

                                                if (array_key_exists('month', $input)) {
                                                    if ($input['month'] && !is_null($input['month'])) {
                                                        $procumentOrders->whereMonth('createdDateTime', '=', $input['month']);
                                                    }
                                                }

                                                if (array_key_exists('year', $input)) {
                                                    if ($input['year'] && !is_null($input['year'])) {
                                                        $procumentOrders->whereYear('createdDateTime', '=', $input['year']);
                                                    }
                                                }

                                                if (array_key_exists('supplierID', $input)) {
                                                    if ($input['supplierID'] && !is_null($input['supplierID'])) {
                                                        $procumentOrders->whereIn('supplierID', $supplierID);
                                                    }
                                                }

                                                if (array_key_exists('financeCategory', $input)) {
                                                    if ($input['financeCategory'] && !is_null($input['financeCategory'])) {
                                                        $procumentOrders->where('financeCategory', $input['financeCategory']);
                                                    }
                                                }

                                                if (array_key_exists('poTypeID', $input)) {
                                                    if ($input['poTypeID'] && !is_null($input['poTypeID'])) {
                                                        $procumentOrders->where('poTypeID', $input['poTypeID']);
                                                    }
                                                }

                                                if (array_key_exists('sentToSupplier', $input)) {
                                                    if (($input['sentToSupplier'] == 0 || $input['sentToSupplier'] == -1) && !is_null($input['sentToSupplier'])) {
                                                        $procumentOrders->where('sentToSupplier', $input['sentToSupplier']);
                                                    }
                                                }

                                                if (array_key_exists('logisticsAvailable', $input)) {
                                                    if (($input['logisticsAvailable'] == 0 || $input['logisticsAvailable'] == -1) && !is_null($input['logisticsAvailable'])) {
                                                        $procumentOrders->where('logisticsAvailable', $input['logisticsAvailable']);
                                                    }
                                                }
                                          })
                                          ->whereHas('budget_master')
                                          ->whereHas('financeyear_by')
                                          ->groupBy('documentSystemCode');
   
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $consumedData = $consumedData->where(function ($query) use ($search) {
                $query->whereHas('purchase_order', function($query) use ($search) {
                    $query->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                    ->orWhere('narration', 'LIKE', "%{$search}%")
                    ->orWhere('referenceNumber', 'LIKE', "%{$search}%")
                    ->orWhere('supplierPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
                });
            });
        }


        return \DataTables::eloquent($consumedData)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('budgetConsumedDataAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getBudgetConsumptionByDoc(Request $request)
    {
        $input = $request->all();

        $consumedData = BudgetConsumedData::selectRaw('companySystemID,companyID,serviceLineSystemID,companyFinanceYearID,serviceLineCode,documentSystemID,documentID,documentSystemCode,documentCode,chartOfAccountID,GLCode,year,month,consumedLocalCurrencyID,SUM(consumedLocalAmount) as consumedLocalAmount ,consumedRptCurrencyID,SUM(consumedRptAmount) as consumedRptAmount,consumeYN,projectID,timestamp')
                                          ->with(['segment_by','reporting_currency','month_by','financeyear_by', 'purchase_order', 'chart_of_account', 'budget_master' => function($query) {
                                                $query->with(['finance_year_by']);
                                            }])
                                          ->whereHas('financeyear_by')
                                          ->whereHas('chart_of_account')
                                          ->whereHas('budget_master')
                                          ->whereHas('purchase_order')
                                          ->where('documentSystemID', $input['documentSystemID'])
                                          ->where('documentSystemCode', $input['documentSystemCode'])
                                          ->groupBy('companyFinanceYearID', 'serviceLineSystemID', 'chartOfAccountID')
                                          ->get();

        $grvRecivedAmount = GRVDetails::selectRaw('(SUM(GRVcostPerUnitComRptCur*noQty) + SUM(VATAmountRpt*noQty)) as rptTotal')
                                      ->where('purchaseOrderMastertID', $input['documentSystemCode'])
                                      ->first();

        $months = Months::selectRaw('monthID as value, monthDes as label')->get();

        $data = [];
        foreach ($consumedData as $key => $value) {
            $changedAmount = BudgetConsumedData::selectRaw('SUM(consumedRptAmount) as consumedRptAmount')
                                               ->where('documentSystemID', $input['documentSystemID'])
                                               ->where('documentSystemCode', $input['documentSystemCode'])
                                               ->where('companyFinanceYearID', $value->companyFinanceYearID)
                                               ->where('consumedRptAmount', '<', 0)
                                               ->first();

            $balanceToReciveRpt = $value->purchase_order->poTotalComRptCurrency - floatval($grvRecivedAmount->rptTotal) - abs(floatval($changedAmount->consumedRptAmount));

            $cutOffDate = isset($value->budget_master->finance_year_by->endingDate) ? Carbon::parse($value->budget_master->finance_year_by->endingDate)->addMonthsNoOverflow($value->budget_master->cutOffPeriod)->format('Y-m-d') : null;


            $recivedAmountAfterCutOff = GRVDetails::selectRaw('(SUM(GRVcostPerUnitComRptCur*noQty) + SUM(VATAmountRpt*noQty)) as rptTotal')
                                                  ->where('purchaseOrderMastertID', $input['documentSystemCode'])
                                                  ->whereHas('grv_master', function($query) use ($cutOffDate) {
                                                        $query->whereDate('grvDate','>', $cutOffDate);
                                                  })
                                                  ->first();

            $availableToChange = $balanceToReciveRpt + floatval($recivedAmountAfterCutOff->rptTotal);

            if ($availableToChange < 0) {
                $availableToChange = 0;
            }

            // $consumedAmountCurrency = \Helper::currencyConversion($value->purchase_order->companySystemID, $value->consumedRptCurrencyID, $value->purchase_order->supplierTransactionCurrencyID, $value->consumedRptAmount);

            $value->consumedAmount = $value->consumedRptAmount;
            $value->availableToChange = ($availableToChange > $value->consumedRptAmount) ? $value->consumedRptAmount : $availableToChange;
            $value->monthData = $months;
            $value->amountToChange = 0;
            $value->financeYears = CompanyFinanceYear::selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as label,companyFinanceYearID as value")
                                                     ->where('companySystemID', $value->companySystemID)
                                                     ->whereDate('bigginingDate', '>', $value->financeyear_by->bigginingDate)
                                                     ->get();

            $value->segemntsData = SegmentMaster::selectRaw('serviceLineSystemID as value, ServiceLineDes as label')
                                                ->where("companySystemID", $value->companySystemID)
                                                ->where('isActive', 1)
                                                ->get();

            if (round($value->consumedAmount, $value->reporting_currency->DecimalPlaces) > 0) {
                $data[] = $value;
            }
        }

        return $this->sendResponse($data, trans('custom.consumed_data_retrived_successfully'));
    }

    public function changeBudgetConsumption(Request $request)
    {
        $input = $request->all();

        if (!isset($input['newSegment'])) {
            return $this->sendError("New Segment is required", 500);
        }

        if (!isset($input['newYear'])) {
            return $this->sendError("New Year is required", 500);
        }

        if (!isset($input['newMonth'])) {
            return $this->sendError("New Month is required", 500);
        }

        if (!isset($input['amountToChange']) || (isset($input['amountToChange']) && $input['amountToChange'] == 0)) {
            return $this->sendError("Amount to change should be greater than zero", 500);
        }

        $checkBudgetMaster = BudgetMaster::where('companySystemID', $input['companySystemID'])
                                         ->where('serviceLineSystemID', $input['newSegment'])
                                         ->where('companyFinanceYearID', $input['newYear'])
                                         ->where('approvedYN', -1)
                                         ->whereHas('template_master', function($query) {
                                            $query->where('reportID', 2);
                                         })
                                         ->whereHas('budget_details', function($query) use ($input){
                                            $query->where('month', $input['newMonth'])
                                                 ->where('chartOfAccountID', $input['chartOfAccountID']);
                                         })
                                         ->first();

        if (!$checkBudgetMaster) {
            return $this->sendError("Budget is not configured for selected segment and period", 500);
        }


        $consumedData = BudgetConsumedData::with(['segment_by','reporting_currency','financeyear_by', 'purchase_order', 'chart_of_account', 'budget_master' => function($query) {
                                                $query->with(['finance_year_by']);
                                            }])
                                          ->whereHas('financeyear_by')
                                          ->whereHas('chart_of_account')
                                          ->whereHas('purchase_order')
                                          ->whereHas('budget_master')
                                          ->where('documentSystemID', $input['documentSystemID'])
                                          ->where('documentSystemCode', $input['documentSystemCode'])
                                          ->first();

        if (!$consumedData) {
            return $this->sendError("Budget consumed data not found", 500);
        }

        $grvRecivedAmount = GRVDetails::selectRaw('(SUM(GRVcostPerUnitComRptCur*noQty) + SUM(VATAmountRpt*noQty)) as rptTotal')
                                      ->where('purchaseOrderMastertID', $input['documentSystemCode'])
                                      ->first();

        $balanceToReciveRpt = $consumedData->purchase_order->poTotalComRptCurrency - floatval($grvRecivedAmount->rptTotal);

        $cutOffDate = isset($consumedData->budget_master->finance_year_by->endingDate) ? Carbon::parse($consumedData->budget_master->finance_year_by->endingDate)->addMonthsNoOverflow($consumedData->budget_master->cutOffPeriod)->format('Y-m-d') : null;


        $recivedAmountAfterCutOff = GRVDetails::selectRaw('(SUM(GRVcostPerUnitComRptCur*noQty) + SUM(VATAmountRpt*noQty)) as rptTotal')
                                              ->where('purchaseOrderMastertID', $input['documentSystemCode'])
                                              ->whereHas('grv_master', function($query) use ($cutOffDate) {
                                                    $query->whereDate('grvDate','>', $cutOffDate);
                                              })
                                              ->first();

        $availableToChange = $balanceToReciveRpt + floatval($recivedAmountAfterCutOff->rptTotal);

        if (floatval($input['amountToChange']) > $availableToChange) {
            return $this->sendError("Amount cannot be greater than available to change amount", 500);
        }

        $consumedAmountCurrency = \Helper::currencyConversion($input['companySystemID'], $input['consumedRptCurrencyID'], $input['consumedRptCurrencyID'], $input['amountToChange']);

        $companyFinanceYear = CompanyFinanceYear::find($input['newYear']);

        if (!$companyFinanceYear) {
            return $this->sendError("New Year not found", 500);
        }

        $data = [
            'companySystemID' => $input['companySystemID'],
            'companyID' => $input['companyID'],
            'serviceLineSystemID' => $input['serviceLineSystemID'],
            'companyFinanceYearID' => $input['companyFinanceYearID'],
            'serviceLineCode' => $input['serviceLineCode'],
            'documentSystemID' => $input['documentSystemID'],
            'documentID' => $input['documentID'],
            'documentSystemCode' => $input['documentSystemCode'],
            'documentCode' => $input['documentCode'],
            'chartOfAccountID' => $input['chartOfAccountID'],
            'GLCode' => $input['GLCode'],
            'year' => $input['year'],
            'month' => $input['month'],
            'consumedLocalCurrencyID' => $input['consumedLocalCurrencyID'],
            'consumedLocalAmount' => $consumedAmountCurrency['localAmount'] * -1,
            'consumedRptCurrencyID' => $input['consumedRptCurrencyID'],
            'consumedRptAmount' => $input['amountToChange'] * -1,
            'consumeYN' => $input['consumeYN'],
            'projectID' => $input['projectID'],
            'timestamp' => date('d/m/Y H:i:s A')
        ];

        DB::beginTransaction();
        try {

            BudgetConsumedData::insert($data);

            $newData = $data;

            $newData['serviceLineSystemID'] = $input['newSegment'];
            $newData['serviceLineCode'] = SegmentMaster::getSegmentCode($input['newSegment']);
            $newData['companyFinanceYearID'] = $input['newYear'];
            $newData['year'] = Carbon::parse($companyFinanceYear->bigginingDate)->format('Y');
            $newData['month'] = $input['newMonth'];
            $newData['consumedLocalAmount'] = $consumedAmountCurrency['localAmount'];
            $newData['consumedRptAmount'] = $input['amountToChange'];

            BudgetConsumedData::insert($newData);

            DB::commit();
            return $this->sendResponse([], trans('custom.budget_year_changed_successfully'));
        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError(trans('custom.error_occurred'), 500);
        }
    }
}
