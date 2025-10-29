<?php
/**
 * =============================================
 * -- File Name : AuditTrailAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Audit Trail
 * -- Author : Mohamed Fayas
 * -- Create date : 22 - October 2018
 * -- Description : This file contains the all CRUD for  Audit Trail
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAuditTrailAPIRequest;
use App\Http\Requests\API\UpdateAuditTrailAPIRequest;
use App\Models\AuditTrail;
use App\Models\Tenant;
use App\Repositories\AuditTrailRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Services\LokiService;
use App\Jobs\AuditLog\MigrateAuditLogsJob;
use DataTables;
use App\helper\CommonJobService;
/**
 * Class AuditTrailController
 * @package App\Http\Controllers\API
 */

class AuditTrailAPIController extends AppBaseController
{
    /** @var  AuditTrailRepository */
    private $auditTrailRepository;
    private $lokiService;

    public function __construct(AuditTrailRepository $auditTrailRepo, LokiService $lokiService)
    {
        $this->auditTrailRepository = $auditTrailRepo;
        $this->lokiService = $lokiService;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/auditTrails",
     *      summary="Get a listing of the AuditTrails.",
     *      tags={"AuditTrail"},
     *      description="Get all AuditTrails",
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
     *                  @SWG\Items(ref="#/definitions/AuditTrail")
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
        $this->auditTrailRepository->pushCriteria(new RequestCriteria($request));
        $this->auditTrailRepository->pushCriteria(new LimitOffsetCriteria($request));
        $auditTrails = $this->auditTrailRepository->all();

        return $this->sendResponse($auditTrails->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.audit_trails')]));
    }

    /**
     * @param CreateAuditTrailAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/auditTrails",
     *      summary="Store a newly created AuditTrail in storage",
     *      tags={"AuditTrail"},
     *      description="Store AuditTrail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AuditTrail that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AuditTrail")
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
     *                  ref="#/definitions/AuditTrail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateAuditTrailAPIRequest $request)
    {
        $input = $request->all();

        $auditTrails = $this->auditTrailRepository->create($input);

        return $this->sendResponse($auditTrails->toArray(), trans('custom.save', ['attribute' => trans('custom.audit_trails')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/auditTrails/{id}",
     *      summary="Display the specified AuditTrail",
     *      tags={"AuditTrail"},
     *      description="Get AuditTrail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AuditTrail",
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
     *                  ref="#/definitions/AuditTrail"
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
        /** @var AuditTrail $auditTrail */
        $auditTrail = $this->auditTrailRepository->findWithoutFail($id);

        if (empty($auditTrail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.audit_trails')]));
        }

        return $this->sendResponse($auditTrail->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.audit_trails')]));
    }

    /**
     * @param int $id
     * @param UpdateAuditTrailAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/auditTrails/{id}",
     *      summary="Update the specified AuditTrail in storage",
     *      tags={"AuditTrail"},
     *      description="Update AuditTrail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AuditTrail",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="AuditTrail that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/AuditTrail")
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
     *                  ref="#/definitions/AuditTrail"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateAuditTrailAPIRequest $request)
    {
        $input = $request->all();

        /** @var AuditTrail $auditTrail */
        $auditTrail = $this->auditTrailRepository->findWithoutFail($id);

        if (empty($auditTrail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.audit_trails')]));
        }

        $auditTrail = $this->auditTrailRepository->update($input, $id);

        return $this->sendResponse($auditTrail->toArray(), trans('custom.update', ['attribute' => trans('custom.audit_trails')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/auditTrails/{id}",
     *      summary="Remove the specified AuditTrail from storage",
     *      tags={"AuditTrail"},
     *      description="Delete AuditTrail",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of AuditTrail",
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
        /** @var AuditTrail $auditTrail */
        $auditTrail = $this->auditTrailRepository->findWithoutFail($id);

        if (empty($auditTrail)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.audit_trails')]));
        }

        $auditTrail->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.audit_trails')]));
    }

    public function auditLogs(Request $request){

        $input = $request->all();
        try {
            $env = env("LOKI_ENV");

            $fromDate = Carbon::parse(env("LOKI_START_DATE"));
            $toDate = Carbon::now();
            $diff = $toDate->diffInDays($fromDate);
            $id = $input['id'];
            $module = $input['module'];

            $table = $this->lokiService->getAuditTables($module);
            $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid']: 'local';

            // Optimize query using labels: env, channel, tenant (no table label to avoid high cardinality)
            $params = 'query?query=rate({env="'.$env.'",channel="audit",tenant="'.$uuid.'"} | json | transaction_id="'.$id.'" | table="'.$table.'" ['.$diff.'d])';

            $data = $this->lokiService->getAuditLogs($params);

            // Check if $data is an error response
            if (is_object($data) && method_exists($data, 'getStatusCode')) {
                return $data;
            }


            $params2 = 'query?query=rate({env="'.$env.'",channel="audit",tenant="'.$uuid.'"} | json | parent_id="'.$id.'" | parent_table="'.$table.'" ['.$diff.'d])';

            $data2 = $this->lokiService->getAuditLogs($params2);

            // Check if $data2 is an error response
            if (is_object($data2) && method_exists($data2, 'getStatusCode')) {
                return $data2;
            }

            $formatedData = [];

            foreach ($data as $key => $value) {
                if (isset($value['metric'])) {
                    $lineData = $value['metric'];

                    $lineData['data'] = isset($value['metric']['data']) ? json_decode($value['metric']['data']) : [];

                    $formatedData[] = $lineData;
                }
            }

            foreach ($data2 as $key => $value) {
                if (isset($value['metric']['data'])) {
                    $lineData = $value['metric'];

                    $lineData['data'] = isset($value['metric']['data']) ? json_decode($value['metric']['data']) : [];

                    $formatedData[] = $lineData;
                }
            }

            $formatedData = collect($formatedData)->sortByDesc('date_time');

            return DataTables::of($formatedData)
                ->addIndexColumn()
                ->make(true);
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Get user audit logs (login, logout, session events)
     * Fetches logs where logType = 'user_audit' from Loki
     *
     * @param Request $request
     * @return Response
     */
    public function userAuditLogs(Request $request){
        $input = $request->all();
        try {
            $env = env("LOKI_ENV");
            
            $fromDate = Carbon::parse(env("LOKI_START_DATE"));
            $toDate = Carbon::now();
            $diff = $toDate->diffInDays($fromDate);
            $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid']: 'local';
            
            // Get current locale and determine filter language
            $locale = app()->getLocale() ?: 'en';
            $langFilter = $locale === 'ar' ? 'ar' : 'en';

            // Build the Loki query
            $query = 'rate({env="'.$env.'",channel="auth",tenant="'.$uuid.'"} | json | locale="'.$langFilter.'"';
            
            // Add event filter if event is specified
            if (isset($input['event']) && $input['event'] != null && $input['event'] != '') {
                // Map event value to TRANSLATED event name based on locale
                // These are the translated values that are actually stored in the logs
                $eventMap = [
                    '1' => $locale === 'ar' ? 'تسجيل الدخول' : 'Login',
                    '2' => $locale === 'ar' ? 'تسجيل الخروج' : 'Logout',
                    '3' => $locale === 'ar' ? 'فشل تسجيل الدخول' : 'Login Failed',
                    '4' => $locale === 'ar' ? 'انتهت صلاحية الجلسة' : 'Session Expired',
                ];
                
                $eventValue = $eventMap[$input['event']] ?? $input['event'];
                $query .= ' | event="'.$eventValue.'"';
            }

            if (isset($input['employeeId']) && $input['employeeId'] != null && $input['employeeId'] != '') {
                $empIdValue = $input['employeeId'];
                $query .= ' | employeeId="'.$empIdValue.'"';
            }
            
            $query .= ' ['.(int)$diff.'d])';
            $params = 'query?query='.$query;

            $data = $this->lokiService->getAuditLogs($params);

            // Check if $data is an error response
            if (is_object($data) && method_exists($data, 'getStatusCode')) {
                return $data; // Return the error response
            }

            $formatedData = [];

            foreach ($data as $key => $value) {
                if (isset($value['metric'])) {
                    $lineData = $value['metric'];
                    $formatedData[] = $lineData;
                }
            }

            $formatedData = collect($formatedData)->sortByDesc('date_time');

            // Get date range filters from request
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');

            // Get search value for client-side filtering
            $searchValue = $request->input('search.value');
            
 

            return DataTables::of($formatedData)
                ->addIndexColumn()
                ->filter(function ($instance) use ($searchValue, $requestFromDate, $requestToDate) {
                    // Filter by date range if provided
                    if (!empty($requestFromDate) && !empty($requestToDate)) {
                        $instance->collection = $instance->collection->filter(function ($item) use ($requestFromDate, $requestToDate) {
                            $itemDateTime = isset($item['date_time']) ? Carbon::parse($item['date_time']) : null;
                            if (!$itemDateTime) {
                                return false;
                            }
                            $from = Carbon::parse($requestFromDate);
                            $to = Carbon::parse($requestToDate);
                            return $itemDateTime->gte($from) && $itemDateTime->lte($to);
                        });
                    }
                    
                    // Filter by search value
                    if (!empty($searchValue)) {
                        $instance->collection = $instance->collection->filter(function ($item) use ($searchValue) {
                            return stripos($item['session_id'] ?? '', $searchValue) !== false ||
                                   stripos($item['event'] ?? '', $searchValue) !== false ||
                                   stripos($item['employeeId'] ?? '', $searchValue) !== false ||
                                   stripos($item['employeeName'] ?? '', $searchValue) !== false ||
                                   stripos($item['role'] ?? '', $searchValue) !== false ||
                                   stripos($item['ipAddress'] ?? '', $searchValue) !== false ||
                                   stripos($item['deviceInfo'] ?? '', $searchValue) !== false;
                        });
                    }
                })
                ->make(true);
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Export user audit logs to Excel
     *
     * @param Request $request
     * @return Response
     */
    public function exportUserAuditLogs(Request $request)
    {
        $input = $request->all();
        try {
            $env = env("LOKI_ENV");
            $fromDate = Carbon::parse(env("LOKI_START_DATE"));
            $toDate = Carbon::now();
            $diff = $toDate->diffInDays($fromDate);
            $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid']: 'local';
            
            // Get current locale and determine filter language
            $locale = app()->getLocale() ?: 'en';
            $langFilter = $locale === 'ar' ? 'ar' : 'en';

            // Build the Loki query
            $query = 'rate({env="'.$env.'",channel="auth",tenant="'.$uuid.'"} | json | locale="'.$langFilter.'"';
            
            // Add event filter if event is specified
            if (isset($input['event']) && $input['event'] != null && $input['event'] != '') {
                // Map event value to TRANSLATED event name based on locale
                // These are the translated values that are actually stored in the logs
                $eventMap = [
                    '1' => $locale === 'ar' ? 'تسجيل الدخول' : 'Login',
                    '2' => $locale === 'ar' ? 'تسجيل الخروج' : 'Logout',
                    '3' => $locale === 'ar' ? 'فشل تسجيل الدخول' : 'Login Failed',
                    '4' => $locale === 'ar' ? 'انتهت صلاحية الجلسة' : 'Session Expired',
                ];
                
                $eventValue = $eventMap[$input['event']] ?? $input['event'];
                $query .= ' | event="'.$eventValue.'"';
            }

            if (isset($input['employeeId']) && $input['employeeId'] != null && $input['employeeId'] != '') {
                $empIdValue = $input['employeeId'];
                $query .= ' | employeeId="'.$empIdValue.'"';
            }
            
            $query .= ' ['.(int)$diff.'d])';
            $params = 'query?query='.$query;

            $data = $this->lokiService->getAuditLogs($params);

            // Check if $data is an error response
            if (is_object($data) && method_exists($data, 'getStatusCode')) {
                return $data; // Return the error response
            }

            $formatedData = [];

            foreach ($data as $key => $value) {
                if (isset($value['metric'])) {
                    $lineData = $value['metric'];
                    $formatedData[] = $lineData;
                }
            }

            // Sort by timestamp
            $formatedData = collect($formatedData)->sortByDesc('date_time')->values()->all();
            
            // Get date range filters from request
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');
            
            // Apply date range filter if specified
            if (!empty($requestFromDate) && !empty($requestToDate)) {
                $formatedData = collect($formatedData)->filter(function ($item) use ($requestFromDate, $requestToDate) {
                    $itemDateTime = isset($item['date_time']) ? Carbon::parse($item['date_time']) : null;
                    if (!$itemDateTime) {
                        return false;
                    }
                    $from = Carbon::parse($requestFromDate);
                    $to = Carbon::parse($requestToDate);
                    return $itemDateTime->gte($from) && $itemDateTime->lte($to);
                })->values()->all();
            }
            
            // Apply search filter if specified (client-side filtering for export)
            $searchValue = $request->input('search.value');
            if (!empty($searchValue)) {
                $formatedData = collect($formatedData)->filter(function ($item) use ($searchValue) {
                    return stripos($item['session_id'] ?? '', $searchValue) !== false ||
                           stripos($item['event'] ?? '', $searchValue) !== false ||
                           stripos($item['employeeId'] ?? '', $searchValue) !== false ||
                           stripos($item['employeeName'] ?? '', $searchValue) !== false ||
                           stripos($item['role'] ?? '', $searchValue) !== false ||
                           stripos($item['ipAddress'] ?? '', $searchValue) !== false ||
                           stripos($item['deviceInfo'] ?? '', $searchValue) !== false;
                })->values()->all();
            }

            // Prepare report data for Blade template
            $reportData = [
                'data' => $formatedData,
                'fromDate' => $requestFromDate,
                'toDate' => $requestToDate,
            ];

            // Generate Excel file using Blade template
            $fileName = trans('custom.user_audit_logs');
            
            return \Excel::create($fileName, function ($excel) use ($reportData) {
                $excel->sheet(trans('custom.new_sheet'), function ($sheet) use ($reportData) {
                    $sheet->loadView('export_report.user_audit_logs', $reportData);
                    
                    // Set right-to-left for Arabic locale
                    if (app()->getLocale() == 'ar') {
                        $sheet->getStyle('A1:Z1000')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                        $sheet->setRightToLeft(true);
                    }
                });
            })->download('xlsx');

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Migrate old audit logs to new format with proper labels
     * Dispatches separate jobs for each table to fetch logs from Loki and re-log them
     *
     * @param Request $request
     * @return Response
     * 
     * Request parameters:
     * - table (optional): specific table name to migrate, or "all" to migrate all tables
     *   If not provided, defaults to migrating all tables
     */
    public function migrateAuditLogs(Request $request)
    {
        $input = $request->all();

        try {
            $env = env("LOKI_ENV");
            $fromDate = Carbon::parse(env("LOKI_START_DATE"));
            $toDate = Carbon::now();
            $diff = $toDate->diffInDays($fromDate);
            
            // Determine which tables to migrate
            $requestedTable = $input['table'] ?? 'all';
            
            if ($requestedTable === 'all' || empty($requestedTable)) {
                $tables = $this->lokiService->getAllAuditTables();
            } else {
                $tables = [$requestedTable];
            }

            // Generate unique batch ID for tracking all related jobs
            $batchId = 'batch_' . uniqid() . '_' . time();
            
            $dispatchedJobs = [];

            // Dispatch a separate job for each table
            foreach ($tables as $table) {
                
                $tenants = Tenant::where('is_active', 1)->get();
                
                foreach ($tenants as $tenant) {
                    $tenantUuid = $tenant->uuid;
                    $jobId = $batchId . '_' . $table . '_' . $tenantUuid;
                    MigrateAuditLogsJob::dispatch($table, $env, $diff, $jobId, $batchId, $tenantUuid)->onQueue('single');
                    $dispatchedJobs[] = [
                        'job_id' => $jobId,
                        'table' => $table,
                        'tenant_uuid' => $tenantUuid
                    ];
                }
            }

            return $this->sendResponse([
                'batch_id' => $batchId,
                'jobs_dispatched' => count($dispatchedJobs),
                'jobs' => $dispatchedJobs,
                'status' => 'queued',
                'message' => "Dispatched " . count($dispatchedJobs) . " migration job(s), one for each table. Check the logs at storage/logs/audit_migration.log for progress and results."
            ], 'Migration jobs dispatched successfully');

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }
}
