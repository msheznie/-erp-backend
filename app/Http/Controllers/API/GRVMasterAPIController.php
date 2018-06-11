<?php
/**
 * =============================================
 * -- File Name : GRVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Master
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for GRV Master
 * -- REVISION HISTORY
 * -- Date: 06-June 2018 By: Nazir Description: Added new functions named as getGoodReceiptVoucherMasterView() For load Master View
 * -- Date: 06-June 2018 By: Nazir Description: Added new functions named as getGRVFormData() For load Master View
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateGRVMasterAPIRequest;
use App\Http\Requests\API\UpdateGRVMasterAPIRequest;
use App\Models\GRVMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\Months;
use App\Models\SupplierAssigned;
use App\Models\CurrencyMaster;
use App\Models\Location;
use App\Repositories\GRVMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class GRVMasterController
 * @package App\Http\Controllers\API
 */
class GRVMasterAPIController extends AppBaseController
{
    /** @var  GRVMasterRepository */
    private $gRVMasterRepository;

    public function __construct(GRVMasterRepository $gRVMasterRepo)
    {
        $this->gRVMasterRepository = $gRVMasterRepo;
    }

    /**
     * Display a listing of the GRVMaster.
     * GET|HEAD /gRVMasters
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->gRVMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->gRVMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $gRVMasters = $this->gRVMasterRepository->all();

        return $this->sendResponse($gRVMasters->toArray(), 'G R V Masters retrieved successfully');
    }

    /**
     * Store a newly created GRVMaster in storage.
     * POST /gRVMasters
     *
     * @param CreateGRVMasterAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateGRVMasterAPIRequest $request)
    {
        $input = $request->all();

        $gRVMasters = $this->gRVMasterRepository->create($input);

        return $this->sendResponse($gRVMasters->toArray(), 'G R V Master saved successfully');
    }

    /**
     * Display the specified GRVMaster.
     * GET|HEAD /gRVMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        return $this->sendResponse($gRVMaster->toArray(), 'Good Receipt Voucher retrieved successfully');
    }

    /**
     * Update the specified GRVMaster in storage.
     * PUT/PATCH /gRVMasters/{id}
     *
     * @param  int $id
     * @param UpdateGRVMasterAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateGRVMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher Master not found');
        }

        $gRVMaster = $this->gRVMasterRepository->update($input, $id);

        return $this->sendResponse($gRVMaster->toArray(), 'Good Receipt Voucher master updated successfully');
    }

    /**
     * Remove the specified GRVMaster from storage.
     * DELETE /gRVMasters/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var GRVMaster $gRVMaster */
        $gRVMaster = $this->gRVMasterRepository->findWithoutFail($id);

        if (empty($gRVMaster)) {
            return $this->sendError('Good Receipt Voucher not found');
        }

        $gRVMaster->delete();

        return $this->sendResponse($id, 'Good Receipt Voucher deleted successfully');
    }

    public function getGoodReceiptVoucherMasterView(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('serviceLineSystemID', 'poCancelledYN', 'poConfirmedYN', 'approved', 'grvRecieved', 'month', 'year', 'invoicedBooked'));
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $grvMaster = GRVMaster::where('companySystemID', $input['companyId']);
        $grvMaster->where('documentSystemID', $input['documentId']);
        $grvMaster->with(['created_by' => function ($query) {
        }, 'segment_by' => function ($query) {
        }, 'location_by' => function ($query) {
        }, 'supplier_by' => function ($query) {
        }, 'currency_by' => function ($query) {
        }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            if ($input['serviceLineSystemID'] && !is_null($input['serviceLineSystemID'])) {
                $grvMaster->where('serviceLineSystemID', $input['serviceLineSystemID']);
            }
        }

        if (array_key_exists('grvCancelledYN', $input)) {
            if (($input['grvCancelledYN'] == 0 || $input['grvCancelledYN'] == -1) && !is_null($input['grvCancelledYN'])) {
                $grvMaster->where('grvCancelledYN', $input['grvCancelledYN']);
            }
        }

        if (array_key_exists('grvConfirmedYN', $input)) {
            if (($input['grvConfirmedYN'] == 0 || $input['grvConfirmedYN'] == 1) && !is_null($input['grvConfirmedYN'])) {
                $grvMaster->where('grvConfirmedYN', $input['grvConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $grvMaster->where('approved', $input['approved']);
            }
        }

        if (array_key_exists('month', $input)) {
            if ($input['month'] && !is_null($input['month'])) {
                $grvMaster->whereMonth('createdDateTime', '=', $input['month']);
            }
        }

        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $grvMaster->whereYear('createdDateTime', '=', $input['year']);
            }
        }

        $grvMaster = $grvMaster->select(
            ['erp_grvmaster.grvAutoID',
                'erp_grvmaster.grvPrimaryCode',
                'erp_grvmaster.documentSystemID',
                'erp_grvmaster.grvDoRefNo',
                'erp_grvmaster.createdDateTime',
                'erp_grvmaster.createdUserSystemID',
                'erp_grvmaster.grvNarration',
                'erp_grvmaster.grvLocation',
                'erp_grvmaster.grvDate',
                'erp_grvmaster.supplierID',
                'erp_grvmaster.serviceLineSystemID',
                'erp_grvmaster.grvConfirmedDate',
                'erp_grvmaster.approvedDate',
                'erp_grvmaster.supplierTransactionCurrencyID',
                'erp_grvmaster.grvTotalSupplierTransactionCurrency',
                'erp_grvmaster.grvCancelledYN',
                'erp_grvmaster.timesReferred',
                'erp_grvmaster.grvConfirmedYN',
                'erp_grvmaster.approved'
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $grvMaster = $grvMaster->where(function ($query) use ($search) {
                $query->where('grvPrimaryCode', 'LIKE', "%{$search}%")
                    ->orWhere('grvNarration', 'LIKE', "%{$search}%")
                    ->orWhere('supplierName', 'LIKE', "%{$search}%");
            });
        }


        $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $input['companyId'])->first();

        $policy = 0;

        if (!empty($historyPolicy)) {
            $policy = $historyPolicy->isYesNO;
        }

        return \DataTables::eloquent($grvMaster)
            ->addColumn('Actions', $policy)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('grvAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getGRVFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $grvAutoID = $request['grvAutoID'];

        $segments = SegmentMaster::where("companySystemID", $companyId);
        if (isset($request['type']) && $request['type'] != 'filter') {
            $segments = $segments->where('isActive', 1);
        }
        $segments = $segments->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = GRVMaster::select(DB::raw("YEAR(createdDateTime) as year"))
            ->whereNotNull('createdDateTime')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();

        $supplier = SupplierAssigned::select(DB::raw("supplierCodeSytem,CONCAT(primarySupplierCode, ' | ' ,supplierName) as supplierName"))
            ->where('companySystemID', $companyId)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $locations = Location::all();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
            ->where('companySystemID', $companyId)
            ->first();


        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'locations' => $locations,
            'financialYears' => $financialYears,
            'suppliers' => $supplier,
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
