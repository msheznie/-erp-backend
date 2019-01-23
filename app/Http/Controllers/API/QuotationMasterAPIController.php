<?php
/**
 * =============================================
 * -- File Name : QuotationMasterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationMaster
 * -- Author : Mohamed Nazir
 * -- Create date : 22 - January 2019
 * -- Description : This file contains the all CRUD for Sales Quotation Master
 * -- REVISION HISTORY
 * -- Date: 23-January 2019 By: Nazir Description: Added new function getSalesQuotationFormData(),
 * -- Date: 23-January 2019 By: Nazir Description: Added new function getSalesPersonFormData(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationMasterAPIRequest;
use App\Http\Requests\API\UpdateQuotationMasterAPIRequest;
use App\Models\CurrencyMaster;
use App\Models\CustomerAssigned;
use App\Models\QuotationMaster;
use App\Models\SalesPersonMaster;
use App\Models\YesNoSelection;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\QuotationMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Response;

/**
 * Class QuotationMasterController
 * @package App\Http\Controllers\API
 */
class QuotationMasterAPIController extends AppBaseController
{
    /** @var  QuotationMasterRepository */
    private $quotationMasterRepository;

    public function __construct(QuotationMasterRepository $quotationMasterRepo)
    {
        $this->quotationMasterRepository = $quotationMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasters",
     *      summary="Get a listing of the QuotationMasters.",
     *      tags={"QuotationMaster"},
     *      description="Get all QuotationMasters",
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
     *                  @SWG\Items(ref="#/definitions/QuotationMaster")
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
        $this->quotationMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationMasters = $this->quotationMasterRepository->all();

        return $this->sendResponse($quotationMasters->toArray(), 'Quotation Masters retrieved successfully');
    }

    /**
     * @param CreateQuotationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationMasters",
     *      summary="Store a newly created QuotationMaster in storage",
     *      tags={"QuotationMaster"},
     *      description="Store QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMaster")
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
     *                  ref="#/definitions/QuotationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationMasterAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        if (isset($input['documentDate'])) {
            if ($input['documentDate']) {
                $input['documentDate'] = new Carbon($input['documentDate']);
            }
        }

        if (isset($input['documentExpDate'])) {
            if ($input['documentExpDate']) {
                $input['documentExpDate'] = new Carbon($input['documentExpDate']);
            }
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }

        // creating document code
        $lastSerial = SalesPersonMaster::where('companySystemID', $input['companySystemID'])
            ->orderBy('salesPersonID', 'desc')
            ->first();

        $lastSerialNumber = 1;
        if ($lastSerial) {
            $lastSerialNumber = intval($lastSerial->salesPersonID) + 1;
        }

        $salesPersonCode = ($company->CompanyID . '\\' . 'REP' . str_pad($lastSerialNumber, 6, '0', STR_PAD_LEFT));
        $input['SalesPersonCode'] = $salesPersonCode;

        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserName'] = $employee->empName;

        $quotationMasters = $this->quotationMasterRepository->create($input);

        return $this->sendResponse($quotationMasters->toArray(), 'Quotation Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationMasters/{id}",
     *      summary="Display the specified QuotationMaster",
     *      tags={"QuotationMaster"},
     *      description="Get QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
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
     *                  ref="#/definitions/QuotationMaster"
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
        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        return $this->sendResponse($quotationMaster->toArray(), 'Quotation Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuotationMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationMasters/{id}",
     *      summary="Update the specified QuotationMaster in storage",
     *      tags={"QuotationMaster"},
     *      description="Update QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationMaster")
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
     *                  ref="#/definitions/QuotationMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        $quotationMaster = $this->quotationMasterRepository->update($input, $id);

        return $this->sendResponse($quotationMaster->toArray(), 'QuotationMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationMasters/{id}",
     *      summary="Remove the specified QuotationMaster from storage",
     *      tags={"QuotationMaster"},
     *      description="Delete QuotationMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationMaster",
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
        /** @var QuotationMaster $quotationMaster */
        $quotationMaster = $this->quotationMasterRepository->findWithoutFail($id);

        if (empty($quotationMaster)) {
            return $this->sendError('Quotation Master not found');
        }

        $quotationMaster->delete();

        return $this->sendResponse($id, 'Quotation Master deleted successfully');
    }

    public function getSalesQuotationFormData(Request $request)
    {
        $companyId = $request['companyId'];

        $isGroup = \Helper::checkIsCompanyGroup($companyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($companyId);
        } else {
            $subCompanies = [$companyId];
        }


        /** Yes and No Selection */
        $yesNoSelection = YesNoSelection::all();

        /** all Units*/
        $yesNoSelectionForMinus = YesNoSelectionForMinus::all();

        $currencies = CurrencyMaster::select(DB::raw("currencyID,CONCAT(CurrencyCode, ' | ' ,CurrencyName) as CurrencyName"))
            ->get();

        $customer = CustomerAssigned::select(DB::raw("customerCodeSystem,CONCAT(CutomerCode, ' | ' ,CustomerName) as CustomerName"))
            ->where('companySystemID', $subCompanies)
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $salespersons = SalesPersonMaster::where('companySystemID', $subCompanies)
            ->get();

        $output = array(
            'yesNoSelection' => $yesNoSelection,
            'yesNoSelectionForMinus' => $yesNoSelectionForMinus,
            'currencies' => $currencies,
            'customer' => $customer,
            'salespersons' => $salespersons
        );

        return $this->sendResponse($output, 'Record retrieved successfully');
    }
}
