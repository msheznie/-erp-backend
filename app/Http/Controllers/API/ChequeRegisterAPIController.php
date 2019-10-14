<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateChequeRegisterAPIRequest;
use App\Http\Requests\API\UpdateChequeRegisterAPIRequest;
use App\Models\BankAssign;
use App\Models\BankMaster;
use App\Models\ChequeRegister;
use App\Models\ChequeRegisterDetail;
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

        return $this->sendResponse($chequeRegisters->toArray(), 'Cheque Registers retrieved successfully');
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
            return $this->sendResponse($chequeRegister->toArray(), 'Cheques Registered successfully');
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
            return $this->sendError('Cheque Register not found');
        }

        return $this->sendResponse($chequeRegister->toArray(), 'Cheque Register retrieved successfully');
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
            return $this->sendError('Cheque Register not found');
        }


        $chequeRegister = $this->chequeRegisterRepository->update($input, $id);

        return $this->sendResponse($chequeRegister->toArray(), 'ChequeRegister updated successfully');
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
            return $this->sendError('Cheque Register not found');
        }

        $chequeRegister->delete();

        return $this->sendResponse($id, 'Cheque Register deleted successfully');
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

        return $this->sendResponse($output, 'Record retrieved successfully');
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
        $input = $this->convertArrayToSelectedValue($input, ['company_id']);

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

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $chequeRegister = $chequeRegister->where(function ($query) use ($search) {
                $query->where('started_cheque_no', 'LIKE', "%{$search}%")
                    ->orWhere('ended_cheque_no', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhereHas('bank', function ($q) use ($search) {
                        return $q->where('bankName', 'LIKE', "%{$search}%");
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
            return $this->sendError('Cheque register data not found', 404);
        }
        return $this->sendResponse($chequeRegister->toArray(), 'Cheque Register data received');
    }

}
