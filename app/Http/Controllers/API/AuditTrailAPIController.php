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
            $env = env("VICTORIALOGS_ENV", env("APP_ENV", "production"));
            $locale = app()->getLocale() ?: 'en';
            $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid']: 'local';

            // Calculate time range
            $fromDate = Carbon::parse(env("LOKI_START_DATE", Carbon::now()->subDays(30)->toDateString()));
            $toDate = Carbon::now();
            
            // Get date range from request (applies to both tracking and regular cases)
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');
            
            if (!empty($requestFromDate)) {
                // Parse date string in format "Y-m-d H:i:s" as UTC to avoid timezone conversion issues
                // Create Carbon instance directly in UTC timezone without conversion
                try {
                    // Parse the date components
                    $parts = date_parse($requestFromDate);
                    if ($parts && !$parts['error_count']) {
                        $fromDate = Carbon::create(
                            $parts['year'],
                            $parts['month'],
                            $parts['day'],
                            $parts['hour'],
                            $parts['minute'],
                            $parts['second'],
                            'UTC'
                        );
                    } else {
                        // Fallback to parse with UTC timezone
                        $fromDate = Carbon::parse($requestFromDate, 'UTC');
                    }
                } catch (\Exception $e) {
                    // Fallback: parse and assume UTC (don't convert from server timezone)
                    $fromDate = Carbon::parse($requestFromDate, 'UTC');
                }
            }
            if (!empty($requestToDate)) {
                // Parse date string in format "Y-m-d H:i:s" as UTC to avoid timezone conversion issues
                // Create Carbon instance directly in UTC timezone without conversion
                try {
                    // Parse the date components
                    $parts = date_parse($requestToDate);
                    if ($parts && !$parts['error_count']) {
                        $toDate = Carbon::create(
                            $parts['year'],
                            $parts['month'],
                            $parts['day'],
                            $parts['hour'],
                            $parts['minute'],
                            $parts['second'],
                            'UTC'
                        );
                    } else {
                        // Fallback to parse with UTC timezone
                        $toDate = Carbon::parse($requestToDate, 'UTC');
                    }
                } catch (\Exception $e) {
                    // Fallback: parse and assume UTC (don't convert from server timezone)
                    $toDate = Carbon::parse($requestToDate, 'UTC');
                }
            }
            
            // Format dates for LogsQL (ISO 8601 format with Z suffix)
            $fromDateStr = $fromDate->format('Y-m-d\TH:i:s\Z');
            $toDateStr = $toDate->format('Y-m-d\TH:i:s\Z');
            
            \Log::info('Audit Logs Date Range:', [
                'env' => $env,
                'tenant_uuid' => $uuid,
                'locale' => $locale,
                'requestFromDate' => $requestFromDate,
                'requestToDate' => $requestToDate,
                'fromDateStr' => $fromDateStr,
                'toDateStr' => $toDateStr
            ]);

            if(isset($input['isFromTracking']) && $input['isFromTracking']){

                // Build LogsQL query for tracking
                // Don't include locale in stream filter - filter from unpacked JSON instead
                $streamFilter = '{tenant="'.$uuid.'", app="erp", env="'.$env.'", channel="audit"}';
                $query = '_time:['.$fromDateStr.', '.$toDateStr.'] '.$streamFilter.' | unpack_json';
                
                // Add locale filter first
                $filters = ['locale:="'.$locale.'"'];
                if(isset($input['accessType']) && $input['accessType'] != null && $input['accessType'] != ''){
                    $eventMap = [
                        '1' => 'C',
                        '2' => 'U',
                        '3' => 'D',
                    ];
                    $crudType = $eventMap[$input['accessType']] ?? $input['accessType'];
                    $filters[] = 'crudType:="'.$crudType.'"';
                }

                if(isset($input['employeeId']) && $input['employeeId'] != null && $input['employeeId'] != ''){
                    $filters[] = 'employeeId:="'.$input['employeeId'].'"';
                }

                if(isset($input['companyId']) && $input['companyId'] !== null && $input['companyId'] !== '' && $input['companyId'] !== 'null'){
                    $filters[] = 'company_system_id:="'.$input['companyId'].'"';
                }

                // Combine filters with 'and' - add pipe before filters
                if (!empty($filters)) {
                    $query .= ' | ' . implode(' and ', $filters);
                }

                // Search filter (uses pipe for regex/substring search)
                $searchValue = $request->input('search.value');
                if (!empty($searchValue)) {
                    $query .= ' |~ "'.addslashes($searchValue).'"';
                }

                // Select fields and sort
                $query .= ' | fields _time, transaction_id, table, user_name, role, employeeId, tenant_uuid, crudType, narration, session_id, date_time, module, parent_id, parent_table, data, locale, company_system_id, doc_code, log_uuid';
                $query .= ' | sort by (_time) desc';
                
                // Apply pagination limit if provided
                $limit = isset($input['length']) && $input['length'] > 0 ? (int)$input['length'] : 10000;
                $query .= ' | limit '.$limit;

                $data = $this->lokiService->queryLogsQL($query);
                $data2 = [];
            } else {
                // Main case: filter by transaction_id and table (or parent_id and parent_table)
                $id = $input['id'];
                $module = $input['module'];
                $table = $this->lokiService->getAuditTables($module);
                
                \Log::info('Audit Logs Query Parameters:', [
                    'id' => $id,
                    'module' => $module,
                    'table' => $table,
                    'env' => $env,
                    'tenant_uuid' => $uuid,
                    'locale' => $locale,
                    'fromDate' => $fromDateStr,
                    'toDate' => $toDateStr,
                    'streamFilter' => '{tenant="'.$uuid.'", app="erp", env="'.$env.'", channel="audit"}'
                ]);
                
                // Don't include locale in stream filter - old logs don't have it in stream, only in unpacked JSON
                // Filter by locale from unpacked JSON data instead (works for both old and new logs)
                $streamFilter = '{tenant="'.$uuid.'", app="erp", env="'.$env.'", channel="audit"}';
                
                // When dates are provided, use a wide _time range (90 days) to get all logs
                // Then filter by date_time field in PHP since _time reflects storage time, not event time
                // If no dates provided, use default range
                $timeRange = !empty($requestFromDate) && !empty($requestToDate) 
                    ? '_time:90d'  // Wide range when filtering by date_time
                    : '_time:['.$fromDateStr.', '.$toDateStr.']';
                
                // First, get total count for both queries using stats
                // Note: Count queries don't filter by date_time, we'll filter in PHP
                $countQuery1 = $timeRange.' '.$streamFilter.' | unpack_json';
                $countQuery1 .= ' | transaction_id:="'.$id.'" and table:="'.$table.'" and locale:="'.$locale.'"';
                $countQuery1 .= ' | stats count() as total';
                
                $countQuery2 = $timeRange.' '.$streamFilter.' | unpack_json';
                $countQuery2 .= ' | parent_id:="'.$id.'" and parent_table:="'.$table.'" and locale:="'.$locale.'"';
                $countQuery2 .= ' | stats count() as total';
                
                $count1Result = $this->lokiService->queryLogsQL($countQuery1);
                $count2Result = $this->lokiService->queryLogsQL($countQuery2);
                
                $totalCount1 = 0;
                $totalCount2 = 0;
                
                if (is_array($count1Result) && !empty($count1Result) && isset($count1Result[0]['total'])) {
                    $totalCount1 = (int) $count1Result[0]['total'];
                } elseif (is_array($count1Result) && !empty($count1Result) && isset($count1Result[0]['count(*)'])) {
                    $totalCount1 = (int) $count1Result[0]['count(*)'];
                }
                
                if (is_array($count2Result) && !empty($count2Result) && isset($count2Result[0]['total'])) {
                    $totalCount2 = (int) $count2Result[0]['total'];
                } elseif (is_array($count2Result) && !empty($count2Result) && isset($count2Result[0]['count(*)'])) {
                    $totalCount2 = (int) $count2Result[0]['count(*)'];
                }
                
                // Note: When dates are provided, totalRecords is approximate (based on _time range)
                // Actual count will be calculated after filtering by date_time in PHP
                $totalRecords = $totalCount1 + $totalCount2;
                
                \Log::info('Audit Logs Total Count (before date_time filter) - Query 1: ' . $totalCount1 . ', Query 2: ' . $totalCount2 . ', Total: ' . $totalRecords);
                
                // Query 1: Filter by transaction_id and table - get all data (no limit for server-side pagination)
                // Use wide time range when dates are provided, then filter by date_time in PHP
                $query1 = $timeRange.' '.$streamFilter.' | unpack_json';
                $query1 .= ' | transaction_id:="'.$id.'" and table:="'.$table.'" and locale:="'.$locale.'"';
                $query1 .= ' | fields _time, transaction_id, table, user_name, role, employeeId, tenant_uuid, crudType, narration, session_id, date_time, module, parent_id, parent_table, data, locale, company_system_id, doc_code, log_uuid';
                $query1 .= ' | sort by (_time) desc';
                // Get a large enough limit to cover all records (or use a reasonable max)
                $query1 .= ' | limit 50000';
                
                \Log::info('Audit Logs Query 1: ' . $query1);
                
                $data = $this->lokiService->queryLogsQL($query1);

                // Query 2: Filter by parent_id and parent_table - get all data
                $query2 = $timeRange.' '.$streamFilter.' | unpack_json';
                $query2 .= ' | parent_id:="'.$id.'" and parent_table:="'.$table.'" and locale:="'.$locale.'"';
                $query2 .= ' | fields _time, transaction_id, table, user_name, role, employeeId, tenant_uuid, crudType, narration, session_id, date_time, module, parent_id, parent_table, data, locale, company_system_id, doc_code, log_uuid';
                $query2 .= ' | sort by (_time) desc';
                $query2 .= ' | limit 50000';
                
                \Log::info('Audit Logs Query 2: ' . $query2);
                
                $data2 = $this->lokiService->queryLogsQL($query2);
            }

            // Check if $data is an error response
            if (is_object($data) && method_exists($data, 'getStatusCode')) {
                return $data;
            }

            // Check if $data2 is an error response
            if (is_object($data2) && method_exists($data2, 'getStatusCode')) {
                return $data2;
            }

            // Combine results
            $formatedData = [];
            
            // Process data from query 1
            if (is_array($data)) {
                foreach ($data as $entry) {
                    // Use _time as date_time if date_time is not available
                    if (!isset($entry['date_time']) && isset($entry['_time'])) {
                        $entry['date_time'] = $entry['_time'];
                    }
                    // Parse data field if it's a JSON string
                    if (isset($entry['data']) && is_string($entry['data'])) {
                        $entry['data'] = json_decode($entry['data'], true) ?: [];
                    } elseif (!isset($entry['data'])) {
                        $entry['data'] = [];
                    }
                    $formatedData[] = $entry;
                }
            }

            // Process data from query 2
            if (is_array($data2)) {
                foreach ($data2 as $entry) {
                    // Use _time as date_time if date_time is not available
                    if (!isset($entry['date_time']) && isset($entry['_time'])) {
                        $entry['date_time'] = $entry['_time'];
                    }
                    // Parse data field if it's a JSON string
                    if (isset($entry['data']) && is_string($entry['data'])) {
                        $entry['data'] = json_decode($entry['data'], true) ?: [];
                    } elseif (!isset($entry['data'])) {
                        $entry['data'] = [];
                    }
                    $formatedData[] = $entry;
                }
            }

            // Filter by date_time if dates were provided (since _time reflects storage time, not event time)
            if (!empty($requestFromDate) && !empty($requestToDate)) {
                $formatedData = collect($formatedData)->filter(function ($item) use ($fromDate, $toDate) {
                    if (!isset($item['date_time'])) {
                        return false;
                    }
                    try {
                        // Parse date_time (format: Y-m-d H:i:s)
                        $itemDate = Carbon::createFromFormat('Y-m-d H:i:s', $item['date_time'], 'UTC');
                        // Check if item date is within the requested range
                        return $itemDate->gte($fromDate) && $itemDate->lte($toDate);
                    } catch (\Exception $e) {
                        // If parsing fails, try Carbon::parse as fallback
                        try {
                            $itemDate = Carbon::parse($item['date_time'], 'UTC');
                            return $itemDate->gte($fromDate) && $itemDate->lte($toDate);
                        } catch (\Exception $e2) {
                            return false;
                        }
                    }
                })->values()->all();
                
                // Update totalRecords to reflect the actual filtered count
                $totalRecords = count($formatedData);
                \Log::info('Audit Logs Filtered Count (after date_time filter): ' . $totalRecords);
            }

            if(isset($input['isFromTracking']) && $input['isFromTracking']){
                // Sort by date_time
                $formatedData = collect($formatedData)->sortByDesc('date_time')->values()->all();

                $formatedData = collect($formatedData)->filter(function ($item) use ($fromDate,$toDate) {
                    $itemDate = isset($item['date_time']) ? Carbon::parse($item['date_time']) : null;
                    if (!$itemDate) {
                        return false;
                    }
                    return $itemDate->gte($fromDate) && $itemDate->lte($toDate);
                })->values()->all();
            } else {
                $formatedData = collect($formatedData)->sortByDesc('date_time')->values()->all();
            }

            // Make the formatedData unique by log_uuid
            $formatedData = collect($formatedData)->unique('log_uuid')->values()->all();
            
            // Log the counts for debugging
            $query1Count = is_array($data) ? count($data) : 0;
            $query2Count = is_array($data2) ? count($data2) : 0;
            $dataCountAfterDedup = count($formatedData);
            $calculatedTotal = isset($totalRecords) ? $totalRecords : $dataCountAfterDedup;
            
            \Log::info('Audit Logs DataTables counts - Query1: ' . $query1Count . ', Query2: ' . $query2Count . ', Combined before dedup: ' . ($query1Count + $query2Count) . ', After dedup: ' . $dataCountAfterDedup . ', Calculated total from count queries: ' . (isset($totalRecords) ? $totalRecords : 'N/A'));
            
            // Get current locale for arrow conversion
            $locale = app()->getLocale() ?: 'en';
            
            // Format date_time for each item and convert navigation path arrows
            $formatedData = collect($formatedData)->map(function ($item) use ($locale) {
                if (isset($item['date_time'])) {
                    $item['date_time'] = $this->formatDateTime($item['date_time']);
                }
                // Convert navigation path arrows based on locale
                if (isset($item['navigationPath'])) {
                    $item['navigationPath'] = $this->convertNavigationPathArrows($item['navigationPath'], $locale);
                }
                return $item;
            })->all();
            
            if(isset($input['isExport']) && $input['isExport']){
                return $formatedData;
            }

            // DataTables will automatically calculate counts from the collection
            // But we need to ensure we have all the data (up to 50000 limit)
            // The counts will be calculated from $formatedData after deduplication
            
            if(isset($input['isFromTracking']) && $input['isFromTracking']){
                return DataTables::of($formatedData)
                    ->filter(function ($query) use ($request) {
                    })
                    ->addIndexColumn()
                    ->make(true);
            } else {
                return DataTables::of($formatedData)
                    ->addIndexColumn()
                    ->make(true);
            }
            
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
        
        // Get date range from request
        $requestFromDate = $request->input('fromDate');
        $requestToDate = $request->input('toDate');
        
        // Use request dates if provided, otherwise fallback to defaults
        $fromDate = !empty($requestFromDate) ? Carbon::parse($requestFromDate) : Carbon::parse(env("LOKI_START_DATE"));
        $toDate = Carbon::now();
        $diff = $toDate->diffInDays($fromDate) + 1;
        
        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid']: 'local';
        $localeValue = app()->getLocale() ?: 'en';
        
        // Build line filters for event
        $eventFilter = '';
        if (isset($input['event']) && $input['event'] != null && $input['event'] != '') {
            $eventMap = [
                '1' => trans('audit.login'),
                '2' => trans('audit.logout'),
                '3' => trans('audit.login_failed'),
                '4' => trans('audit.session_expired'),
            ];
            
            $eventValue = $eventMap[$input['event']] ?? $input['event'];
            $eventFilter = ' |= `\"event\":\"'.$eventValue.'\"`';
        }

        // Build line filter for employeeId
        $employeeIdFilter = '';
        if (isset($input['employeeId']) && $input['employeeId'] != null && $input['employeeId'] != '') {
            $empIdValue = $input['employeeId'];
            $employeeIdFilter = ' |= `\"employeeId\":\"'.$empIdValue.'\"`';
        }

        // Build line filter for search
        $searchFilter = '';
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            $escapedSearch = preg_quote($searchValue, '/');
            $searchFilter = ' |~ `(?i)'.$escapedSearch.'`';
        }
        
        // $query = 'rate({env="'.$env.'",channel="auth",tenant="'.$uuid.'"} ['.(int)$diff.'d] | json';
        $query = 'rate({env="'.$env.'"}|= `\"channel\":\"auth\"` |= `\"tenant_uuid\":\"'.$uuid.'\"` |= `\"locale\":\"'.$localeValue.'\"`'.$eventFilter.$employeeIdFilter.$searchFilter.' | json ['.(int)$diff.'d])';
        $params = 'query?query='.$query;
        
        $data = $this->lokiService->getAuditLogs($params);

        // Check if $data is an error response
        if (is_object($data) && method_exists($data, 'getStatusCode')) {
            throw new \Exception('Failed to fetch data from Loki: HTTP ' . $data->getStatusCode());
        }

        $formatedData = [];

        foreach ($data as $key => $value) {
            if (isset($value['metric']['log'])) {
                $lineData = $value['metric']['log'];

                $formatedData[] = $lineData;
            }
        }

        $formatedData = collect($formatedData)->sortByDesc('date_time');

        $formatedData = collect($formatedData)->filter(function ($item) use ($fromDate,$toDate) {
            return $item['date_time'] >= $fromDate && $item['date_time'] <= $toDate;
        })->values()->all();

        //make the formatedData unique by log_uuid
        $formatedData = collect($formatedData)->unique('log_uuid')->values()->all();
        
        // Format date_time for each item
        $formatedData = collect($formatedData)->map(function ($item) {
            if (isset($item['date_time'])) {
                $item['date_time'] = $this->formatDateTime($item['date_time']);
            }
            return $item;
        })->all();
        
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
            
            // Convert date_time to RTL format for Arabic locale
            if (app()->getLocale() == 'ar') {
                $formatedData = collect($formatedData)->map(function ($item) {
                    if (isset($item['date_time'])) {
                        $item['date_time'] = $this->convertDateTimeToRTL($item['date_time']);
                    }
                    return $item;
                })->all();
            }
            
            // Prepare report data for Blade template
            $reportData = [
                'data' => $formatedData,
                'fromDate' => $requestFromDate,
                'toDate' => $requestToDate,
            ];

            // Generate Excel file using Blade template
            $fileName = trans('custom.user_audit_logs');

            $lang = app()->getLocale();
            $fontFamily = \Helper::getExcelFontFamily($lang);

            return \Excel::create($fileName, function ($excel) use ($reportData, $fontFamily) {
                $excel->sheet(trans('custom.new_sheet'), function ($sheet) use ($reportData, $fontFamily) {
                    // Set default font for entire sheet
                    $sheet->setStyle([
                        'font' => [
                            'name' => $fontFamily,
                            'size' => 11,
                        ]
                    ]);
                    $sheet->loadView('export_report.user_audit_logs', $reportData);

                    $lastRow = $sheet->getHighestRow();
                    $lastColumn = $sheet->getHighestColumn();
                    if ($lastRow > 0 && $lastColumn) {
                        try {
                            $spreadsheet = $sheet->getDelegate();
                            $worksheet = $spreadsheet->getActiveSheet();
                            $worksheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setName($fontFamily);
                        } catch (\Exception $e) {
                            $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setName($fontFamily);
                        }
                    }
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
     * Export event tracking logs to Excel
     *
     * @param Request $request
     * @return Response
     */
    public function exportEventTrackingLogs(Request $request)
    {
        try {
            // Add isExport flag to request and call auditLogs function
            $request->merge(['isExport' => true, 'isFromTracking' => true]);
            
            // Call auditLogs function which will return formatted data when isExport is true
            $formatedData = $this->auditLogs($request);
            
            // Check if response is an error
            if (is_object($formatedData) && method_exists($formatedData, 'getStatusCode')) {
                return $formatedData;
            }
            
            // Check if there's no data to export
            if (empty($formatedData)) {
                return $this->sendError(trans('custom.no_event_tracking_logs_found'), 404);
            }
            
            // Get date range filters for displaying in Excel
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');
            
            // Convert date_time to RTL format for Arabic locale
            if (app()->getLocale() == 'ar') {
                $formatedData = collect($formatedData)->map(function ($item) {
                    if (isset($item['date_time'])) {
                        $item['date_time'] = $this->convertDateTimeToRTL($item['date_time']);
                    }
                    return $item;
                })->all();
            }
            
            // Prepare report data for Blade template
            $reportData = [
                'data' => $formatedData,
                'fromDate' => $requestFromDate,
                'toDate' => $requestToDate,
            ];

            // Generate Excel file using Blade template
            $fileName = trans('custom.event_tracking_logs');

            $lang = app()->getLocale();
            $fontFamily = \Helper::getExcelFontFamily($lang);

            return \Excel::create($fileName, function ($excel) use ($reportData, $fontFamily) {
                $excel->sheet(trans('custom.new_sheet'), function ($sheet) use ($reportData, $fontFamily) {
                    // Set default font for entire sheet
                    $sheet->setStyle([
                        'font' => [
                            'name' => $fontFamily,
                            'size' => 11,
                        ]
                    ]);
                    $sheet->loadView('export_report.event_tracking_logs', $reportData);

                    $lastRow = $sheet->getHighestRow();
                    $lastColumn = $sheet->getHighestColumn();
                    if ($lastRow > 0 && $lastColumn) {
                        try {
                            $spreadsheet = $sheet->getDelegate();
                            $worksheet = $spreadsheet->getActiveSheet();
                            $worksheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setName($fontFamily);
                        } catch (\Exception $e) {
                            $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setName($fontFamily);
                        }
                    }
                    
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
        
        // Get date range from request
        $requestFromDate = $request->input('fromDate');
        $requestToDate = $request->input('toDate');
        
        // Use request dates if provided, otherwise fallback to defaults
        $fromDate = !empty($requestFromDate) ? Carbon::parse($requestFromDate) : Carbon::parse(env("LOKI_START_DATE"));
        $toDate = Carbon::now();
        $diff = $toDate->diffInDays($fromDate) + 1;
        
        $uuid = isset($input['tenant_uuid']) ? $input['tenant_uuid']: 'local';
        
        $localeValue = app()->getLocale() ?: 'en';
        
        // Build line filter for employeeId
        $employeeIdFilter = '';
        if (isset($input['employeeId']) && $input['employeeId'] != null && $input['employeeId'] != '') {
            $empIdValue = $input['employeeId'];
            $employeeIdFilter = ' |= `\"employeeId\":\"'.$empIdValue.'\"`';
        }

        // Build line filter for companyId
        $companyIdFilter = '';
        if (isset($input['companyId']) && $input['companyId'] != null && $input['companyId'] != '') {
            $companyIdValue = $input['companyId'];
            $companyIdFilter = ' |= `\"companyID\":\"'.$companyIdValue.'\"`';
        }
        
        // Build line filter for accessType
        $accessTypeFilter = '';
        if (isset($input['accessType']) && $input['accessType'] != null && $input['accessType'] != '') {
            $accessTypeValue = $input['accessType'];
            if (is_numeric($accessTypeValue)) {
                switch ((int)$accessTypeValue) {
                    case 1: $accessTypeValue = trans('audit.read', [], $localeValue); break;
                    case 2: $accessTypeValue = trans('audit.create', [], $localeValue); break;
                    case 3: $accessTypeValue = trans('audit.edit', [], $localeValue); break;
                    case 4: $accessTypeValue = trans('audit.delete', [], $localeValue); break;
                    default: $accessTypeValue = trans('audit.read', [], $localeValue);
                }
            }
            $accessTypeFilter = ' |= `\"accessType\":\"'.$accessTypeValue.'\"`';
        }

        // Build line filter for search
        $searchFilter = '';
        $searchValue = $request->input('search.value');
        if (!empty($searchValue)) {
            // Escape special regex characters for Loki
            $escapedSearch = preg_quote($searchValue, '/');
            $searchFilter = ' |~ `(?i)'.$escapedSearch.'`';
        }
        
        $query = 'rate({env="'.$env.'"}|= `\"channel\":\"navigation\"` |= `\"tenant_uuid\":\"'.$uuid.'\"` |= `\"locale\":\"'.$localeValue.'\"`'.$employeeIdFilter.$companyIdFilter.$accessTypeFilter.$searchFilter.' | json ['.(int)$diff.'d])';
        $params = 'query?query='.$query;
        
        $data = $this->lokiService->getAuditLogs($params);
        
        if (is_object($data) && method_exists($data, 'getStatusCode')) {
            throw new \Exception('Failed to fetch data from Loki: HTTP ' . $data->getStatusCode());
        }

        $formatedData = [];

        foreach ($data as $key => $value) {
            if (isset($value['metric']['log'])) {
                $lineData = $value['metric']['log'];

                $formatedData[] = $lineData;
            }
        }

        $formatedData = collect($formatedData)->sortByDesc('date_time');

        $formatedData = collect($formatedData)->filter(function ($item) use ($fromDate,$toDate) {
            return $item['date_time'] >= $fromDate && $item['date_time'] <= $toDate;
        })->values()->all();

        //make the formatedData unique by log_uuid
        $formatedData = collect($formatedData)->unique('log_uuid')->values()->all();
        
        // Format date_time for each item and convert navigation path arrows
        $formatedData = collect($formatedData)->map(function ($item) use ($localeValue) {
            if (isset($item['date_time'])) {
                $item['date_time'] = $this->formatDateTime($item['date_time']);
            }
            // Convert navigation path arrows based on locale
            if (isset($item['navigationPath'])) {
                $item['navigationPath'] = $this->convertNavigationPathArrows($item['navigationPath'], $localeValue                          );
            }
            return $item;
        })->all();
        
        return $formatedData;
    }

    /**
     * Format date_time to match frontend format: dd/MM/yyyy HH:mm AM/PM
     * 
     * @param string|Carbon $dateTime
     * @param bool $rtl If true, format as RTL (AM/PM HH:mm:ss dd/MM/yyyy) for Arabic
     * @return string
     */
    private function formatDateTime($dateTime, $rtl = false)
    {
        if (empty($dateTime)) {
            return '';
        }
        
        try {
            // Parse the date_time (could be string or Carbon instance)
            $carbon = $dateTime instanceof Carbon ? $dateTime : Carbon::parse($dateTime);
            
            // Format date as dd/MM/yyyy
            $date = $carbon->format('d/m/Y');
            
            // Format time as 12-hour with AM/PM
            $hour = (int)$carbon->format('H');
            $minute = $carbon->format('i');
            $second = $carbon->format('s');
            
            // Convert 24-hour to 12-hour format
            $hour12 = $hour % 12;
            if ($hour12 == 0) {
                $hour12 = 12;
            }
            $ampm = $hour < 12 ? 'AM' : 'PM';
            
            if ($rtl) {
                // RTL format: AM/PM HH:mm:ss dd/MM/yyyy (for Arabic)
                return $ampm . ' ' . str_pad($hour12, 2, '0', STR_PAD_LEFT) . ':' . $minute . ':' . $second . ' ' . $date;
            } else {
                // LTR format: dd/MM/yyyy HH:mm:ss AM/PM
                return $date . ' ' . str_pad($hour12, 2, '0', STR_PAD_LEFT) . ':' . $minute . ':' . $second . ' ' . $ampm;
            }
        } catch (\Exception $e) {
            // Return original value if parsing fails
            return $dateTime;
        }
    }

    /**
     * Convert formatted date_time from LTR to RTL format for Arabic exports
     * Converts: "dd/MM/yyyy HH:mm:ss AM/PM" to "AM/PM HH:mm:ss dd/MM/yyyy"
     * 
     * @param string $formattedDateTime
     * @return string
     */
    private function convertDateTimeToRTL($formattedDateTime)
    {
        if (empty($formattedDateTime)) {
            return '';
        }
        
        // Pattern: "dd/MM/yyyy HH:mm:ss AM/PM"
        // Extract parts using regex
        if (preg_match('/^(\d{2}\/\d{2}\/\d{4})\s+(\d{2}:\d{2}:\d{2})\s+(AM|PM)$/i', $formattedDateTime, $matches)) {
            // Reorder: AM/PM HH:mm:ss dd/MM/yyyy
            return $matches[3] . ' ' . $matches[2] . ' ' . $matches[1];
        }
        
        // If pattern doesn't match, try to parse and reformat
        try {
            $carbon = Carbon::parse($formattedDateTime);
            return $this->formatDateTime($carbon, true);
        } catch (\Exception $e) {
            return $formattedDateTime;
        }
    }

    /**
     * Convert navigation path arrows based on locale (RTL or LTR)
     * Converts arrows in navigation paths to match the language direction
     * 
     * @param string $navigationPath
     * @param string $locale
     * @return string
     */
    private function convertNavigationPathArrows($navigationPath, $locale)
    {
        if (empty($navigationPath)) {
            return $navigationPath;
        }
        
        $isRTL = $this->isRTL($locale);
        
        // Replace arrows based on language direction
        if ($isRTL) {
            // Convert right arrows (→) to left arrows (←) for RTL languages
            $navigationPath = str_replace(' → ', ' ← ', $navigationPath);
            $navigationPath = str_replace('→', '←', $navigationPath);
        } else {
            // Convert left arrows (←) to right arrows (→) for LTR languages
            $navigationPath = str_replace(' ← ', ' → ', $navigationPath);
            $navigationPath = str_replace('←', '→', $navigationPath);
        }
        
        return $navigationPath;
    }

    /**
     * Check if a language is RTL (Right-to-Left)
     * 
     * @param string $languageCode
     * @return bool
     */
    private function isRTL($languageCode)
    {
        // List of RTL language codes
        $rtlLanguages = ['ar', 'he', 'fa', 'ur']; // Arabic, Hebrew, Persian, Urdu
        
        return in_array(strtolower($languageCode), $rtlLanguages);
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
            
            // Convert date_time to RTL format for Arabic locale
            if (app()->getLocale() == 'ar') {
                $formatedData = collect($formatedData)->map(function ($item) {
                    if (isset($item['date_time'])) {
                        $item['date_time'] = $this->convertDateTimeToRTL($item['date_time']);
                    }
                    return $item;
                })->all();
            }

            // Prepare report data for Blade template
            $reportData = [
                'data' => $formatedData,
                'fromDate' => $requestFromDate,
                'toDate' => $requestToDate,
            ];

            // Generate Excel file using Blade template
            $fileName = trans('custom.navigation_access_logs');

            $lang = app()->getLocale();
            $fontFamily = \Helper::getExcelFontFamily($lang);

            return \Excel::create($fileName, function ($excel) use ($reportData, $fontFamily) {
                $excel->sheet(trans('custom.new_sheet'), function ($sheet) use ($reportData, $fontFamily) {
                    // Set default font for entire sheet
                    $sheet->setStyle([
                        'font' => [
                            'name' => $fontFamily,
                            'size' => 11,
                        ]
                    ]);
                    $sheet->loadView('export_report.navigation_access_logs', $reportData);

                    // Apply font to all cells after loading view
                    $lastRow = $sheet->getHighestRow();
                    $lastColumn = $sheet->getHighestColumn();
                    if ($lastRow > 0 && $lastColumn) {
                        try {
                            $spreadsheet = $sheet->getDelegate();
                            $worksheet = $spreadsheet->getActiveSheet();
                            $worksheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setName($fontFamily);
                        } catch (\Exception $e) {
                            $sheet->getStyle('A1:' . $lastColumn . $lastRow)->getFont()->setName($fontFamily);
                        }
                    }
                    
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
