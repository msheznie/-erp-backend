<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateExpenseClaimMasterAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimMasterAPIRequest;
use App\Models\ExpenseClaimMaster;
use App\Repositories\ExpenseClaimMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseClaimMasterController
 * @package App\Http\Controllers\API
 */

class ExpenseClaimMasterAPIController extends AppBaseController
{
    /** @var  ExpenseClaimMasterRepository */
    private $expenseClaimMasterRepository;

    public function __construct(ExpenseClaimMasterRepository $expenseClaimMasterRepo)
    {
        $this->expenseClaimMasterRepository = $expenseClaimMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimMasters",
     *      summary="Get a listing of the ExpenseClaimMasters.",
     *      tags={"ExpenseClaimMaster"},
     *      description="Get all ExpenseClaimMasters",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaimMaster")
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
        $this->expenseClaimMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaimMasters = $this->expenseClaimMasterRepository->all();

        return $this->sendResponse($expenseClaimMasters->toArray(), 'Expense Claim Masters retrieved successfully');
    }

    /**
     * @param CreateExpenseClaimMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseClaimMasters",
     *      summary="Store a newly created ExpenseClaimMaster in storage",
     *      tags={"ExpenseClaimMaster"},
     *      description="Store ExpenseClaimMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimMaster")
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
     *                  ref="#/definitions/ExpenseClaimMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimMasterAPIRequest $request)
    {
        $input = $request->all();

        $expenseClaimMaster = $this->expenseClaimMasterRepository->create($input);

        return $this->sendResponse($expenseClaimMaster->toArray(), 'Expense Claim Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimMasters/{id}",
     *      summary="Display the specified ExpenseClaimMaster",
     *      tags={"ExpenseClaimMaster"},
     *      description="Get ExpenseClaimMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimMaster",
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
     *                  ref="#/definitions/ExpenseClaimMaster"
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
        /** @var ExpenseClaimMaster $expenseClaimMaster */
        $expenseClaimMaster = $this->expenseClaimMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimMaster)) {
            return $this->sendError('Expense Claim Master not found');
        }

        return $this->sendResponse($expenseClaimMaster->toArray(), 'Expense Claim Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseClaimMasters/{id}",
     *      summary="Update the specified ExpenseClaimMaster in storage",
     *      tags={"ExpenseClaimMaster"},
     *      description="Update ExpenseClaimMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimMaster")
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
     *                  ref="#/definitions/ExpenseClaimMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ExpenseClaimMaster $expenseClaimMaster */
        $expenseClaimMaster = $this->expenseClaimMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimMaster)) {
            return $this->sendError('Expense Claim Master not found');
        }

        $expenseClaimMaster = $this->expenseClaimMasterRepository->update($input, $id);

        return $this->sendResponse($expenseClaimMaster->toArray(), 'ExpenseClaimMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaimMasters/{id}",
     *      summary="Remove the specified ExpenseClaimMaster from storage",
     *      tags={"ExpenseClaimMaster"},
     *      description="Delete ExpenseClaimMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimMaster",
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
        /** @var ExpenseClaimMaster $expenseClaimMaster */
        $expenseClaimMaster = $this->expenseClaimMasterRepository->findWithoutFail($id);

        if (empty($expenseClaimMaster)) {
            return $this->sendError('Expense Claim Master not found');
        }

        $expenseClaimMaster->delete();

        return $this->sendSuccess('Expense Claim Master deleted successfully');
    }

    public function getExpenseClaimMasterAudit(Request $request)
    {
        $id = $request->get('id');
        $expenseClaim = $this->expenseClaimMasterRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $expenseClaim->docRefNo = \Helper::getCompanyDocRefNo($expenseClaim->companyID, $expenseClaim->documentID);

        return $this->sendResponse($expenseClaim->toArray(), 'Expense Claim retrieved successfully');
    }

    public function printExpenseClaimMaster(Request $request)
    {
        $id = $request->get('id');
        $lang = $request->get('lang', 'en');
        $expenseClaim = $this->expenseClaimMasterRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $expenseClaim->docRefNo = \Helper::getCompanyDocRefNo($expenseClaim->companyID, $expenseClaim->documentID);
        $expenseClaim->localDecimal = 3;
        $expenseClaim->localDecimal = 'OMR';
        $expenseClaim->total = 0;


        $grandTotal = collect($expenseClaim->details)->pluck('companyLocalAmount')->toArray();
        $expenseClaim->total = array_sum($grandTotal);

        foreach ($expenseClaim->details as $item) {
            $item->currencyDecimal = 2;
            $item->localDecimal = 3;

            if ($item->currency) {
                $item->currencyDecimal = $item->currency->DecimalPlaces;
            }
            if ($item->local_currency) {
                $item->localDecimal = $item->local_currency->DecimalPlaces;
            }
        }

        if ($expenseClaim->company) {
            if ($expenseClaim->company->localcurrency) {
                $expenseClaim->localDecimal = $expenseClaim->company->localcurrency->DecimalPlaces;
                $expenseClaim->localCurrencyCode = $expenseClaim->company->localcurrency->CurrencyCode;
            }
        }

        $array = array('entity' => $expenseClaim, 'lang' => $lang);
        $time = strtotime("now");
        $fileName = 'expense_claim' . $id . '_' . $time . '.pdf';
        
        // Check if Arabic language for RTL support
        $isRTL = ($lang === 'ar');
        
        // Configure mPDF for RTL support if Arabic
        $mpdfConfig = [
            'tempDir' => public_path('tmp'), 
            'mode' => 'utf-8', 
            'format' => 'A4-L', 
            'setAutoTopMargin' => 'stretch', 
            'autoMarginPadding' => -10
        ];
        
        if ($isRTL) {
            $mpdfConfig['direction'] = 'rtl';
        }
        
        $html = view('print.expense_claim', $array);
        $mpdf = new \Mpdf\Mpdf($mpdfConfig);
        $mpdf->AddPage('L');
        $mpdf->setAutoBottomMargin = 'stretch';
        $mpdf->WriteHTML($html);
        return $mpdf->Output($fileName, 'I');
    }


    public function getExpenseClaimMasterByCompany(Request $request)
    {

        $input = $request->all();

        $input = $this->convertArrayToSelectedValue($input, array('confirmedYN', 'glCodeAssignedYN', 'approvedYN', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $search = $request->input('search.value');

        $expenseClaims = $this->expenseClaimMasterRepository->expenseClaimMasterListQuery($request, $input, $search);

        return \DataTables::of($expenseClaims)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('expenseClaimMasterAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getExpenseClaimMasterPaymentStatusHistory(Request $request)
    {
        $id = $request->get('id');
        $expenseClaim = $this->expenseClaimMasterRepository->getAudit($id);

        if (empty($expenseClaim)) {
            return $this->sendError('Expense Claim not found');
        }

        $detail = \DB::select('SELECT
                                erp_paysupplierinvoicemaster.BPVNarration,
                                erp_paysupplierinvoicemaster.BPVcode,
                                erp_paysupplierinvoicemaster.documentID,
                                erp_paysupplierinvoicemaster.BPVdate,
                                erp_paysupplierinvoicemaster.companyID,
                                erp_paysupplierinvoicemaster.createdUserID,
                                employees.empName,
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_directpaymentdetails.expenseClaimMasterAutoID 
                            FROM
                                ( erp_directpaymentdetails INNER JOIN erp_paysupplierinvoicemaster ON erp_directpaymentdetails.directPaymentAutoID = erp_paysupplierinvoicemaster.PayMasterAutoId )
                                LEFT JOIN employees ON erp_paysupplierinvoicemaster.createdUserID = employees.empID 
                            WHERE
                                erp_directpaymentdetails.expenseClaimMasterAutoID = ' . $id . '
                            GROUP BY
                                erp_paysupplierinvoicemaster.PayMasterAutoId,
                                erp_paysupplierinvoicemaster.documentSystemID,
                                erp_paysupplierinvoicemaster.companySystemID,
                                erp_directpaymentdetails.expenseClaimMasterAutoID 
                            HAVING
                                ( ( ( erp_directpaymentdetails.expenseClaimMasterAutoID ) != 0 ) ) UNION ALL
                            SELECT
                                srp_erp_payrollmaster.narration AS BPVNarration,
                                srp_erp_payrollmaster.documentCode AS BPVcode,
                                srp_erp_payrollmaster.documentID AS documentID,
                                CONCAT(srp_erp_payrollmaster.payrollYear, "/", srp_erp_payrollmaster.payrollMonth, "/","1") AS BPVdate,
                                srp_erp_payrollmaster.companyID,
                                srp_erp_payrollmaster.confirmedByEmpID,
                                employees.empName,
                                NULL AS PayMasterAutoId,
                                NULL AS expenseClaimMasterAutoID 
                            FROM
                                ( srp_erp_payrolldetail INNER JOIN srp_erp_payrollmaster ON srp_erp_payrolldetail.payrollMasterID = srp_erp_payrollmaster.payrollMasterID )
                                LEFT JOIN employees ON srp_erp_payrollmaster.confirmedByEmpID = employees.empID
                            WHERE
                                `srp_erp_payrolldetail`.fromTB="EC" and detailTBID= ' . $id . '
                            ;');


        return $this->sendResponse($detail, 'payment status retrieved successfully');
    }
}
