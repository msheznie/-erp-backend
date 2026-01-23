<?php
/**
 * =============================================
 * -- File Name : BankAccountRefferedBackAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Bank Account Reffered Back
 * -- Author : Mohamed Fayas
 * -- Create date : 21 - December 2018
 * -- Description : This file contains the all CRUD for  Bank Account
 * -- REVISION HISTORY
 * -- Date: 21-December 2018 By: Fayas Description: Added new functions named as getAccountsReferBackHistory()
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateBankAccountRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateBankAccountRefferedBackAPIRequest;
use App\Models\BankAccountRefferedBack;
use App\Repositories\BankAccountRefferedBackRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\Helper;

/**
 * Class BankAccountRefferedBackController
 * @package App\Http\Controllers\API
 */

class BankAccountRefferedBackAPIController extends AppBaseController
{
    /** @var  BankAccountRefferedBackRepository */
    private $bankAccountRefferedBackRepository;

    public function __construct(BankAccountRefferedBackRepository $bankAccountRefferedBackRepo)
    {
        $this->bankAccountRefferedBackRepository = $bankAccountRefferedBackRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankAccountRefferedBacks",
     *      summary="Get a listing of the BankAccountRefferedBacks.",
     *      tags={"BankAccountRefferedBack"},
     *      description="Get all BankAccountRefferedBacks",
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
     *                  @SWG\Items(ref="#/definitions/BankAccountRefferedBack")
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
        $this->bankAccountRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $this->bankAccountRefferedBackRepository->pushCriteria(new LimitOffsetCriteria($request));
        $bankAccountRefferedBacks = $this->bankAccountRefferedBackRepository->all();

        return $this->sendResponse($bankAccountRefferedBacks->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_account_reffered_backs')]));
    }

    /**
     * @param CreateBankAccountRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/bankAccountRefferedBacks",
     *      summary="Store a newly created BankAccountRefferedBack in storage",
     *      tags={"BankAccountRefferedBack"},
     *      description="Store BankAccountRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankAccountRefferedBack that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankAccountRefferedBack")
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
     *                  ref="#/definitions/BankAccountRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateBankAccountRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        $bankAccountRefferedBacks = $this->bankAccountRefferedBackRepository->create($input);

        return $this->sendResponse($bankAccountRefferedBacks->toArray(), trans('custom.save', ['attribute' => trans('custom.bank_account_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/bankAccountRefferedBacks/{id}",
     *      summary="Display the specified BankAccountRefferedBack",
     *      tags={"BankAccountRefferedBack"},
     *      description="Get BankAccountRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankAccountRefferedBack",
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
     *                  ref="#/definitions/BankAccountRefferedBack"
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
        /** @var BankAccountRefferedBack $bankAccountRefferedBack */
        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->with(['currency','confirmed_by','chart_of_account'])->findWithoutFail($id);

        if (empty($bankAccountRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_account_reffered_backs')]));
        }
        $bankAccountRefferedBack->accountIBAN = $bankAccountRefferedBack['accountIBAN#'];
        return $this->sendResponse($bankAccountRefferedBack->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.bank_account_reffered_backs')]));
    }

    /**
     * @param int $id
     * @param UpdateBankAccountRefferedBackAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/bankAccountRefferedBacks/{id}",
     *      summary="Update the specified BankAccountRefferedBack in storage",
     *      tags={"BankAccountRefferedBack"},
     *      description="Update BankAccountRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankAccountRefferedBack",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="BankAccountRefferedBack that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/BankAccountRefferedBack")
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
     *                  ref="#/definitions/BankAccountRefferedBack"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateBankAccountRefferedBackAPIRequest $request)
    {
        $input = $request->all();

        /** @var BankAccountRefferedBack $bankAccountRefferedBack */
        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->findWithoutFail($id);

        if (empty($bankAccountRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_account_reffered_backs')]));
        }

        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->update($input, $id);

        return $this->sendResponse($bankAccountRefferedBack->toArray(), trans('custom.update', ['attribute' => trans('custom.bank_account_reffered_backs')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/bankAccountRefferedBacks/{id}",
     *      summary="Remove the specified BankAccountRefferedBack from storage",
     *      tags={"BankAccountRefferedBack"},
     *      description="Delete BankAccountRefferedBack",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of BankAccountRefferedBack",
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
        /** @var BankAccountRefferedBack $bankAccountRefferedBack */
        $bankAccountRefferedBack = $this->bankAccountRefferedBackRepository->findWithoutFail($id);

        if (empty($bankAccountRefferedBack)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.bank_account_reffered_backs')]));
        }

        $bankAccountRefferedBack->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.bank_account_reffered_backs')]));
    }

    public function getAccountsReferBackHistory(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('month', 'year'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['companyId'];
        $isGroup = Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }

        $bankAccounts = BankAccountRefferedBack::whereIn('companySystemID', $subCompanies)
                                //->where('isAccountActive',1)
                                ->where('bankAccountAutoID',$input['id'])
                                ->with(['currency']);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $bankAccounts = $bankAccounts->where(function ($query) use ($search) {
                $query->where('AccountNo', 'LIKE', "%{$search}%")
                    ->orWhere('bankBranch', 'LIKE', "%{$search}%")
                    ->orWhere('glCodeLinked', 'LIKE', "%{$search}%")
                    ->orWhere('accountSwiftCode', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($bankAccounts)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('bankAccountAutoIDRefferedBack', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
}
