<?php
/**
 * =============================================
 * -- File Name : ChequeRegisterAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Cheque Registry
 * -- Author : Mohamed Rilwan
 * -- Create date : 24 - September 2019
 * -- Description : This file contains the all CRUD for Cheque Registry master
 * -- REVISION HISTORY

 * --  By: Rilwan Description: Added new functions named as getChequeRegisterFormData()
 * --  By: Rilwan Description: Added new functions named as getAllChequeRegistersByCompany()
 * --  By: Rilwan Description: Added new functions named as getChequeRegisterByMasterID()
 * --  By: Rilwan Description: Added new functions named as getChequeRegisterFormData()
 * -- Date: 15- October 2019 By: Rilwan Description: Added new functions named as exportChequeRegistry()
 */
namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateChequeRegisterAPIRequest;
use App\Http\Requests\API\UpdateChequeRegisterAPIRequest;
use App\Models\BankAssign;
use App\Models\BankMaster;
use App\Models\ChequeRegister;
use App\Models\ChequeRegisterDetail;
use App\Models\Company;
use App\Repositories\ChequeRegisterDetailRepository;
use App\Repositories\ChequeRegisterRepository;
use Carbon\Carbon;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\CreateExcel;
/**
 * Class ChequeRegisterController
 * @package App\Http\Controllers\API
 */
class ChequeRegisterAPIController extends AppBaseController
{
    /** @var  ChequeRegisterRepository */
    private $chequeRegisterRepository;
    private $chequeRegisterDetailRepository;

    public function __construct(ChequeRegisterRepository $chequeRegisterRepo, ChequeRegisterDetailRepository $chequeRegisterDetailRepository)
    {
        $this->chequeRegisterRepository = $chequeRegisterRepo;
        $this->chequeRegisterDetailRepository = $chequeRegisterDetailRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeRegisters",
     *      summary="Get a listing of the ChequeRegisters.",
     *      tags={"ChequeRegister"},
     *      description="Get all ChequeRegisters",
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
     *                  @SWG\Items(ref="#/definitions/ChequeRegister")
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
        $this->chequeRegisterRepository->pushCriteria(new RequestCriteria($request));
        $this->chequeRegisterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chequeRegisters = $this->chequeRegisterRepository->all();

        return $this->sendResponse($chequeRegisters->toArray(), trans('custom.cheque_registers_retrieved_successfully'));
    }

    /**
     * @param CreateChequeRegisterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chequeRegisters",
     *      summary="Store a newly created ChequeRegister in storage",
     *      tags={"ChequeRegister"},
     *      description="Store ChequeRegister",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeRegister that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeRegister")
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
     *                  ref="#/definitions/ChequeRegister"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChequeRegisterAPIRequest $request)
    {
        $input = $request->all();

        $messages = [
            'bank_id.required' => 'Bank is required',
            'bank_account_id.required' => 'Bank Account is required.'
        ];

        $validator = \Validator::make($input, [
            'company_id' => 'required',
            'bank_id' => 'required',
            'bank_account_id' => 'required',
            'no_of_cheques' => 'required',
            'started_cheque_no' => 'required',
            'description' => 'required'
        ], $messages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }
        DB::beginTransaction();
        try {
            $input['created_at'] = Helper::currentDateTime();
            $input['created_by'] = Helper::getEmployeeSystemID();
            $input['created_pc'] = gethostname();
            $input['isActive'] = 0;


            $chequeRegister = $this->chequeRegisterRepository->create($input);

            if (!empty($chequeRegister)) {

                $started_check_no = $chequeRegister['started_cheque_no'];
                $no_of_cheques = $chequeRegister['no_of_cheques'];
                $id = $chequeRegister['id'];

                $str_array = str_split($started_check_no);
                $arr_length = count($str_array);

                if ($str_array[0] != 0) {  // when started number doesnt start with zero

                    $cheque_no = $started_check_no;

                    for ($i = 0; $i < $no_of_cheques; $i++) {
                        $insert_array = [
                            'cheque_no' => $cheque_no,
                            'cheque_register_master_id' => $id,
                            'description' => $input['description'],
                            'company_id' => $input['company_id'],
                            'created_at' => $input['created_at'],
                            'created_by' => $input['created_by'],
                            'created_pc' => $input['created_pc']
                        ];

                        $isExist = ChequeRegister::whereHas('details', function ($query) use ($cheque_no) {
                            $query->where('cheque_no', $cheque_no);
                        })
                            ->where('bank_id', $input['bank_id'])
                            ->where('bank_account_id', $input['bank_account_id'])->count();

                        if ($isExist) {
                            return $this->sendError('Cheque No should be unique for bank and accounts.' . $cheque_no . ' Already exist', 500);
                        }

                        ChequeRegisterDetail::create($insert_array);
                        $cheque_no++;
                    }


                } else {  // when started number start with zero

                    $cheque_no = ltrim($started_check_no, '0');

                    for ($i = 0; $i < $no_of_cheques; $i++) {
                        $insert_array = [
                            'cheque_no' => $this->pad($cheque_no, $arr_length),
                            'cheque_register_master_id' => $id,
                            'description' => $input['description'],
                            'company_id' => $input['company_id'],
                            'created_at' => $input['created_at'],
                            'created_by' => $input['created_by'],
                            'created_pc' => $input['created_pc']
                        ];
                        $checkNoWithZero = $this->pad($cheque_no, $arr_length);
                        $isExist = ChequeRegister::whereHas('details', function ($query) use ($checkNoWithZero) {
                            $query->where('cheque_no', $checkNoWithZero);
                        })
                            ->where('bank_id', $input['bank_id'])
                            ->where('bank_account_id', $input['bank_account_id'])->first();
                        if (!empty($isExist)) {
                            return $this->sendError('Cheque No should be unique for bank and accounts. Cheque no ' . $checkNoWithZero . ' Already exist', 500);
                        }

                        ChequeRegisterDetail::create($insert_array);
                        $cheque_no++;
                    }
                }

            }
            DB::commit();
            return $this->sendResponse($chequeRegister->toArray(), trans('custom.cheques_registered_successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage(), 500);
        }


    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeRegisters/{id}",
     *      summary="Display the specified ChequeRegister",
     *      tags={"ChequeRegister"},
     *      description="Get ChequeRegister",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeRegister",
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
     *                  ref="#/definitions/ChequeRegister"
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
        /** @var ChequeRegister $chequeRegister */
        $chequeRegister = $this->chequeRegisterRepository->findWithoutFail($id);

        if (empty($chequeRegister)) {
            return $this->sendError(trans('custom.cheque_register_not_found'));
        }

        return $this->sendResponse($chequeRegister->toArray(), trans('custom.cheque_register_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateChequeRegisterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chequeRegisters/{id}",
     *      summary="Update the specified ChequeRegister in storage",
     *      tags={"ChequeRegister"},
     *      description="Update ChequeRegister",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeRegister",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeRegister that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeRegister")
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
     *                  ref="#/definitions/ChequeRegister"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChequeRegisterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ChequeRegister $chequeRegister */
        $chequeRegister = $this->chequeRegisterRepository->findWithoutFail($id);

        if (empty($chequeRegister)) {
            return $this->sendError(trans('custom.cheque_register_not_found'));
        }


        $chequeRegister = $this->chequeRegisterRepository->update($input, $id);

        return $this->sendResponse($chequeRegister->toArray(), trans('custom.chequeregister_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chequeRegisters/{id}",
     *      summary="Remove the specified ChequeRegister from storage",
     *      tags={"ChequeRegister"},
     *      description="Delete ChequeRegister",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeRegister",
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
        /** @var ChequeRegister $chequeRegister */
        $chequeRegister = $this->chequeRegisterRepository->findWithoutFail($id);

        if (empty($chequeRegister)) {
            return $this->sendError(trans('custom.cheque_register_not_found'));
        }

        $checkChequeRegisterDetail = ChequeRegisterDetail::where('cheque_register_master_id', $id)
                                                         ->whereNotNull('document_id')
                                                         ->first();

        if ($checkChequeRegisterDetail) {
            return $this->sendError(trans('custom.you_cannot_delete_this_cheque_register_cheques_are'));
        }


        $chequeRegister->delete();

        ChequeRegisterDetail::where('cheque_register_master_id', $id)
                            ->delete();

        return $this->sendResponse($id, trans('custom.cheque_register_deleted_successfully'));
    }

    public function getChequeRegisterFormData(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        $validator = \Validator::make($input, [
            'companyId' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $output['banks'] = $bank = BankAssign::where('companySystemID', $input['companyId'])
            ->where('isActive', 1)
            ->where('isAssigned', -1)
            ->get();

        $output['cheque_statuses'] = array(
            [
                'cheque_status_id' => '',
                'cheque_status' => trans('custom.all')
            ], [
                'cheque_status_id' => 0,
                'cheque_status' => trans('custom.unused')
            ],
            [
                'cheque_status_id' => 1,
                'cheque_status' => trans('custom.used')
            ],
            [
                'cheque_status_id' => 2,
                'cheque_status' => trans('custom.cancelled')
            ],
        );

        return $this->sendResponse($output, trans('custom.record_retrieved_successfully_1'));
    }

    private function pad($number, $size)
    {

        $s = (string)$number;

        if (strlen($s) < $size) {
            $zeroCount = $size - strlen($s);

            for ($i = 0; $i < $zeroCount; $i++) {
                $s = '0' . $s;

            }
        }
        return $s;
    }

    public function getAllChequeRegistersByCompany(Request $request)
    {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array('company_id', 'bank_id'));

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $bankmasterAutoID = $request['bank_id'];
        $bankmasterAutoID = (array)$bankmasterAutoID;
        $bankmasterAutoID = collect($bankmasterAutoID)->pluck('id');

        $selectedCompanyId = $request['company_id'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        

        $search = $request->input('search.value');

        $chequeRegister = ChequeRegister::whereIn('company_id', $subCompanies)
            ->with(['bank', 'bank_account.currency'])
            ->withCount(['details', 'details as unused_count' => function ($q) {
                $q->where('status', 0);
            }]);

        if (array_key_exists('bank_id', $input)) {
            if ($input['bank_id'] != null) {
                $chequeRegister->whereIn('bank_id', $bankmasterAutoID);
            }
        }

        if (array_key_exists('bank_acc_id', $input)) {
            if ($input['bank_acc_id'] != null) {
                $chequeRegister->where('bank_account_id', $input['bank_acc_id']);
            }
        }

        if (array_key_exists('cheque_status_id', $input)) {
            if ($input['cheque_status_id'] != null) {
                $chequeRegister->whereHas('details', function ($q) use ($input) {
                    return $q->where('status', $input['cheque_status_id']);
                });
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $chequeRegister = $chequeRegister->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('bank', function ($q) use ($search) {
                        return $q->where('bankName', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('details', function ($q) use ($search) {
                        return $q->where('cheque_no', 'LIKE', "%{$search}%");
                    })->orWhereHas('bank_account', function ($q) use ($search) {
                        return $q->where('AccountNo', 'LIKE', "%{$search}%")
                            ->orWhere('glCodeLinked', 'LIKE', "%{$search}%");
                    });
            });
        }

        return \DataTables::eloquent($chequeRegister)
            ->addColumn('Actions', 'Actions', "Actions")
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);

    }

    public function getChequeRegisterByMasterID(Request $request)
    {

        $input = $request->all();

        $validator = \Validator::make($input, [
            'id' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }


        $chequeRegister = ChequeRegister::with(['bank', 'bank_account.currency'])
            ->withCount(['details as cheque_count', 'details as unused_count' => function ($q) {
                $q->where('status', 0);
            }])
            ->where('id', $input['id'])
            ->first();
        if (empty($chequeRegister)) {
            return $this->sendError(trans('custom.cheque_register_data_not_found'), 404);
        }
        return $this->sendResponse($chequeRegister->toArray(), 'Cheque Register data received');
    }

    public function exportChequeRegistry(Request $request)
    {
        
        $data = array();
        $output = ChequeRegisterDetail::with(['document','master']);
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, ['company_id']);
        $type = $input['type'];

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $selectedCompanyId = $request['company_id'];
        $isGroup = \Helper::checkIsCompanyGroup($selectedCompanyId);

        if ($isGroup) {
            $subCompanies = \Helper::getGroupCompany($selectedCompanyId);
        } else {
            $subCompanies = [$selectedCompanyId];
        }
        $search = $request->input('search.value');

        $chequeRegister = ChequeRegister::whereIn('company_id', $subCompanies)
            ->with(['bank', 'bank_account.currency'])
            ->withCount(['details', 'details as unused_count' => function ($q) {
                $q->where('status', 0);
            }]);

        if (array_key_exists('bank_id', $input)) {
            if ($input['bank_id'] != null) {
                $chequeRegister->where('bank_id', $input['bank_id']);
            }
        }

        if (array_key_exists('bank_acc_id', $input)) {
            if ($input['bank_acc_id'] != null) {
                $chequeRegister->where('bank_account_id', $input['bank_acc_id']);
            }
        }

        if (array_key_exists('cheque_status_id', $input)) {
            if ($input['cheque_status_id'] != null) {
                $chequeRegister->whereHas('details', function ($q) use ($input) {
                    return $q->where('status', $input['cheque_status_id']);
                });
            }
        }

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $chequeRegister = $chequeRegister->where(function ($query) use ($search) {
                $query->where('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('bank', function ($q) use ($search) {
                        return $q->where('bankName', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('details', function ($q) use ($search) {
                        return $q->where('cheque_no', 'LIKE', "%{$search}%");
                    })->orWhereHas('bank_account', function ($q) use ($search) {
                        return $q->where('AccountNo', 'LIKE', "%{$search}%")
                            ->orWhere('glCodeLinked', 'LIKE', "%{$search}%");
                    });
            });
        }

        $output = $chequeRegister->get();
        

        if(!empty($output)){
            $x = 0;
            foreach ($output as $value) {
                    $data[$x][trans('custom.description')] = $value->description;
                    $data[$x][trans('custom.bank_name')] = $value->bank->bankName;
                    $data[$x][trans('custom.account')] = $value->bank_account->AccountNo;
                    $data[$x][trans('custom.start_cheque_no')] = $value->started_cheque_no;
                    $data[$x][trans('custom.end_cheque_no')] = $value->ended_cheque_no;
                    $data[$x][trans('custom.no_of_cheques')] = $value->no_of_cheques;
                    $data[$x][trans('custom.unused_cheques')] = $value->unused_count;

                $x++;
            }

        }
        $companyMaster = Company::find(isset($input['company_id'])?$input['company_id']: null);
        $companyCode = isset($companyMaster->CompanyID)?$companyMaster->CompanyID:'common';
        $detail_array = array(
            'company_code'=>$companyCode,
        );

        $fileName = 'cheque_registry';
        $path = 'treasury/transaction/cheque_registry/excel/';
        $basePath = CreateExcel::process($data,$type,$fileName,$path, $detail_array);

        if($basePath == '')
        {
             return $this->sendError('Unable to export excel');
        }
        else
        {
             return $this->sendResponse($basePath, trans('custom.success_export'));
        }



    }

    public function checkChequeRegisterStatus(Request $request){
        $input = $request->all();

        $chequeRegister = ChequeRegister::find($input['registerID']);

        if (empty($chequeRegister)) {
            return $this->sendError(trans('custom.cheque_register_not_found'));
        }

        if($chequeRegister->isActive == 0) {

            $sameAccounts = ChequeRegister::where('bank_id', $chequeRegister->bank_id)->where('bank_account_id', $chequeRegister->bank_account_id)->where('isActive', 1)->where('id', '!=', $input['registerID'])->get();
            if ($sameAccounts->isEmpty()) {
                $sameAccounts = null;
            }
        }
        else{
            $sameAccounts = null;
        }

        return $this->sendResponse($sameAccounts, "Status updated successfully");

    }


    public function chequeRegisterStatusChange(Request $request)
    {
        $input = $request->all();

        $chequeRegister = ChequeRegister::find($input['registerID']);

        if (empty($chequeRegister)) {
            return $this->sendError(trans('custom.cheque_register_not_found'));
        }

        if($chequeRegister->isActive == 0) {
            ChequeRegister::where('bank_id', $chequeRegister->bank_id)->where('bank_account_id', $chequeRegister->bank_account_id)->where('isActive', 1)->where('id', '!=', $input['registerID'])->update(['isActive' => 0]);
        }

        $chequeRegister->isActive = ($chequeRegister->isActive == 1) ? 0 : 1;
        $chequeRegister->save();




         return $this->sendResponse([], "Status updated successfully");
    }
}
