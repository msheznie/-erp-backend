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
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class PoAdvancePaymentController
 * @package App\Http\Controllers\API
 */

class PoAdvancePaymentAPIController extends AppBaseController
{
    /** @var  PoAdvancePaymentRepository */
    private $poAdvancePaymentRepository;

    public function __construct(PoAdvancePaymentRepository $poAdvancePaymentRepo)
    {
        $this->poAdvancePaymentRepository = $poAdvancePaymentRepo;
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

        $poAdvancePayments = $this->poAdvancePaymentRepository->create($input);

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


}
