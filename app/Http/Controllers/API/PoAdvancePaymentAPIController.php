<?php

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
            if ($input['comDate']) {
                $input['reqDate'] = new Carbon($input['comDate']);
            }
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


}
