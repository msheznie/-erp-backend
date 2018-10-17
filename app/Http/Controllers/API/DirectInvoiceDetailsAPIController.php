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

use App\Http\Requests\API\CreateDirectInvoiceDetailsAPIRequest;
use App\Http\Requests\API\UpdateDirectInvoiceDetailsAPIRequest;
use App\Models\BookInvSuppMaster;
use App\Models\ChartOfAccount;
use App\Models\DirectInvoiceDetails;
use App\Models\SegmentMaster;
use App\Repositories\DirectInvoiceDetailsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
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

    public function __construct(DirectInvoiceDetailsRepository $directInvoiceDetailsRepo)
    {
        $this->directInvoiceDetailsRepository = $directInvoiceDetailsRepo;
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

/*        $alreadyAdded = BookInvSuppMaster::where('bookingSuppMasInvAutoID', $BookInvSuppMaster->bookingSuppMasInvAutoID)
            ->whereHas('directdetail', function ($query) use ($input) {
                $query->where('chartOfAccountSystemID', $input['chartOfAccountSystemID']);
            })
            ->first();

        if ($alreadyAdded) {
            return $this->sendError("Selected item is already added. Please check again", 500);
        }*/



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
        $input['localCurrencyER' ] = $companyCurrencyConversion['trasToLocER'];
        $input['comRptCurrency'] =   $BookInvSuppMaster->companyReportingCurrencyID;
        $input['comRptCurrencyER'] = $companyCurrencyConversion['trasToRptER'];

        if ($BookInvSuppMaster->FYBiggin) {
            $finYearExp = explode('-', $BookInvSuppMaster->FYBiggin);
            $input['budgetYear'] = $finYearExp[0];
        } else {
            $input['budgetYear'] = date("Y");
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
        $input = array_except($input, ['segment']);
        $input = $this->convertArrayToValue($input);
        $serviceLineError = array('type' => 'serviceLine');

        /** @var DirectInvoiceDetails $directInvoiceDetails */
        $directInvoiceDetails = $this->directInvoiceDetailsRepository->findWithoutFail($id);

        if (empty($directInvoiceDetails)) {
            return $this->sendError('Direct Invoice Details not found');
        }

        $BookInvSuppMaster = BookInvSuppMaster::find($input['directInvoiceAutoID']);

        if (empty($BookInvSuppMaster)) {
            return $this->sendError('Book Inv Supp Master not found');
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

        if( $input['DIAmount'] == ""){
            $input['DIAmount'] = 0;
        }

        $companyCurrencyConversion = \Helper::currencyConversion($input['companySystemID'], $BookInvSuppMaster->supplierTransactionCurrencyID,$BookInvSuppMaster->supplierTransactionCurrencyID, $input['DIAmount']);

        $input['localAmount' ]        = $companyCurrencyConversion['localAmount'];
        $input['comRptAmount']        = $companyCurrencyConversion['reportingAmount'];
        $input['localCurrencyER' ]    = $companyCurrencyConversion['trasToLocER'];
        $input['comRptCurrencyER']    = $companyCurrencyConversion['trasToRptER'];

        $directInvoiceDetails = $this->directInvoiceDetailsRepository->update($input, $id);

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

        $directInvoiceDetails->delete();

        return $this->sendResponse($id, 'Direct Invoice Details deleted successfully');
    }

    public function getDirectItems(Request $request)
    {
        $input = $request->all();
        $invoiceID = $input['invoiceID'];

        $items = DirectInvoiceDetails::where('directInvoiceAutoID', $invoiceID)
            ->with(['segment'])
            ->get();

        return $this->sendResponse($items->toArray(), 'Direct Invoice Details retrieved successfully');
    }

    public function deleteAllSIDirectDetail(Request $request)
    {
        $input = $request->all();

        $directInvoiceAutoID = $input['directInvoiceAutoID'];

        $detailExistAll = DirectInvoiceDetails::where('directInvoiceAutoID', $directInvoiceAutoID)
            ->get();

        if (empty($detailExistAll)) {
            return $this->sendError('There are no details to delete');
        }

        if (!empty($detailExistAll)) {

            foreach ($detailExistAll as $cvDeatil) {

                $deleteDetails = DirectInvoiceDetails::where('directInvoiceDetailsID', $cvDeatil['directInvoiceDetailsID'])->delete();

                }
            }

        return $this->sendResponse($directInvoiceAutoID, 'Details deleted successfully');
    }
}
