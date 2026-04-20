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
use App\Http\Requests\API\EmployeeActivityAuditReportAPIRequest;
use App\Http\Requests\API\UpdateAuditTrailAPIRequest;
use App\Models\AuditTrail;
use App\Models\Tenant;
use App\Repositories\AuditTrailRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Services\VictoriaLogsService;
use App\Services\LokiService;
use DataTables;
use App\helper\CommonJobService;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Traits\AuditLogsTrait;
use App\Services\AuditLog\EmployeeAuditReportService;
use Illuminate\Support\Facades\DB;
use App\helper\Helper;
use App\Exports\BladeViewExcelExport;
use App\Exports\UserAuditLogsExport;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class AuditTrailController
 * @package App\Http\Controllers\API
 * 
 */

class AuditTrailAPIController extends AppBaseController
{
    use AuditLogsTrait;
    /** @var  AuditTrailRepository */
    private $auditTrailRepository;
    private $victoriaLogsService;
    private $lokiService;

    private EmployeeAuditReportService $employeeAuditReportService;

    public function __construct(
        AuditTrailRepository $auditTrailRepo,
        VictoriaLogsService $victoriaLogsService,
        LokiService $lokiService,
        EmployeeAuditReportService $employeeAuditReportService
    ) {
        $this->auditTrailRepository = $auditTrailRepo;
        $this->victoriaLogsService = $victoriaLogsService;
        $this->lokiService = $lokiService;
        $this->employeeAuditReportService = $employeeAuditReportService;
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

    /**
     * 
     * 
     * Supports two modes:
     * 1. Transaction-specific logs (id + module) - for shared/audit-logs component
     * 2. Action tracking logs (isFromTracking) - for action-tracking-logs component
     * 
     * @param Request $request
     * @return mixed
     */
    public function auditLogs(Request $request){
        $input = $request->all();
        
        try {
            $locale = app()->getLocale() ?: 'en';
            $tenantUuid = $input['tenant_uuid'] ?? 'local';
            
            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'companyId' => $input['companyId'] ?? null,
                'start' => $input['start'] ?? 0,
                'length' => $input['length'] ?? 15,
                'search' => $input['search'] ?? [],
            ];
            
            if (!empty($input['module'])) {
                $params['id'] = data_get($input, 'id');
                $params['module'] = $this->lokiService->getAuditTables($input['module']);
                $params['fromDate'] = $input['fromDate'] ?? null;
                $params['toDate'] = $input['toDate'] ?? null;
                $params['departmentSystemID'] = $input['departmentSystemID'] ?? null;
            }
            
            if (!empty($input['isFromTracking'])) {
                $params['isFromTracking'] = true;
                $params['fromDate'] = $input['fromDate'] ?? null;
                $params['toDate'] = $input['toDate'] ?? null;
                $params['employeeId'] = $input['employeeId'] ?? null;
                $params['accessType'] = $input['accessType'] ?? null;
            }
            
            $result = $this->victoriaLogsService->getAuditLogs($params);
            
            $formatedData = collect($result['data'] ?? [])->map(function ($item) use ($locale) {
                if (isset($item['date_time'])) {
                    $item['date_time'] = $this->formatDateTime($item['date_time']);
                }
                if (isset($item['navigationPath'])) {
                    $item['navigationPath'] = $this->convertNavigationPathArrows($item['navigationPath'], $locale);
                }
                if (isset($item['data']) && is_string($item['data'])) {
                    $item['data'] = json_decode($item['data']);
                }
                return $item;
            })->values()->all();
            
            if (!empty($input['isExport'])) {
                return $formatedData;
            }
            
            return \DataTables::of($formatedData)
                ->filter(function() {
                })
                ->addIndexColumn()
                ->make(true);
            
        } catch (\Exception $exception) {
            Log::error('Error in auditLogs', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * Get user audit logs (login, logout, session events)
     *
     * @param Request $request
     * @return Response
     */
    public function userAuditLogs(Request $request){
        try {
            $input = $request->all();
            $locale = app()->getLocale() ?: 'en';
            $tenantUuid = $input['tenant_uuid'] ?? 'local';
            
            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'fromDate' => $input['fromDate'] ?? null,
                'toDate' => $input['toDate'] ?? null,
                'employeeId' => $input['employeeId'] ?? null,
                'event' => $input['event'] ?? null,
                'start' => $input['start'] ?? 0,
                'length' => $input['length'] ?? 15,
                'search' => $input['search'] ?? [],
            ];
            
            $result = $this->victoriaLogsService->getUserAuditLogs($params);
            
            $formatedData = collect($result['data'] ?? [])->map(function ($item) {
                if (isset($item['date_time'])) {
                    $item['date_time'] = $this->formatDateTime($item['date_time']);
                }
                return $item;
            })->values()->all();
            
            return \DataTables::of($formatedData)
                ->filter(function() {
                })
                ->addIndexColumn()
                ->make(true);
            
        } catch (\Exception $exception) {
            Log::error('Error in userAuditLogs', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
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
            $input = $request->all();
            $locale = app()->getLocale() ?: 'en';
            $tenantUuid = $input['tenant_uuid'] ?? 'local';
            
            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'fromDate' => $input['fromDate'] ?? null,
                'toDate' => $input['toDate'] ?? null,
                'employeeId' => $input['employeeId'] ?? null,
                'event' => $input['event'] ?? null,
                'start' => 0,
                'length' => 10000,
                'search' => $input['search'] ?? [],
            ];
            
            $result = $this->victoriaLogsService->getUserAuditLogs($params);
            $formatedData = $result['data'] ?? [];
            
            if (empty($formatedData)) {
                return $this->sendError(trans('custom.no_user_audit_logs_found'), 404);
            }
            
            $formatedData = collect($formatedData)->map(function ($item) {
                if (isset($item['date_time'])) {
                    $item['date_time'] = $this->formatDateTime($item['date_time']);
                }
                return $item;
            })->all();
            
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');
            
            if (app()->getLocale() == 'ar') {
                $formatedData = collect($formatedData)->map(function ($item) {
                    if (isset($item['date_time'])) {
                        $item['date_time'] = $this->convertDateTimeToRTL($item['date_time']);
                    }
                    return $item;
                })->all();
            }
            
            $reportData = [
                'data' => $formatedData,
                'fromDate' => $requestFromDate,
                'toDate' => $requestToDate,
            ];

            $fileName = trans('custom.user_audit_logs');

            $lang = app()->getLocale();
            $fontFamily = Helper::getExcelFontFamily($lang);
            $isRtl = $lang === 'ar';

            return Excel::download(
                new UserAuditLogsExport($reportData, $fontFamily, $isRtl),
                $fileName . '.xlsx'
            );

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
            $request->merge(['isExport' => true, 'isFromTracking' => true]);
            
            $formatedData = $this->auditLogs($request);
            
            if (is_object($formatedData) && method_exists($formatedData, 'getStatusCode')) {
                return $formatedData;
            }
            
            if (empty($formatedData)) {
                return $this->sendError(trans('custom.no_event_tracking_logs_found'), 404);
            }
            
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');
            
            if (app()->getLocale() == 'ar') {
                $formatedData = collect($formatedData)->map(function ($item) {
                    if (isset($item['date_time'])) {
                        $item['date_time'] = $this->convertDateTimeToRTL($item['date_time']);
                    }
                    return $item;
                })->all();
            }
            
            $reportData = [
                'data' => $formatedData,
                'fromDate' => $requestFromDate,
                'toDate' => $requestToDate,
            ];

            $fileName = trans('custom.event_tracking_logs');

            $lang = app()->getLocale();
            $fontFamily = Helper::getExcelFontFamily($lang);
            $isRtl = $lang === 'ar';

            return Excel::download(
                new BladeViewExcelExport('export_report.event_tracking_logs', $reportData, $fontFamily, $isRtl),
                $fileName . '.xlsx'
            );

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
            $carbon = $dateTime instanceof Carbon ? $dateTime : Carbon::parse($dateTime);
            
            $date = $carbon->format('d/m/Y');
            
            $hour = (int)$carbon->format('H');
            $minute = $carbon->format('i');
            $second = $carbon->format('s');
            
            $hour12 = $hour % 12;
            if ($hour12 == 0) {
                $hour12 = 12;
            }
            $ampm = $hour < 12 ? 'AM' : 'PM';
            
            if ($rtl) {
                return $ampm . ' ' . str_pad($hour12, 2, '0', STR_PAD_LEFT) . ':' . $minute . ':' . $second . ' ' . $date;
            } else {
                return $date . ' ' . str_pad($hour12, 2, '0', STR_PAD_LEFT) . ':' . $minute . ':' . $second . ' ' . $ampm;
            }
        } catch (\Exception $e) {
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
        
        if (preg_match('/^(\d{2}\/\d{2}\/\d{4})\s+(\d{2}:\d{2}:\d{2})\s+(AM|PM)$/i', $formattedDateTime, $matches)) {
            return $matches[3] . ' ' . $matches[2] . ' ' . $matches[1];
        }
        
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
        
        if ($isRTL) {
            $navigationPath = str_replace(' → ', ' ← ', $navigationPath);
            $navigationPath = str_replace('→', '←', $navigationPath);
        } else {
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
        $rtlLanguages = ['ar', 'he', 'fa', 'ur'];
        
        return in_array(strtolower($languageCode), $rtlLanguages);
    }

    /**
     * Get navigation access logs
     * Fetches logs where channel = 'navigation' from Loki
     *
     * @param Request $request
     * @return Response
     */
    /**
     * Get navigation access logs
     *
     * @param Request $request
     * @return Response
     */
    public function navigationAccessLogs(Request $request){
        try {
            $input = $request->all();
            $locale = app()->getLocale() ?: 'en';
            $tenantUuid = $input['tenant_uuid'] ?? 'local';
            
            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'fromDate' => $input['fromDate'] ?? null,
                'companyId' => $input['companyId'] ?? null,
                'toDate' => $input['toDate'] ?? null,
                'employeeId' => $input['employeeId'] ?? null,
                'accessType' => $input['accessType'] ?? null,
                'start' => $input['start'] ?? 0,
                'length' => $input['length'] ?? 15,
                'search' => $input['search'] ?? [],
            ];
            
            $result = $this->victoriaLogsService->getNavigationAccessLogs($params);
            
            $formatedData = collect($result['data'] ?? [])->map(function ($item) use ($locale) {
                if (isset($item['date_time'])) {
                    $item['date_time'] = $this->formatDateTime($item['date_time']);
                }
                if (isset($item['navigationPath'])) {
                    $item['navigationPath'] = $this->convertNavigationPathArrows($item['navigationPath'], $locale);
                }
                return $item;
            })->values()->all();
            
            return \DataTables::of($formatedData)
                ->filter(function() {
                })
                ->addIndexColumn()
                ->make(true);
            
        } catch (\Exception $exception) {
            Log::error('Error in navigationAccessLogs', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
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
            $input = $request->all();
            $locale = app()->getLocale() ?: 'en';
            $tenantUuid = $input['tenant_uuid'] ?? 'local';
            
            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'fromDate' => $input['fromDate'] ?? null,
                'companyId' => $input['companyId'] ?? null,
                'toDate' => $input['toDate'] ?? null,
                'employeeId' => $input['employeeId'] ?? null,
                'accessType' => $input['accessType'] ?? null,
                'start' => 0,
                'length' => 10000,
                'search' => $input['search'] ?? [],
            ];
            
            $result = $this->victoriaLogsService->getNavigationAccessLogs($params);
            $formatedData = $result['data'] ?? [];
            
            if (empty($formatedData)) {
                return $this->sendError(trans('custom.no_navigation_access_logs_found'), 404);
            }
            
            $formatedData = collect($formatedData)->map(function ($item) use ($locale) {
                if (isset($item['date_time'])) {
                    $item['date_time'] = $this->formatDateTime($item['date_time']);
                }
                if (isset($item['navigationPath'])) {
                    $item['navigationPath'] = $this->convertNavigationPathArrows($item['navigationPath'], $locale);
                }
                return $item;
            })->all();
            
            $requestFromDate = $request->input('fromDate');
            $requestToDate = $request->input('toDate');
            
            if (app()->getLocale() == 'ar') {
                $formatedData = collect($formatedData)->map(function ($item) {
                    if (isset($item['date_time'])) {
                        $item['date_time'] = $this->convertDateTimeToRTL($item['date_time']);
                    }
                    return $item;
                })->all();
            }

            $reportData = [
                'data' => $formatedData,
                'fromDate' => $requestFromDate,
                'toDate' => $requestToDate,
            ];

            $fileName = trans('custom.navigation_access_logs');

            $lang = app()->getLocale();
            $fontFamily = Helper::getExcelFontFamily($lang);
            $isRtl = $lang === 'ar';

            return Excel::download(
                new BladeViewExcelExport('export_report.navigation_access_logs', $reportData, $fontFamily, $isRtl),
                $fileName . '.xlsx'
            );

        } catch (\Exception $exception) {
            return $this->sendError($exception->getMessage());
        }
    }


    /**
     * Create an audit log entry
     * This endpoint allows Portal_BackEnd to create audit logs through Gears_BackEnd
     *
     * @param Request $request
     * @return Response
     */
    public function createAuditLog(Request $request)
    {
        try {
            $input = $request->all();

            $requiredFields = ['dataBase', 'transactionID', 'tenant_uuid', 'table', 'narration', 'crudType'];
            foreach ($requiredFields as $field) {
                if (!isset($input[$field])) {
                    \Log::error('createAuditLog missing required field', ['field' => $field, 'input' => $input]);
                    return $this->sendError("Missing required field: {$field}", 400);
                }
            }

            $dataBase = $input['dataBase'];
            $transactionID = $input['transactionID'];
            $tenant_uuid = $input['tenant_uuid'];
            $table = $input['table'];
            $narration = $input['narration'];
            $crudType = $input['crudType'];
            $newValue = $input['newValue'] ?? [];
            $previosValue = $input['previosValue'] ?? [];
            $parentID = $input['parentID'] ?? null;
            $parentTable = $input['parentTable'] ?? null;
            $empID = $input['empID'] ?? null;

            $this->auditLog(
                $dataBase,
                $transactionID,
                $tenant_uuid,
                $table,
                $narration,
                $crudType,
                $newValue,
                $previosValue,
                $parentID,
                $parentTable,
                $empID
            );

            return $this->sendResponse(['success' => true], 'Audit log created successfully');
        } catch (\Exception $exception) {
            \Log::error('createAuditLog exception', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString()
            ]);
            return $this->sendError($exception->getMessage());
        }
    }

    public function auditReportFilters(Request $request)
    {
        $companyID = $request->get('companyID');
        $languageCode = app()->getLocale() ?? 'en';
        $isArabic = ($languageCode === 'ar');
        
        // Get navigation menus filtered by isPortalYN = 0
        $navigationMenus = DB::table('srp_erp_navigationmenus')
            ->select(DB::raw('srp_erp_navigationmenus.*, srp_erp_navigationmenus_languages.description as secondaryLanguageDescription'))
            ->leftJoin('srp_erp_navigationmenus_languages', function ($join) use ($languageCode) {
                $join->on('srp_erp_navigationmenus.navigationMenuID', '=', 'srp_erp_navigationmenus_languages.navigationMenuID')
                    ->where('srp_erp_navigationmenus_languages.languageCode', '=', $languageCode);
            })
            ->where('srp_erp_navigationmenus.isPortalYN', '=', 0)
            ->orderBy('srp_erp_navigationmenus.sortOrder')
            ->get();

        // Build navigation map for quick lookup
        $navMap = [];
        foreach ($navigationMenus as $nav) {
            $navMap[$nav->navigationMenuID] = $nav;
        }

        // Build tree paths for leaf nodes (nodes without children)
        $leafNodes = [];
        foreach ($navigationMenus as $nav) {
            // Check if this is a leaf node (no children)
            $hasChildren = false;
            foreach ($navigationMenus as $child) {
                if ($child->masterID == $nav->navigationMenuID) {
                    $hasChildren = true;
                    break;
                }
            }
            
            if (!$hasChildren) {
                $leafNodes[] = $nav;
            }
        }

        // Build paths for each leaf node
        $paths = [];
        foreach ($leafNodes as $leaf) {
            $path = [];
            $current = $leaf;
            
            // Traverse up the tree to build the path
            while ($current) {
                // Use description for English, secondaryLanguageDescription for Arabic/other languages
                if ($languageCode === 'en') {
                    $label = $current->description;
                } else {
                    // For Arabic and other languages, prefer secondaryLanguageDescription, fallback to description
                    $label = $current->secondaryLanguageDescription ?: $current->description;
                }
                
                if ($label) {
                    $path[] = $label;
                }
                
                // Move to parent
                if ($current->masterID && isset($navMap[$current->masterID])) {
                    $current = $navMap[$current->masterID];
                } else {
                    $current = null;
                }
            }
            
            // Reverse path to get root-to-leaf order
            $path = array_reverse($path);
            
            if (!empty($path)) {
                // Join with arrow separator (RTL-aware for Arabic)
                $separator = $isArabic ? ' ← ' : ' → ';
                $pathString = implode($separator, $path);
                
                $paths[] = [
                    'navigationMenuID' => $leaf->navigationMenuID,
                    'description' => $pathString,
                ];
            }
        }

        usort($paths, function($a, $b) {
            return strcmp($a['description'], $b['description']);
        });

        return $this->sendResponse($paths, trans('custom.retrieve', ['attribute' => trans('custom.record')]));
    }


    /**
     * Helper method to fetch user audit logs for employee activity report
     *
     * @param Request $request
     * @return array
     */
    protected function fetchUserAuditLogs(Request $request)
    {
        $input = $request->all();
        $locale = $request->get('locale', app()->getLocale()) ?: 'en';
        $tenantUuid = $input['tenant_uuid'] ?? 'local';
        
        $params = [
            'tenant_uuid' => $tenantUuid,
            'locale' => $locale,
            'fromDate' => $input['fromDate'] ?? null,
            'toDate' => $input['toDate'] ?? null,
            'employeeId' => $input['employeeId'] ?? null,
            'event' => null,
            'search' => [],
        ];
        
        $result = $this->victoriaLogsService->getUserAuditLogs($params);
        return $result['data'] ?? [];
    }

    /**
     * Helper method to fetch navigation access logs for employee activity report
     *
     * @param Request $request
     * @return array
     */
    protected function fetchNavigationAccessLogs(Request $request)
    {
        $input = $request->all();
        $locale = $request->get('locale', app()->getLocale()) ?: 'en';
        $tenantUuid = $input['tenant_uuid'] ?? 'local';
        
        $params = [
            'tenant_uuid' => $tenantUuid,
            'locale' => $locale,
            'fromDate' => $input['fromDate'] ?? null,
            'toDate' => $input['toDate'] ?? null,
            'employeeId' => $input['employeeId'] ?? null,
            'accessType' => null, // Fetch all access types
            'search' => [],
        ];
        
        // Limit is configured in config/victorialogs.php
        $result = $this->victoriaLogsService->getNavigationAccessLogs($params);
        return $result['data'] ?? [];
    }

    public function employeeActivityAuditReport(EmployeeActivityAuditReportAPIRequest $request)
    {
        try {
            $input = $request->validated();
            $input['locale'] = app()->getLocale() ?: 'en';
            if (empty($input['tenant_uuid'])) {
                $input['tenant_uuid'] = $request->input('tenant_uuid', 'local');
            }

            $columns = $this->employeeAuditReportService->resolveColumns($input['columns'] ?? []);
            $result = $this->employeeAuditReportService->getPaginatedUnifiedRows($input);
            $locale = $input['locale'];

            $formatted = collect($result['rows'])->map(function (array $row) use ($locale) {
                return $this->formatUnifiedEmployeeActivityRow($row, $locale);
            })->values()->all();

            return $this->sendResponse([
                'data' => $formatted,
                'recordsTotal' => $result['total'],
                'recordsFiltered' => $result['total'],
                'columns' => $columns,
            ], trans('custom.retrieve', ['attribute' => trans('custom.record')]));
        } catch (\Exception $exception) {
            Log::error('Error in employeeActivityAuditReport', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return $this->sendError($exception->getMessage());
        }
    }

    public function exportEmployeeActivityAuditReport(EmployeeActivityAuditReportAPIRequest $request)
    {
        try {
            $input = $request->validated();
            $input['locale'] = app()->getLocale() ?: 'en';
            if (empty($input['tenant_uuid'])) {
                $input['tenant_uuid'] = $request->input('tenant_uuid', 'local');
            }

            $columns = $this->employeeAuditReportService->resolveColumns($input['columns'] ?? []);
            $merged = $this->employeeAuditReportService->buildMergedRows($input);

            if ($merged === []) {
                return $this->sendError(trans('custom.no_employee_activity_logs_found'), 404);
            }

            $locale = $input['locale'];
            $formatted = collect($merged)->map(function (array $row) use ($locale) {
                return $this->formatUnifiedEmployeeActivityRow($row, $locale);
            })->all();

            $data = collect($formatted)->map(function (array $row) use ($columns) {
                return Arr::only($row, $columns);
            })->all();

            if (app()->getLocale() === 'ar') {
                $data = collect($data)->map(function (array $row) {
                    foreach (['amendedDateTime', 'loginTs', 'logoutTs'] as $k) {
                        if (! empty($row[$k])) {
                            $row[$k] = $this->convertDateTimeToRTL($row[$k]);
                        }
                    }

                    return $row;
                })->all();
            }

            $reportData = [
                'data' => $data,
                'fromDate' => $input['fromDate'],
                'toDate' => $input['toDate'],
                'selectedColumns' => $columns,
            ];

            $fileName = trans('custom.employee_activity_audit_report');
            $lang = app()->getLocale();
            $fontFamily = Helper::getExcelFontFamily($lang);
            $isRtl = $lang === 'ar';

            return Excel::download(
                new BladeViewExcelExport('export_report.employee_activity_audit_report', $reportData, $fontFamily, $isRtl),
                $fileName.'.xlsx'
            );
        } catch (\Exception $exception) {
            Log::error('Error in exportEmployeeActivityAuditReport', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);

            return $this->sendError($exception->getMessage());
        }
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>
     */
    private function formatUnifiedEmployeeActivityRow(array $row, string $locale): array
    {
        foreach (['amendedDateTime', 'loginTs', 'logoutTs'] as $key) {
            if (! empty($row[$key])) {
                $row[$key] = $this->formatDateTime($row[$key]);
            }
        }

        if (! empty($row['navigationPath'])) {
            $row['navigationPath'] = $this->convertNavigationPathArrows($row['navigationPath'], $locale);
        }

        return $row;
    }

    /**
     * Get third-party API log detail by external reference
     *
     * @param Request $request
     * @return Response
     */
    public function getThirdPartyApiLogDetail(Request $request)
    {
        $input = $request->all();

        try {
            $externalReference = $input['external_reference'] ?? null;
            $logId = $input['logId'] ?? null;
            $tenantUuid = $input['tenant_uuid'] ?? 'local';
            $isWebhook = (string) ($input['is_webhook'] ?? '0');
            $locale = app()->getLocale() ?: 'en';

            if (!$externalReference) {
                return $this->sendError('External reference is required');
            }

            $params = [
                'tenant_uuid' => $tenantUuid,
                'locale' => $locale,
                'external_reference' => $externalReference,
                'logId' => $logId,
                'is_webhook' => $isWebhook,
            ];

            $result = $this->victoriaLogsService->getThirdPartyApiLogs($params);
            $logs = $result['data'] ?? [];

            if (empty($logs)) {
                return $this->sendError('No logs found for the given criteria');
            }

            $formatedData = [];

            foreach ($logs as $log) {
                $lineData = $log;
                $lineData['raw_data'] = isset($log['data']) ? $log['data'] : '';
                $parsedData = isset($log['data']) ? (is_string($log['data']) ? json_decode($log['data'], true) : $log['data']) : [];
                
                // Normalize webhook log structure to match regular API log structure
                if (isset($parsedData['request_payload']['webhook']) && $parsedData['request_payload']['webhook'] === true) {
                    // This is a webhook log - normalize the structure
                    $normalizedPayload = $parsedData;
                    
                    // Convert webhook payload.payload to body for frontend compatibility
                    if (isset($parsedData['request_payload']['payload'])) {
                        $normalizedPayload['request_payload']['body'] = $parsedData['request_payload']['payload'];
                    }
                    
                    // Add missing fields that frontend expects (with webhook-appropriate values)
                    if (!isset($normalizedPayload['request_payload']['ip'])) {
                        $normalizedPayload['request_payload']['ip'] = 'N/A (Webhook)';
                    }
                    if (!isset($normalizedPayload['request_payload']['user_agent'])) {
                        $normalizedPayload['request_payload']['user_agent'] = 'ERP-Webhook-Service/1.0';
                    }
                    
                    // Add webhook-specific metadata for display
                    $normalizedPayload['is_webhook'] = true;
                    $normalizedPayload['original_external_reference'] = $parsedData['request_payload']['original_external_reference'] ?? null;
                    
                    $lineData['parsed_data'] = $normalizedPayload;
                } else {
                    // Regular API log - use as is
                    $lineData['parsed_data'] = $parsedData;
                }
                
                $formatedData[] = $lineData;
            }

            // Sort by date_time descending and return the most recent
            $formatedData = collect($formatedData)->sortByDesc(function ($item) {
                return $item['date_time'] ?? $item['_time'] ?? '';
            });

            return $this->sendResponse($formatedData->first(), 'Detailed log retrieved successfully');
        } catch (\Exception $exception) {
            Log::error('Error in getThirdPartyApiLogDetail', [
                'error' => $exception->getMessage(),
                'trace' => $exception->getTraceAsString(),
            ]);
            return $this->sendError($exception->getMessage());
        }
    }
}
