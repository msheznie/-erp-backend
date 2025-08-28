<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyDepartmentSegmentAPIRequest;
use App\Http\Requests\API\UpdateCompanyDepartmentSegmentAPIRequest;
use App\Models\CompanyDepartmentSegment;
use App\Models\SegmentMaster;
use App\Models\SegmentAssigned;
use App\Repositories\CompanyDepartmentSegmentRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\AuditLogsTrait;

class CompanyDepartmentSegmentAPIController extends AppBaseController
{
    use AuditLogsTrait;

    private $companyDepartmentSegmentRepository;

    public function __construct(CompanyDepartmentSegmentRepository $companyDepartmentSegmentRepo)
    {
        $this->companyDepartmentSegmentRepository = $companyDepartmentSegmentRepo;
    }

    /**
     * Get all department segments for DataTables
     */
    public function getAllDepartmentSegments(Request $request)
    {
        $input = $request->all();

        $departmentSystemID = $request->get('departmentSystemID');
        
        if (!$departmentSystemID) {
            return $this->sendError('Department ID is required');
        }

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $query = CompanyDepartmentSegment::where('departmentSystemID', $departmentSystemID)
                 ->with(['segment', 'department'])
                 ->orderBy('departmentSegmentSystemID', $sort);

        $search = $request->input('search.value');

        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $query = $query->whereHas('segment', function ($query) use ($search) {
                $query->where('ServiceLineCode', 'LIKE', "%{$search}%")
                    ->orWhere('ServiceLineDes', 'LIKE', "%{$search}%");
            });
        }
        else {
            $query = $query->get();
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('segmentCode', function ($departmentSegment) {
                return $departmentSegment->segment ? $departmentSegment->segment->ServiceLineCode : '';
            })
            ->addColumn('segmentName', function ($departmentSegment) {
                return $departmentSegment->segment ? $departmentSegment->segment->ServiceLineDes : '';
            })
            ->addColumn('activeStatus', function ($departmentSegment) {
                return $departmentSegment->isActive == 1 ? 'Active' : 'Inactive';
            })
            ->make(true);
    }

    /**
     * Get form data for segment assignment
     */
    public function getDepartmentSegmentFormData(Request $request)
    {
        $companySystemID = $request->get('companySystemID');
        
        if (!$companySystemID) {
            return $this->sendError('Company ID is required');
        }

        // Get final segments that are approved and assigned to the company
        $segments = SegmentMaster::where('isFinalLevel', 1)
                                ->where('isDeleted', 0)
                                ->where('isActive', 1)
                                ->where('approved_yn', 1)
                                ->whereExists(function ($query) use ($companySystemID) {
                                    $query->select(DB::raw(1))
                                          ->from('service_line_assigned')
                                          ->whereRaw('service_line_assigned.serviceLineSystemID = serviceline.serviceLineSystemID')
                                          ->where('service_line_assigned.companySystemID', $companySystemID)
                                          ->where('service_line_assigned.isActive', 1)
                                          ->where('service_line_assigned.isAssigned', 1);
                                })
                                ->get(['serviceLineSystemID', 'ServiceLineCode', 'ServiceLineDes']);

        return $this->sendResponse([
            'segments' => $segments
        ], 'Form data retrieved successfully');
    }

    /**
     * Store newly created department segments
     */
    public function store(CreateCompanyDepartmentSegmentAPIRequest $request)
    {
        $input = $request->all();

        try {
            DB::beginTransaction();

            if (isset($input['segments']) && is_array($input['segments'])) {
                $results = [];
                $errorMessages = [];

                foreach ($input['segments'] as $segmentData) {
                    $processedData = $this->processUpdateData($segmentData);

                    // Check if segment is already assigned to this department
                    $exists = CompanyDepartmentSegment::where('departmentSystemID', $processedData['departmentSystemID'])
                                                    ->where('serviceLineSystemID', $processedData['serviceLineSystemID'])
                                                    ->exists();
                    if ($exists) {
                        $segmentCode = SegmentMaster::getSegmentCode($processedData['serviceLineSystemID']);
                        $errorMessages[] = 'Segment ' . $segmentCode . ' is already assigned to this department';
                        continue;
                    }

                    $companyDepartmentSegment = $this->companyDepartmentSegmentRepository->create($processedData);
                    
                    $uuid = $request->get('tenant_uuid', 'local');
                    $db = $request->get('db', '');
                    $this->auditLog($db, $companyDepartmentSegment->departmentSegmentSystemID, $uuid, "company_departments_segments", "Segment assigned to department", "C", $companyDepartmentSegment->toArray(), [], $processedData['departmentSystemID'], 'company_departments');
                    
                    $results[] = $companyDepartmentSegment;
                }

                
                if (!empty($errorMessages)) {
                    DB::rollback();
                    return $this->sendError('Some segments could not be assigned: ' . implode(', ', $errorMessages));
                }
                
                DB::commit();
                return $this->sendResponse($results, count($results) . ' segment(s) assigned to department successfully');
            } else {
                // Handle single segment assignment (backward compatibility)
                $processedData = $this->processUpdateData($input);
                $companyDepartmentSegment = $this->companyDepartmentSegmentRepository->create($processedData);
                
                $uuid = $request->get('tenant_uuid', 'local');
                $db = $request->get('db', '');
                $this->auditLog($db, $companyDepartmentSegment->departmentSegmentSystemID, $uuid, "company_departments_segments", "Segment assigned to department", "C", $companyDepartmentSegment->toArray(), [], $processedData['departmentSystemID'], 'company_departments');
                
                DB::commit();
                return $this->sendResponse($companyDepartmentSegment->toArray(), 'Segment assigned to department successfully');
            }

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error assigning segment to department - '.$e->getMessage());
        }
    }

    /**
     * Update the specified department segment
     */
    public function update($id, UpdateCompanyDepartmentSegmentAPIRequest $request)
    {
        $companyDepartmentSegment = $this->companyDepartmentSegmentRepository->find($id);

        if (empty($companyDepartmentSegment)) {
            return $this->sendError('Department Segment not found');
        }

        $input = $request->all();
        $oldValues = $companyDepartmentSegment->toArray();

        try {
            DB::beginTransaction();

            $processedData = $this->processUpdateData($input);
            $companyDepartmentSegment = $this->companyDepartmentSegmentRepository->update($processedData, $id);

            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog($db, $id, $uuid, "company_departments_segments", "Department segment assignment updated", "U", $companyDepartmentSegment->toArray(), $oldValues, $processedData['departmentSystemID'], 'company_departments');

            DB::commit();

            return $this->sendResponse($companyDepartmentSegment->toArray(), 'Department Segment updated successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error updating department segment', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified department segment
     */
    public function destroy($id, Request $request)
    {
        $companyDepartmentSegment = $this->companyDepartmentSegmentRepository->find($id);

        if (empty($companyDepartmentSegment)) {
            return $this->sendError('Department Segment not found');
        }

        try {
            DB::beginTransaction();

            $oldValues = $companyDepartmentSegment->toArray();
            $this->companyDepartmentSegmentRepository->delete($id);

            $uuid = $request->get('tenant_uuid', 'local');
            $db = $request->get('db', '');
            $this->auditLog($db, $id, $uuid, "company_departments_segments", "Segment removed from department", "D", [], $oldValues, $oldValues['departmentSystemID'], 'company_departments');

            DB::commit();

            return $this->sendResponse($id, 'Department Segment deleted successfully');

        } catch (\Exception $e) {
            DB::rollback();
            return $this->sendError('Error deleting department segment', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Process update data - handle array inputs and type casting
     */
    private function processUpdateData($input)
    {
        $allowedFields = [
            'departmentSystemID', 'serviceLineSystemID', 'isActive'
        ];
        
        $processedData = [];
        
        foreach ($allowedFields as $field) {
            if (isset($input[$field])) {
                $value = $input[$field];
                
                if (is_array($value)) {
                    $value = count($value) > 0 ? $value[0] : null;
                }
                
                switch ($field) {
                    case 'isActive':
                    case 'departmentSystemID':
                    case 'serviceLineSystemID':
                        $processedData[$field] = is_numeric($value) ? (int)$value : $value;
                        break;
                    default:
                        $processedData[$field] = $value;
                        break;
                }
            }
        }
        
        return $processedData;
    }
} 
