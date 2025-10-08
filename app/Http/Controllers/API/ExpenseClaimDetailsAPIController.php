<?php
/**
 * =============================================
 * -- File Name : ExpenseClaimDetailsAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Expense Claim
 * -- Author : Mohamed Fayas
 * -- Create date : 10 - September 2018
 * -- Description : This file contains the all CRUD for Expense Claim Details
 * -- REVISION HISTORY
 * -- Date: 10- September 2018 By: Fayas Description: Added new function getDetailsByExpenseClaim()
 * -- Date: 23- November 2018 By: Fayas Description: Added new function preCheckECDetailEdit()
 */

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateExpenseClaimDetailsAPIRequest;
use App\Http\Requests\API\UpdateExpenseClaimDetailsAPIRequest;
use App\Models\ChartOfAccount;
use App\Models\Company;
use App\Models\CountryMaster;
use App\Models\ExpenseClaim;
use App\Models\ExpenseClaimCategories;
use App\Models\ExpenseClaimDetails;
use App\Models\SegmentMaster;
use App\Repositories\DocumentAttachmentsRepository;
use App\Repositories\ExpenseClaimDetailsRepository;
use App\Repositories\ExpenseClaimRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ExpenseClaimDetailsController
 * @package App\Http\Controllers\API
 */
class ExpenseClaimDetailsAPIController extends AppBaseController
{
    /** @var  ExpenseClaimDetailsRepository */
    private $expenseClaimDetailsRepository;
    private $expenseClaimRepository;
    private $documentAttachmentsRepo;

    public function __construct(ExpenseClaimDetailsRepository $expenseClaimDetailsRepo, ExpenseClaimRepository $expenseClaimRepo, DocumentAttachmentsRepository $documentAttachmentsRepo)
    {
        $this->expenseClaimDetailsRepository = $expenseClaimDetailsRepo;
        $this->expenseClaimRepository = $expenseClaimRepo;
        $this->documentAttachmentsRepo = $documentAttachmentsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimDetails",
     *      summary="Get a listing of the ExpenseClaimDetails.",
     *      tags={"ExpenseClaimDetails"},
     *      description="Get all ExpenseClaimDetails",
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
     *                  @SWG\Items(ref="#/definitions/ExpenseClaimDetails")
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
        $this->expenseClaimDetailsRepository->pushCriteria(new RequestCriteria($request));
        $this->expenseClaimDetailsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->all();

        return $this->sendResponse($expenseClaimDetails->toArray(), trans('custom.expense_claim_details_retrieved_successfully'));
    }

    /**
     * @param CreateExpenseClaimDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/expenseClaimDetails",
     *      summary="Store a newly created ExpenseClaimDetails in storage",
     *      tags={"ExpenseClaimDetails"},
     *      description="Store ExpenseClaimDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimDetails that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimDetails")
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
     *                  ref="#/definitions/ExpenseClaimDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateExpenseClaimDetailsAPIRequest $request)
    {
        $input = $request->all();

        $expenseClaimDetails = $this->expenseClaimDetailsRepository->create($input);

        return $this->sendResponse($expenseClaimDetails->toArray(), trans('custom.expense_claim_details_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/expenseClaimDetails/{id}",
     *      summary="Display the specified ExpenseClaimDetails",
     *      tags={"ExpenseClaimDetails"},
     *      description="Get ExpenseClaimDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetails",
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
     *                  ref="#/definitions/ExpenseClaimDetails"
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
        /** @var ExpenseClaimDetails $expenseClaimDetails */
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            return $this->sendError(trans('custom.expense_claim_details_not_found_1'));
        }

        return $this->sendResponse($expenseClaimDetails->toArray(), trans('custom.expense_claim_details_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateExpenseClaimDetailsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/expenseClaimDetails/{id}",
     *      summary="Update the specified ExpenseClaimDetails in storage",
     *      tags={"ExpenseClaimDetails"},
     *      description="Update ExpenseClaimDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetails",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ExpenseClaimDetails that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ExpenseClaimDetails")
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
     *                  ref="#/definitions/ExpenseClaimDetails"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateExpenseClaimDetailsAPIRequest $request)
    {
        $input = $request->all();
        $input = array_except($input, ['segment', 'chart_of_account', 'currency', 'category', 'local_currency']);
        $input = $this->convertArrayToValue($input);
        $validator = \Validator::make($input, [
            'expenseClaimCategoriesAutoID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        /** @var ExpenseClaimDetails $expenseClaimDetails */
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            return $this->sendError(trans('custom.expense_claim_details_not_found_1'));
        }

        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($expenseClaimDetails->expenseClaimMasterAutoID);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }


        if ($expenseClaim->approved != -1) {
            return $this->sendError(trans('custom.this_expense_claim_is_not_approved_you_cannot_edit'), 500);
        }

        if ($expenseClaim->addedForPayment != 0) {
            return $this->sendError(trans('custom.cannot_edit_this_expense_claim_is_already_paid'), 500);
        }

        $category = ExpenseClaimCategories::find($input['expenseClaimCategoriesAutoID']);

        if (empty($category)) {
            return $this->sendError(trans('custom.category_not_found'));
        }

        $chartOfAccount = ChartOfAccount::where('AccountCode', $category->glCode)->first();

        if (empty($chartOfAccount)) {
            return $this->sendError(trans('custom.gl_code_not_found'));
        }

        $updateArray = ['expenseClaimCategoriesAutoID' => $input['expenseClaimCategoriesAutoID'],
            'chartOfAccountSystemID' => $chartOfAccount->chartOfAccountSystemID,
            'glCode' => $chartOfAccount->AccountCode, 'glCodeDescription' => $chartOfAccount->AccountDescription];

        $expenseClaimDetails = $this->expenseClaimDetailsRepository->update($updateArray, $id);

        return $this->sendResponse($expenseClaimDetails->toArray(), trans('custom.expenseclaimdetails_updated_successfully'));
    }

    public function preCheckECDetailEdit(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        /** @var ExpenseClaimDetails $expenseClaimDetails */
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            return $this->sendError(trans('custom.expense_claim_details_not_found_1'));
        }

        $expenseClaim = $this->expenseClaimRepository->findWithoutFail($expenseClaimDetails->expenseClaimMasterAutoID);

        if (empty($expenseClaim)) {
            return $this->sendError(trans('custom.expense_claim_not_found'));
        }


        if ($expenseClaim->approved != -1) {
            return $this->sendError(trans('custom.this_expense_claim_is_not_approved_you_cannot_edit'), 500);
        }

        if ($expenseClaim->addedForPayment != 0) {
            return $this->sendError(trans('custom.cannot_edit_this_expense_claim_is_already_paid'), 500);
        }

        return $this->sendResponse($expenseClaimDetails->toArray(), trans('custom.expense_claim_details_can_update_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/expenseClaimDetails/{id}",
     *      summary="Remove the specified ExpenseClaimDetails from storage",
     *      tags={"ExpenseClaimDetails"},
     *      description="Delete ExpenseClaimDetails",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ExpenseClaimDetails",
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
        /** @var ExpenseClaimDetails $expenseClaimDetails */
        $expenseClaimDetails = $this->expenseClaimDetailsRepository->findWithoutFail($id);

        if (empty($expenseClaimDetails)) {
            return $this->sendError(trans('custom.expense_claim_details_not_found_1'));
        }

        $expenseClaimDetails->delete();

        return $this->sendResponse($id, trans('custom.expense_claim_details_deleted_successfully'));
    }

    public function getDetailsByExpenseClaim(Request $request)
    {
        $input = $request->all();
        $id = $input['expenseClaimMasterAutoID'];

        $items = ExpenseClaimDetails::where('expenseClaimMasterAutoID', $id)
            ->with(['segment', 'chart_of_account', 'currency', 'category', 'local_currency'])
            ->get();

        return $this->sendResponse($items->toArray(), trans('custom.expense_claim_details_retrieved_successfully'));
    }

    public function saveExpenseClaimDetails(CreateExpenseClaimDetailsAPIRequest $request)
    {

        $inputArray = $request->all();

        $messages = [
            'details.required' => 'Expense claim details is required.',
        ];

        $validator = \Validator::make($inputArray, [
            'expenseClaimMasterAutoID' => 'required',
            'details' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        $this->saveExpenseClaimDetailsMultipleArray($inputArray);
        return $this->sendResponse([], trans('custom.expense_claim_details_saved_successfully'));
    }

    private function saveExpenseClaimDetailsMultipleArray($inputArray)
    {
        foreach ($inputArray['details'] as $input) {

            $detail_messages = [
                'expenseClaimCategoriesAutoID.required' => 'Claim Category is required.',
                'serviceLineCode.required' => 'Department is required.',
                'currencyID.required' => 'Currency is required.',
            ];

            $detail_validator = \Validator::make($input, [
                'expenseClaimCategoriesAutoID' => 'required',
                'serviceLineCode' => 'required',
                'currencyID' => 'required',
                'amount' => 'required',
                'description' => 'required',
                'docRef' => 'required',
                'amount' => 'required'
            ], $detail_messages);

            if ($detail_validator->fails()) {
                return $this->sendError($detail_validator->messages(), 422);
            }

            $expenseClaim = ExpenseClaim::find($inputArray['expenseClaimMasterAutoID']);
            if ($inputArray['expenseClaimMasterAutoID'] == 0 || empty($expenseClaim)) {
                return $this->sendError(trans('custom.expense_claim_master_not_found'), 422);
            }

            $companyID = $expenseClaim->companyID;
            $companySystemID = $expenseClaim->companySystemID;

            $localCurrency = null;
            $reportingCurrency = null;
            $company = Company::find($companySystemID);
            if (!empty($company)) {
                $localCurrency = $company->localCurrencyID;
                $reportingCurrency = $company->reportingCurrency;
            }

            $claimCategories = ExpenseClaimCategories::select('glCode', 'glCodeDescription', 'chartOfAccountSystemID')
                ->where('expenseClaimCategoriesAutoID', $input['expenseClaimCategoriesAutoID'])
                ->first();

            $chartOfAccountSystemID = 0;
            $glCode = null;
            $glCodeDescription = null;

            if (!empty($claimCategories)) {
                $chartOfAccountSystemID = $claimCategories->chartOfAccountSystemID;
                $glCode = $claimCategories->glCode;
                $glCodeDescription = $claimCategories->glCodeDescription;
            }

            $serviceLine = SegmentMaster::where('ServiceLineCode', $input['serviceLineCode'])->first();
            $serviceLineSystemID = $serviceLine->serviceLineSystemID;

            $companyCurrencyConversion = Helper::currencyConversion($companySystemID, $input['currencyID'], $input['currencyID'], $input['amount']);

            $array = array(
                'chartOfAccountSystemID' => $chartOfAccountSystemID,
                'glCode' => $glCode,
                'glCodeDescription' => $glCodeDescription,
                'companyID' => $companyID,
                'companySystemID' => $companySystemID,

                'localCurrency' => $companyCurrencyConversion['trasToLocER'],
                'comRptCurrency' => $companyCurrencyConversion['trasToRptER'],
                'localCurrencyER' => $localCurrency,
                'comRptCurrencyER' => $reportingCurrency,
                'currencyER' => 1,

                'comRptAmount' => $companyCurrencyConversion['reportingAmount'],
                'localAmount' => $companyCurrencyConversion['localAmount'],
                'amount' => $input['amount'],

                'expenseClaimMasterAutoID' => $inputArray['expenseClaimMasterAutoID'],
                'expenseClaimCategoriesAutoID' => $input['expenseClaimCategoriesAutoID'],
                'description' => $input['description'],
                'docRef' => $input['docRef'],
                'serviceLineCode' => $input['serviceLineCode'],
                'serviceLineSystemID' => $serviceLineSystemID,
                'currencyID' => $input['currencyID'],
            );

            if (isset($input['expenseClaimDetailsID']) && $input['expenseClaimDetailsID'] != 0) {
                //update
                ExpenseClaimDetails::where('expenseClaimDetailsID', $input['expenseClaimDetailsID'])->update($array);
            } else {
                //insert
                ExpenseClaimDetails::insert($array);
            }

        }

    }

    public function saveExpenseClaimDetailsSingle(CreateExpenseClaimDetailsAPIRequest $request)
    {
        $input = $request->all();
        $detail_messages = [
            'expenseClaimCategoriesAutoID.required' => 'Claim Category is required.',
            'serviceLineCode.required' => 'Department is required.',
            'currencyID.required' => 'Currency is required.',
        ];

        $detail_validator = \Validator::make($input, [
            'expenseClaimCategoriesAutoID' => 'required',
            'serviceLineCode' => 'required',
            'currencyID' => 'required',
            'amount' => 'required',
            'description' => 'required',
            'docRef' => 'required',
            'amount' => 'required'
        ], $detail_messages);

        if ($detail_validator->fails()) {
            return $this->sendError($detail_validator->messages(), 422);
        }
        if (isset($input['expenseClaimMasterAutoID']) && $input['expenseClaimMasterAutoID'] > 0) {
            $expenseClaim = ExpenseClaim::find($input['expenseClaimMasterAutoID']);
            if (empty($expenseClaim)) {
                return $this->sendError(trans('custom.expense_claim_master_not_found'), 422);
            }
        } else {
            return $this->sendError(trans('custom.expense_claim_master_not_found'), 422);
        }

        $companyID = $expenseClaim->companyID;
        $companySystemID = $expenseClaim->companySystemID;

        $localCurrency = null;
        $reportingCurrency = null;
        $company = Company::find($companySystemID);
        if (!empty($company)) {
            $localCurrency = $company->localCurrencyID;
            $reportingCurrency = $company->reportingCurrency;
        }

        $claimCategories = ExpenseClaimCategories::select('glCode', 'glCodeDescription', 'chartOfAccountSystemID')
            ->where('expenseClaimCategoriesAutoID', $input['expenseClaimCategoriesAutoID'])
            ->first();

        $chartOfAccountSystemID = 0;
        $glCode = null;
        $glCodeDescription = null;

        if (!empty($claimCategories)) {
            $chartOfAccountSystemID = $claimCategories->chartOfAccountSystemID;
            $glCode = $claimCategories->glCode;
            $glCodeDescription = $claimCategories->glCodeDescription;
        }

        $serviceLine = SegmentMaster::where('ServiceLineCode', $input['serviceLineCode'])->first();
        $serviceLineSystemID = $serviceLine->serviceLineSystemID;

        $companyCurrencyConversion = Helper::currencyConversion($companySystemID, $input['currencyID'], $input['currencyID'], $input['amount']);

        $array = array(
            'chartOfAccountSystemID' => $chartOfAccountSystemID,
            'glCode' => $glCode,
            'glCodeDescription' => $glCodeDescription,
            'companyID' => $companyID,
            'companySystemID' => $companySystemID,

            'localCurrency' => $companyCurrencyConversion['trasToLocER'],
            'comRptCurrency' => $companyCurrencyConversion['trasToRptER'],
            'localCurrencyER' => $localCurrency,
            'comRptCurrencyER' => $reportingCurrency,
            'currencyER' => 1,

            'comRptAmount' => $companyCurrencyConversion['reportingAmount'],
            'localAmount' => $companyCurrencyConversion['localAmount'],
            'amount' => $input['amount'],

            'expenseClaimCategoriesAutoID' => $input['expenseClaimCategoriesAutoID'],
            'description' => $input['description'],
            'docRef' => $input['docRef'],
            'serviceLineCode' => $input['serviceLineCode'],
            'serviceLineSystemID' => $serviceLineSystemID,
            'currencyID' => $input['currencyID'],
        );

        if (isset($input['expenseClaimDetailsID']) && $input['expenseClaimDetailsID'] != 0) {
            //update
            $details = $this->expenseClaimDetailsRepository->update($array, $input['expenseClaimDetailsID']);
        } else {
            //insert
            $array['expenseClaimMasterAutoID'] = $input['expenseClaimMasterAutoID'];
            $details = $this->expenseClaimDetailsRepository->create($array);
        }
        return $this->sendResponse($details, trans('custom.expense_claim_details_saved_successfully_1'));
    }


    public function saveAttachments(Request $request)
    {
        $input = $request->all();
        $extension = $input['fileType'];

        $blockExtensions = ['ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
            'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
            'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
            'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
            'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
            'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
            'xbap', 'xnk','php'];

        if (in_array($extension, $blockExtensions))
        {
            return $this->sendError('This type of file not allow to upload.',500);
        }

        if(isset($input['size'])){
            if ($input['size'] > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than ".\Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')),500);
            }
            $input['sizeInKbs'] = $input['size'];
        }

        if (isset($input['docExpirtyDate']) && $input['docExpirtyDate']) {
            $input['docExpirtyDate'] = new Carbon($input['docExpirtyDate']);
        }

        $input = $this->convertArrayToValue($input);

        if(isset($input['expenseClaimMasterAutoID'])){
            $expenseClaim = ExpenseClaim::find($input['expenseClaimMasterAutoID']);
            if (empty($expenseClaim)) {
                return $this->sendError("Expense Claim Master Details Not Found", 200);
            }
        }else{
            return $this->sendError("Expense Claim Master ID Not Set", 200);
        }

        $input['companySystemID'] = $expenseClaim->companySystemID;
        $input['companyID'] = $expenseClaim->companyID;
        $input['documentSystemID'] = $expenseClaim->documentSystemID;
        $input['documentID'] = $expenseClaim->documentID;
        $input['documentSystemCode'] = $expenseClaim->expenseClaimMasterAutoID;
        $documentAttachments = $this->documentAttachmentsRepo->create($input);
        $file = $request->request->get('file');
        $decodeFile = base64_decode($file);

        $input['myFileName'] = $documentAttachments->companyID . '_' . $documentAttachments->documentID . '_' . $documentAttachments->documentSystemCode . '_' . $documentAttachments->attachmentID . '.' . $extension;

        if (Helper::checkPolicy($input['companySystemID'], 50)) {
            $path = $expenseClaim->companyID. '/G_ERP/' .$documentAttachments->documentID . '/' . $documentAttachments->documentSystemCode . '/' . $input['myFileName'];
        } else {
            $path = $documentAttachments->documentID . '/' . $documentAttachments->documentSystemCode . '/' . $input['myFileName'];
        }

        Storage::disk(Helper::policyWiseDisk($expenseClaim->companySystemID, 'public'))->put($path, $decodeFile);

        $input['isUploaded'] = 1;
        $input['path'] = $path;

        $documentAttachments = $this->documentAttachmentsRepo->update($input, $documentAttachments->attachmentID);

        return $this->sendResponse($documentAttachments->toArray(), trans('custom.document_attachments_saved_successfully'));
    }
}
