<?php
/**
 * =============================================
 * -- File Name : QuotationDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  QuotationDetails
 * -- Author : Mohamed Nazir
 * -- Create date : 24 - January 2019
 * -- Description : This file contains the all CRUD for Sales Quotation Details
 * -- REVISION HISTORY
 * -- Date: 24-January 2019 By: Nazir Description: Added new function getSalesQuotationDetails(),
 */

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateQuotationDetailsAPIRequest;
use App\Http\Requests\API\UpdateQuotationDetailsAPIRequest;
use App\Models\ItemAssigned;
use App\Models\QuotationDetails;
use App\Models\QuotationMaster;
use App\Models\Unit;
use App\Models\Company;
use App\Repositories\QuotationDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Carbon\Carbon;
use Response;

/**
 * Class QuotationDetailsController
 * @package App\Http\Controllers\API
 */
class QuotationDetailsAPIController extends AppBaseController
{
    /** @var  QuotationDetailsRepository */
    private $quotationDetailsRepository;

    public function __construct(QuotationDetailsRepository $quotationDetailsRepo)
    {
        $this->quotationDetailsRepository = $quotationDetailsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationDetails",
     *      summary="Get a listing of the QuotationDetails.",
     *      tags={"QuotationDetails"},
     *      description="Get all QuotationDetails",
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
     *                  @SWG\Items(ref="#/definitions/QuotationDetails")
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
        $this->quotationDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->quotationDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $quotationDetails = $this->quotationDetailsRepository->all();

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details retrieved successfully');
    }

    /**
     * @param CreateQuotationDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/quotationDetails",
     *      summary="Store a newly created QuotationDetails in storage",
     *      tags={"QuotationDetails"},
     *      description="Store QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationDetails")
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
     *                  ref="#/definitions/QuotationDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateQuotationDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($request->all(), 'unit');
        $input = $this->convertArrayToValue($input);

        $employee = \Helper::getEmployeeInfo();

        $companySystemID = $input['companySystemID'];

        $item = ItemAssigned::where('itemCodeSystem', $input['itemAutoID'])
            ->where('companySystemID', $companySystemID)
            ->first();

        $itemExist = QuotationDetails::where('itemAutoID', $input['itemAutoID'])
            ->where('quotationMasterID', $input['quotationMasterID'])
            ->first();

        if (!empty($itemExist)) {
            return $this->sendError('Added item already exist');
        }

        if (empty($item)) {
            return $this->sendError('Added item not found in item master');
        }

        $quotationMasterData = QuotationMaster::find($input['quotationMasterID']);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation Master not found');
        }

        $input['itemSystemCode'] = $item->itemPrimaryCode;
        $input['itemDescription'] = $item->itemDescription;
        $input['itemCategory'] = $item->financeCategoryMaster;
        $input['itemReferenceNo'] = $item->secondaryItemCode;
        $input['unitOfMeasureID'] = $item->itemUnitOfMeasure;
        $input['wacValueLocal'] = $item->wacValueLocal;

        if ($quotationMasterData->documentSystemID == 68) {
            $input['unittransactionAmount'] = round(\Helper::currencyConversion($quotationMasterData->companySystemID, $quotationMasterData->companyLocalCurrencyID, $quotationMasterData->transactionCurrencyID, $item->wacValueLocal)['documentAmount'], $quotationMasterData->transactionCurrencyDecimalPlaces);
        }

        $input['wacValueReporting'] = $item->wacValueReporting;

        $unitMasterData = Unit::find($item->itemUnitOfMeasure);
        if ($unitMasterData) {
            $input['unitOfMeasure'] = $unitMasterData->UnitShortCode;
        }

        $company = Company::where('companySystemID', $input['companySystemID'])->first();
        if ($company) {
            $input['companyID'] = $company->CompanyID;
        }
        $input['createdPCID'] = gethostname();
        $input['createdUserID'] = $employee->empID;
        $input['createdUserName'] = $employee->empName;

        $quotationDetails = $this->quotationDetailsRepository->create($input);

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/quotationDetails/{id}",
     *      summary="Display the specified QuotationDetails",
     *      tags={"QuotationDetails"},
     *      description="Get QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
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
     *                  ref="#/definitions/QuotationDetails"
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
        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateQuotationDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/quotationDetails/{id}",
     *      summary="Update the specified QuotationDetails in storage",
     *      tags={"QuotationDetails"},
     *      description="Update QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="QuotationDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/QuotationDetails")
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
     *                  ref="#/definitions/QuotationDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateQuotationDetailsAPIRequest $request)
    {
        $input = $request->all();

        $employee = \Helper::getEmployeeInfo();

        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        $quotationMasterData = QuotationMaster::find($input['quotationMasterID']);

        if (empty($quotationMasterData)) {
            return $this->sendError('Quotation Master not found');
        }

        // updating transaction amount for local and reporting
        $currencyConversion = \Helper::currencyConversion($input['companySystemID'], $quotationMasterData->transactionCurrencyID, $quotationMasterData->transactionCurrencyID, $input['transactionAmount']);

        $input['companyLocalAmount'] = \Helper::roundValue($currencyConversion['localAmount']);
        $input['companyReportingAmount'] = \Helper::roundValue($currencyConversion['reportingAmount']);

        // adding customer default currencyID base currency conversion

        $currencyConversionDefault = \Helper::currencyConversion($input['companySystemID'], $quotationMasterData->customerCurrencyID, $quotationMasterData->customerCurrencyID, $input['transactionAmount']);

        $input['customerAmount'] = \Helper::roundValue($currencyConversionDefault['documentAmount']);

        $input['modifiedDateTime'] = Carbon::now();
        $input['modifiedPCID'] = gethostname();
        $input['modifiedUserID'] = $employee->empID;
        $input['modifiedUserName'] = $employee->empName;

        $quotationDetails = $this->quotationDetailsRepository->update($input, $id);

        return $this->sendResponse($quotationDetails->toArray(), 'Quotation Details updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/quotationDetails/{id}",
     *      summary="Remove the specified QuotationDetails from storage",
     *      tags={"QuotationDetails"},
     *      description="Delete QuotationDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of QuotationDetails",
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
        /** @var QuotationDetails $quotationDetails */
        $quotationDetails = $this->quotationDetailsRepository->findWithoutFail($id);

        if (empty($quotationDetails)) {
            return $this->sendError('Quotation Details not found');
        }

        $quotationDetails->delete();

        return $this->sendResponse($id, 'Quotation Details deleted successfully');
    }

    public function getSalesQuotationDetails(Request $request)
    {
        $input = $request->all();
        $quotationMasterID = $input['quotationMasterID'];

        $items = QuotationDetails::where('quotationMasterID', $quotationMasterID)
            ->get();

        return $this->sendResponse($items->toArray(), 'Quotation Details retrieved successfully');
    }

    public function salesQuotationDetailsDeleteAll(Request $request)
    {
        $input = $request->all();

        $quotationMasterID = $input['quotationMasterID'];

        $detailExistAll = QuotationDetails::where('quotationMasterID', $quotationMasterID)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }

        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {

                $deleteDetails = QuotationDetails::where('quotationDetailsID', $cvDeatil['quotationDetailsID'])->delete();

            }
        }

        return $this->sendResponse($quotationMasterID, 'Quotation details deleted successfully');
    }

    public function getSalesQuotationDetailForInvoice(Request $request){
        $input = $request->all();
        $id = $input['quotationMasterID'];

        $detail = DB::select('SELECT
	quotationdetails.*,
	erp_quotationmaster.serviceLineSystemID,
	"" AS isChecked,
	"" AS noQty,
	IFNULL(dodetails.invTakenQty,0) as invTakenQty 
FROM
	erp_quotationdetails quotationdetails
	INNER JOIN erp_quotationmaster ON quotationdetails.quotationMasterID = erp_quotationmaster.quotationMasterID
	LEFT JOIN ( SELECT erp_customerinvoiceitemdetails.customerItemDetailID,quotationDetailsID, SUM( qtyIssuedDefaultMeasure ) AS invTakenQty FROM erp_customerinvoiceitemdetails GROUP BY customerItemDetailID, itemCodeSystem ) AS dodetails ON quotationdetails.quotationDetailsID = dodetails.quotationDetailsID 
WHERE
	quotationdetails.quotationMasterID = ' . $id . ' 
	AND fullyOrdered != 2 AND erp_quotationmaster.isInDOorCI != 1 ');

        return $this->sendResponse($detail, 'Quotation Details retrieved successfully');
    }


}
