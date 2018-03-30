<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProcumentOrderAPIRequest;
use App\Http\Requests\API\UpdateProcumentOrderAPIRequest;
use App\Models\Months;
use App\Models\ProcumentOrder;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\ProcumentOrderRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;


/**
 * Class ProcumentOrderController
 * @package App\Http\Controllers\API
 */
class ProcumentOrderAPIController extends AppBaseController
{
    /** @var  ProcumentOrderRepository */
    private $procumentOrderRepository;

    public function __construct(ProcumentOrderRepository $procumentOrderRepo)
    {
        $this->procumentOrderRepository = $procumentOrderRepo;
    }

    /**
     * Display a listing of the ProcumentOrder.
     * GET|HEAD /procumentOrders
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->procumentOrderRepository->pushCriteria(new RequestCriteria($request));
        $this->procumentOrderRepository->pushCriteria(new LimitOffsetCriteria($request));
        $procumentOrders = $this->procumentOrderRepository->all();

        return $this->sendResponse($procumentOrders->toArray(), 'Procument Orders retrieved successfully');
    }

    /**
     * Store a newly created ProcumentOrder in storage.
     * POST /procumentOrders
     *
     * @param CreateProcumentOrderAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateProcumentOrderAPIRequest $request)
    {
        $input = $request->all();

        $procumentOrders = $this->procumentOrderRepository->create($input);

        return $this->sendResponse($procumentOrders->toArray(), 'Procument Order saved successfully');
    }

    /**
     * Display the specified ProcumentOrder.
     * GET|HEAD /procumentOrders/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procument Order not found');
        }

        return $this->sendResponse($procumentOrder->toArray(), 'Procument Order retrieved successfully');
    }

    /**
     * Update the specified ProcumentOrder in storage.
     * PUT/PATCH /procumentOrders/{id}
     *
     * @param  int $id
     * @param UpdateProcumentOrderAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateProcumentOrderAPIRequest $request)
    {
        $input = $request->all();

        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procument Order not found');
        }

        $procumentOrder = $this->procumentOrderRepository->update($input, $id);

        return $this->sendResponse($procumentOrder->toArray(), 'ProcumentOrder updated successfully');
    }

    /**
     * Remove the specified ProcumentOrder from storage.
     * DELETE /procumentOrders/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procument Order not found');
        }

        $procumentOrder->delete();

        return $this->sendResponse($id, 'Procument Order deleted successfully');
    }

    public function getProcumentOrderByDocumentType(Request $request)
    {
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $procumentOrders = ProcumentOrder::where('companySystemID', $input['companyId'])
            ->where('documentSystemID', $input['documentId'])
            ->with(['created_by' => function ($query) {
                //$query->select(['empName']);
            }, 'location' => function ($query) {
            }, 'supplier' => function ($query) {
            }, 'currency' => function ($query) {
            }, 'segment' => function ($query) {

            }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            $procumentOrders->where('serviceLineSystemID', $input['serviceLineSystemID']);
        }

        if (array_key_exists('cancelledYN', $input)) {
            if ($input['cancelledYN'] == 0 || $input['cancelledYN'] == -1) {
                $procumentOrders->where('cancelledYN', $input['cancelledYN']);
            }
        }

        if (array_key_exists('PRConfirmedYN', $input)) {
            if ($input['PRConfirmedYN'] == 0 || $input['PRConfirmedYN'] == 1) {
                $procumentOrders->where('PRConfirmedYN', $input['PRConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if ($input['approved'] == 0 || $input['approved'] == 1) {
                $procumentOrders->where('PRConfirmedYN', $input['PRConfirmedYN']);
            }
        }

        if (array_key_exists('month', $input)) {
            $procumentOrders->whereMonth('createdDateTime', '=', $input['month']);
        }

        if (array_key_exists('year', $input)) {
            $procumentOrders->whereYear('createdDateTime', '=', $input['year']);
        }

        $procumentOrders = $procumentOrders->select(
            ['erp_purchaseordermaster.purchaseOrderID',
                'erp_purchaseordermaster.purchaseOrderCode',
                'erp_purchaseordermaster.budgetYear',
                'erp_purchaseordermaster.createdDateTime',
                'erp_purchaseordermaster.createdUserSystemID',
                'erp_purchaseordermaster.narration',
                'erp_purchaseordermaster.poLocation',
                'erp_purchaseordermaster.poCancelledYN',
                'erp_purchaseordermaster.poConfirmedYN',
                'erp_purchaseordermaster.poConfirmedDate',
                'erp_purchaseordermaster.approved',
                'erp_purchaseordermaster.approvedDate',
                'erp_purchaseordermaster.timesReferred',
                'erp_purchaseordermaster.serviceLineSystemID',
                'erp_purchaseordermaster.supplierID',
                'erp_purchaseordermaster.supplierName',
                'erp_purchaseordermaster.expectedDeliveryDate',
                'erp_purchaseordermaster.referenceNumber',
                'erp_purchaseordermaster.supplierTransactionCurrencyID',
                'erp_purchaseordermaster.poTotalSupplierTransactionCurrency',
            ]);

        return \DataTables::eloquent($procumentOrders)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('purchaseOrderID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
        ///return $this->sendResponse($supplierMasters->toArray(), 'Supplier Masters retrieved successfully');*/
    }
}
