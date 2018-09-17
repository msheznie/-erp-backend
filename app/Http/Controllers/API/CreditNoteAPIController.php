<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCreditNoteAPIRequest;
use App\Http\Requests\API\UpdateCreditNoteAPIRequest;
use App\Models\CreditNote;
use App\Models\CreditNoteDetails;
use App\Models\YesNoSelectionForMinus;
use App\Models\YesNoSelection;
use App\Models\Months;
use App\Models\CustomerAssigned;
use App\Models\DocumentMaster;
use App\Models\DocumentApproved;
use App\Models\EmployeesDepartment;
use App\Models\CompanyDocumentAttachment;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyFinancePeriod;
use App\Models\CustomerMaster;
use App\Models\Company;
use App\Models\SegmentMaster;
use Carbon\Carbon;
use App\Models\CustomerCurrency;
use App\Repositories\CreditNoteRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CreditNoteController
 * @package App\Http\Controllers\API
 */
class CreditNoteAPIController extends AppBaseController
{
    /** @var  CreditNoteRepository */
    private $creditNoteRepository;

    public function __construct(CreditNoteRepository $creditNoteRepo)
    {
        $this->creditNoteRepository = $creditNoteRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNotes",
     *      summary="Get a listing of the CreditNotes.",
     *      tags={"CreditNote"},
     *      description="Get all CreditNotes",
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
     *                  @SWG\Items(ref="#/definitions/CreditNote")
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
        $this->creditNoteRepository->pushCriteria(new RequestCriteria($request));
        $this->creditNoteRepository->pushCriteria(new LimitOffsetCriteria($request));
        $creditNotes = $this->creditNoteRepository->all();

        return $this->sendResponse($creditNotes->toArray(), 'Credit Notes retrieved successfully');
    }

    /**
     * @param CreateCreditNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/creditNotes",
     *      summary="Store a newly created CreditNote in storage",
     *      tags={"CreditNote"},
     *      description="Store CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNote that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNote")
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
     *                  ref="#/definitions/CreditNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCreditNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'companyFinanceYearID', 'currencyID', 'customerCurrencyID'));
        $company = Company::select('CompanyID')->where('companySystemID', $input['companySystemID'])->first();
        $CompanyFinanceYear = CompanyFinanceYear::where('companyFinanceYearID', $input['companyFinanceYearID'])->first();
        $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
        $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
        /**/
        /*companySystemID*/
        $serialNo = CreditNote::select(DB::raw('IFNULL(MAX(serialNo),0)+1 as serialNo'))->where('documentID', 'CN')->where('companySystemID', $input['companySystemID'])->orderBy('serialNo', 'desc')->first();
        $y = date('Y', strtotime($CompanyFinanceYear->bigginingDate));
        $creditNoteCode = ($company->CompanyID . '\\' . $y . '\\CN' . str_pad($serialNo['serialNo'], 6, '0', STR_PAD_LEFT));

        $input['companyID'] = $company->CompanyID;
        $input['documentSystemiD'] = 19;
        $input['documentID'] = 'CN';
        $input['serialNo'] = $serialNo->serialNo;
        $input['FYBiggin'] = $CompanyFinanceYear->bigginingDate;
        $input['FYEnd'] = $CompanyFinanceYear->endingDate;
        $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
        $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;
        $input['creditNoteCode'] = $creditNoteCode;
        $input['creditNoteDate'] = Carbon::parse($input['creditNoteDate'])->format('Y-m-d') . ' 00:00:00';
        $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;
        $input['customerGLCode'] = $customer->custGLaccount;
        $input['documentType'] = 12;

        /*currency*/
        $myCurr = $input['customerCurrencyID'];

        $companyCurrency = \Helper::companyCurrency($input['customerCurrencyID']);
        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $myCurr, $myCurr, 0);
        /*exchange added*/
        $input['customerCurrencyER'] = 1;
        $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
        $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
        $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
        $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        /*end of currency*/

        $input['createdUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['createdUserID'] = \Helper::getEmployeeID();
        $input['createdPcID'] = getenv('COMPUTERNAME');
        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        if (($input['creditNoteDate'] >= $companyfinanceperiod->dateFrom) && ($input['creditNoteDate'] <= $companyfinanceperiod->dateTo)) {
            $creditNotes = $this->creditNoteRepository->create($input);
            return $this->sendResponse($creditNotes->toArray(), 'Credit Note saved successfully');
        } else {
            return $this->sendError('Credit note document date should be between financial period start and end date', 500);
        }


    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/creditNotes/{id}",
     *      summary="Display the specified CreditNote",
     *      tags={"CreditNote"},
     *      description="Get CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
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
     *                  ref="#/definitions/CreditNote"
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
        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->with(['currency', 'finance_year_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(bigginingDate,'%d/%m/%Y'),' | ',DATE_FORMAT(endingDate,'%d/%m/%Y')) as financeYear,companyFinanceYearID");
        }, 'finance_period_by' => function ($query) {
            $query->selectRaw("CONCAT(DATE_FORMAT(dateFrom,'%d/%m/%Y'),' | ',DATE_FORMAT(dateTo,'%d/%m/%Y')) as financePeriod,companyFinancePeriodID");
        }])->findWithoutFail($id);

        if (empty($creditNote)) {
            return $this->sendError('Credit Note not found');
        }

        return $this->sendResponse($creditNote->toArray(), 'Credit Note retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCreditNoteAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/creditNotes/{id}",
     *      summary="Update the specified CreditNote in storage",
     *      tags={"CreditNote"},
     *      description="Update CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CreditNote that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CreditNote")
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
     *                  ref="#/definitions/CreditNote"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCreditNoteAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('companyFinancePeriodID', 'confirmedYN', 'companyFinanceYearID', 'customerID', 'secondaryLogoCompanySystemID', 'customerCurrencyID'));
        $input = array_except($input, array('finance_period_by', 'finance_year_by', 'currency','createdDateAndTime'));

        $input['modifiedUserSystemID'] = \Helper::getEmployeeSystemID();
        $input['modifiedUser'] = \Helper::getEmployeeID();
        $input['modifiedPc'] = getenv('COMPUTERNAME');

        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);
        $detail = CreditNoteDetails::where('creditNoteAutoID', $id)->get();
        $input['departmentSystemID'] = 4;
        if (empty($creditNote)) {
            return $this->sendError('Credit Note not found', 500);
        }

        /**/
        if ($input['customerCurrencyID'] != $creditNote->customerCurrencyID) {
            if (count($detail) == 0) {
                /*currency*/
                $myCurr = $input['customerCurrencyID'];

                $companyCurrency = \Helper::companyCurrency($input['customerCurrencyID']);
                $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $myCurr, $myCurr, 0);
                /*exchange added*/
                $input['customerCurrencyER'] = 1;
                $input['companyReportingCurrencyID'] = $companyCurrency->reportingcurrency->currencyID;
                $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
                $input['localCurrencyID'] = $companyCurrency->localcurrency->currencyID;;
                $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
                /*end of currency*/
            } else {
                return $this->sendError('Credit note details exist. You can not change the currency.', 500);
            }
        }

        /*financial Year check*/
        $companyFinanceYearCheck = \Helper::companyFinanceYearCheck($input);
        if (!$companyFinanceYearCheck["success"]) {
            return $this->sendError($companyFinanceYearCheck["message"], 500);
        }
        /*financial Period check*/
        $companyFinancePeriodCheck = \Helper::companyFinancePeriodCheck($input);
        if (!$companyFinancePeriodCheck["success"]) {
            return $this->sendError($companyFinancePeriodCheck["message"], 500);
        }

        if ($input['companyFinancePeriodID'] != $creditNote->companyFinancePeriodID) {
            $companyfinanceperiod = CompanyFinancePeriod::where('companyFinancePeriodID', $input['companyFinancePeriodID'])->first();
            $input['FYPeriodDateFrom'] = $companyfinanceperiod->dateFrom;
            $input['FYPeriodDateTo'] = $companyfinanceperiod->dateTo;
        }


        if ($input['secondaryLogoCompanySystemID'] != $creditNote->secondaryLogoCompanySystemID) {
            if ($input['secondaryLogoCompanySystemID'] != '') {
                $company = Company::select('companyLogo', 'CompanyID')->where('companySystemID', $input['secondaryLogoCompanySystemID'])->first();
                $input['secondaryLogoCompID'] = $company->CompanyID;
                $input['secondaryLogo'] = $company->companyLogo;
            } else {
                $input['secondaryLogoCompID'] = NULL;
                $input['secondaryLogo'] = NULL;
            }

        }

        /*customer*/
        if ($input['customerID'] != $creditNote->customerID) {
            if (count($detail) > 0) {
                return $this->sendError('Invoice details exist. You can not change the customer.', 500);
            }
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            /* if ($customer->creditDays == 0 || $customer->creditDays == '') {
                 return $this->sendError($customer->CustomerName . ' - Credit days not mentioned for this customer', 500);
             }*/

            /*if customer change*/
            $customer = CustomerMaster::where('customerCodeSystem', $input['customerID'])->first();
            $input['customerGLCode'] = $customer->custGLaccount;
            $input['customerGLCodeSystemID'] = $customer->custGLAccountSystemID;

            /**/

        }

        $_post['creditNoteDate'] = Carbon::parse($input['creditNoteDate'])->format('Y-m-d') . ' 00:00:00';


        if (($_post['creditNoteDate'] >= $input['FYPeriodDateFrom']) && ($_post['creditNoteDate'] <= $input['FYPeriodDateTo'])) {

        } else {
            return $this->sendError('Document Date should be between financial period start date and end date.', 500);

        }

        /*end of customer*/

        /**/

        if ($input['confirmedYN'] == 1) {
            if ($creditNote->confirmedYN == 0) {
                $messages = [

                    'customerCurrencyID.required' => 'Currency is required.',
                    'customerID.required' => 'Customer is required.',
                    'companyFinanceYearID.required' => 'Financial Year is required.',
                    'companyFinancePeriodID.required' => 'Financial Period is required.',

                ];
                $validator = \Validator::make($input, [
                    'customerCurrencyID' => 'required|numeric|min:1',
                    'customerID' => 'required|numeric|min:1',
                    'companyFinanceYearID' => 'required|numeric|min:1',
                    'companyFinancePeriodID' => 'required|numeric|min:1',

                ], $messages);

                if ($validator->fails()) {
                    return $this->sendError($validator->messages(), 422);
                }

                /*details check*/

                if (count($detail) == 0) { /*==*/
                    return $this->sendError('You can not confirm. Credit note details not found.', 500);
                } else {

                    $detailValidation = CreditNoteDetails::selectRaw("IF ( serviceLineSystemID IS NULL OR serviceLineSystemID = '' OR serviceLineSystemID = 0, null, 1 ) AS serviceLineSystemID, IF ( contractUID IS NULL OR contractUID = '' OR contractUID = 0, null, 1 ) AS contractUID,
                    IF ( creditAmount IS NULL OR creditAmount = '' OR creditAmount = 0, null, 1 ) AS creditAmount")->
                    where('creditNoteAutoID', $id)
                        ->where(function ($query) {

                            $query->whereIn('serviceLineSystemID', [null, 0])
                                ->orwhereIn('contractUID', [null, 0])
                                ->orwhereIn('creditAmount', [null, 0]);
                        });

                    if (!empty($detailValidation->get()->toArray())) {
                        foreach ($detailValidation->get()->toArray() as $item) {

                            $validators = \Validator::make($item, [
                                'serviceLineSystemID' => 'required|numeric|min:1',
                                'contractUID' => 'required|numeric|min:1',
                                'creditAmount' => 'required|numeric|min:1'
                            ], [

                                'serviceLineSystemID.required' => 'Department is required.',
                                'contractUID.required' => 'Contract no. is required.',
                                'creditAmount.required' => 'Amount is required.',

                            ]);
                            if ($validators->fails()) {
                                return $this->sendError($validators->messages(), 422);
                            }
                        }
                    }



                    /*serviceline and contract validation*/
                    $groupby = CreditNoteDetails::select('serviceLineSystemID')->where('creditNoteAutoID', $id)->groupBy('serviceLineSystemID')->get();
                    $groupbycontract = CreditNoteDetails::select('contractUID')->where('creditNoteAutoID', $id)->groupBy('contractUID')->get();
                    if (count($groupby) != 0 || count($groupby) != 0) {

                        if (count($groupby) > 1 || count($groupbycontract) > 1) {
                            return $this->sendError('You can not continue . multiple service line or contract exist in details.', 500);
                        } else {
                            $params = array('autoID' => $id,
                                'company' => $creditNote->companySystemID,
                                'document' => $creditNote->documentSystemiD,
                                'segment' => '',
                                'category' => '',
                                'amount' => ''
                            );

                            $confirm = \Helper::confirmDocument($params);
                            if (!$confirm["success"]) {
                                $customerInvoiceDirect = $this->creditNoteRepository->update($input, $id);
                                return $this->sendError($confirm["message"], 500);
                            } else {
                                return $this->sendResponse('s', 'Credit note confirmed successfully');
                            }
                        }
                    } else {
                        return $this->sendError('Credit note details not found.', 500);
                    }
                }
            }
        } else {


            $creditNote = $this->creditNoteRepository->update($input, $id);
        }
        /*   exit;
           $creditNote = $this->creditNoteRepository->update($input, $id);*/

        return $this->sendResponse($creditNote->toArray(), 'Credit note updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/creditNotes/{id}",
     *      summary="Remove the specified CreditNote from storage",
     *      tags={"CreditNote"},
     *      description="Delete CreditNote",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CreditNote",
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
        /** @var CreditNote $creditNote */
        $creditNote = $this->creditNoteRepository->findWithoutFail($id);

        if (empty($creditNote)) {
            return $this->sendError('Credit Note not found');
        }

        $creditNote->delete();

        return $this->sendResponse($id, 'Credit Note deleted successfully');
    }

    public function getCreditNoteMasterRecord(Request $request)
    {
        $input = $request->all();

        $output = CreditNote::where('creditNoteAutoID', $input['creditNoteAutoID'])->with(['details' => function ($query) {
            $query->with('segment');
        }, 'approved_by' => function ($query) {
            $query->with('employee');
            $query->where('documentSystemID', 19);
        }, 'company', 'currency', 'customer', 'confirmed_by', 'createduser'])->first();
        return $this->sendResponse($output, 'Data retrieved successfully');

    }

    public function getCreditNoteViewFormData(Request $request)
    {
        $input = $request->all();
        /*companySystemID*/
        $companySystemID = $input['companyId'];
        $type = $input['type']; /*value ['filter','create','getCurrency']*/
        switch ($type) {
            case 'filter':
                $output['yesNoSelectionForMinus'] = YesNoSelectionForMinus::all();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['month'] = Months::all();
                $output['years'] = CreditNote::select(DB::raw("YEAR(creditNoteDate) as year"))
                    ->whereNotNull('creditNoteDate')
                    ->where('companySystemID', $companySystemID)
                    ->groupby('year')
                    ->orderby('year', 'desc')
                    ->get();
                break;
            case 'create':
                $output['customer'] = CustomerAssigned::select('*')->where('companySystemID', $companySystemID)->where('isAssigned', '-1')->where('isActive', '1')->get();
                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));
                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID);
                $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companySystemID)->first();
                break;
            case 'getCurrency':
                $customerID = $input['customerID'];
                $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                break;

            case 'edit' :
                $id = $input['id'];
                $master = CreditNote::where('creditNoteAutoID', $id)->first();
                $output['company'] = Company::select('CompanyName', 'CompanyID')->where('companySystemID', $companySystemID)->first();

                if ($master->customerID != '') {
                    $output['currencies'] = DB::table('customercurrency')->join('currencymaster', 'customercurrency.currencyID', '=', 'currencymaster.currencyID')->where('customerCodeSystem', $master->customerID)->where('isAssigned', -1)->select('currencymaster.currencyID', 'currencymaster.CurrencyCode', 'isDefault')->get();
                } else {
                    $output['currencies'] = [];
                }
                $output['customer'] = CustomerAssigned::select('*')->where('companySystemID', $companySystemID)->where('isAssigned', '-1')->where('isActive', '1')->get();
                $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
                    array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

                $output['companyFinanceYear'] = \Helper::companyFinanceYear($companySystemID);
                $output['companyLogo'] = Company::select('companySystemID', 'CompanyID', 'CompanyName', 'companyLogo')->get();
                $output['yesNoSelection'] = YesNoSelection::all();
                $output['segment'] = SegmentMaster::where('isActive', 1)->where('companySystemID', $companySystemID)->get();
        }


        return $this->sendResponse($output, 'Form data');
    }

    public function creditNoteMasterDataTable(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'month', 'approved', 'year'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }
        $master = DB::table('erp_creditnote')
            ->leftjoin('currencymaster', 'customerCurrencyID', '=', 'currencyID')
            ->leftjoin('employees', 'erp_creditnote.createdUserSystemID', '=', 'employees.employeeSystemID')
            ->leftjoin('customermaster', 'customermaster.customerCodeSystem', '=', 'erp_creditnote.customerID')
            ->where('erp_creditnote.companySystemID', $input['companyId'])
            ->where('erp_creditnote.documentSystemID', $input['documentId']);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $master->where('erp_creditnote.confirmedYN', $input['confirmedYN']);
            }
        }
        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $master->where('erp_creditnote.approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $master->whereMonth('creditNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $master->whereYear('creditNoteDate', '=', $input['year']);
            }
        }

        /*   if (array_key_exists('year', $input)) {
               if ($input['year'] && !is_null($input['year'])) {
                   $creditNoteDate = $input['year'] . '-12-31';
                   if (array_key_exists('month', $input)) {
                       if ($input['month'] && !is_null($input['month'])) {
                           $creditNoteDate = $input['year'] . '-' . $input['month'] . '-31';
                       }
                   }

                   $master->where('creditNoteDate', '<=', $creditNoteDate);

               }
           }*/


        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $master = $master->where(function ($query) use ($search) {
                $query->Where('creditNoteCode', 'LIKE', "%{$search}%")
                    ->orwhere('employees.empName', 'LIKE', "%{$search}%")
                    ->orwhere('customermaster.CustomerName', 'LIKE', "%{$search}%")
                    ->orWhere('comments', 'LIKE', "%{$search}%");
            });
        }
        $request->request->remove('search.value');
        $master->select('creditNoteCode', 'CurrencyCode', 'erp_creditnote.approvedDate', 'creditNoteDate', 'erp_creditnote.comments', 'empName', 'DecimalPlaces', 'erp_creditnote.confirmedYN', 'erp_creditnote.approved', 'creditNoteAutoID', 'customermaster.CustomerName', 'creditAmountTrans');

        return \DataTables::of($master)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('creditNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function creditNoteReopen(request $request)
    {
        $input = $request->all();
        $creditNoteAutoID = $input['creditNoteAutoID'];

        $creditnote = CreditNote::find($creditNoteAutoID);
        $emails = array();
        if (empty($creditnote)) {
            return $this->sendError('Credit note not found');
        }

        if ($creditnote->RollLevForApp_curr > 1) {
            return $this->sendError('You cannot reopen this credit note it is already partially approved');
        }

        if ($creditnote->approved == -1) {
            return $this->sendError('You cannot reopen this credit note it is already fully approved');
        }

        if ($creditnote->confirmedYN == 0) {
            return $this->sendError('You cannot reopen this credit note, it is not confirmed');
        }

        // updating fields
        $creditnote->confirmedYN = 0;
        $creditnote->confirmedByEmpSystemID = null;
        $creditnote->confirmedByEmpID = null;
        $creditnote->confirmedByName = null;
        $creditnote->confirmedDate = null;
        $creditnote->RollLevForApp_curr = 1;
        $creditnote->save();

        $employee = \Helper::getEmployeeInfo();

        $document = DocumentMaster::where('documentSystemID', $creditnote->documentSystemiD)->first();

        $cancelDocNameBody = $document->documentDescription . ' <b>' . $creditnote->creditNoteCode . '</b>';
        $cancelDocNameSubject = $document->documentDescription . ' ' . $creditnote->creditNoteCode;

        $subject = $cancelDocNameSubject . ' is reopened';

        $body = '<p>' . $cancelDocNameBody . ' is reopened by ' . $employee->empID . ' - ' . $employee->empFullName . '</p><p>Comment : ' . $input['reopenComments'] . '</p>';

        $documentApproval = DocumentApproved::where('companySystemID', $creditnote->companySystemID)
            ->where('documentSystemCode', $creditnote->custInvoiceDirectAutoID)
            ->where('documentSystemID', $creditnote->documentSystemiD)
            ->where('rollLevelOrder', 1)
            ->first();

        if ($documentApproval) {
            if ($documentApproval->approvedYN == 0) {
                $companyDocument = CompanyDocumentAttachment::where('companySystemID', $creditnote->companySystemID)
                    ->where('documentSystemID', $creditnote->documentSystemID)
                    ->first();

                /*if (empty($companyDocument)) {
                    return ['success' => false, 'message' => 'Policy not found for this document'];
                }*/

                $approvalList = EmployeesDepartment::where('employeeGroupID', $documentApproval->approvalGroupID)
                    ->where('companySystemID', $documentApproval->companySystemID)
                    ->where('documentSystemID', $documentApproval->documentSystemID);

                if ($companyDocument['isServiceLineApproval'] == -1) {
                    $approvalList = $approvalList->where('ServiceLineSystemID', $documentApproval->serviceLineSystemID);
                }

                $approvalList = $approvalList
                    ->with(['employee'])
                    ->groupBy('employeeSystemID')
                    ->get();

                foreach ($approvalList as $da) {
                    if ($da->employee) {
                        $emails[] = array('empSystemID' => $da->employee->employeeSystemID,
                            'companySystemID' => $documentApproval->companySystemID,
                            'docSystemID' => $documentApproval->documentSystemID,
                            'alertMessage' => $subject,
                            'emailAlertMessage' => $body,
                            'docSystemCode' => $documentApproval->documentSystemCode);
                    }
                }

                $sendEmail = \Email::sendEmail($emails);
                if (!$sendEmail["success"]) {
                    return ['success' => false, 'message' => $sendEmail["message"]];
                }
            }
        }

        $deleteApproval = DocumentApproved::where('documentSystemCode', $creditNoteAutoID)
            ->where('companySystemID', $creditnote->companySystemID)
            ->where('documentSystemID', $creditnote->documentSystemiD)
            ->delete();

        return $this->sendResponse('s', 'Credit note reopened successfully');

    }

    public function creditNoteAudit(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $creditNote = $this->creditNoteRepository->with(['createduser', 'confirmed_by', 'modified_by', 'approved_by' => function ($query) {
            $query->with('employee')
                ->where('documentSystemID', 19);
        }, 'company', 'currency', 'companydocumentattachment_by' => function ($query) {
            $query->where('documentSystemID', 19);
        }])->findWithoutFail($id);


        if (empty($creditNote)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($creditNote->toArray(), 'Credit Note retrieved successfully');
    }

    public function printCreditNote(Request $request){
        $id = $request->get('id');
        $creditNote = $this->creditNoteRepository->getAudit($id);



        if (empty($creditNote)) {
            return $this->sendError('Credit note not found.');
        }


        $creditNote->docRefNo = \Helper::getCompanyDocRefNo($creditNote->companySystemID, $creditNote->documentSystemiD);

        $array = array('request' => $creditNote);
        $time = strtotime("now");
        $fileName = 'credit_note_' . $id . '_' . $time . '.pdf';
        $html = view('print.credit_note', $array);
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($html);

        return $pdf->setPaper('a4')->setWarnings(false)->stream($fileName);
    }

    public function getCreditNoteApprovedByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'confirmedYN', 'approved', 'wareHouseFrom', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $creditNote = DB::table('erp_documentapproved')
            ->select(
                'erp_creditnote.*',
                'employees.empName As created_emp',
                'currencymaster.DecimalPlaces As DecimalPlaces',
                'currencymaster.CurrencyCode As CurrencyCode',
'customermaster.CustomerName',
                'erp_documentapproved.documentApprovedID',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('erp_creditnote', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'creditNoteAutoID')
                    ->where('erp_creditnote.companySystemID', $companyId)
                    ->where('erp_creditnote.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', -1)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('customermaster', 'customerCodeSystem', 'erp_creditnote.customerID')
            ->leftJoin('currencymaster', 'currencyID', 'erp_creditnote.customerCurrencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [19])
            ->where('erp_documentapproved.companySystemID', $companyId)
            ->where('erp_documentapproved.employeeSystemID', $empID);

        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $creditNote = $creditNote->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $creditNote = $creditNote->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $creditNote = $creditNote->whereMonth('creditNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $creditNote = $creditNote->whereYear('creditNoteDate', '=', $input['year']);
            }
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $creditNote = $creditNote->where(function ($query) use ($search) {
                $query->where('creditNoteCode', 'LIKE', "%{$search}%");
                $query->orwhere('comments', 'LIKE', "%{$search}%");
                $query->orwhere('CustomerName', 'LIKE', "%{$search}%");

            });
        }

        return \DataTables::of($creditNote)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('creditNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getCreditNoteApprovalByUser(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'approved', 'month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $input['companyId'];
        $empID = \Helper::getEmployeeSystemID();

        $search = $request->input('search.value');
        $creditNote = DB::table('erp_documentapproved')
            ->select(
                'erp_creditnote.*',
                'employees.empName As created_emp',
                'currencymaster.DecimalPlaces As DecimalPlaces',
                'currencymaster.CurrencyCode As CurrencyCode',
                'erp_documentapproved.documentApprovedID',
                'customermaster.CustomerName',
                'rollLevelOrder',
                'approvalLevelID',
                'documentSystemCode')
            ->join('employeesdepartments', function ($query) use ($companyId, $empID) {
                $query->on('erp_documentapproved.approvalGroupID', '=', 'employeesdepartments.employeeGroupID')
                    ->on('erp_documentapproved.documentSystemID', '=', 'employeesdepartments.documentSystemID')
                    ->on('erp_documentapproved.companySystemID', '=', 'employeesdepartments.companySystemID');

                $serviceLinePolicy = CompanyDocumentAttachment::where('companySystemID', $companyId)
                    ->where('documentSystemID', 19)
                    ->first();

                if ($serviceLinePolicy && $serviceLinePolicy->isServiceLineApproval == -1) {
                    //$query->on('erp_documentapproved.serviceLineSystemID', '=', 'employeesdepartments.ServiceLineSystemID');
                }

                $query->whereIn('employeesdepartments.documentSystemID', [19])
                    ->where('employeesdepartments.companySystemID', $companyId)
                    ->where('employeesdepartments.employeeSystemID', $empID);
            })
            ->join('erp_creditnote', function ($query) use ($companyId, $search) {
                $query->on('erp_documentapproved.documentSystemCode', '=', 'creditNoteAutoID')
                    ->on('erp_documentapproved.rollLevelOrder', '=', 'RollLevForApp_curr')
                    ->where('erp_creditnote.companySystemID', $companyId)
                    ->where('erp_creditnote.approved', 0)
                    ->where('erp_creditnote.confirmedYN', 1);
            })
            ->where('erp_documentapproved.approvedYN', 0)
            ->leftJoin('employees', 'createdUserSystemID', 'employees.employeeSystemID')
            ->leftJoin('customermaster', 'customerCodeSystem', 'erp_creditnote.customerID')
            ->leftJoin('currencymaster', 'currencyID', 'erp_creditnote.customerCurrencyID')
            ->where('erp_documentapproved.rejectedYN', 0)
            ->whereIn('erp_documentapproved.documentSystemID', [19])
            ->where('erp_documentapproved.companySystemID', $companyId);


        if (array_key_exists('confirmedYN', $input)) {
            if (($input['confirmedYN'] == 0 || $input['confirmedYN'] == 1) && !is_null($input['confirmedYN'])) {
                $creditNote = $creditNote->where('confirmedYN', $input['confirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $creditNote = $creditNote->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $creditNote = $creditNote->whereMonth('creditNoteDate', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $creditNote = $creditNote->whereYear('creditNoteDate', '=', $input['year']);
            }
        }


        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $creditNote = $creditNote->where(function ($query) use ($search) {
                $query->where('creditNoteCode', 'LIKE', "%{$search}%");
                $query->orwhere('comments', 'LIKE', "%{$search}%");
                $query->orwhere('CustomerName', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($creditNote)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('creditNoteAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }




}
