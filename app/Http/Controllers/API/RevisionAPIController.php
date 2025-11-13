<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateRevisionAPIRequest;
use App\Http\Requests\API\UpdateRevisionAPIRequest;
use App\Models\Revision;
use App\Repositories\RevisionRepository;
use App\Traits\AuditLogsTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\helper\Helper;
use App\Models\DepartmentBudgetPlanning;
use App\Models\CompanyDepartmentEmployee;
use App\Models\Employee;
use App\Services\ChartOfAccountService;
use App\Models\RevisionAttachment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

/**
 * Class RevisionAPIController
 * @package App\Http\Controllers\API
 */

class RevisionAPIController extends AppBaseController
{
    use AuditLogsTrait;

    /** @var  RevisionRepository */
    private $revisionRepository;
    
    /** @var  ChartOfAccountService */
    private $chartOfAccountService;

    public function __construct(RevisionRepository $revisionRepo, ChartOfAccountService $chartOfAccountService)
    {
        $this->revisionRepository = $revisionRepo;
        $this->chartOfAccountService = $chartOfAccountService;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/revisions",
     *      summary="Get a listing of the Revisions.",
     *      tags={"Revision"},
     *      description="Get all Revisions",
     *      produces={"application/json"},
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
     *                  @OA\Items(ref="#/definitions/Revision")
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
        $this->revisionRepository->pushCriteria(new RequestCriteria($request));
        $this->revisionRepository->pushCriteria(new LimitOffsetCriteria($request));
        $revisions = $this->revisionRepository->all();

        return $this->sendResponse($revisions->toArray(), 'Revisions retrieved successfully');
    }

    /**
     * @param CreateRevisionAPIRequest $request
     * @return Response
     *
     * @OA\Post(
     *      path="/revisions",
     *      summary="Store a newly created Revision in storage",
     *      tags={"Revision"},
     *      description="Store Revision",
     *      produces={"application/json"},
     *      @OA\Parameter(
     *          name="body",
     *          in="body",
     *          description="Revision that should be stored",
     *          required=false,
     *          @OA\Schema(ref="#/definitions/Revision")
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
     *                  ref="#/definitions/Revision"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateRevisionAPIRequest $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);

        // Format the date properly
        if (isset($input['submittedDate'])) {
            try {
                $input['submittedDate'] = Carbon::parse($input['submittedDate'])->format('Y-m-d');
            } catch (\Exception $e) {
                return $this->sendError('Invalid date format for submittedDate');
            }
        }

        $validator = \Validator::make($input, [
            'budgetPlanningId' => 'required|integer',
            'submittedBy' => 'required|string|max:255',
            'submittedDate' => 'required|date',
            'reviewComments' => 'required|string',
            'revisionType' => 'required|string|max:255',
            'reopenEditableSection' => 'boolean'
        ]);

        if ($validator->fails()) {
            return $this->sendAPIError('Validation Error.', 422, $validator->errors()->toArray());
        }

        // Check if budget planning exists
        $budgetPlanning = DepartmentBudgetPlanning::find($input['budgetPlanningId']);
        if (!$budgetPlanning) {
            return $this->sendError('Budget Planning not found');
        }

        // Set additional fields
        $input['revisionId'] = $this->generateRevisionId($input['budgetPlanningId']);
        $input['revisionStatus'] = 1; // Active
        $input['sentDateTime'] = Carbon::now();
        $input['created_by'] = Helper::getEmployeeSystemID();
        $input['created_at'] = Carbon::now();

        $revision = $this->revisionRepository->create($input);

        // Update budget planning status to "Sent Back for Revision"
        $budgetPlanning->update(['financeTeamStatus' => 3]);

        // Log the action
        $this->logAuditTrail('Revision', 'Created', $revision->id, 'Revision created for budget planning ID: ' . $input['budgetPlanningId']);

        return $this->sendResponse($revision->toArray(), 'Revision created successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/revisions/{id}",
     *      summary="Display the specified Revision",
     *      tags={"Revision"},
     *      description="Get Revision",
     *      produces={"application/json"},
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Revision",
     *          type="integer",
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
     *                  ref="#/definitions/Revision"
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
        /** @var Revision $revision */
        $revision = $this->revisionRepository->findWithoutFail($id);

        if (empty($revision)) {
            return $this->sendError('Revision not found');
        }

        return $this->sendResponse($revision->toArray(), 'Revision retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateRevisionAPIRequest $request
     * @return Response
     *
     * @OA\Put(
     *      path="/revisions/{id}",
     *      summary="Update the specified Revision in storage",
     *      tags={"Revision"},
     *      description="Update Revision",
     *      produces={"application/json"},
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Revision",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Parameter(
     *          name="body",
     *          in="body",
     *          description="Revision that should be updated",
     *          required=false,
     *          @OA\Schema(ref="#/definitions/Revision")
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
     *                  ref="#/definitions/Revision"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateRevisionAPIRequest $request)
    {
        $input = $request->all();

        /** @var Revision $revision */
        $revision = $this->revisionRepository->findWithoutFail($id);

        if (empty($revision)) {
            return $this->sendError('Revision not found');
        }

        $input['modified_by'] = Helper::getEmployeeSystemID();
        $input['modified_at'] = Carbon::now();

        $revision = $this->revisionRepository->update($input, $id);

        // Log the action
        $this->logAuditTrail('Revision', 'Updated', $revision->id, 'Revision updated');

        return $this->sendResponse($revision->toArray(), 'Revision updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/revisions/{id}",
     *      summary="Remove the specified Revision from storage",
     *      tags={"Revision"},
     *      description="Delete Revision",
     *      produces={"application/json"},
     *      @OA\Parameter(
     *          name="id",
     *          description="id of Revision",
     *          type="integer",
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
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var Revision $revision */
        $revision = $this->revisionRepository->findWithoutFail($id);

        if (empty($revision)) {
            return $this->sendError('Revision not found');
        }

        $revision->delete();

        // Log the action
        $this->logAuditTrail('Revision', 'Deleted', $id, 'Revision deleted');

        return $this->sendSuccess('Revision deleted successfully');
    }

    /**
     * Send back for revision
     *
     * @param Request $request
     * @return Response
     */
    public function sendBackForRevision(Request $request)
    {
        $input = $request->all();

        // Format the date properly
        if (isset($input['submittedDate'])) {
            try {
                $input['submittedDate'] = Carbon::parse($input['submittedDate'])->format('Y-m-d');
            } catch (\Exception $e) {
                return $this->sendError('Invalid date format for submittedDate');
            }
        }

        $validator = \Validator::make($input, [
            'budgetPlanningId' => 'required|integer',
            'submittedBy' => 'required|string|max:255',
            'submittedDate' => 'required|date',
            'reviewComments' => 'required|string',
            'revisionType' => 'required|string|max:255',
            'reopenEditableSection' => 'required|string|in:full_section,gl_section',
            'attachments' => 'nullable|array'
        ]);

        if ($validator->fails()) {
            return $this->sendAPIError('Validation Error.', 422, $validator->errors()->toArray());
        }

        try {
            // Check if budget planning exists
            $budgetPlanning = DepartmentBudgetPlanning::with('revisions')->find($input['budgetPlanningId']);
            
            if (!$budgetPlanning) {
                return $this->sendError('Budget Planning not found');
            }

            if($budgetPlanning->revisions->count() > 0){
                return $this->sendError('Budget Planning already has a revision');
            }

            if(!empty($input['selectedGlSections'])){
                $input['selectedGlSections'] = collect($input['selectedGlSections'])->pluck('id')->toArray();
            }

            // Prepare revision data
            $revisionData = [
                'budgetPlanningId' => $input['budgetPlanningId'],
                'submittedBy' => $input['submittedBy'],
                'submittedDate' => $input['submittedDate'],
                'reviewComments' => $input['reviewComments'],
                'revisionType' => $input['revisionType'],
                'reopenEditableSection' => $input['reopenEditableSection'],
                'selectedGlSections' => json_encode($input['selectedGlSections'] ?? []),
                'revisionId' => $this->generateRevisionId($input['budgetPlanningId']),
                'revisionStatus' => 1, // Active
                'sentDateTime' => Carbon::now(),
                'created_by' => Helper::getEmployeeSystemID(),
                'created_at' => Carbon::now()
            ];

            // Create revision record
            $revision = $this->revisionRepository->create($revisionData);

            // Update budget planning status to "Sent Back for Revision"
            $budgetPlanning->update(['financeTeamStatus' => 3]);

            // Handle attachments if provided
            if (isset($input['attachments']) && is_array($input['attachments']) && !empty($input['attachments'])) {
                $this->storeRevisionAttachments($revision->id, $input['attachments']);
            }

            // Log the action
            // $this->logAuditTrail('Revision', 'Created', $revision->id, 'Revision created for budget planning ID: ' . $input['budgetPlanningId']);

            return $this->sendResponse($revision->toArray(), 'Revision sent back successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error creating revision: ' . $e->getMessage());
        }
    }

    /**
     * Get revisions for a specific budget planning
     *
     * @param Request $request
     * @return Response
     */
    public function getRevisions(Request $request)
    {
        $input = $request->all();
        $validator = \Validator::make($input, [
            'budgetPlanningId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->sendAPIError('Validation Error.', 422, $validator->errors()->toArray());
        }

        $revisions = $this->revisionRepository->with(['createdBy', 'attachments.createdBy'])->findWhere([
            'budgetPlanningId' => $input['budgetPlanningId']
        ])->sortByDesc('created_at');

        return \DataTables::of($revisions)
        ->addIndexColumn()
        ->addColumn('reviewed_by', function($row) {
            return $row->createdBy ? $row->createdBy->empFullName : 'N/A';
        })
        ->addColumn('attachment_count', function($row) {
            return $row->attachments ? $row->attachments->count() : 0;
        })
        ->addColumn('attachments', function($row) {
            if ($row->attachments && $row->attachments->count() > 0) {
                return ($row->attachments->map(function($attachment) {
                    return [
                        'id' => $attachment->id,
                        'fileName' => $attachment->fileName,
                        'fileType' => $attachment->fileType,
                        'fileExtension' => $attachment->file_extension,
                        'filePath' => $attachment->filePath,
                    ];
                })->toArray());
            }
            return json_encode([]);
        })
        ->rawColumns(['attachments'])
        ->make(true);
    }

    /**
     * Get revision details with attachments
     *
     * @param Request $request
     * @return Response
     */
    public function getRevisionDetails(Request $request)
    {
        $input = $request->all();
        
        if (!isset($input['revisionId'])) {
            return $this->sendError('Revision ID is required');
        }

        $revision = $this->revisionRepository->with(['createdBy', 'attachments.createdBy'])->findWithoutFail($input['revisionId']);
        
        if (empty($revision)) {
            return $this->sendError('Revision not found');
        }

        // Format the revision data with attachments
        $revisionData = [
            'id' => $revision->id,
            'revisionId' => $revision->revisionId,
            'budgetPlanningId' => $revision->budgetPlanningId,
            'submittedBy' => $revision->submittedBy,
            'submittedDate' => $revision->submittedDate,
            'reviewComments' => $revision->reviewComments,
            'revisionType' => $revision->revisionType,
            'revisionTypeText' => $revision->revision_type_text,
            'reopenEditableSection' => $revision->reopenEditableSection,
            'selectedGlSections' => $revision->selectedGlSections,
            'revisionStatus' => $revision->revisionStatus,
            'revisionStatusText' => $revision->revision_status_text,
            'sentDateTime' => $revision->sentDateTime,
            'completionComments' => $revision->completionComments,
            'completedDateTime' => $revision->completedDateTime,
            'created_at' => $revision->created_at,
            'created_by' => $revision->createdBy ? [
                'id' => $revision->createdBy->employeeSystemID,
                'name' => $revision->createdBy->empFullName,
                'email' => $revision->createdBy->empEmail
            ] : null,
            'attachments' => $revision->attachments ? $revision->attachments->map(function($attachment) {
                return [
                    'id' => $attachment->id,
                    'fileName' => $attachment->fileName,
                    'filePath' => $attachment->filePath,
                    'fileType' => $attachment->fileType,
                    'fileSize' => $attachment->fileSize,
                    'fileSizeHuman' => $attachment->file_size_human,
                    'fileExtension' => $attachment->file_extension,
                    'isImage' => $attachment->is_image,
                    'isDocument' => $attachment->is_document,
                    'createdAt' => $attachment->created_at,
                    'createdBy' => $attachment->createdBy ? [
                        'id' => $attachment->createdBy->employeeSystemID,
                        'name' => $attachment->createdBy->empFullName
                    ] : null
                ];
            }) : []
        ];

        return $this->sendResponse($revisionData, 'Revision details retrieved successfully');
    }



    /**
     * Download revision attachment
     *
     * @param Request $request
     * @return Response
     */
    public function downloadRevisionAttachment(Request $request)
    {
        $input = $request->all();
        
        if (!isset($input['filePath']) || !isset($input['fileName'])) {
            return $this->sendError('File path and file name are required');
        }

        $filePath = urldecode($input['filePath']);
        $fileName = urldecode($input['fileName']);

        try {
            // Check if file exists
            if (!Storage::disk('local')->exists($filePath)) {
                return $this->sendError('File not found at path: ' . $filePath, 404);
            }

            // Get file contents
            $fileContents = Storage::disk('local')->get($filePath);
            
            // Get file MIME type
            $mimeType = Storage::disk('local')->mimeType($filePath);
            
            // Return file download response
            return response($fileContents, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"')
                ->header('Content-Length', strlen($fileContents));

        } catch (\Exception $e) {
            return $this->sendError('Error downloading file: ' . $e->getMessage(), 500);
        }
    }

    /**
     * View revision attachment
     *
     * @param Request $request
     * @return Response
     */
    public function viewRevisionAttachment(Request $request)
    {
        $input = $request->all();
        
        if (!isset($input['filePath'])) {
            return $this->sendError('File path is required');
        }

        $filePath = urldecode($input['filePath']);

        try {
            // Check if file exists
            if (!Storage::disk('local')->exists($filePath)) {
                return $this->sendError('File not found at path: ' . $filePath, 404);
            }

            // Get file contents
            $fileContents = Storage::disk('local')->get($filePath);
            
            // Get file MIME type
            $mimeType = Storage::disk('local')->mimeType($filePath);
            
            // Return file view response
            return response($fileContents, 200)
                ->header('Content-Type', $mimeType)
                ->header('Content-Disposition', 'inline');

        } catch (\Exception $e) {
            return $this->sendError('Error viewing file: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Complete a revision
     *
     * @param Request $request
     * @return Response
     */
    public function completeRevision(Request $request)
    {
        $input = $request->all();

        $validator = \Validator::make($input, [
            'revisionId' => 'required|integer',
            'completionComments' => 'string'
        ]);

        if ($validator->fails()) {
            return $this->sendAPIError('Validation Error.', 422, $validator->errors()->toArray());
        }

        /** @var Revision $revision */
        $revision = $this->revisionRepository->findWithoutFail($input['revisionId']);

        if (empty($revision)) {
            return $this->sendError('Revision not found');
        }

        // Update revision status to completed
        $revision->update([
            'revisionStatus' => 2, // Completed
            'completionComments' => $input['completionComments'] ?? null,
            'completedDateTime' => Carbon::now(),
            'modified_by' => Helper::getEmployeeSystemID(),
            'modified_at' => Carbon::now()
        ]);

        // Update budget planning status back to "Under Review"
        $budgetPlanning = DepartmentBudgetPlanning::find($revision->budgetPlanningId);
        if ($budgetPlanning) {
            $budgetPlanning->update(['financeTeamStatus' => 2]);
        }

        // Log the action
        $this->logAuditTrail('Revision', 'Completed', $revision->id, 'Revision completed');

        return $this->sendResponse($revision->toArray(), 'Revision completed successfully');
    }

    /**
     * Generate unique revision ID
     *
     * @param int $budgetPlanningId
     * @return string
     */
    private function generateRevisionId($budgetPlanningId)
    {
        // Get the department code from DepartmentBudgetPlanning
        $budgetPlanning = DepartmentBudgetPlanning::with('department')->find($budgetPlanningId);
        
        if (!$budgetPlanning || !$budgetPlanning->department) {
            throw new \Exception('Department not found for budget planning');
        }
        
        $departmentCode = $budgetPlanning->department->departmentCode;
        $prefix = $departmentCode . 'RV';
        
        // Get the last revision ID for this department
        $lastRevision = $this->revisionRepository->findWhere([
            ['revisionId', 'like', $prefix . '%']
        ])->sortByDesc('revisionId')->first();

        if ($lastRevision) {
            // Extract the number part from the last revision ID
            $lastNumber = (int) substr($lastRevision->revisionId, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Get revision GL codes
     *
     * @param Request $request
     * @return Response
     */
    public function getRevisionGL(Request $request)
    {
        $input = $request->all();

        

        $validator = \Validator::make($input, [
            'revisionId' => 'required|integer'
        ]);

        if ($validator->fails()) {
            return $this->sendAPIError('Validation Error.', 422, $validator->errors()->toArray());
        }

        try {
            // Find the revision
            $revision = $this->revisionRepository->findWithoutFail($input['revisionId']);

            if (empty($revision)) {
                return $this->sendError('Revision not found');
            }

            // Get the selected GL sections from the revision
            $selectedGlSections = json_decode($revision->selectedGlSections, true);

            if (empty($selectedGlSections) || !is_array($selectedGlSections)) {
                return $this->sendResponse(['data' => []], 'No GL codes assigned to this revision');
            }

            // Use the service to get chart of accounts based on workflow method
            $chartOfAccountSystemIDs = $this->chartOfAccountService->getChartOfAccountsByRevisionGlSections($selectedGlSections, $revision->budgetPlanningId);

            return $this->sendResponse(['data' => $chartOfAccountSystemIDs], 'Revision GL codes retrieved successfully');

        } catch (\Exception $e) {
            return $this->sendError('Error retrieving revision GL codes: ' . $e->getMessage());
        }
    }

    /**
     * Get parent category for GL code
     *
     * @param \App\Models\ChartOfAccount $glCode
     * @return string
     */
    private function getParentCategory($glCode)
    {
        // Determine parent category based on catogaryBLorPL and controlAccounts
        if ($glCode->catogaryBLorPL === 'BS') {
            if ($glCode->controlAccounts) {
                return $glCode->controlAccounts; // BSA, BSL, BSE
            }
            return 'BS'; // General Balance Sheet
        } else if ($glCode->catogaryBLorPL === 'PL') {
            if ($glCode->controlAccounts) {
                return $glCode->controlAccounts; // PLI, PLE
            }
            return 'PL'; // General Profit & Loss
        }
        return $glCode->catogaryBLorPL || 'Unknown';
    }

    /**
     * Store revision attachments with validation and file handling
     *
     * @param int $revisionId
     * @param array $attachments
     * @return void
     * @throws \Exception
     */
    private function storeRevisionAttachments($revisionId, $attachments)
    {
        foreach ($attachments as $attachment) {
            // Validate attachment data
            $this->validateRevisionAttachment($attachment);
            
            // Process and store the attachment
            $this->processRevisionAttachment($revisionId, $attachment);
        }
    }

    /**
     * Validate revision attachment data
     *
     * @param array $attachment
     * @throws \Exception
     */
    private function validateRevisionAttachment($attachment)
    {
        $messages = [
            'fileName.required' => 'File name is required',
            'fileName.string' => 'File name must be a string',
            'fileType.required' => 'File type is required',
            'fileType.string' => 'File type must be a string',
            'fileSize.required' => 'File size is required',
            'fileSize.integer' => 'File size must be an integer',
            'fileSize.max' => 'File size exceeds maximum allowed size',
            'fileData.required' => 'File content is required',
            'fileData.string' => 'File content must be a string',
        ];

        $validator = \Validator::make($attachment, [
            'fileName' => 'required|string',
            'fileType' => 'required|string',
            'fileSize' => 'required|integer|max:' . env('ATTACH_UPLOAD_SIZE_LIMIT', 10485760),
            'fileData' => 'required|string'
        ], $messages);

        if ($validator->fails()) {
            throw new \Exception('Attachment validation failed: ' . implode(', ', $validator->errors()->all()));
        }

        // Check file extension
        $extension = pathinfo($attachment['fileName'], PATHINFO_EXTENSION);
        if (empty($extension) && isset($attachment['fileType'])) {
            $extension = str_replace(['image/', 'application/', 'text/'], '', $attachment['fileType']);
        }

        $blockedExtensions = [
            'ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
            'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
            'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
            'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
            'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
            'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
            'xbap', 'xnk', 'php'
        ];

        if (in_array(strtolower($extension), $blockedExtensions)) {
            throw new \Exception("This file type is not allowed: {$extension}");
        }
    }

    /**
     * Process and store revision attachment
     *
     * @param int $revisionId
     * @param array $attachment
     * @throws \Exception
     */
    private function processRevisionAttachment($revisionId, $attachment)
    {
        try {
            return DB::transaction(function () use ($revisionId, $attachment) {
                $extension = pathinfo($attachment['fileName'], PATHINFO_EXTENSION);
                if (empty($extension) && isset($attachment['fileType'])) {
                    $extension = str_replace(['image/', 'application/', 'text/'], '', $attachment['fileType']);
                }

                // Decode file content
                $fileData = $attachment['fileData'];
                if (strpos($fileData, 'data:') === 0) {
                    $fileData = substr($fileData, strpos($fileData, ',') + 1);
                }

                $decodedFile = base64_decode($fileData);
                if ($decodedFile === false) {
                    throw new \Exception("Invalid file data provided");
                }

                // Create attachment record
                $attachmentRecord = RevisionAttachment::create([
                    'revisionId' => $revisionId,
                    'fileName' => $attachment['fileName'],
                    'fileType' => $attachment['fileType'],
                    'fileSize' => $attachment['fileSize'],
                    'fileData' => "",
                    'created_by' => Helper::getEmployeeSystemID(),
                    'created_at' => Carbon::now()
                ]);

                // Generate file path and store file
                $fileName = 'REVISION_' . $revisionId . '_' . $attachmentRecord->id . '.' . $extension;
                $filePath = 'REVISIONS/' . $revisionId . '/' . $fileName;

                // Ensure directory exists
                $directory = 'REVISIONS/' . $revisionId;
                if (!Storage::disk('local')->exists($directory)) {
                    Storage::disk('local')->makeDirectory($directory);
                }

                // Store file to storage
                Storage::disk('local')->put($filePath, $decodedFile);

                // Update attachment record with file path
                $attachmentRecord->update([
                    'filePath' => $filePath
                ]);

                return $attachmentRecord;
            });
        } catch (\Exception $exception) {
            throw new \Exception('Error processing attachment: ' . $exception->getMessage());
        }
    }

}
