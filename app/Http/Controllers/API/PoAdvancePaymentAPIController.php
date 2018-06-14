<?php
/**
 * =============================================
 * -- File Name : PoAdvancePaymentAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Po Advance Payment
 * -- Author : Mohamed Nazir
 * -- Create date : 02 - April 2018
 * -- Description : This file contains the all CRUD for Po Advance Payment
 * -- REVISION HISTORY
 * -- Date: 02-April 2018 By: Nazir Description: Added new functions named as poPaymentTermsAdvanceDetailView()
 * -- Date: 05-April 2018 By: Nazir Description: Added new functions named as loadPoPaymentTermsLogistic()
 * -- Date: 29-May 2018 By: Nazir Description: Added new functions named as storePoPaymentTermsLogistic()
 * -- Date: 31-April 2018 By: Nazir Description: Added new functions named as getLogisticPrintDetail()
 * -- Date: 14-June 2018 By: Nazir Description: Added new functions named as loadPoPaymentTermsLogisticForGRV()
 **/
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatePoAdvancePaymentAPIRequest;
use App\Http\Requests\API\UpdatePoAdvancePaymentAPIRequest;
use App\Models\PoAdvancePayment;
use App\Repositories\PoAdvancePaymentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use App\Models\ProcumentOrder;
use App\Models\CurrencyMaster;
use App\Models\PoPaymentTermTypes;
use App\Repositories\UserRepository;
use App\Models\SupplierMaster;
use Illuminate\Support\Facades\DB;
use App\Models\PoPaymentTerms;
use Carbon\Carbon;
use Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class PoAdvancePaymentController
 * @package App\Http\Controllers\API
 */
class PoAdvancePaymentAPIController extends AppBaseController
{
    /** @var  PoAdvancePaymentRepository */
    private $poAdvancePaymentRepository;
    private $userRepository;

    public function __construct(PoAdvancePaymentRepository $poAdvancePaymentRepo, UserRepository $userRepo)
    {
        $this->poAdvancePaymentRepository = $poAdvancePaymentRepo;
        $this->userRepository = $userRepo;
    }

    /**
     * Display a listing of the PoAdvancePayment.
     * GET|HEAD /poAdvancePayments
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poAdvancePaymentRepository->pushCriteria(new RequestCriteria($request));
        $this->poAdvancePaymentRepository->pushCriteria(new LimitOffsetCriteria($request));
        $poAdvancePayments = $this->poAdvancePaymentRepository->all();

        return $this->sendResponse($poAdvancePayments->toArray(), 'Po Advance Payments retrieved successfully');
    }

    /**
     * Store a newly created PoAdvancePayment in storage.
     * POST /poAdvancePayments
     *
     * @param CreatePoAdvancePaymentAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatePoAdvancePaymentAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['timestamp']);
        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['poID'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        if (empty($input['comAmount']) || $input['comAmount'] == 0) {
            return $this->sendError('Amount should be greater than 0');
        }


        $input['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
        $input['serviceLineID'] = $purchaseOrder->serviceLine;
        $input['companySystemID'] = $purchaseOrder->companySystemID;
        $input['companyID'] = $purchaseOrder->companyID;
        $input['supplierID'] = $purchaseOrder->supplierID;
        $input['SupplierPrimaryCode'] = $purchaseOrder->supplierPrimaryCode;
        $input['currencyID'] = $purchaseOrder->supplierTransactionCurrencyID;

        $input['poCode'] = $purchaseOrder->purchaseOrderCode;
        $input['poTermID'] = $input['paymentTermID'];
        $input['narration'] = $input['paymentTemDes'];

        if (isset($input['comDate'])) {
            $masterDate = str_replace('/', '-', $input['comDate']);
            $input['reqDate'] = date('Y-m-d', strtotime($masterDate));
        }
        $input['reqAmount'] = $input['comAmount'];
        $input['reqAmountTransCur_amount'] = $input['comAmount'];

        $companyCurrencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID, $purchaseOrder->supplierTransactionCurrencyID, $purchaseOrder->supplierTransactionCurrencyID, $input['comAmount']);

        $input['reqAmountInPOTransCur'] = $input['comAmount'];
        $input['reqAmountInPOLocalCur'] = $companyCurrencyConversion['localAmount'];
        $input['reqAmountInPORptCur'] = $companyCurrencyConversion['reportingAmount'];

        $input['requestedByEmpID'] = $user->employee['empID'];
        $input['requestedByEmpName'] = $user->employee['empName'];

        $poAdvancePayments = $this->poAdvancePaymentRepository->create($input);

        if ($poAdvancePayments) {
            $update = PoPaymentTerms::where('paymentTermID', $input['paymentTermID'])
                ->update(['isRequested' => 1]);
        }

        return $this->sendResponse($poAdvancePayments->toArray(), 'Po Advance Payment saved successfully');
    }

    /**
     * Display the specified PoAdvancePayment.
     * GET|HEAD /poAdvancePayments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */

    public function show($id)
    {
        /** @var PoAdvancePayment $poAdvancePayment */
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            return $this->sendError('Po Advance Payment not found');
        }

        return $this->sendResponse($poAdvancePayment->toArray(), 'Po Advance Payment retrieved successfully');
    }

    /**
     * Update the specified PoAdvancePayment in storage.
     * PUT/PATCH /poAdvancePayments/{id}
     *
     * @param  int $id
     * @param UpdatePoAdvancePaymentAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoAdvancePaymentAPIRequest $request)
    {
        $input = $request->all();

        /** @var PoAdvancePayment $poAdvancePayment */
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            return $this->sendError('Po Advance Payment not found');
        }

        $poAdvancePayment = $this->poAdvancePaymentRepository->update($input, $id);

        return $this->sendResponse($poAdvancePayment->toArray(), 'PoAdvancePayment updated successfully');
    }

    /**
     * Remove the specified PoAdvancePayment from storage.
     * DELETE /poAdvancePayments/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var PoAdvancePayment $poAdvancePayment */
        $poAdvancePayment = $this->poAdvancePaymentRepository->findWithoutFail($id);

        if (empty($poAdvancePayment)) {
            return $this->sendError('Po Advance Payment not found');
        }

        $poAdvancePayment->delete();

        return $this->sendResponse($id, 'Po Advance Payment deleted successfully');
    }

    public function poPaymentTermsAdvanceDetailView(Request $request)
    {
        $input = $request->all();

        $AdvancePayment = PoAdvancePayment::where('poTermID', $input['paymentTermID'])->first();

        if (empty($AdvancePayment)) {
            return $this->sendError('Po Payment Terms not found');
        }

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $AdvancePayment->poID)->first();

        $currency = CurrencyMaster::where('currencyID', $purchaseOrder->supplierTransactionCurrencyID)->first();

        $detailPaymentType = PoPaymentTermTypes::where('paymentTermsCategoryID', $AdvancePayment->LCPaymentYN)->first();


        $output = array('pomaster' => $purchaseOrder,
            'advancedetail' => $AdvancePayment,
            'currency' => $currency,
            'ptype' => $detailPaymentType
        );

        return $this->sendResponse($output, 'Data retrieved successfully');
    }

    public function loadPoPaymentTermsLogistic(Request $request)
    {
        $input = $request->all();
        $poID = $input['purchaseOrderID'];

        $items = PoAdvancePayment::where('poID', $poID)
            ->where('poTermID', 0)
            ->where('confirmedYN', 1)
            ->where('isAdvancePaymentYN', 1)
            ->where('approvedYN', -1)
            ->with(['currency', 'supplier_by' => function ($query) {
            }])->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function storePoPaymentTermsLogistic(Request $request)
    {
        $input = $request->all();

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $input['purchaseOrderID'])
            ->first();

        if (empty($purchaseOrder)) {
            return $this->sendError('Purchase Order not found');
        }

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['detail']['supplierID'])->first();

        if (empty($supplier)) {
            return $this->sendError('Supplier not found');
        }

        // checking grv detail exist

        $detail = DB::select('SELECT
	erp_grvmaster.grvAutoID,
	erp_grvmaster.grvPrimaryCode,
	erp_grvdetails.purchaseOrderMastertID
FROM
	erp_grvmaster
INNER JOIN erp_grvdetails ON erp_grvmaster.grvAutoID = erp_grvdetails.grvAutoID
WHERE
	erp_grvmaster.grvConfirmedYN = 1
AND erp_grvmaster.approved = 0
GROUP BY
	erp_grvmaster.grvAutoID,
	erp_grvmaster.grvPrimaryCode,
	erp_grvdetails.purchaseOrderMastertID
HAVING
	erp_grvdetails.purchaseOrderMastertID = ' . $input['purchaseOrderID'] . '
ORDER BY
	erp_grvmaster.grvAutoID DESC');

        if (!empty($detail) && empty($input['detail']['grvAutoID'])) {
            return $this->sendError('Please select a GRV as there is a GRV done for this PO');
        }

        $input['serviceLineSystemID'] = $purchaseOrder->serviceLineSystemID;
        $input['serviceLineID'] = $purchaseOrder->serviceLine;
        $input['companySystemID'] = $purchaseOrder->companySystemID;
        $input['companyID'] = $purchaseOrder->companyID;
        $input['SupplierPrimaryCode'] = $purchaseOrder->supplierPrimaryCode;

        $input['poID'] = $input['purchaseOrderID'];
        $input['poCode'] = $purchaseOrder->purchaseOrderCode;
        $input['narration'] = $input['detail']['narration'];

        //grv code sorting
        if (isset($input['detail']['grvAutoID']) && !empty($input['detail']['grvAutoID'])) {
            $input['grvAutoID'] = $input['detail']['grvAutoID'];
        } else {
            $input['grvAutoID'] = 0;
        }
        if (isset($input['detail']['reqDate'])) {
            $masterDate = str_replace('/', '-', $input['detail']['reqDate']);
            $input['reqDate'] = date('Y-m-d', strtotime($masterDate));
        }
        $input['currencyID'] = $input['detail']['currencyID'][0];
        $input['reqAmount'] = $input['detail']['reqAmount'];
        $input['reqAmountTransCur_amount'] = $input['detail']['reqAmount'];

        $companyCurrencyConversion = \Helper::currencyConversion($purchaseOrder->companySystemID,  $input['currencyID'], $purchaseOrder->supplierTransactionCurrencyID, $input['detail']['reqAmount']);

        //$input['detail']['reqAmount'];
        $input['reqAmountInPOTransCur'] = $companyCurrencyConversion['documentAmount'];
        $input['reqAmountInPOLocalCur'] = $companyCurrencyConversion['localAmount'];
        $input['reqAmountInPORptCur'] = $companyCurrencyConversion['reportingAmount'];

        $input['requestedByEmpID'] = $user->employee['empID'];
        $input['requestedByEmpName'] = $user->employee['empName'];

        //updating supplier details coloums
        if ($supplier) {
            $input['supplierID'] = $input['detail']['supplierID'];
            $input['SupplierPrimaryCode'] = $supplier->primarySupplierCode;
        }

        //updating default coloums
        $input['poTermID'] = 0;
        $input['confirmedYN'] = 1;
        $input['approvedYN'] = -1;
        $input['isAdvancePaymentYN'] = 1;
        $input['selectedToPayment'] = 0;
        $input['fullyPaid'] = 0;

        $poAdvancePayments = $this->poAdvancePaymentRepository->create($input);

        return $this->sendResponse($poAdvancePayments->toArray(), 'Po Advance Payment saved successfully');
    }

    public function getLogisticPrintDetail(Request $request)
    {
        $input = $request->all();
        $poAdvPaymentID = $input['poAdvPaymentID'];

        $items = PoAdvancePayment::where('poAdvPaymentID', $poAdvPaymentID)
            ->with(['company', 'currency', 'supplier_by' => function ($query) {
            }])->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }

    public function loadPoPaymentTermsLogisticForGRV(Request $request)
    {
        $input = $request->all();
        $grvAutoID= $input['grvAutoID'];

        $items = PoAdvancePayment::where('grvAutoID', $grvAutoID)
            ->where('confirmedYN', 1)
            ->where('approvedYN', -1)
            ->with(['currency', 'supplier_by' => function ($query) {
            }])->get();

        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');
    }


}
