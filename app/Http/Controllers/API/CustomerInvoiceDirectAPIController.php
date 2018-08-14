<?php
/**
 * =============================================
 * -- File Name : GRVMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  GRV Master
 * -- Author : Mohamed Nazir
 * -- Create date : 11-June 2018
 * -- Description : This file contains the all CRUD for GRV Master
 * -- REVISION HISTORY
 * -- Date: 13 Aug 2018 By: Shahmy Description: Added new functions named as getINVFormData() For load form View
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCustomerInvoiceDirectAPIRequest;
use App\Http\Requests\API\UpdateCustomerInvoiceDirectAPIRequest;
use App\Models\CustomerAssigned;
use App\Models\CustomerInvoiceDirect;
use App\Models\CustomerInvoiceDirectDetail;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Models\Months;
use App\Models\Company;

use App\Repositories\CustomerInvoiceDirectRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CustomerInvoiceDirectController
 * @package App\Http\Controllers\API
 */
class CustomerInvoiceDirectAPIController extends AppBaseController
{
    /** @var  CustomerInvoiceDirectRepository */
    private $customerInvoiceDirectRepository;

    public function __construct(CustomerInvoiceDirectRepository $customerInvoiceDirectRepo)
    {
        $this->customerInvoiceDirectRepository = $customerInvoiceDirectRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirects",
     *      summary="Get a listing of the CustomerInvoiceDirects.",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Get all CustomerInvoiceDirects",
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
     *                  @SWG\Items(ref="#/definitions/CustomerInvoiceDirect")
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
        $this->customerInvoiceDirectRepository->pushCriteria(new RequestCriteria($request));
        $this->customerInvoiceDirectRepository->pushCriteria(new LimitOffsetCriteria($request));
        $customerInvoiceDirects = $this->customerInvoiceDirectRepository->all();

        return $this->sendResponse($customerInvoiceDirects->toArray(), 'Customer Invoice Directs retrieved successfully');
    }

    /**
     * @param CreateCustomerInvoiceDirectAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/customerInvoiceDirects",
     *      summary="Store a newly created CustomerInvoiceDirect in storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Store CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirect that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirect")
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCustomerInvoiceDirectAPIRequest $request)
    {
        $input = $request->all();

        $customerInvoiceDirects = $this->customerInvoiceDirectRepository->create($input);

        return $this->sendResponse($customerInvoiceDirects->toArray(), 'Customer Invoice Direct saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Display the specified CustomerInvoiceDirect",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Get CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
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
        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        return $this->sendResponse($customerInvoiceDirect->toArray(), 'Customer Invoice Direct retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateCustomerInvoiceDirectAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Update the specified CustomerInvoiceDirect in storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Update CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="CustomerInvoiceDirect that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/CustomerInvoiceDirect")
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
     *                  ref="#/definitions/CustomerInvoiceDirect"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCustomerInvoiceDirectAPIRequest $request)
    {
        $input = $request->all();

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->update($input, $id);

        return $this->sendResponse($customerInvoiceDirect->toArray(), 'CustomerInvoiceDirect updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/customerInvoiceDirects/{id}",
     *      summary="Remove the specified CustomerInvoiceDirect from storage",
     *      tags={"CustomerInvoiceDirect"},
     *      description="Delete CustomerInvoiceDirect",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of CustomerInvoiceDirect",
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
        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found');
        }

        $customerInvoiceDirect->delete();

        return $this->sendResponse($id, 'Customer Invoice Direct deleted successfully');
    }

    public function customerInvoiceDetails(request $request)
    {
        $input = $request->all();
        $id = $input['id'];

        /** @var CustomerInvoiceDirect $customerInvoiceDirect */
        $customerInvoiceDirect = $this->customerInvoiceDirectRepository->with(['company', 'customer', 'tax', 'createduser', 'bankaccount', 'currency', 'approved_by' => function ($query) {
            $query->with('employee.details.designation')
                ->where('documentSystemID', 20);
        }, 'invoicedetails'
        => function ($query) {
                $query->with(['performadetails' => function ($query) {
                    $query->with(['freebillingmaster' => function ($query) {
                        $query->with(['ticketmaster' => function ($query) {
                            $query->with(['field']);
                        }]);
                    }]);
                }]);
            }
        ])->findWithoutFail($id);

        if (empty($customerInvoiceDirect)) {
            return $this->sendError('Customer Invoice Direct not found', 500);
        } else {
            /*   $CustomerInvoiceDirectDetail = CustomerInvoiceDirectDetail::select('*')->where('custInvoiceDirectID', $id)->get();
               $data['data']['master'] = $customerInvoiceDirect;
               $data['data']['detail'] = $CustomerInvoiceDirectDetail;*/

            return $this->sendResponse($customerInvoiceDirect, 'Customer Invoice Direct deleted successfully');
        }
    }


    public function getINVFormData(Request $request)
    {
        $companyId = $request['companyId'];

        //$grvAutoID = $request['grvAutoID'];


        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $month = Months::all();

        $years = CustomerInvoiceDirect::select(DB::raw("YEAR(bookingDate) as year"))
            ->whereNotNull('bookingDate')
            ->groupby('year')
            ->orderby('year', 'desc')
            ->get();


        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'month' => $month,
            'years' => $years,

        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }

    public function getCustomerInvoiceMasterView(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('invConfirmedYN', 'month', 'approved', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $invMaster = CustomerInvoiceDirect::where('companySystemID', $input['companyId']);
        $invMaster->where('documentSystemID', $input['documentId']);
        $invMaster->with(['currency', 'createduser', 'customer']);


        $invMaster->where('isPerforma', 1);

        if (array_key_exists('invConfirmedYN', $input)) {
            if (($input['invConfirmedYN'] == 0 || $input['invConfirmedYN'] == 1) && !is_null($input['invConfirmedYN'])) {
                $invMaster->where('confirmedYN', $input['invConfirmedYN']);
            }
        }

        if (array_key_exists('approved', $input)) {
            if (($input['approved'] == 0 || $input['approved'] == -1) && !is_null($input['approved'])) {
                $invMaster->where('approved', $input['approved']);
            }
        }


        if (array_key_exists('year', $input)) {
            if ($input['year'] && !is_null($input['year'])) {
                $invoiceDate = $input['year'] . '-12-01';
                if (array_key_exists('month', $input)) {
                    if ($input['month'] && !is_null($input['month'])) {
                        $invoiceDate = $input['year'] . '-' . $input['month'] . '-01';
                    }
                }

                $invMaster->where('bookingDate', '<=', $invoiceDate);

            }
        }

        /*    $grvMaster = $invMaster->select(
                [ 'erp_custinvoicedirect.custInvoiceDirectAutoID',
                    'erp_custinvoicedirect.custInvoiceDirectAutoID','bookingDate',
                    'erp_custinvoicedirect.comments',
                    'bookingInvCode',
                    'erp_custinvoicedirect.customerID',
                    'custTransactionCurrencyID',
                    'bookingAmountTrans',
                    'erp_custinvoicedirect.createdUserID',
                    'erp_custinvoicedirect.approvedDate',

                  'erp_custinvoicedirect.confirmedYN','approved','VATAmount'
                ]);*/

        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $invMaster = $invMaster->where(function ($query) use ($search) {
                $query->where('employees.empName', 'LIKE', "%{$search}%")
                    ->orWhere('bookingInvCode', 'LIKE', "%{$search}%")
                    ->orWhere('customermaster.CustomerName', 'LIKE', "%{$search}%");
            });
        }


        /*       $historyPolicy = CompanyPolicyMaster::where('companyPolicyCategoryID', 29)
                   ->where('companySystemID', $input['companyId'])->first();

               $policy = 0;

               if (!empty($historyPolicy)) {
                   $policy = $historyPolicy->isYesNO;
               }*/

        return \DataTables::eloquent($invMaster)
            /*  ->addColumn('Actions', $policy)*/
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('custInvoiceDirectAutoID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    function getcreateINVFormData(Request $request)
    {
        $companyId = $request['companyId'];
        $output['customer'] = CustomerAssigned::select('*')->where('companySystemID', $companyId)->where('isAssigned','-1')->where('isActive','1')->get();
        $output['financialYears'] = array(array('value' => intval(date("Y")), 'label' => date("Y")),
            array('value' => intval(date("Y", strtotime("-1 year"))), 'label' => date("Y", strtotime("-1 year"))));

        $output['companyFinanceYear'] = \Helper::companyFinanceYear($companyId);
        $output['company'] = Company::select('CompanyName')->where('companySystemID', $companyId)->first();
        return $this->sendResponse($output, 'Record retrieved successfully');
    }

}
