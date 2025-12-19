<?php
/**
 * =============================================
 * -- File Name : BankMemoPayeeAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Bank Memo Payee
 * -- Author : Fayas
 * -- Create date : 26 - November 2018
 * -- Description : This file contains the all CRUD for Bank Memo Payee
 * -- REVISION HISTORY
 * -- Date: 26 - November 2018 By: Fayas Description: Added a new function named as payeeBankMemosByDocument(),addBulkPayeeMemos(),payeeBankMemoDeleteAll()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankMemoPayeeAPIRequest;
use App\Http\Requests\API\UpdateBankMemoPayeeAPIRequest;
use App\Models\BankMemoPayee;
use App\Models\PaySupplierInvoiceMaster;
use App\Repositories\BankMemoPayeeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class BankMemoPayeeController
 * @package App\Http\Controllers\API
 */
class BankMemoPayeeAPIController extends AppBaseController
{
    /** @var  BankMemoPayeeRepository */
    private $bankMemoPayeeRepository;

    public function __construct(BankMemoPayeeRepository $bankMemoPayeeRepo)
    {
        $this->bankMemoPayeeRepository = $bankMemoPayeeRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankMemoPayees",
     *      summary="Get a listing of the BankMemoPayees.",
     *      tags={"BankMemoPayee"},
     *      description="Get all BankMemoPayees",
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
     *                  @SWG\Items(ref="#/definitions/BankMemoPayee")
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
        $this->bankMemoPayeeRepository->pushCriteria(new RequestCriteria($request));
        $this->bankMemoPayeeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankMemoPayees = $this->bankMemoPayeeRepository->all();

        return $this->sendResponse($bankMemoPayees->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_payees')]));
    }

    /**
     * @param CreateBankMemoPayeeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankMemoPayees",
     *      summary="Store a newly created BankMemoPayee in storage",
     *      tags={"BankMemoPayee"},
     *      description="Store BankMemoPayee",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankMemoPayee that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankMemoPayee")
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
     *                  ref="#/definitions/BankMemoPayee"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankMemoPayeeAPIRequest $request)
    {
        $input = $request->all();

        $bankMemoPayees = $this->bankMemoPayeeRepository->create($input);

        return $this->sendResponse($bankMemoPayees->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_memo_payees')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankMemoPayees/{id}",
     *      summary="Display the specified BankMemoPayee",
     *      tags={"BankMemoPayee"},
     *      description="Get BankMemoPayee",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankMemoPayee",
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
     *                  ref="#/definitions/BankMemoPayee"
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
        /** @var BankMemoPayee $bankMemoPayee */
        $bankMemoPayee = $this->bankMemoPayeeRepository->findWithoutFail($id);

        if (empty($bankMemoPayee)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_payees')]));
        }

        return $this->sendResponse($bankMemoPayee->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_payees')]));
    }

    /**
     * @param int $id
     * @param UpdateBankMemoPayeeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankMemoPayees/{id}",
     *      summary="Update the specified BankMemoPayee in storage",
     *      tags={"BankMemoPayee"},
     *      description="Update BankMemoPayee",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankMemoPayee",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankMemoPayee that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankMemoPayee")
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
     *                  ref="#/definitions/BankMemoPayee"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankMemoPayeeAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankMemoPayee $bankMemoPayee */
        $bankMemoPayee = $this->bankMemoPayeeRepository->findWithoutFail($id);

        if (empty($bankMemoPayee)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_payees')]));
        }

        $updateArray = ['memoDetail' => $input['memoDetail']];

        $bankMemoPayee = $this->bankMemoPayeeRepository->update($updateArray, $id);

        return $this->sendResponse($bankMemoPayee->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_memo_payees')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankMemoPayees/{id}",
     *      summary="Remove the specified BankMemoPayee from storage",
     *      tags={"BankMemoPayee"},
     *      description="Delete BankMemoPayee",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankMemoPayee",
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
        /** @var BankMemoPayee $bankMemoPayee */
        $bankMemoPayee = $this->bankMemoPayeeRepository->findWithoutFail($id);

        if (empty($bankMemoPayee)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_memo_payees')]));
        }

        $bankMemoPayee->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_memo_payees')]));
    }

    public function payeeBankMemosByDocument(Request $request)
    {

        $count = BankMemoPayee::where('documentSystemCode', $request['id'])
            ->orderBySort()
            ->count();

        $bankMemoByDocument = BankMemoPayee::where('documentSystemCode', $request['id'])
            ->orderBySort()
            ->get();

        $data = array('bankMemos' => $bankMemoByDocument->toArray(), 'count' => $count);
        return $this->sendResponse($data, trans('custom.retrieve', ['attribute' => trans('custom.bank_memo_payees')]));
    }

    public function addBulkPayeeMemos(Request $request)
    {


        $companyDefaultBankMemos = $request->get('memos');
        $createdArray = array();
        $employee = \Helper::getEmployeeInfo();

        $document = PaySupplierInvoiceMaster::find($request->get('id'));

        if (empty($document)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.payment_voucher')]));
        }

        foreach ($companyDefaultBankMemos as $value) {
            if ($value['isChecked']) {
                $temArray = array(
                    'companySystemID' => $document->companySystemID,
                    'companyID' => $document->companyID,
                    'documentSystemID' => $document->documentSystemID,
                    'documentID' => $document->documentID,
                    'documentSystemCode' => $document->PayMasterAutoId,
                    'memoHeader' => $value['bankMemoHeader'],
                    'bankMemoTypeID' => $value['bankMemoTypeID'],
                    'memoDetail' => '',
                    'supplierCodeSystem' => $request['supplierCodeSystem'],
                    'supplierCurrencyID' => $request['supplierCurrencyID'],
                    'updatedByUserID' => $employee->empID,
                    'updatedByUserName' => $employee->empName);
                $this->bankMemoPayeeRepository->create($temArray);
            }
        }

        return $this->sendResponse($createdArray, trans('custom.save', ['attribute' => trans('custom.bank_memo_suppliers')]));
    }

    public function payeeBankMemoDeleteAll(Request $request)
    {

        $bankMemoSupplier = BankMemoPayee::where('documentSystemCode', $request['id'])
                                            ->delete();

        return $this->sendResponse($bankMemoSupplier, trans('custom.delete', ['attribute' => trans('custom.bank_memos')]));
    }


}
