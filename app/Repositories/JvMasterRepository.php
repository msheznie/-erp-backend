<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\JvMaster;
use InfyOm\Generator\Common\BaseRepository;
use App\helper\StatusService;
use App\Models\GeneralLedger;

/**
 * Class JvMasterRepository
 * @package App\Repositories
 * @version September 25, 2018, 7:43 am UTC
 *
 * @method JvMaster findWithoutFail($id, $columns = ['*'])
 * @method JvMaster find($id, $columns = ['*'])
 * @method JvMaster first($columns = ['*'])
*/
class JvMasterRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'companySystemID',
        'companyID',
        'documentSystemID',
        'documentID',
        'serialNo',
        'companyFinanceYearID',
        'FYBiggin',
        'FYEnd',
        'companyFinancePeriodID',
        'FYPeriodDateFrom',
        'FYPeriodDateTo',
        'JVcode',
        'JVdate',
        'recurringjvMasterAutoId',
        'recurringMonth',
        'recurringYear',
        'JVNarration',
        'currencyID',
        'currencyER',
        'rptCurrencyID',
        'rptCurrencyER',
        'empID',
        'confirmedYN',
        'confirmedByEmpSystemID',
        'confirmedByEmpID',
        'confirmedByName',
        'confirmedDate',
        'approved',
        'approvedDate',
        'postedDate',
        'jvType',
        'type',
        'isReverseAccYN',
        'timesReferred',
        'isRelatedPartyYN',
        'createdUserGroup',
        'createdUserSystemID',
        'createdUserID',
        'createdPcID',
        'modifiedUserSystemID',
        'modifiedUser',
        'modifiedPc',
        'createdDateTime',
        'reversalDate',
        'timestamp'
    ];

    /**
     * Configure the Model
     **/
    public function model()
    {
        return JvMaster::class;
    }

    public function jvMasterListQuery($request, $input, $search = '') {

        $invMaster = JvMaster::where('companySystemID', $input['companySystemID']);
        //$invMaster->where('documentSystemID', $input['documentId']);
        $invMaster->with(['created_by', 'transactioncurrency','reportingcurrency', 'detail' => function ($query) {
            $query->selectRaw('COALESCE(SUM(debitAmount),0) as debitSum,COALESCE(SUM(creditAmount),0) as creditSum,jvMasterAutoId');
            $query->groupBy('jvMasterAutoId');
        } ,'company'=> function ($query) {
            $query->with(['localcurrency','reportingcurrency']);
        } ]);

        if (array_key_exists('createdBy', $input)) {
            if($input['createdBy'] && !is_null($input['createdBy']))
            {
                $createdBy = collect($input['createdBy'])->pluck('id')->toArray();
                $invMaster->whereIn('createdUserSystemID', $createdBy);
            }

        }

        if (array_key_exists('jvType', $input)) {
            if (($input['jvType'] == 0 || $input['jvType'] == 1 || $input['jvType'] == 2 || $input['jvType'] == 3 || $input['jvType'] == 4 || $input['jvType'] == 5) && !is_null($input['jvType'])) {
                $invMaster->where('jvType', $input['jvType']);
            }
        }

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $invMaster->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $invMaster->whereMonth('JVdate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invMaster->whereYear('JVdate', '=', $input['year']);
            }
        }


        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('JVcode', 'LIKE', "%{$search}%")
                    ->orWhere('JVNarration', 'LIKE', "%{$search}%");
            });
        }

        return $invMaster;
    }

    public function setExportExcelData($dataSet) {

        $dataSet = $dataSet->get();
        if (count($dataSet) > 0) {
            $x = 0;

            foreach ($dataSet as $val) {
                $data[$x][trans('custom.jv_code')] = $val->JVcode;
                $data[$x][trans('custom.type')] = StatusService::getjvType($val->jvType);
                $data[$x][trans('custom.jv_date')] = \Helper::dateFormat($val->JVdate);
                $data[$x][trans('custom.narration')] = $val->JVNarration;
                $data[$x][trans('custom.created_by')] = $val->created_by? $val->created_by->empName : '';
                $data[$x][trans('custom.created_at')] = \Helper::convertDateWithTime($val->createdDateTime);
                $data[$x][trans('custom.confirmed_on')] = \Helper::convertDateWithTime($val->confirmedDate);
                $data[$x][trans('custom.approved_on')] = \Helper::convertDateWithTime($val->approvedDate);
                $data[$x][trans('custom.transaction_currency')] = $val->transactioncurrency? $val->transactioncurrency->CurrencyCode : '';



                $data[$x][trans('custom.transaction_debit_amount')] = $val->detail->count() > 0? number_format($val->detail[0]->debitSum, $val->transactioncurrency? $val->transactioncurrency->DecimalPlaces : '', ".", "") : 0;
                $data[$x][trans('custom.transaction_credit_amount')] = $val->detail->count() > 0? number_format($val->detail[0]->creditSum, $val->transactioncurrency? $val->transactioncurrency->DecimalPlaces : '', ".", "") : 0;


                $debitAmount = $val->detail->count() > 0? $val->detail[0]->debitSum : 0;
                $creditAmount = $val->detail->count() > 0? $val->detail[0]->creditSum : 0;

                $debitRptAmount = 0;
                $creditRptAmount = 0;
                $debitLocalAmount = 0;
                $creditLocalAmount = 0;

                $localCurrencyCode = '';
                $reportingCurrencyCode = '';
                $localCurrencyDecimal = '';
                $reportingCurrencyDecimal = '';


                if($val->approved == 0){
                    $localCurrencyCode = $val->company->localcurrency? $val->company->localcurrency->CurrencyCode : '';
                    $reportingCurrencyCode = $val->company->reportingcurrency? $val->company->reportingcurrency->CurrencyCode : '';
                    $localCurrencyDecimal = $val->company->localcurrency? $val->company->localcurrency->DecimalPlaces : '';
                    $reportingCurrencyDecimal = $val->company->reportingcurrency? $val->company->reportingcurrency->DecimalPlaces : '';

                    if(($val->rptCurrencyID && $val->currencyID) && ($debitAmount > 0 || $creditAmount > 0)){
                        if($debitAmount > 0){
                            $debitCurrencyConversionAmount = Helper::currencyConversion($val->companySystemID, $val->currencyID, $val->currencyID, $debitAmount);
                            $debitRptAmount = $debitCurrencyConversionAmount['reportingAmount'];
                            $debitLocalAmount = $debitCurrencyConversionAmount['localAmount'];
                        }
    
                        if($creditAmount > 0){
                            $creditCurrencyConversionAmount = Helper::currencyConversion($val->companySystemID, $val->currencyID, $val->currencyID, $creditAmount);
                            $creditRptAmount = $creditCurrencyConversionAmount['reportingAmount'];
                            $creditLocalAmount = $creditCurrencyConversionAmount['localAmount'];
                        }
                    }
                }


                if($val->approved == -1){
                    $trasToTransER = $val->currencyER;
                    

                    $jvGl = GeneralLedger::with(['localcurrency','rptcurrency'])
                    ->where('documentCode',$val->JVcode)
                    ->first();
                    
                   


                    if($jvGl){
                        $localCurrencyCode = $jvGl->localcurrency? $jvGl->localcurrency->CurrencyCode : '';
                        $reportingCurrencyCode = $jvGl->rptcurrency? $jvGl->rptcurrency->CurrencyCode : '';
                        $localCurrencyDecimal = $jvGl->localcurrency? $jvGl->localcurrency->DecimalPlaces : '';
                        $reportingCurrencyDecimal = $jvGl->rptcurrency? $jvGl->rptcurrency->DecimalPlaces : '';

                        $trasToRptER = $jvGl->documentRptCurrencyER;
                        $trasToLocER = $jvGl->documentLocalCurrencyER;

                        if ($trasToRptER > $trasToTransER) {
                            if ($trasToRptER > 1) {
                                if ((is_numeric($debitAmount) || is_numeric($creditAmount)) && is_numeric($trasToRptER)) {
                                    if(is_numeric($debitAmount)){
                                        $debitRptAmount = $debitAmount / $trasToRptER;
                                    } else {
                                        $debitRptAmount = 0;
                                    }
        
                                    if(is_numeric($creditAmount)){
                                        $creditRptAmount = $creditAmount / $trasToRptER;
                                    } else {
                                        $creditRptAmount = 0;
                                    }
        
                                } else {
                                    $debitRptAmount = 0;
                                    $creditRptAmount = 0;
                                }
                            } else {
                                if ((is_numeric($debitAmount) || is_numeric($creditAmount)) && is_numeric($trasToRptER)) 
                                {
                                    if(is_numeric($debitAmount)){
                                        $debitRptAmount = $debitAmount * $trasToRptER;
                                    } else {
                                        $debitRptAmount = 0;
                                    }
        
                                    if(is_numeric($creditAmount)){
                                        $creditRptAmount = $creditAmount * $trasToRptER;
                                    } else {
                                        $creditRptAmount = 0;
                                    }
        
                                } else {
                                    $debitRptAmount = 0;
                                    $creditRptAmount = 0;                        
                                }
                            }
                        } else {
                            if ($trasToRptER > 1) {
                                if ((is_numeric($debitAmount) || is_numeric($creditAmount)) && is_numeric($trasToRptER)) 
                                {
                                    if(is_numeric($debitAmount)){
                                        $debitRptAmount = $debitAmount * $trasToRptER;
                                    } else {
                                        $debitRptAmount = 0;
                                    }
        
                                    if(is_numeric($creditAmount)){
                                        $creditRptAmount = $creditAmount * $trasToRptER;
                                    } else {
                                        $creditRptAmount = 0;
                                    }                        
                                } else {
                                    $debitRptAmount = 0;
                                    $creditRptAmount = 0;                        
                                }
                            } else {
                                if ((is_numeric($debitAmount) || is_numeric($creditAmount)) && is_numeric($trasToRptER)) {
                                    if(is_numeric($debitAmount)){
                                        $debitRptAmount = $debitAmount / $trasToRptER;
                                    } else {
                                        $debitRptAmount = 0;
                                    }
        
                                    if(is_numeric($creditAmount)){
                                        $creditRptAmount = $creditAmount / $trasToRptER;
                                    } else {
                                        $creditRptAmount = 0;
                                    }                       
                                } else {
                                    $debitRptAmount = 0;
                                    $creditRptAmount = 0;                        
                                }
                            }
                        }

                        if ($trasToLocER > $trasToTransER) {
                            if ($trasToLocER > 1) {
                                if ((is_numeric($debitAmount) || is_numeric($creditAmount)) && is_numeric($trasToRptER)) {
                                    if(is_numeric($debitAmount)){
                                        $debitLocalAmount = $debitAmount / $trasToLocER;
                                    } else {
                                        $debitLocalAmount = 0;
                                    }
        
                                    if(is_numeric($creditAmount)){
                                        $creditLocalAmount = $creditAmount / $trasToLocER;
                                    } else {
                                        $creditLocalAmount = 0;
                                    }
        
                                } else {
                                    $debitLocalAmount = 0;
                                    $creditLocalAmount = 0;
                                }

                            } else {
                                if ((is_numeric($debitAmount) || is_numeric($creditAmount)) && is_numeric($trasToRptER)) {
                                    if(is_numeric($debitAmount)){
                                        $debitLocalAmount = $debitAmount * $trasToLocER;
                                    } else {
                                        $debitLocalAmount = 0;
                                    }
        
                                    if(is_numeric($creditAmount)){
                                        $creditLocalAmount = $creditAmount * $trasToLocER;
                                    } else {
                                        $creditLocalAmount = 0;
                                    }
        
                                } else {
                                    $debitLocalAmount = 0;
                                    $creditLocalAmount = 0;
                                }

                            }
                        } else {
                            if ($trasToLocER > 1) {
                                if ((is_numeric($debitAmount) || is_numeric($creditAmount)) && is_numeric($trasToRptER)) {
                                    if(is_numeric($debitAmount)){
                                        $debitLocalAmount = $debitAmount * $trasToLocER;
                                    } else {
                                        $debitLocalAmount = 0;
                                    }
        
                                    if(is_numeric($creditAmount)){
                                        $creditLocalAmount = $creditAmount * $trasToLocER;
                                    } else {
                                        $creditLocalAmount = 0;
                                    }
        
                                } else {
                                    $debitLocalAmount = 0;
                                    $creditLocalAmount = 0;
                                }

                            } else {
                                if ((is_numeric($debitAmount) || is_numeric($creditAmount)) && is_numeric($trasToRptER)) {
                                    if(is_numeric($debitAmount)){
                                        $debitLocalAmount = $debitAmount / $trasToLocER;
                                    } else {
                                        $debitLocalAmount = 0;
                                    }
        
                                    if(is_numeric($creditAmount)){
                                        $creditLocalAmount = $creditAmount / $trasToLocER;
                                    } else {
                                        $creditLocalAmount = 0;
                                    }
        
                                } else {
                                    $debitLocalAmount = 0;
                                    $creditLocalAmount = 0;
                                }
                            }
                        }
                    } else {
                        $debitRptAmount = 0;
                        $creditRptAmount = 0;  
                        $debitLocalAmount = 0;
                        $creditLocalAmount = 0;
                    }

                }


                $data[$x][trans('custom.local_currency')] = $localCurrencyCode;
                $data[$x][trans('custom.local_debit_amount')] = $debitLocalAmount > 0? number_format($debitLocalAmount, $localCurrencyDecimal, ".", "") : 0;
                $data[$x][trans('custom.local_credit_amount')] = $creditLocalAmount > 0? number_format($creditLocalAmount, $localCurrencyDecimal, ".", "") : 0;

                $data[$x][trans('custom.reporting_currency')] = $reportingCurrencyCode;
                $data[$x][trans('custom.reporting_debit_amount')] = $debitRptAmount > 0? number_format($debitRptAmount, $reportingCurrencyDecimal, ".", "") : 0;
                $data[$x][trans('custom.reporting_credit_amount')] = $creditRptAmount > 0? number_format($creditRptAmount, $reportingCurrencyDecimal, ".", "") : 0;

                $data[$x][trans('custom.status')] = StatusService::getStatus(NULL, NULL, $val->confirmedYN, $val->approved, $val->refferedBackYN);

                $x++;
            }
        } else {
            $data = array();
        }

        return $data;
    }
}
