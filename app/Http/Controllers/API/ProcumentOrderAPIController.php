<?php
/**
 * =============================================
 * -- File Name : PurchaseRequestAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Purchase Order
 * -- Author : Mohamed Nazir
 * -- Create date : 28 - March 2018
 * -- Description : This file contains the all CRUD for Purchase Order
 * -- REVISION HISTORY
 * -- Date: 28-March 2018 By: Nazir Description: Added new functions named as getProcumentOrderByDocumentType() For load Master View
 * -- Date: 29-March 2018 By: Nazir Description: Added new functions named as getProcumentOrderFormData() for Master View Filter
 * -- Date: 10-April 2018 By: Nazir Description: Added new functions named as getShippingAndInvoiceDetails() for pull details from erp_address table
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateProcumentOrderAPIRequest;
use App\Http\Requests\API\UpdateProcumentOrderAPIRequest;
use App\Models\Months;
use App\Models\Company;
use App\Models\SupplierMaster;
use App\Models\CompanyPolicyMaster;
use App\Models\CurrencyMaster;
use App\Models\DocumentMaster;
use App\Models\FinanceItemCategoryMaster;
use App\Models\Location;
use App\Models\ProcumentOrder;
use App\Models\SegmentMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\ItemAssigned;
use App\Models\PurchaseOrderDetails;
use App\Models\ErpAddress;
use App\Models\PoPaymentTermTypes;
use App\Models\SupplierAssigned;
use App\Models\CompanyDocumentAttachment;
use App\Repositories\ProcumentOrderRepository;
use Illuminate\Http\Request;
use App\Repositories\UserRepository;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


/**
 * Class ProcumentOrderController
 * @package App\Http\Controllers\API
 */
class ProcumentOrderAPIController extends AppBaseController
{
    /** @var  ProcumentOrderRepository */
    private $procumentOrderRepository;
    private $userRepository;

    public function __construct(ProcumentOrderRepository $procumentOrderRepo, UserRepository $userRepo)
    {
        $this->procumentOrderRepository = $procumentOrderRepo;
        $this->userRepository = $userRepo;
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
        $input = $this->convertArrayToValue($input);

        $id = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($id);

        $input['createdPcID'] = gethostname();
        $input['createdUserID'] = $user->employee['empID'];
        $input['createdUserSystemID'] = $user->employee['empCompanySystemID'];
        $input['departmentID'] = 'PROC';

        $lastSerial = ProcumentOrder::where('companySystemID', $input['companySystemID'])
            ->orderBy('purchaseOrderID', 'desc')
            ->first();

        $lastSerialNumber = 0;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->serialNumber) + 1;
        }

        $erpAddress = ErpAddress::where("companySystemID", $input['companySystemID'])
            ->where('isDefault', -1)
            ->get();

        if(!empty($erpAddress)){
            foreach($erpAddress as $address){
                if($address['addressTypeID'] == 1){
                    $input['shippingAddressID']             = $address['addressID'];
                    $input['shippingAddressDescriprion']    = $address['addressDescrption'];
                    $input['shipTocontactPersonID']         = $address['contactPersonID'];
                    $input['shipTocontactPersonTelephone']  = $address['contactPersonTelephone'];
                    $input['shipTocontactPersonFaxNo']      = $address['contactPersonFaxNo'];
                    $input['shipTocontactPersonEmail']      = $address['contactPersonEmail'];
                }else if($address['addressTypeID'] == 2){
                    $input['invoiceToAddressID']             = $address['addressID'];
                    $input['invoiceToAddressDescription']    = $address['addressDescrption'];
                    $input['invoiceTocontactPersonID']         = $address['contactPersonID'];
                    $input['invoiceTocontactPersonTelephone']  = $address['contactPersonTelephone'];
                    $input['invoiceTocontactPersonFaxNo']      = $address['contactPersonFaxNo'];
                    $input['invoiceTocontactPersonEmail']      = $address['contactPersonEmail'];
                }else if($address['addressTypeID'] == 3){
                    $input['soldToAddressID']             = $address['addressID'];
                    $input['soldToAddressDescriprion']    = $address['addressDescrption'];
                    $input['soldTocontactPersonID']         = $address['contactPersonID'];
                    $input['soldTocontactPersonTelephone']  = $address['contactPersonTelephone'];
                    $input['soldTocontactPersonFaxNo']      = $address['contactPersonFaxNo'];
                    $input['soldTocontactPersonEmail']      = $address['contactPersonEmail'];
                }
            }
        }

        $input['serialNumber'] = $lastSerialNumber;
        $input['purchaseOrderCode'] = $lastSerialNumber;

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLine'] = $segment->ServiceLineCode;
        }

        if (isset($input['expectedDeliveryDate'])) {
            if ($input['expectedDeliveryDate']) {
                $input['expectedDeliveryDate'] = new Carbon($input['expectedDeliveryDate']);
            }
        }

        $document = DocumentMaster::where('documentSystemID', $input['documentSystemID'])->first();
        if ($document) {
            $input['documentID'] = $document->documentID;
        }

        $companyDocumentAttachment = CompanyDocumentAttachment::where('companySystemID', $input['companySystemID'])
            ->where('documentSystemID', $input['documentSystemID'])
            ->first();

        if ($companyDocumentAttachment) {
            $input['docRefNo'] = $companyDocumentAttachment->docRefNumber;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], 0);

        //var_dump($companyCurrencyConversion);
        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
            $input['localCurrencyID'] = $company->localCurrencyID;
            $input['companyReportingCurrencyID'] = $company->reportingCurrency;
            $input['companyReportingER'] = $companyCurrencyConversion['trasToRptER'];
            $input['localCurrencyER'] = $companyCurrencyConversion['trasToLocER'];
        }

        $supplier = SupplierMaster::where('supplierCodeSystem', $input['supplierID'])->first();
        if ($supplier) {
            $input['supplierPrimaryCode'] = $supplier->primarySupplierCode;
            $input['supplierName'] = $supplier->supplierName;
            $input['supplierAddress'] = $supplier->address;
            $input['supplierTelephone'] = $supplier->telephone;
            $input['supplierFax'] = $supplier->fax;
            $input['supplierEmail'] = $supplier->supEmail;
            $input['creditPeriod'] = $supplier->creditPeriod;

            $input['supplierDefaultCurrencyID'] = $supplier->currency;
            //$input['supplierDefaultER'] = ;
            $input['supplierTransactionER'] = 1;

        }

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
        $procumentOrder = $this->procumentOrderRepository->with(['created_by', 'confirmed_by'])->findWithoutFail($id);

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
        //$empInfo = self::getEmployeeInfo();
        $userId = Auth::id();
        $user = $this->userRepository->with(['employee'])->findWithoutFail($userId);

        $input = $request->all();
        $input = array_except($input, ['created_by', 'confirmed_by']);
        $input = $this->convertArrayToValue($input);

        if (isset($input['expectedDeliveryDate'])) {
            if ($input['expectedDeliveryDate']) {
                $input['expectedDeliveryDate'] = new Carbon($input['expectedDeliveryDate']);
            }
        }

        /** @var ProcumentOrder $procumentOrder */
        $procumentOrder = $this->procumentOrderRepository->findWithoutFail($id);

        if (empty($procumentOrder)) {
            return $this->sendError('Procurement Order not found');
        }

        $segment = SegmentMaster::where('serviceLineSystemID', $input['serviceLineSystemID'])->first();
        if ($segment) {
            $input['serviceLineCode'] = $segment->ServiceLineCode;
        }

        $input['modifiedPc'] = gethostname();
        $input['modifiedUser'] = $user->employee['empID'];
        $input['modifiedUserSystemID'] = $user->employee['employeeSystemID'];

        if ($procumentOrder->poConfirmedYN == 0 && $input['poConfirmedYN'] == 1) {

            $poDetailExist = PurchaseOrderDetails::select(DB::raw('purchaseOrderDetailsID'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->first();

            if (empty($poDetailExist)) {
                return $this->sendError('PO Document cannot confirm without details');
            }

            unset($input['poConfirmedYN']);
            unset($input['poConfirmedByEmpSystemID']);
            unset($input['poConfirmedByEmpID']);
            unset($input['poConfirmedByName']);
            unset($input['poConfirmedDate']);

            //getting total sum of PO detail Amount
            $poMasterSum = PurchaseOrderDetails::select(DB::raw('COALESCE(SUM(netAmount),0) as masterTotalSum'))
                ->where('purchaseOrderMasterID', $input['purchaseOrderID'])
                ->first();

            if (!empty($poMasterSum)) {

                $currencyConversionMaster = \Helper::currencyConversion($input["companySystemID"], $input['supplierTransactionCurrencyID'], $input['supplierTransactionCurrencyID'], $poMasterSum['masterTotalSum']);

                $input['poTotalComRptCurrency'] = $currencyConversionMaster['reportingAmount'];
                $input['poTotalLocalCurrency'] = $currencyConversionMaster['localAmount'];
                $input['poTotalSupplierDefaultCurrency'] = 0;
                $input['poTotalSupplierTransactionCurrency'] = $poMasterSum['masterTotalSum'];

                $params = array('autoID' => $id, 'company' => $input["companySystemID"], 'document' => $input["documentSystemID"], 'segment' => $input["serviceLineSystemID"], 'category' => $input["financeCategory"], 'amount' => $poMasterSum['masterTotalSum']);
                $confirm = \Helper::confirmDocument($params);
                if (!$confirm["success"]) {
                    return $this->sendError($confirm["message"]);
                }
            }

        }

        $procumentOrder = $this->procumentOrderRepository->update($input, $id);

        return $this->sendResponse($procumentOrder->toArray(), 'Procurement Order updated successfully');
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
            $sort ='desc';
        }

        $procumentOrders = ProcumentOrder::where('companySystemID', $input['companyId'])
            ->where('documentSystemID', $input['documentId'])
            ->with(['created_by' => function ($query) {
                //$query->select(['empName']);
            }, 'location' => function ($query) {
            }, 'supplier' => function ($query) {
            }, 'currency' => function ($query) {
            }, 'fcategory' => function ($query) {
            }, 'segment' => function ($query) {
            }]);

        if (array_key_exists('serviceLineSystemID', $input)) {
            $procumentOrders->where('serviceLineSystemID', $input['serviceLineSystemID']);
        }

        if (array_key_exists('poCancelledYN', $input)) {
            if ($input['poCancelledYN'] == 0 || $input['poCancelledYN'] == -1) {
                $procumentOrders->where('poCancelledYN', $input['poCancelledYN']);
            }
        }

        if (array_key_exists('poConfirmedYN', $input)) {
            if ($input['poConfirmedYN'] == 0 || $input['poConfirmedYN'] == 1) {
                $procumentOrders->where('poConfirmedYN', $input['poConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if ($input['approved'] == 0 || $input['approved'] == 1) {
                $procumentOrders->where('approved', $input['approved']);
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
                'erp_purchaseordermaster.financeCategory',
            ]);

        $search = $request->input('search.value');
        if ($search) {
            $procumentOrders = $procumentOrders->where('purchaseOrderCode', 'LIKE', "%{$search}%")
                ->orWhere('narration', 'LIKE', "%{$search}%");
        }

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


    public function getProcumentOrderFormData(Request $request)
    {

        $companyId = $request['companyId'];

        $purchaseOrderID = $request['purchaseOrderID'];

        $segments = SegmentMaster::where("companySystemID", $companyId)->get();

        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = ProcumentOrder::select(DB::raw("YEAR(createdDateTime) as year"))
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

        $detailSum = PurchaseOrderDetails::select(DB::raw('sum(netAmount) as total'))
            ->where('purchaseOrderMasterID', $purchaseOrderID)
            ->get();

        $financeCategories = FinanceItemCategoryMaster::all();

        $locations = Location::all();

        $financialYears = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));


        $checkBudget = CompanyPolicyMaster::where('companyPolicyCategoryID', 17)
            ->where('companySystemID', $companyId)
            ->first();

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyId)
            ->first();

        $allowPRinPO = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
            ->where('companySystemID', $companyId)
            ->first();

        $addressTypeShippings = DB::table("erp_address")
            ->select('addressID', 'addressTypeDescription')
            ->join("erp_addresstype", "erp_addresstype.addressTypeID", "=", "erp_address.addressTypeID")
            ->where("erp_address.addressTypeID", "1")
            ->where("companySystemID", $companyId)
            ->get();

        $addressTypeInvoice = DB::table("erp_address")
            ->select('addressID', 'addressTypeDescription')
            ->join("erp_addresstype", "erp_addresstype.addressTypeID", "=", "erp_address.addressTypeID")
            ->where("erp_address.addressTypeID", "2")
            ->where("companySystemID", $companyId)
            ->get();

        $addressTypeSold = DB::table("erp_address")
            ->select('addressID', 'addressTypeDescription')
            ->join("erp_addresstype", "erp_addresstype.addressTypeID", "=", "erp_address.addressTypeID")
            ->where("erp_address.addressTypeID", "3")
            ->where("companySystemID", $companyId)
            ->get();

        $PoPaymentTermTypes = PoPaymentTermTypes::all();

        if (!empty($purchaseOrderID)) {
            $checkDetailExist = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                ->where('companySystemID', $companyId)
                ->first();

            if (!empty($checkDetailExist)) {
                $detail = 1;
            }
        }

        $conditions = array('checkBudget' => 0, 'allowFinanceCategory' => 0, 'detailExist' => 0, 'pullPRPolicy' => 0);

        if ($checkBudget) {
            $conditions['checkBudget'] = $checkBudget->isYesNO;
        }

        if ($allowFinanceCategory) {
            $conditions['allowFinanceCategory'] = $allowFinanceCategory->isYesNO;
        }

        if ($allowPRinPO) {
            $conditions['pullPRPolicy'] = $allowPRinPO->isYesNO;
        }

        if (!empty($purchaseOrderID)) {
            $checkDetailExist = PurchaseOrderDetails::where('purchaseOrderMasterID', $purchaseOrderID)
                ->where('companySystemID', $companyId)
                ->first();

            if (!empty($checkDetailExist)) {
                $conditions['detailExist'] = 1;
            }
        }

        $output = array('segments' => $segments,
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,
            'currencies' => $currencies,
            'financeCategories' => $financeCategories,
            'locations' => $locations,
            'financialYears' => $financialYears,
            'conditions' => $conditions,
            'suppliers' => $supplier,
            'addresstypeShippings' => $addressTypeShippings,
            'addresstypeinvoice' => $addressTypeInvoice,
            'addresstypesold' => $addressTypeSold,
            'paymentterms' => $PoPaymentTermTypes,
            'detailSum' => $detailSum
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getItemsOptionForProcumentOrder(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];
        $purchaseOrderID = $input['purchaseOrderID'];

        $policy = 1;

        $financeCategoryId = 0;

        $allowFinanceCategory = CompanyPolicyMaster::where('companyPolicyCategoryID', 20)
            ->where('companySystemID', $companyId)
            ->first();

        if ($allowFinanceCategory) {
            $policy = $allowFinanceCategory->isYesNO;

            if ($policy == 0) {

                $purchaseOrder = ProcumentOrder::where('purchaseOrderID', $purchaseOrderID)->first();

                if ($purchaseOrder) {
                    $financeCategoryId = $purchaseOrder->financeCategory;
                }
            }
        }

        $items = ItemAssigned::where('companySystemID', $companyId);

        if ($financeCategoryId != 0) {
            $items = $items->where('financeCategoryMaster', $financeCategoryId);
        }

        if (array_key_exists('search', $input)) {

            $search = $input['search'];

            $items = $items->where('itemPrimaryCode', 'LIKE', "%{$search}%")
                ->orWhere('itemDescription', 'LIKE', "%{$search}%");
        }

        $items = $items
            ->take(20)
            ->get();
        return $this->sendResponse($items->toArray(), 'Data retrieved successfully');

    }

    public function getShippingAndInvoiceDetails(Request $request)
    {
        $input = $request->all();

        $companyId = $input['companyId'];
        $addressID = $input['addressID'];

        $erpAddressDetails = ErpAddress::where('addressID', $addressID)
            ->where('companySystemID', $companyId)
            ->first();

        return $this->sendResponse($erpAddressDetails->toArray(), 'Data retrieved successfully');

    }


    public function getProcurementOrderRecord(Request $request)
    {
        $output = ProcumentOrder::where('purchaseOrderID',$request->purchaseOrderID)->with(['detail' => function($query){
            $query->with('unit');
        },'approved' => function($query){
            $query->with('employee');
            $query->where('documentSystemID',2);
        }])->first();
        return $this->sendResponse($output, 'Data retrieved successfully');

    }

}
