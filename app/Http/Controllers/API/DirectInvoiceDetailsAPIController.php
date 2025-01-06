<?php
/**
 * =============================================
 * -- File Name : DirectInvoiceDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  DirectInvoiceDetails
 * -- Author : Mohamed Nazir
 * -- Create date : 09 - August 2018
 * -- Description : This file contains the all CRUD for Direct Invoice Details
 * -- REVISION HISTORY
 * -- Date: 06 September 2018 By: Nazir Description: Added new function getDirectItems()
 * -- Date: 18 September 2018 By: Nazir Description: Added new function deleteAllSIDirectDetail()
 */
namespace App\Http\Controllers\API;

use App\helper\SupplierInvoice;
use App\helper\TaxService;
use App\Http\Requests\API\CreateDirectInvoiceDetailsAPIRequest;
use App\Http\Requests\API\UpdateDirectInvoiceDetailsAPIRequest;
use App\Models\BookInvSuppMaster;
use App\Models\ExpenseEmployeeAllocation;
use App\Models\ChartOfAccount;
use App\Models\CompanyFinanceYear;
use App\Models\CompanyPolicyMaster;
use App\Models\DirectInvoiceDetails;
use App\Models\SegmentMaster;
use App\Repositories\DirectInvoiceDetailsRepository;
use App\Repositories\ExpenseAssetAllocationRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\ServiceLine;
use App\Models\SrpEmployeeDetails;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class DirectInvoiceDetailsController
 * @package App\Http\Controllers\API
 */
class DirectInvoiceDetailsAPIController extends AppBaseController
{
    /** @var  DirectInvoiceDetailsRepository */
    private $directInvoiceDetailsRepository;
    private $expenseAssetAllocationRepository;

    public function __construct(
        DirectInvoiceDetailsRepository $directInvoiceDetailsRepo,
        ExpenseAssetAllocationRepository $expenseAssetAllocationRepo
    )
    {
        $this->directInvoiceDetailsRepository = $directInvoiceDetailsRepo;
        $this->expenseAssetAllocationRepository = $expenseAssetAllocationRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/directInvoiceDetails",
     *      summary="Get a listing of the DirectInvoiceDetails.",
     *      tags={"DirectInvoiceDetails"},
     *      description="Get all DirectInvoiceDetails",
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
     *                  @SWG\Items(ref="#/definitions/DirectInvoiceDetails")
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
        $this->directInvoiceDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->directInvoiceDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->all();

        return $this->sendResponse($directInvoiceDetails->toArray(), 'Direct Invoice Details retrieved successfully');
    }

    /**
     * @param CreateDirectInvoiceDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/directInvoiceDetails",
     *      summary="Store a newly created DirectInvoiceDetails in storage",
     *      tags={"DirectInvoiceDetails"},
     *      description="Store DirectInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectInvoiceDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectInvoiceDetails")
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
     *                  ref="#/definitions/DirectInvoiceDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDirectInvoiceDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $companySystemID = $input['companySystemID'];
        $BookInvSuppMaster = BookInvSuppMaster::find($input['directInvoiceAutoID']);

        if (empty($BookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice not found');
        }


        if (empty($BookInvSuppMaster->supplierTransactionCurrencyID)) {
            return $this->sendError('Please select a document currency');
        }

        if($BookInvSuppMaster->confirmedYN){
            return $this->sendError('You cannot add detail, this document already confirmed',500);
        }

/*        $alreadyAdded = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $BookInvSuppMaster->bookingSuppMasInvAutoID)
            ->whereHas('directdetail', function ($query) use ($input) {
                $query->where('chartOfAccountSystemID', $input['chartOfAccountSystemID']);
            })
            ->first();

        if ($alreadyAdded) {
            return $this->sendError("Selected item is already added. Please check again", 500);
        }*/


        if($BookInvSuppMaster->employeeID > 0){
            $employeeSegment = SrpEmployeeDetails::where('EIdNo',$BookInvSuppMaster->employeeID)->first();
            if($employeeSegment && $employeeSegment->segmentID > 0){
                $segment = SegmentMaster::where('serviceLineSystemID',$employeeSegment->segmentID)->where('isActive',1)->first();
                if($segment){
                    $input['serviceLineSystemID'] = $segment->serviceLineSystemID;
                    $input['serviceLineCode'] = $segment->ServiceLineCode;
                }
            }
        }

        $input['comments'] = $BookInvSuppMaster->comments;
        $input['companySystemID'] = $BookInvSuppMaster->companySystemID;
        $input['companyID'] = $BookInvSuppMaster->companyID;

        $chartOfAccount = ChartOfAccount::find($input['chartOfAccountSystemID']);
        if (empty($chartOfAccount)) {
            return $this->sendError('Chart of Account not found');
        }

        $input['glCode'] = $chartOfAccount->AccountCode;
        $input['glCodeDes'] = $chartOfAccount->AccountDescription;

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $BookInvSuppMaster->supplierTransactionCurrencyID,$BookInvSuppMaster->supplierTransactionCurrencyID, 0);

        $input['DIAmountCurrency'] = $BookInvSuppMaster->supplierTransactionCurrencyID;
        $input['DIAmountCurrencyER'] = 1;
        $input['localCurrency' ] =   $BookInvSuppMaster->localCurrencyID;
        $input['comRptCurrency'] =   $BookInvSuppMaster->companyReportingCurrencyID;

        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;

        if(($BookInvSuppMaster->documentType == 1 || $BookInvSuppMaster->documentType == 4) && $policy == true){
            $input['localCurrencyER' ]    = $BookInvSuppMaster->localCurrencyER;
            $input['comRptCurrencyER']    = $BookInvSuppMaster->companyReportingER;
        }
        if($BookInvSuppMaster->documentType != 1 || $policy == false){
            $input['localCurrencyER' ]    = $companyCurrencyConversion['trasToLocER'];
            $input['comRptCurrencyER']    = $companyCurrencyConversion['trasToRptER'];
        }

        if ($BookInvSuppMaster->FYBiggin) {
            $finYearExp = explode('-', $BookInvSuppMaster->FYBiggin);
            $input['budgetYear'] = $finYearExp[0];
        } else {
            $input['budgetYear'] = CompanyFinanceYear::budgetYearByDate(now(), $input['companySystemID']);
        }

        $isVATEligible = TaxService::checkCompanyVATEligible($BookInvSuppMaster->companySystemID);

        if ($isVATEligible) {
            $defaultVAT = TaxService::getDefaultVAT($BookInvSuppMaster->companySystemID, $BookInvSuppMaster->supplierID);
            $input['vatSubCategoryID'] = $defaultVAT['vatSubCategoryID'];
            $input['VATPercentage'] = $defaultVAT['percentage'];
            $input['vatMasterCategoryID'] = $defaultVAT['vatMasterCategoryID'];
        }

        $directInvoiceDetails = $this->directInvoiceDetailsRepository->create($input);

        return $this->sendResponse($directInvoiceDetails->toArray(), 'Direct Invoice Details saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/directInvoiceDetails/{id}",
     *      summary="Display the specified DirectInvoiceDetails",
     *      tags={"DirectInvoiceDetails"},
     *      description="Get DirectInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetails",
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
     *                  ref="#/definitions/DirectInvoiceDetails"
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
        /** @var DirectInvoiceDetails $directInvoiceDetails */
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            return $this->sendError('Direct Invoice Details not found');
        }

        return $this->sendResponse($directInvoiceDetails->toArray(), 'Direct Invoice Details retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateDirectInvoiceDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/directInvoiceDetails/{id}",
     *      summary="Update the specified DirectInvoiceDetails in storage",
     *      tags={"DirectInvoiceDetails"},
     *      description="Update DirectInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="DirectInvoiceDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/DirectInvoiceDetails")
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
     *                  ref="#/definitions/DirectInvoiceDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDirectInvoiceDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['segment', 'purchase_order','chartofaccount']);
        $input = $this->convertArrayToValue($input);
        $serviceLineError = array('type' => 'serviceLine');

        /** @var DirectInvoiceDetails $directInvoiceDetails */
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            return $this->sendError('Direct Invoice Details not found');
        }

        $BookInvSuppMaster = BookInvSuppMaster::find($input['directInvoiceAutoID']);

        if (empty($BookInvSuppMaster)) {
            return $this->sendError('Supplier Invoice Master not found');
        }

        $validateVATCategories = TaxService::validateVatCategoriesInDocumentDetails($BookInvSuppMaster->documentSystemID, $BookInvSuppMaster->companySystemID, $id, $input, $BookInvSuppMaster->supplierID, $BookInvSuppMaster->documentType);

        if (!$validateVATCategories['status']) {
            return $this->sendError($validateVATCategories['message'], 500, array('type' => 'vat'));
        } else {
            $input['vatMasterCategoryID'] = $validateVATCategories['vatMasterCategoryID'];        
            $input['vatSubCategoryID'] = $validateVATCategories['vatSubCategoryID'];        
        }

        if($BookInvSuppMaster->confirmedYN){
            return $this->sendError('You cannot update detail, this document already confirmed',500);
        }

        if (isset($input['serviceLineSystemID'])) {

            if($input['serviceLineSystemID'] > 0) {
                $checkDepartmentActive = SegmentMaster::find($input['serviceLineSystemID']);
                if (empty($checkDepartmentActive)) {
                    return $this->sendError('Department not found');
                }

                if ($checkDepartmentActive->isActive == 0) {
                    $this->$directInvoiceDetails->update(['serviceLineSystemID' => null, 'serviceLineCode' => null], $id);
                    return $this->sendError('Please select an active department', 500, $serviceLineError);
                }

                $input['serviceLineCode'] = $checkDepartmentActive->ServiceLineCode;
            }
        }

        if($input['serviceLineSystemID'] == 0){
            $input['serviceLineSystemID'] = null;
            $input['serviceLineCode'] = null;
        }

        if( $input['DIAmount'] == ""){
            $input['DIAmount'] = 0;
        }
        $input['DIAmount'] = isset($input['DIAmount']) ?  \Helper::stringToFloat($input['DIAmount']) : 0;
        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $BookInvSuppMaster->supplierTransactionCurrencyID,$BookInvSuppMaster->supplierTransactionCurrencyID, $input['DIAmount']);

        $policy = CompanyPolicyMaster::where('companySystemID', $input['companySystemID'])
            ->where('companyPolicyCategoryID', 67)
            ->where('isYesNO', 1)
            ->first();
        $policy = isset($policy->isYesNO) && $policy->isYesNO == 1;


        if(($BookInvSuppMaster->documentType == 1 || $BookInvSuppMaster->documentType == 4) && $policy == true){
            $input['localAmount' ]        = \Helper::roundValue($input['DIAmount'] / $BookInvSuppMaster->localCurrencyER);
            $input['comRptAmount']        = \Helper::roundValue($input['DIAmount'] / $BookInvSuppMaster->companyReportingER);
            $input['localCurrencyER' ]    = $BookInvSuppMaster->localCurrencyER;
            $input['comRptCurrencyER']    = $BookInvSuppMaster->companyReportingER;
        }

        if($BookInvSuppMaster->documentType != 1 || $policy == false){
            $input['localAmount' ]        = \Helper::roundValue($companyCurrencyConversion['localAmount']);
            $input['comRptAmount']        = \Helper::roundValue($companyCurrencyConversion['reportingAmount']);
            $input['localCurrencyER' ]    = $companyCurrencyConversion['trasToLocER'];
            $input['comRptCurrencyER']    = $companyCurrencyConversion['trasToRptER'];
        }


        $input['VATAmount'] = isset($input['VATAmount']) ?  \Helper::stringToFloat($input['VATAmount']) : 0;
        $currencyConversionVAT = \Helper::currencyConversion($input['companySystemID'], $BookInvSuppMaster->supplierTransactionCurrencyID,$BookInvSuppMaster->supplierTransactionCurrencyID, $input['VATAmount']);



        if($policy == true) {
            $input['VATAmountLocal'] = \Helper::roundValue($input['VATAmount'] / $BookInvSuppMaster->localCurrencyER);
            $input['VATAmountRpt'] = \Helper::roundValue($input['VATAmount'] / $BookInvSuppMaster->companyReportingER);
        }  if($policy == false) {
        $input['VATAmountLocal'] = \Helper::roundValue($currencyConversionVAT['localAmount']);
        $input['VATAmountRpt'] = \Helper::roundValue($currencyConversionVAT['reportingAmount']);
    }
        $input['VATAmount'] = \Helper::roundValue($input['VATAmount']);

        $input['netAmount'] = isset($input['netAmount']) ?  \Helper::stringToFloat($input['netAmount']) : 0;
        $totalCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $BookInvSuppMaster->supplierTransactionCurrencyID, $BookInvSuppMaster->supplierTransactionCurrencyID, $input['netAmount']);

        if($policy == true) {
            $input['netAmountLocal'] = \Helper::roundValue( $input['netAmount']/ $BookInvSuppMaster->localCurrencyER);
            $input['netAmountRpt'] = \Helper::roundValue($input['netAmount'] / $BookInvSuppMaster->companyReportingER);
        } if($policy == false) {
        $input['netAmountLocal'] = \Helper::roundValue($totalCurrencyConversion['localAmount']);
        $input['netAmountRpt'] = \Helper::roundValue($totalCurrencyConversion['reportingAmount']);
    }

        $directInvoiceDetails = $this->directInvoiceDetailsRepository->update($input, $id);

        SupplierInvoice::updateMaster($input['directInvoiceAutoID']);

        \Helper::updateSupplierRetentionAmount($input['directInvoiceAutoID'],$BookInvSuppMaster);
        \Helper::updateSupplierDirectWhtAmount($input['directInvoiceAutoID'],$BookInvSuppMaster);


        return $this->sendResponse($directInvoiceDetails->toArray(), 'Direct Invoice Details updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/directInvoiceDetails/{id}",
     *      summary="Remove the specified DirectInvoiceDetails from storage",
     *      tags={"DirectInvoiceDetails"},
     *      description="Delete DirectInvoiceDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of DirectInvoiceDetails",
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
        /** @var DirectInvoiceDetails $directInvoiceDetails */
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            return $this->sendError('Direct Invoice Details not found');
        }

        if($directInvoiceDetails->supplier_invoice_master && $directInvoiceDetails->supplier_invoice_master->confirmedYN){
            return $this->sendError('You cannot delete Supplier Invoice Details, this document already confirmed',500);
        }


        ExpenseEmployeeAllocation::where('documentSystemID', 11)
                                 ->where('documentDetailID', $id)
                                 ->where('documentSystemCode', $directInvoiceDetails->directInvoiceAutoID)
                                 ->delete();

        $this->expenseAssetAllocationRepository->deleteExpenseAssetAllocation(
            $directInvoiceDetails->directInvoiceAutoID,
            $directInvoiceDetails->supplier_invoice_master->documentSystemID,
            $id
        );

        $directInvoiceDetails->delete();

        $bookInvSuppMaster = BookInvSuppMaster::find($directInvoiceDetails->directInvoiceAutoID);
        \Helper::updateSupplierRetentionAmount($directInvoiceDetails->directInvoiceAutoID,$bookInvSuppMaster);
        \Helper::updateSupplierDirectWhtAmount($directInvoiceDetails->directInvoiceAutoID,$bookInvSuppMaster);
        SupplierInvoice::updateMaster($directInvoiceDetails->directInvoiceAutoID);

        return $this->sendResponse($id, 'Direct Invoice Details deleted successfully');
    }

    public function getDirectItems(Request $request)
    {
        $input = $request->all();
        $invoiceID = $input['invoiceID'];

        $items = DirectInvoiceDetails::where('directInvoiceAutoID', $invoiceID)
            ->with(['segment', 'purchase_order','chartofaccount'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Direct Invoice Details retrieved successfully');
    }

    public function deleteAllSIDirectDetail(Request $request)
    {
        $input = $request->all();

        $directInvoiceAutoID = isset($input['directInvoiceAutoID']) ? $input['directInvoiceAutoID'] : 0;

        $supInvoice = BookInvSuppMaster::find($directInvoiceAutoID);

        if (empty($supInvoice)) {
            return $this->sendError('Supplier Invoice not found');
        }

        if($supInvoice->confirmedYN){
            return $this->sendError('You cannot delete Supplier Invoice Details , this document already confirmed',500);
        }


        $detailExistAll = DirectInvoiceDetails::where('directInvoiceAutoID', $directInvoiceAutoID)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete',500);
        }

        $this->expenseAssetAllocationRepository->deleteExpenseAssetAllocation($directInvoiceAutoID, $supInvoice->documentSystemID);

        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {

                $deleteDetails = DirectInvoiceDetails::where('directInvoiceDetailsID', $cvDeatil['directInvoiceDetailsID'])->delete();

                }
            }
        \Helper::updateSupplierRetentionAmount($directInvoiceAutoID,$supInvoice);
        \Helper::updateSupplierDirectWhtAmount($directInvoiceAutoID,$supInvoice);
        SupplierInvoice::updateMaster($directInvoiceAutoID);

        return $this->sendResponse($directInvoiceAutoID, 'Details deleted successfully');
    }
}
