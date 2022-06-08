<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderSupplierAssigneeAPIRequest;
use App\Http\Requests\API\UpdateTenderSupplierAssigneeAPIRequest;
use App\Models\TenderSupplierAssignee;
use App\Repositories\TenderSupplierAssigneeRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use Carbon\Carbon;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EmailForQueuing;
use App\Models\TenderMaster;
use App\Repositories\SupplierRegistrationLinkRepository;

/**
 * Class TenderSupplierAssigneeController
 * @package App\Http\Controllers\API
 */

class TenderSupplierAssigneeAPIController extends AppBaseController
{
    /** @var  TenderSupplierAssigneeRepository */
    private $tenderSupplierAssigneeRepository;
    private $registrationLinkRepository;
    public function __construct(TenderSupplierAssigneeRepository $tenderSupplierAssigneeRepo, SupplierRegistrationLinkRepository $registrationLinkRepository)
    {
        $this->tenderSupplierAssigneeRepository = $tenderSupplierAssigneeRepo;
        $this->registrationLinkRepository = $registrationLinkRepository;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderSupplierAssignees",
     *      summary="Get a listing of the TenderSupplierAssignees.",
     *      tags={"TenderSupplierAssignee"},
     *      description="Get all TenderSupplierAssignees",
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
     *                  @SWG\Items(ref="#/definitions/TenderSupplierAssignee")
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
        $this->tenderSupplierAssigneeRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderSupplierAssigneeRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderSupplierAssignees = $this->tenderSupplierAssigneeRepository->all();

        return $this->sendResponse($tenderSupplierAssignees->toArray(), 'Tender Supplier Assignees retrieved successfully');
    }

    /**
     * @param CreateTenderSupplierAssigneeAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderSupplierAssignees",
     *      summary="Store a newly created TenderSupplierAssignee in storage",
     *      tags={"TenderSupplierAssignee"},
     *      description="Store TenderSupplierAssignee",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderSupplierAssignee that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderSupplierAssignee")
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
     *                  ref="#/definitions/TenderSupplierAssignee"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderSupplierAssigneeAPIRequest $request)
    {
        $input = $request->all();

        $tenderSupplierAssignee = $this->tenderSupplierAssigneeRepository->create($input);

        return $this->sendResponse($tenderSupplierAssignee->toArray(), 'Tender Supplier Assignee saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderSupplierAssignees/{id}",
     *      summary="Display the specified TenderSupplierAssignee",
     *      tags={"TenderSupplierAssignee"},
     *      description="Get TenderSupplierAssignee",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderSupplierAssignee",
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
     *                  ref="#/definitions/TenderSupplierAssignee"
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
        /** @var TenderSupplierAssignee $tenderSupplierAssignee */
        $tenderSupplierAssignee = $this->tenderSupplierAssigneeRepository->findWithoutFail($id);

        if (empty($tenderSupplierAssignee)) {
            return $this->sendError('Tender Supplier Assignee not found');
        }

        return $this->sendResponse($tenderSupplierAssignee->toArray(), 'Tender Supplier Assignee retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderSupplierAssigneeAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderSupplierAssignees/{id}",
     *      summary="Update the specified TenderSupplierAssignee in storage",
     *      tags={"TenderSupplierAssignee"},
     *      description="Update TenderSupplierAssignee",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderSupplierAssignee",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderSupplierAssignee that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderSupplierAssignee")
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
     *                  ref="#/definitions/TenderSupplierAssignee"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderSupplierAssigneeAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderSupplierAssignee $tenderSupplierAssignee */
        $tenderSupplierAssignee = $this->tenderSupplierAssigneeRepository->findWithoutFail($id);

        if (empty($tenderSupplierAssignee)) {
            return $this->sendError('Tender Supplier Assignee not found');
        }

        $tenderSupplierAssignee = $this->tenderSupplierAssigneeRepository->update($input, $id);

        return $this->sendResponse($tenderSupplierAssignee->toArray(), 'TenderSupplierAssignee updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderSupplierAssignees/{id}",
     *      summary="Remove the specified TenderSupplierAssignee from storage",
     *      tags={"TenderSupplierAssignee"},
     *      description="Delete TenderSupplierAssignee",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderSupplierAssignee",
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
        /** @var TenderSupplierAssignee $tenderSupplierAssignee */
        $tenderSupplierAssignee = $this->tenderSupplierAssigneeRepository->findWithoutFail($id);

        if (empty($tenderSupplierAssignee)) {
            return $this->sendError('Tender Supplier Assignee not found');
        }

        $tenderSupplierAssignee->delete();

        return $this->sendSuccess('Tender Supplier Assignee deleted successfully');
    }

    public function deleteSupplierAssign(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $tenderSupplierAssignee = $this->tenderSupplierAssigneeRepository->findWithoutFail($id);

        if (empty($tenderSupplierAssignee)) {
            return $this->sendError('Not Found');
        }
        $tenderSupplierAssignee->delete();
        return $this->sendResponse($id, 'File Deleted');
    }
    public function supplierAssignCRUD(Request $request)
    {
        $input = $request->all();
        $name = $input['name'];
        $email = $input['email'];
        $regNo = $input['regNo'];
        $tenderId = $input['tenderId'];
        $companySystemID = $input['companySystemID'];
        $employee = \Helper::getEmployeeInfo();

        $validator = \Validator::make($input, [
            'email' => 'required|email|max:255',
            'name' => 'required|max:255',
            'regNo' => 'required|max:255',
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        DB::beginTransaction();
        try {
            $data['tender_master_id'] = $tenderId;
            $data['supplier_name'] = $name;
            $data['supplier_email'] = $email;
            $data['registration_number'] = $regNo;
            $data['created_by'] = $employee->employeeSystemID;
            $data['company_id'] = $companySystemID;
            $result = TenderSupplierAssignee::create($data);
            if ($result) {
                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
    public function sendSupplierInvitation(Request $request)
    {
        $input = $request->all();
        $tenderId = $input['tenderId'];
        $companyId = $input['companySystemId'];

        $companyName = "";
        $company = Company::find($companyId);
        if (isset($company->CompanyName)) {
            $companyName =  $company->CompanyName;
        }
        $apiKey = $request->input('api_key');
        $getSupplierAssignedData = TenderSupplierAssignee::with(['supplierAssigned'])
            ->where('tender_master_id', $tenderId)
            ->where('company_id', $companyId)
            ->where('mail_sent', 0)
            ->get();

        if (count($getSupplierAssignedData) > 0) {
            foreach ($getSupplierAssignedData as $val) {
                $token = md5(Carbon::now()->format('YmdHisu'));
                $name = (!is_null($val['supplierAssigned']['supplierName'])) ? $val['supplierAssigned']['supplierName'] : $val['supplier_name'];
                $email = (!is_null($val['supplierAssigned']['supEmail'])) ? $val['supplierAssigned']['supEmail'] : $val['supplier_email'];
                $regNo = (!is_null($val['supplierAssigned']['registrationNumber'])) ? $val['supplierAssigned']['registrationNumber'] : $val['registration_number'];
                $isBidTender =  (!is_null($val['supplierAssigned']['registrationNumber'])) ? 0 : 1;
                $isCreated = $this->registrationLinkRepository->save(request()->merge([
                    'name' => $name, 'email' => $email, 'registration_number' => $regNo, 'company_id' => $companyId,
                    'is_bid_tender' => $isBidTender, 'created_via' => 1
                ]), $token);
                $loginUrl = env('SRM_LINK') . $token . '/' . $apiKey;
                if ($isCreated['status'] == true) {
                    $this->sendSupplierEmailInvitation($email, $companyName, $loginUrl,$tenderId,$companyId);
                    TenderSupplierAssignee::find($val['id'])
                        ->update(['mail_sent' => 1, 'registration_link_id' => $isCreated['id']]);
                }
            }
            return $this->sendResponse([], 'Invitation sent successfully');
        } else {
            return $this->sendError('No records found', 500);
        }
    }
    public function reSendInvitaitonLink(Request $request)
    {
        $input = $request->all();
        $tenderId = $input['tenderId'];
        $companySystemId = $input['companySystemId'];
        $tenderAssigneeId = $input['tenderAssigneeId'];
        $companyName = "";
        $company = Company::find($companySystemId);
        if (isset($company->CompanyName)) {
            $companyName =  $company->CompanyName;
        }
        $apiKey = $request->input('api_key');

        $getSupplierAssignedData = TenderSupplierAssignee::with(['supplierAssigned'])
            ->where('tender_master_id', $tenderId)
            ->where('id', $tenderAssigneeId)
            ->where('company_id', $companySystemId)
            ->first();

        DB::beginTransaction();
        try {
            $token = md5(Carbon::now()->format('YmdHisu'));
            $loginUrl = env('SRM_LINK') . $token . '/' . $apiKey;

            $name = (!is_null($getSupplierAssignedData['supplierAssigned']['supplierName'])) ? $getSupplierAssignedData['supplierAssigned']['supplierName'] : $getSupplierAssignedData['supplier_name'];
            $email = (!is_null($getSupplierAssignedData['supplierAssigned']['supEmail'])) ? $getSupplierAssignedData['supplierAssigned']['supEmail'] : $getSupplierAssignedData['supplier_email'];
            $regNo = (!is_null($getSupplierAssignedData['supplierAssigned']['registrationNumber'])) ? $getSupplierAssignedData['supplierAssigned']['registrationNumber'] : $getSupplierAssignedData['registration_number'];
            $isBidTender =  (!is_null($getSupplierAssignedData['supplierAssigned']['registrationNumber'])) ? 0 : 1;
            $isCreated = $this->registrationLinkRepository->save(request()->merge([
                'name' => $name, 'email' => $email, 'registration_number' => $regNo, 'company_id' => $companySystemId,
                'is_bid_tender' => $isBidTender, 'created_via' => 1
            ]), $token);

            if ($isCreated['status'] == true) {
                $this->sendSupplierEmailInvitation($email, $companyName, $loginUrl,$tenderId,$companySystemId);
                TenderSupplierAssignee::find($getSupplierAssignedData['id'])
                    ->update(['mail_sent' => 1, 'registration_link_id' => $isCreated['id']]);
                DB::commit();
                return $this->sendResponse([], 'Invitation re-sent successfully');
            }
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->sendError($exception->getLine() . $exception->getMessage());
        }
    }

    public function sendSupplierEmailInvitation($email, $companyName, $loginUrl,$tenderId,$companySystemId)
    {
        $tenderMaster = TenderMaster::select('title')
        ->where('id',$tenderId)
        ->where('company_id',$companySystemId)
        ->first();

        Mail::to($email)->send(new EmailForQueuing("Registration Link", "Dear Supplier," . "<br /><br />" . "
        You are invited to participate in a new tender, ".$tenderMaster['title'].".
        Please find the below link to register at " . $companyName . " supplier portal. It will expire in 48 hours. " . "<br /><br />" . "Click Here: " . "</b><a href='" . $loginUrl . "'>" . $loginUrl . "</a><br /><br />" . " Thank You" . "<br /><br /><b>"));
    }
}
