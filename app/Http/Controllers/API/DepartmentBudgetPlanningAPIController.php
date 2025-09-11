<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateDepartmentBudgetPlanningAPIRequest;
use App\Http\Requests\API\UpdateDepartmentBudgetPlanningAPIRequest;
use App\Jobs\ProcessDepartmentBudgetPlanningDetailsJob;
use App\Models\DepartmentBudgetPlanning;
use App\Models\DeptBudgetPlanningTimeRequest;
use App\Repositories\DepartmentBudgetPlanningRepository;
use App\Rules\NoEmoji;
use App\Traits\AuditLogsTrait;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\Helper;
use App\Models\CompanyBudgetPlanning;
use App\Models\DepartmentBudgetTemplate;
use App\Models\DepBudgetTemplateGl;

/**
 * Class DepartmentBudgetPlanningController
 * @package App\Http\Controllers\API
 */

class DepartmentBudgetPlanningAPIController extends AppBaseController
{
    use AuditLogsTrait;

    /** @var  DepartmentBudgetPlanningRepository */
    private $departmentBudgetPlanningRepository;

    public function __construct(DepartmentBudgetPlanningRepository $departmentBudgetPlanningRepo)
    {
        $this->departmentBudgetPlanningRepository = $departmentBudgetPlanningRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/departmentBudgetPlannings",
     *      summary="getDepartmentBudgetPlanningList",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Get all DepartmentBudgetPlannings",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/DepartmentBudgetPlanning")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->departmentBudgetPlanningRepository->pushCriteria(new RequestCriteria($request));
        $this->departmentBudgetPlanningRepository->pushCriteria(new LimitOffsetCriteria($request));
        $departmentBudgetPlannings = $this->departmentBudgetPlanningRepository->all();

        return $this->sendResponse($departmentBudgetPlannings->toArray(), 'Department Budget Plannings retrieved successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/departmentBudgetPlannings",
     *      summary="createDepartmentBudgetPlanning",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Create DepartmentBudgetPlanning",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DepartmentBudgetPlanning"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateDepartmentBudgetPlanningAPIRequest $request)
    {
        $input = $request->all();

        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->create($input);

        return $this->sendResponse($departmentBudgetPlanning->toArray(), 'Department Budget Planning saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/departmentBudgetPlannings/{id}",
     *      summary="getDepartmentBudgetPlanningItem",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Get DepartmentBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentBudgetPlanning",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DepartmentBudgetPlanning"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var DepartmentBudgetPlanning $departmentBudgetPlanning */
        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->with(['masterBudgetPlannings.workflow', 'department','delegateAccess'])->findWithoutFail($id);

        if (empty($departmentBudgetPlanning)) {
            return $this->sendError('Department Budget Planning not found');
        }

        return $this->sendResponse($departmentBudgetPlanning->toArray(), 'Department Budget Planning retrieved successfully');
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/departmentBudgetPlannings/{id}",
     *      summary="updateDepartmentBudgetPlanning",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Update DepartmentBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentBudgetPlanning",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/DepartmentBudgetPlanning"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateDepartmentBudgetPlanningAPIRequest $request)
    {
        $input = $request->all();

        /** @var DepartmentBudgetPlanning $departmentBudgetPlanning */
        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->findWithoutFail($id);

        if (empty($departmentBudgetPlanning)) {
            return $this->sendError('Department Budget Planning not found');
        }

        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->update($input, $id);

        return $this->sendResponse($departmentBudgetPlanning->toArray(), 'DepartmentBudgetPlanning updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/departmentBudgetPlannings/{id}",
     *      summary="deleteDepartmentBudgetPlanning",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Delete DepartmentBudgetPlanning",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of DepartmentBudgetPlanning",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var DepartmentBudgetPlanning $departmentBudgetPlanning */
        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->findWithoutFail($id);

        if (empty($departmentBudgetPlanning)) {
            return $this->sendError('Department Budget Planning not found');
        }

        $departmentBudgetPlanning->delete();

        return $this->sendSuccess('Department Budget Planning deleted successfully');
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/updateBudgetPlanningStatus",
     *      summary="updateBudgetPlanningStatus",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Update Department Budget Planning Status",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={"budgetPlanningId", "status"},
     *                @OA\Property(
     *                    property="budgetPlanningId",
     *                    description="Budget Planning ID",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="status",
     *                    description="New Status",
     *                    type="integer"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function updateStatus(Request $request)
    {
        $input = $request->all();

        // Validate required fields
        if (!isset($input['budgetPlanningId']) || !isset($input['workStatus'])) {
            return $this->sendError('Budget Planning ID and Status are required');
        }

        /** @var DepartmentBudgetPlanning $departmentBudgetPlanning */
        $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->findWithoutFail($input['budgetPlanningId']);

        if (empty($departmentBudgetPlanning)) {
            return $this->sendError('Department Budget Planning not found');
        }

        //prevent status change if new status is 0
        if ($input['workStatus'] == 1) {
            return $this->sendError('Status cannot be changed to Not Started');
        }

        if (($input['workStatus'] == 3) && ($departmentBudgetPlanning->workStatus == 1)) {
            return $this->sendError('Status cannot be changed to Submitted');
        }

        try {
            \DB::beginTransaction();

            // Load department budget planning with relationships for validation
            $departmentBudgetPlanning = \App\Models\DepartmentBudgetPlanning::with([
                'masterBudgetPlannings.company',
                'department'
            ])->find($input['budgetPlanningId']);

            if (!$departmentBudgetPlanning) {
                return $this->sendError('Department Budget Planning not found');
            }

            // Validate budget template assignment before processing
            $validationResult = $this->validateBudgetTemplateAssignment($departmentBudgetPlanning);
            if (!$validationResult['valid']) {
                return $this->sendError($validationResult['message'], 400);
            }

            $oldValue = $departmentBudgetPlanning->toArray();

            // Update only the status field
            $updateData = ['workStatus' => $input['workStatus']];
            $departmentBudgetPlanning = $this->departmentBudgetPlanningRepository->update($updateData, $input['budgetPlanningId']);

            if ($input['workStatus'] == 2) {

                // Get database from request (added by TenantEnforce middleware)
                $db = $request->input('db', '');

                // Dispatch job to process department budget planning details
                \App\Jobs\ProcessDepartmentBudgetPlanningDetailsJob::dispatch(
                    $db,
                    $departmentBudgetPlanning->id,
                    auth()->id()
                );
            }

            // Add audit log
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog(
                $db,
                $input['budgetPlanningId'],
                $uuid,
                "department_budget_plannings",
                "Department Budget Planning ".$departmentBudgetPlanning->planningCode." has been updated",
                "U",
                $departmentBudgetPlanning->toArray(),
                $oldValue,
                0
            );

            \DB::commit();

            return $this->sendResponse($departmentBudgetPlanning->toArray(), 'Department Budget Planning status updated successfully and details processing initiated');
        } catch (\Exception $e) {
            \DB::rollback();
            return $this->sendError('Error updating status - ' . $e->getMessage(), 500);
        }
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/createTimeExtensionRequest",
     *      summary="createTimeExtensionRequest",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Create Time Extension Request",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/json",
     *            @OA\Schema(
     *                type="object",
     *                required={"budgetPlanningId", "requestCode", "currentSubmissionDate", "dateOfRequest", "reasonForExtension"},
     *                @OA\Property(
     *                    property="budgetPlanningId",
     *                    description="Budget Planning ID",
     *                    type="integer"
     *                ),
     *                @OA\Property(
     *                    property="requestCode",
     *                    description="Request Code",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="currentSubmissionDate",
     *                    description="Current Submission Date",
     *                    type="string",
     *                    format="date"
     *                ),
     *                @OA\Property(
     *                    property="dateOfRequest",
     *                    description="Date of Request",
     *                    type="string",
     *                    format="date"
     *                ),
     *                @OA\Property(
     *                    property="reasonForExtension",
     *                    description="Reason for Extension",
     *                    type="string"
     *                ),
     *                @OA\Property(
     *                    property="attachments",
     *                    description="Attachments",
     *                    type="array",
     *                    @OA\Items(
     *                        type="object",
     *                        @OA\Property(property="fileName", type="string"),
     *                        @OA\Property(property="originalFileName", type="string"),
     *                        @OA\Property(property="fileSize", type="integer"),
     *                        @OA\Property(property="fileType", type="string"),
     *                        @OA\Property(property="fileData", type="string", description="Base64 encoded file data")
     *                    )
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function createTimeExtensionRequest(Request $request)
    {
        $input = $request->all();

        // Validate required fields
        $validator = \Validator::make($input, [
            'budgetPlanningId' => 'required|integer|exists:department_budget_plannings,id',
            'requestCode' => ['required', 'string', 'max:20', new \App\Rules\UniqueRequestCodePerBudgetPlanning($input['budgetPlanningId'])],
            'currentSubmissionDate' => 'required|date_format:d/m/Y',
            'dateOfRequest' => 'required|date_format:d/m/Y|after:currentSubmissionDate',
            'reasonForExtension' => ['required', 'string', new NoEmoji()],
            'attachments' => 'nullable|array',
            'attachments.*.fileName' => 'required_with:attachments|string',
            'attachments.*.originalFileName' => 'required_with:attachments|string',
            'attachments.*.fileSize' => 'required_with:attachments|integer|max:10485760', // 10MB max
            'attachments.*.fileType' => 'required_with:attachments|string',
            'attachments.*.fileData' => 'required_with:attachments|string'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->errors(), 422);
        }

        try {
            \DB::beginTransaction();

            // Prepare the data array with proper date formatting
            // Frontend sends dates in MM/dd/yyyy format from transformDate method
            try {
                $currentSubmissionDate = \Carbon\Carbon::createFromFormat('d/m/Y', $input['currentSubmissionDate'])->format('Y-m-d');
                $dateOfRequest = \Carbon\Carbon::createFromFormat('d/m/Y', $input['dateOfRequest'])->format('Y-m-d');

            } catch (\Exception $dateError) {
                // Fallback to generic parse if the format doesn't match
                try {
                    $currentSubmissionDate = \Carbon\Carbon::parse($input['currentSubmissionDate'])->format('Y-m-d');
                    $dateOfRequest = \Carbon\Carbon::parse($input['dateOfRequest'])->format('Y-m-d');
                } catch (\Exception $secondError) {
                    return $this->sendError('Date parsing error', 'Invalid date format - Expected dd/MM/yyyy: ' . $secondError->getMessage() . ' | Original: ' . $dateError->getMessage());
                }
            }

            //validate date of request is should greater than current date and current submission date
            if ($dateOfRequest <= \Carbon\Carbon::now()->format('Y-m-d')) {
                return $this->sendError('Date of request should be greater than current date');
            } else if ($dateOfRequest <= $currentSubmissionDate) {
                return $this->sendError('Date of request should be greater than current submission date');
            }

            $requestData = [
                'department_budget_planning_id' => $input['budgetPlanningId'],
                'request_code' => $input['requestCode'],
                'current_submission_date' => $currentSubmissionDate,
                'date_of_request' => $dateOfRequest,
                'reason_for_extension' => $input['reasonForExtension'],
                'status' => 1, // 1 = Time requested
                'created_by' => auth()->id() ?: 1, // Fallback to user ID 1 if no auth
                'updated_by' => auth()->id() ?: 1 // Fallback to user ID 1 if no auth
            ];

            // Create time extension request using Eloquent model
            $timeRequest = \App\Models\DeptBudgetPlanningTimeRequest::create($requestData);

            // Handle base64 file attachments
            if (isset($input['attachments']) && is_array($input['attachments'])) {
                foreach ($input['attachments'] as $attachment) {
                    // Get file extension from file type or original file name
                    $extension = pathinfo($attachment['originalFileName'], PATHINFO_EXTENSION);
                    if (empty($extension) && isset($attachment['fileType'])) {
                        $extension = str_replace(['image/', 'application/', 'text/'], '', $attachment['fileType']);
                    }

                    // Validate file extension using same blocked extensions as DocumentAttachmentsAPIController
                    $blockExtensions = [
                        'ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
                        'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
                        'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
                        'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
                        'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
                        'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
                        'xbap', 'xnk', 'php'
                    ];

                    if (in_array(strtolower($extension), $blockExtensions)) {
                        return $this->sendError('This type of file not allow to upload: ' . $extension, 500);
                    }

                    // Validate file size using same limit as DocumentAttachmentsAPIController
                    if (isset($attachment['fileSize'])) {
                        if ($attachment['fileSize'] > env('ATTACH_UPLOAD_SIZE_LIMIT', 10485760)) { // 10MB default
                            return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than " . \Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
                        }
                    }

                    // Decode base64 file data
                    $fileData = $attachment['fileData'];

                    // Remove the data URL prefix (e.g., "data:image/png;base64,")
                    if (strpos($fileData, 'data:') === 0) {
                        $fileData = substr($fileData, strpos($fileData, ',') + 1);
                    }

                    $decodedFile = base64_decode($fileData);

                    if ($decodedFile === false) {
                        continue; // Skip invalid base64 data
                    }

                    // Create attachment record first to get ID for file naming
                    $attachmentRecord = \App\Models\DeptBudgetPlanningTimeRequestAttachment::create([
                        'time_request_id' => $timeRequest->id,
                        'original_file_name' => $attachment['originalFileName'],
                        'file_type' => $attachment['fileType'],
                        'file_size' => $attachment['fileSize'],
                        'uploaded_by' => auth()->id() ?: 1
                    ]);

                    // Generate file name following DocumentAttachmentsAPIController pattern
                    $fileName = 'TIME_EXT_' . $timeRequest->id . '_' . $attachmentRecord->id . '.' . $extension;

                    // Get company information for file path
                    $budgetPlanning = \App\Models\DepartmentBudgetPlanning::with(['masterBudgetPlannings.company'])->find($input['budgetPlanningId']);
                    $companySystemID = $budgetPlanning->masterBudgetPlannings->companySystemID ?? 1;
                    $companyId = $budgetPlanning->masterBudgetPlannings->company->CompanyID ?? 'DEFAULT';

                    $planningCode = $budgetPlanning->masterBudgetPlannings->planningCode ?? 'DEFAULT';

                    // Use Helper::checkPolicy and Helper::policyWiseDisk like DocumentAttachmentsAPIController
                    if (\Helper::checkPolicy($companySystemID, 50)) {
                        $filePath = $companyId . '/G_ERP/TIME_EXT/' . $planningCode . '/' . $timeRequest->request_code . '/' . $fileName;
                    } else {
                        $filePath = 'TIME_EXT/' . $planningCode . '/' . $timeRequest->request_code . '/' . $fileName;
                    }

                    // Store the file using policy-wise disk (S3 or local based on company policy)
                    $disk = \Helper::policyWiseDisk($companySystemID, 'public');
                     \Storage::disk($disk)->put($filePath, $decodedFile);

                    // Update attachment record with file details
                    $attachmentRecord->update([
                        'file_name' => $fileName,
                        'file_path' => $filePath
                    ]);
                }
            }


            // Add audit log
            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog(
                $db,
                $input['budgetPlanningId'],
                $uuid,
                "department_budget_plannings",
                "Time extension request ".$timeRequest->request_code." has been created",
                "C",
                $timeRequest->toArray(),
                [],
                1
            );

            \DB::commit();

            return $this->sendResponse(['id' => $timeRequest->id], 'Time extension request created successfully');

        } catch (\Exception $e) {
            \DB::rollback();
            return $this->sendError('Error creating time extension request - '.$e->getMessage());
        }
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getTimeExtensionRequests(Request $request)
    {
        $input = $request->all();

        $budgetPlanningId = $request->input('budgetPlanningId');

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }


        $query = \App\Models\DeptBudgetPlanningTimeRequest::with(['creator'])
            ->forBudgetPlanning($budgetPlanningId)
            ->select([
                'id',
                'request_code',
                'current_submission_date',
                'date_of_request',
                'reason_for_extension',
                'status',
                'review_comments',
                'reviewed_at',
                'new_time',
                'reviewed_by',
                'created_at',
                'created_by'
            ])
            ->selectRaw('(SELECT COUNT(*) FROM dept_budget_planning_time_request_attachments WHERE dept_budget_planning_time_request_attachments.time_request_id = dept_budget_planning_time_requests.id) as attachments_count')
            ->orderBy('id', $sort);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->where(function ($query) use ($search) {
                $query->where('request_code', 'LIKE', "%{$search}%")
                    ->orWhere('reason_for_extension', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('current_submission_date', function ($row) {
                return $row->current_submission_date ? $row->current_submission_date->format('d/m/Y') : '';
            })
            ->editColumn('date_of_request', function ($row) {
                return $row->date_of_request ? $row->date_of_request->format('d/m/Y') : '';
            })
            ->addColumn('created_by_name', function ($row) {
                return $row->creator ? $row->creator->name : 'Unknown';
            })
            ->addColumn('attachment_count', function ($row) {
                return $row->attachments_count;
            })
            ->make(true);
    }

    /**
     * @param int $budgetPlanningId
     * @return Response
     *
     * @OA\Get(
     *      path="/generateTimeExtensionRequestCode/{budgetPlanningId}",
     *      summary="generateTimeExtensionRequestCode",
     *      tags={"DepartmentBudgetPlanning"},
     *      description="Generate Time Extension Request Code",
     *      @OA\Parameter(
     *          name="budgetPlanningId",
     *          description="Budget Planning ID",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="object",
     *                  @OA\Property(
     *                      property="requestCode",
     *                      type="string"
     *                  )
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function generateTimeExtensionRequestCode(Request $request)
    {

        $budgetPlanningId = $request->input('id');
        // Check if budget planning exists using Eloquent model
        $budgetPlanning = \App\Models\DepartmentBudgetPlanning::find($budgetPlanningId);

        $companySystemID = $request->input('companySystemID');

        // Handle case where companySystemID might be an array
        if (is_array($companySystemID)) {
            $companySystemID = $companySystemID[0] ?? null;
        }

        if (!$companySystemID) {
            return $this->sendError('Company not found');
        }


        if (!$budgetPlanning) {
            return $this->sendError('Department Budget Planning not found');
        }

        // Get the last request code for this budget planning using Eloquent model
        $lastRequest = \App\Models\DeptBudgetPlanningTimeRequest::forBudgetPlanning($budgetPlanningId)->whereHas('departmentBudgetPlanning.masterBudgetPlannings', function($query) use ($companySystemID) {
                $query->where('companySystemID', $companySystemID);
            })
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = 1;

        if ($lastRequest) {
            // Extract the number from the last request code (e.g., "RQ000001" -> 1)
            $lastCode = $lastRequest->request_code;
            if (preg_match('/RQ(\d+)/', $lastCode, $matches)) {
                $nextNumber = intval($matches[1]) + 1;
            }
        }

        // Generate the new request code
        $requestCode = 'RQ' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        return $this->sendResponse(['requestCode' => $requestCode], 'Request code generated successfully');
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function getReversions(Request $request)
    {
        $budgetPlanningId = $request->input('budgetPlanningId');

        $query = [];

        return \DataTables::of($query)
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Get time extension request attachments
     */
    public function getTimeExtensionRequestAttachments($timeRequestId)
    {
        try {
            $attachments = \App\Models\DeptBudgetPlanningTimeRequestAttachment::where('time_request_id', $timeRequestId)
                ->with(['uploader'])
                ->select([
                    'id',
                    'original_file_name',
                    'file_name',
                    'file_path',
                    'file_type',
                    'file_size',
                    'uploaded_by',
                    'created_at'
                ])
                ->orderBy('created_at', 'desc')
                ->get();

            // Add uploader name
            foreach ($attachments as $attachment) {
                $attachment->uploaded_by_name = $attachment->uploader ? $attachment->uploader->name : 'Unknown';
            }

            return $this->sendResponse($attachments, 'Attachments retrieved successfully');
        } catch (\Exception $e) {
            return $this->sendError('Error retrieving attachments - ' . $e->getMessage(), 500);
        }
    }

    /**
     * Download time extension request attachment
     */
    public function downloadTimeExtensionAttachment(Request $request)
    {
        try {
            $attachmentId = $request->input('id');
            $attachment = \App\Models\DeptBudgetPlanningTimeRequestAttachment::find($attachmentId);

            if (!$attachment) {
                return $this->sendError('Attachment not found', 404);
            }

            // Get company info for determining disk
            $timeRequest = \App\Models\DeptBudgetPlanningTimeRequest::with(['departmentBudgetPlanning.masterBudgetPlannings.company'])
                ->find($attachment->time_request_id);

            if (!$timeRequest) {
                return $this->sendError('Time request not found');
            }

            $companySystemID = $timeRequest->departmentBudgetPlanning->masterBudgetPlannings->companySystemID ?? 1;

            // Determine disk based on company policy
            $disk = \Helper::policyWiseDisk($companySystemID, 'public');

            // Check if file exists
            if (!\Storage::disk($disk)->exists($attachment->file_path)) {
                return $this->sendError('File not found on storage');
            }

            // Get file contents
            $fileContents = \Storage::disk($disk)->get($attachment->file_path);

            // Return file as response
            return response($fileContents, 200)
                ->header('Content-Type', $attachment->file_type)
                ->header('Content-Disposition', 'attachment; filename="' . $attachment->original_file_name . '"');

        } catch (\Exception $e) {
            return $this->sendError('Error downloading attachment - ' . $e->getMessage(), 500);
        }
    }

    /**
     * Validate budget template assignment for department
     *
     * @param \App\Models\DepartmentBudgetPlanning $departmentBudgetPlanning
     * @return array
     */
    private function validateBudgetTemplateAssignment($departmentBudgetPlanning)
    {
        try {
            // Get company budget planning type
            $companyBudgetPlanning = CompanyBudgetPlanning::find($departmentBudgetPlanning->masterBudgetPlannings->id);
            if (!$companyBudgetPlanning) {
                return [
                    'valid' => false,
                    'message' => 'Company Budget Planning not found'
                ];
            }

            $budgetType = $companyBudgetPlanning->typeID;

            // Check if department has budget template assigned and active
            $departmentBudgetTemplate = DepartmentBudgetTemplate::where('departmentSystemID', $departmentBudgetPlanning->departmentID)
                ->whereHas('budgetTemplate', function ($query) use ($budgetType) {
                    $query->where('type', $budgetType);
                })
                ->where('isActive', 1)
                ->first();

            if (!$departmentBudgetTemplate) {
                return [
                    'valid' => false,
                    'message' => 'No active budget template found for this department and budget type. Please assign a budget template before updating status.'
                ];
            }

            // Check if GL accounts are assigned to this template
            $budgetTemplateGls = DepBudgetTemplateGl::where('departmentBudgetTemplateID', $departmentBudgetTemplate->departmentBudgetTemplateID)->count();

            if ($budgetTemplateGls == 0) {
                return [
                    'valid' => false,
                    'message' => 'No GL accounts are assigned to the budget template. Please assign GL accounts before updating status.'
                ];
            }

            return [
                'valid' => true,
                'message' => 'Validation successful',
                'template' => $departmentBudgetTemplate,
                'gl_count' => $budgetTemplateGls
            ];

        } catch (\Exception $e) {
            return [
                'valid' => false,
                'message' => 'Error during validation: ' . $e->getMessage()
            ];
        }
    }

    public function cancelDepartmentTimeExtensionRequests(Request $request) {
        $input = $request->all();

        $timeExtensionRequest = DeptBudgetPlanningTimeRequest::find($input['id']);
        if (!$timeExtensionRequest) {
            return $this->sendError('Time extension request not found');
        }

        $oldValue = $timeExtensionRequest->toArray();

        $timeExtensionRequest->status = 4;
        $timeExtensionRequest->save();

        // Add audit log
        $uuid = $request->get('tenant_uuid', 'local');
        $db = $request->get('db', '');
        $this->auditLog(
            $db,
            $timeExtensionRequest->department_budget_planning_id,
            $uuid,
            "department_budget_plannings",
            "Time extension request ".$timeExtensionRequest->request_code." has been updated",
            "U",
            $timeExtensionRequest->refresh()->toArray(),
            $oldValue,
            1
        );

        return $this->sendResponse(null,'Time extension request cancelled successfully');
    }
}
