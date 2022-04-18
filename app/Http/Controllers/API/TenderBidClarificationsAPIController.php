<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateTenderBidClarificationsAPIRequest;
use App\Http\Requests\API\UpdateTenderBidClarificationsAPIRequest;
use App\Models\TenderBidClarifications;
use App\Repositories\TenderBidClarificationsRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\Company;
use App\Models\DocumentAttachments;
use App\Models\DocumentMaster;
use App\Models\Employee;
use App\Models\TenderMaster;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * Class TenderBidClarificationsController
 * @package App\Http\Controllers\API
 */

class TenderBidClarificationsAPIController extends AppBaseController
{
    /** @var  TenderBidClarificationsRepository */
    private $tenderBidClarificationsRepository;

    public function __construct(TenderBidClarificationsRepository $tenderBidClarificationsRepo)
    {
        $this->tenderBidClarificationsRepository = $tenderBidClarificationsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidClarifications",
     *      summary="Get a listing of the TenderBidClarifications.",
     *      tags={"TenderBidClarifications"},
     *      description="Get all TenderBidClarifications",
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
     *                  @SWG\Items(ref="#/definitions/TenderBidClarifications")
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
        $this->tenderBidClarificationsRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderBidClarificationsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderBidClarifications = $this->tenderBidClarificationsRepository->all();

        return $this->sendResponse($tenderBidClarifications->toArray(), 'Tender Bid Clarifications retrieved successfully');
    }

    /**
     * @param CreateTenderBidClarificationsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderBidClarifications",
     *      summary="Store a newly created TenderBidClarifications in storage",
     *      tags={"TenderBidClarifications"},
     *      description="Store TenderBidClarifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidClarifications that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidClarifications")
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
     *                  ref="#/definitions/TenderBidClarifications"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderBidClarificationsAPIRequest $request)
    {
        $input = $request->all();

        $tenderBidClarifications = $this->tenderBidClarificationsRepository->create($input);

        return $this->sendResponse($tenderBidClarifications->toArray(), 'Tender Bid Clarifications saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderBidClarifications/{id}",
     *      summary="Display the specified TenderBidClarifications",
     *      tags={"TenderBidClarifications"},
     *      description="Get TenderBidClarifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidClarifications",
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
     *                  ref="#/definitions/TenderBidClarifications"
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
        /** @var TenderBidClarifications $tenderBidClarifications */
        $tenderBidClarifications = $this->tenderBidClarificationsRepository->findWithoutFail($id);

        if (empty($tenderBidClarifications)) {
            return $this->sendError('Tender Bid Clarifications not found');
        }

        return $this->sendResponse($tenderBidClarifications->toArray(), 'Tender Bid Clarifications retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderBidClarificationsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderBidClarifications/{id}",
     *      summary="Update the specified TenderBidClarifications in storage",
     *      tags={"TenderBidClarifications"},
     *      description="Update TenderBidClarifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidClarifications",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderBidClarifications that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderBidClarifications")
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
     *                  ref="#/definitions/TenderBidClarifications"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderBidClarificationsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderBidClarifications $tenderBidClarifications */
        $tenderBidClarifications = $this->tenderBidClarificationsRepository->findWithoutFail($id);

        if (empty($tenderBidClarifications)) {
            return $this->sendError('Tender Bid Clarifications not found');
        }

        $tenderBidClarifications = $this->tenderBidClarificationsRepository->update($input, $id);

        return $this->sendResponse($tenderBidClarifications->toArray(), 'TenderBidClarifications updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderBidClarifications/{id}",
     *      summary="Remove the specified TenderBidClarifications from storage",
     *      tags={"TenderBidClarifications"},
     *      description="Delete TenderBidClarifications",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderBidClarifications",
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
        /** @var TenderBidClarifications $tenderBidClarifications */
        $tenderBidClarifications = $this->tenderBidClarificationsRepository->findWithoutFail($id);

        if (empty($tenderBidClarifications)) {
            return $this->sendError('Tender Bid Clarifications not found');
        }

        $tenderBidClarifications->delete();

        return $this->sendSuccess('Tender Bid Clarifications deleted successfully');
    }
    public function getPreBidClarifications(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companySystemID'];
        $data = TenderMaster::with(['tenderPreBidClarification' => function ($q) {
            $q->where('parent_id', 0);
            $q->with(['supplier']);
        }])
            ->whereHas('tenderPreBidClarification', function ($q) {
                $q->where('parent_id', 0);
            })
            ->get();

        return $data;
    }
    public function getPreBidClarificationsResponse(Request $request)
    {
        $input = $request->all();
        $id = $input['Id'];
        $employeeId = Helper::getEmployeeSystemID();

        $data['response'] = TenderBidClarifications::with(['supplier', 'employee' => function ($q) {
            $q->with(['profilepic']);
        },'attachment'])
            ->where('id', '=', $id)
            ->orWhere('parent_id', '=', $id)
            ->orderBy('parent_id', 'asc')
            ->get();
        $profilePic = Employee::with(['profilepic'])
            ->where('employeeSystemID', $employeeId)
            ->first();
        $data['profilePic'] = $profilePic['profilepic']['profile_image_url'];
        return $data;
    }
    public function createResponse(Request $request)
    {
        $input = $request->all();
        $employeeId = Helper::getEmployeeSystemID();
        $response = $input['comments'];
        $id = $input['id'];
        $companySystemID = $input['companySystemID'];
        $tenderParentPost = TenderBidClarifications::where('id', $id)->first();
        $company = Company::where('companySystemID', $companySystemID)->first();
        $documentCode = DocumentMaster::where('documentSystemID', 109)->first();

        DB::beginTransaction();
        try {
            $data['tender_master_id'] = $tenderParentPost['tender_master_id'];
            $data['posted_by_type'] = 1;
            $data['post'] = $response;
            $data['user_id'] = $employeeId;
            $data['is_public'] = $tenderParentPost['is_public'];
            $data['parent_id'] = $id;
            $data['created_by'] = $employeeId;
            $data['company_id'] = $companySystemID;
            $data['document_system_id'] = $documentCode->documentSystemID;
            $data['document_id'] = $documentCode->documentID; 
            $result = TenderBidClarifications::create($data); 
            if (isset($input['Attachment']) && !empty($input['Attachment'])) {
                $attachment = $input['Attachment'];
                if (!empty($attachment) && isset($attachment['file'])) {
                    $extension = $attachment['fileType'];
                    $allowExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'txt', 'xlsx'];

                    if (!in_array(strtolower($extension), $allowExtensions)) {
                        return $this->sendError('This type of file not allow to upload.', 500);
                    }

                    if (isset($attachment['size'])) {
                        if ($attachment['size'] > 2097152) {
                            return $this->sendError("Maximum allowed file size is 2 MB. Please upload lesser than 2 MB.", 500);
                        }
                    }
                    $file = $attachment['file'];
                    $decodeFile = base64_decode($file);
                    $attch = time() . '_PreBidClarificationCompany.' . $extension;
                    $path = $companySystemID . '/PreBidClarification/' . $attch;
                    Storage::disk(Helper::policyWiseDisk($companySystemID, 'public'))->put($path, $decodeFile);

                    $att['companySystemID'] = $companySystemID;
                    $att['companyID'] = $company->CompanyID;
                    $att['documentSystemID'] = $documentCode->documentSystemID;
                    $att['documentID'] = $documentCode->documentID;
                    $att['documentSystemCode'] = $result->id;
                    $att['attachmentDescription'] = 'Pre-Bid Clarification ' . time();
                    $att['path'] = $path;
                    $att['originalFileName'] = $attachment['originalFileName'];
                    $att['myFileName'] = $company->CompanyID . '_' . time() . '_PreBidClarification.' . $extension;
                    $att['sizeInKbs'] = $attachment['sizeInKbs'];
                    $att['isUploaded'] = 1;
                    DocumentAttachments::create($att);
                }
            }

            if ($result) {
                $updateRec['is_answered'] = 1;
                $result =  TenderBidClarifications::where('id', $id)
                    ->update($updateRec);
                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
}
