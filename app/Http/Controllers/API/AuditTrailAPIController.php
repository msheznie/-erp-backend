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
use Illuminate\Support\Facades\Log;
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
     * Fetch and format user audit logs from Loki
     * 
     * @param Request $request
     * @return array
     */
    private function fetchUserAuditLogs(Request $request)
    {
        $input = $request->all();
        $env = env("LOKI_ENV");
        

        $fromDate = Carbon::parse(env("LOKI_START_DATE"));
        $toDate = Carbon::now();
        $diff = $toDate->diffInDays($fromDate);
        
        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid']: 'local';
        
        // Get current locale and determine filter language
        $locale = app()->getLocale() ?: 'en';
        $langFilter = $locale === 'ar' ? 'ar' : 'en';

        // Build the Loki query - just filter by channel
        $query = 'rate({env="'.$env.'",channel="auth",tenant="'.$uuid.'"} | json';
        
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
            throw new \Exception('Failed to fetch data from Loki: HTTP ' . $data->getStatusCode());
        }

        // Handle empty data or non-array response
        if (empty($data) || !is_array($data)) {
            $data = [];
        }

        $formatedData = [];

        foreach ($data as $key => $value) {
            if (isset($value['metric'])) {
                $lineData = $value['metric'];
                
                // Filter by locale if present
                if (isset($lineData['locale']) && $lineData['locale'] === $langFilter) {
                    $formatedData[] = $lineData;
                } elseif (!isset($lineData['locale'])) {
                    // If locale is not set, include it (for backward compatibility)
                    $formatedData[] = $lineData;
                }
            }
        }
        
        // Sort by date_time
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
        
        // Apply search filter if specified
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $formatedData = collect($formatedData)->filter(function ($item) use ($searchValue) {
                return str_contains($item['employeeName'] ?? '', $searchValue) ||
                       str_contains($item['employeeId'] ?? '', $searchValue) ||
                       str_contains($item['role'] ?? '', $searchValue) ||
                       str_contains($item['event'] ?? '', $searchValue) ||
                       str_contains($item['session_id'] ?? '', $searchValue) ||
                       str_contains($item['ipAddress'] ?? '', $searchValue) ||
                       str_contains($item['deviceInfo'] ?? '', $searchValue);
            })->values()->all();
        }
        
        return $formatedData;
    }

    /**
     * Get user audit logs (login, logout, session events)
     * Fetches logs where logType = 'user_audit' from Loki
     *
     * @param Request $request
     * @return Response
     */
    public function userAuditLogs(Request $request){
        try {
            // Use shared method to fetch data with all filters applied
            $formatedData = $this->fetchUserAuditLogs($request);
            
            return DataTables::of($formatedData)
            ->filter(function ($query) use ($request) {
            })
                ->addIndexColumn()
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
        try {
            // Use shared method to fetch data with all filters applied
            $formatedData = $this->fetchUserAuditLogs($request);
            
            // Check if there's no data to export
            if (empty($formatedData)) {
                return $this->sendError(trans('custom.no_user_audit_logs_found'), 404);
            }
            
            // Get date range filters for displaying in Excel
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');
            
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
     * Fetch navigation access logs from Loki
     * Filters by channel="navigation" and applies date range, search, and locale filters
     *
     * @param Request $request
     * @return array
     */
    private function fetchNavigationAccessLogs(Request $request)
    {
        $input = $request->all();
        $env = env("LOKI_ENV");
        
        // Get date range from request input
        $requestFromDate = $request->input('fromDate');
        $requestToDate = $request->input('toDate');
        
        // Calculate diff for Loki query range
        if (!empty($requestFromDate) && !empty($requestToDate)) {
            $fromDate = Carbon::parse($requestFromDate);
            $toDate = Carbon::parse($requestToDate);
            $diff = $toDate->diffInDays($fromDate);
            
            // Ensure minimum 1 day range for Loki query (diffInDays can be 0 for same day)
            if ($diff == 0) {
                $diff = 1; // Use at least 1 day range
            }
        
        } else {
            // Fallback to environment-based dates if not provided in request
            $fromDate = Carbon::parse(env("LOKI_START_DATE"));
            $toDate = Carbon::now();
            $diff = $toDate->diffInDays($fromDate);
        }
        
        // Get tenant_uuid - try multiple sources (same as fetchUserAuditLogs)
        // Check request input first, then request attribute, then headers
        $uuid = $request->input('tenant_uuid');
        if (empty($uuid)) {
            $uuid = $request->get('tenant_uuid');
        }
        if (empty($uuid)) {
            $uuid = $request->header('tenant-uuid');
        }
        if (empty($uuid)) {
            // Try accessing as attribute (set by middleware)
            $uuid = $request->attributes->get('tenant_uuid');
        }
        if (empty($uuid)) {
            $uuid = 'local';
        }
        
        // Get current locale and determine filter language
        $locale = app()->getLocale() ?: 'en';
        $langFilter = $locale === 'ar' ? 'ar' : 'en';

        // Build the Loki query - filter by channel="navigation"
        // Use same pattern as fetchUserAuditLogs with rate()
        $query = 'rate({env="'.$env.'",channel="navigation",tenant="'.$uuid.'"} | json';
        
        // Add employeeId filter if specified
        if (isset($input['employeeId']) && $input['employeeId'] != null && $input['employeeId'] != '') {
            $empIdValue = $input['employeeId'];
            $query .= ' | employeeId="'.$empIdValue.'"';
        }
        
        // Add accessType filter if specified (supports numeric 1,2,3 -> read,create,edit)
        if (isset($input['accessType']) && $input['accessType'] != null && $input['accessType'] != '') {
            $accessTypeValue = $input['accessType'];
            if (is_numeric($accessTypeValue)) {
                switch ((int)$accessTypeValue) {
                    case 1: $accessTypeValue = 'Read'; break;
                    case 2: $accessTypeValue = 'Create'; break;
                    case 3: $accessTypeValue = 'Edit'; break;
                    case 4: $accessTypeValue = 'Delete'; break;
                    default: $accessTypeValue = 'Read';
                }
            }
            $query .= ' | accessType="'.$accessTypeValue.'"';
        }
        
        $query .= ' ['.(int)$diff.'d])';
        $params = 'query?query='.$query;
        
        $data = $this->lokiService->getAuditLogs($params);
        
        // Check if $data is an error response
        if (is_object($data) && method_exists($data, 'getStatusCode')) {
            throw new \Exception('Failed to fetch data from Loki: HTTP ' . $data->getStatusCode());
        }

        // Handle empty data or non-array response
        if (empty($data) || !is_array($data)) {
            $data = [];
        }

        $formatedData = [];

        foreach ($data as $key => $value) {
            if (isset($value['metric'])) {
                $lineData = $value['metric'];
                
                // Filter by locale if present
                if (isset($lineData['locale']) && $lineData['locale'] === $langFilter) {
                    $formatedData[] = $lineData;
                } elseif (!isset($lineData['locale'])) {
                    // If locale is not set, include it (for backward compatibility)
                    $formatedData[] = $lineData;
                }
            }
        }
        

        // Sort by date_time
        $formatedData = collect($formatedData)->sortByDesc('date_time')->values()->all();
        
        // Get date range filters from request
        $requestFromDate = $request->input('fromDate');
        $requestToDate = $request->input('toDate');
        
        // Apply date range filter if specified
        if (!empty($requestFromDate) && !empty($requestToDate)) {
            $countBeforeDateFilter = count($formatedData);
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
        
        // Apply search filter if specified
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $countBeforeSearch = count($formatedData);
            $formatedData = collect($formatedData)->filter(function ($item) use ($searchValue) {
                return str_contains($item['session_id'] ?? '', $searchValue) ||
                       str_contains($item['employeeId'] ?? '', $searchValue) ||
                       str_contains($item['employeeName'] ?? '', $searchValue) ||
                       str_contains($item['role'] ?? '', $searchValue) ||
                       str_contains($item['accessType'] ?? '', $searchValue) ||
                       str_contains($item['screenAccessed'] ?? '', $searchValue) ||
                       str_contains($item['navigationPath'] ?? '', $searchValue);
            })->values()->all();
        }
        
        
        return $formatedData;
    }

    /**
     * Get navigation access logs
     * Fetches logs where channel = 'navigation' from Loki
     *
     * @param Request $request
     * @return Response
     */
    public function navigationAccessLogs(Request $request){
        try {
            // Use shared method to fetch data with all filters applied
            $formatedData = $this->fetchNavigationAccessLogs($request);
            
            return DataTables::of($formatedData)
                ->filter(function ($query) use ($request) {
                })
                ->addIndexColumn()
                ->make(true);
        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Export navigation access logs to Excel
     *
     * @param Request $request
     * @return Response
     */
    public function exportNavigationAccessLogs(Request $request)
    {
        try {
            // Use shared method to fetch data with all filters applied
            $formatedData = $this->fetchNavigationAccessLogs($request);
            
            // Check if there's no data to export
            if (empty($formatedData)) {
                return $this->sendError(trans('custom.no_navigation_access_logs_found'), 404);
            }
            
            // Get date range filters for displaying in Excel
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');
            

            // Prepare report data for Blade template
            $reportData = [
                'data' => $formatedData,
                'fromDate' => $requestFromDate,
                'toDate' => $requestToDate,
            ];

            // Generate Excel file using Blade template
            $fileName = trans('custom.navigation_access_logs');
            
            return \Excel::create($fileName, function ($excel) use ($reportData) {
                $excel->sheet(trans('custom.new_sheet'), function ($sheet) use ($reportData) {
                    $sheet->loadView('export_report.navigation_access_logs', $reportData);
                    
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
