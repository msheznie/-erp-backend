<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\SupplierRegistrationLink;
use App\Models\TenderSupplierAssignee;
use App\Models\TenderSupplierAssigneeEditLog;
use Illuminate\Container\Container as Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Common\BaseRepository;
use App\Services\SrmDocumentModifyService;
use mysql_xdevapi\Exception;

/**
 * Class TenderSupplierAssigneeRepository
 * @package App\Repositories
 * @version June 2, 2022, 12:07 pm +04
 *
 * @method TenderSupplierAssignee findWithoutFail($id, $columns = ['*'])
 * @method TenderSupplierAssignee find($id, $columns = ['*'])
 * @method TenderSupplierAssignee first($columns = ['*'])
*/
class TenderSupplierAssigneeRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'company_id',
        'created_by',
        'registration_link_id',
        'supplier_assigned_id',
        'supplier_email',
        'supplier_name',
        'tender_master_id',
        'updated_by',
        'mail_sent'
    ];
    protected $srmDocumentModifyService;
    public function __construct(Application $app, SrmDocumentModifyService $srmDocumentModifyService)
    {
        parent::__construct($app);
        $this->srmDocumentModifyService = $srmDocumentModifyService;
    }

    /**
     * Configure the Model
     **/
    public function model()
    {
        return TenderSupplierAssignee::class;
    }

    public function deleteAllAssignedSuppliers($input) {
        try{
            return DB::transaction(function () use ($input) {
                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($input['tenderId']);
                if($requestData['enableRequestChange']){
                    TenderSupplierAssigneeEditLog::where('tender_master_id',$input['tenderId'])
                        ->where('company_id',$input['companySystemId'])
                        ->where('mail_sent',0)
                        ->whereNotIn('supplier_assigned_id', $input['removedSupplierAssignedIds'])
                        ->where('version_id', $requestData['versionID'])
                        ->where('is_deleted', 0)
                        ->update(['is_deleted' => 1]);
                } else {
                    TenderSupplierAssignee::where('tender_master_id',$input['tenderId'])
                        ->where('company_id',$input['companySystemId'])
                        ->where('mail_sent',0)
                        ->whereNotIn('supplier_assigned_id', $input['removedSupplierAssignedIds'])
                        ->delete();
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.deleted_successfully')];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }

    
    public function deleteAllSelectedSuppliers($input) {
        try{
            return DB::transaction(function () use ($input) {
                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($input['tenderId']);
                if($requestData['enableRequestChange']){
                    TenderSupplierAssigneeEditLog::where('tender_master_id', $input['tenderId'])
                        ->where('company_id', $input['companySystemId'])
                        ->where('version_id', $requestData['versionID'])
                        ->where('is_deleted', 0)
                        ->whereIn('amd_id', $input['deleteList'])
                        ->update(['is_deleted' => 1, 'updated_at' => now()]);
                } else {
                    TenderSupplierAssignee::where('tender_master_id',$input['tenderId'])->where('company_id',$input['companySystemId'])->whereIn('id',$input['deleteList'])->delete();
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.deleted_successfully')];
            });
        } catch (\Exception $ex) {
            return ['success' => false, 'message' => $ex->getMessage()];
        }
    }
    public function supplierAssignCRUD($input)
    {
        try{
            return DB::transaction(function () use ($input) {
                $name = $input['name'];
                $email = $input['email'];
                $regNo = $input['regNo'];
                $tenderId = $input['tenderId'];
                $companySystemID = $input['companySystemID'];
                $employee = Helper::getEmployeeInfo();
                $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tenderId);
                $validateFields = $this->validateFileds($input);

                if(!$validateFields['status']){
                    return ['success' => false, 'message' => $validateFields['message'], 'code' => $validateFields['code']];
                }

                $data = [
                    'tender_master_id' => $tenderId,
                    'supplier_name' => $name,
                    'supplier_email' => $email,
                    'registration_number' => $regNo,
                    'created_by' => $employee->employeeSystemID,
                    'company_id' => $companySystemID,
                    'created_at' => now()
                ];
                if($requestData['enableRequestChange']){
                    $data['id'] = null;
                    $data['level_no'] = 0;
                    $data['version_id'] = $requestData['versionID'];

                    $result = TenderSupplierAssigneeEditLog::create($data);
                } else {
                    $result = TenderSupplierAssignee::create($data);
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_saved'), 'data' => $result];
            });
        } catch(\Exception $ex){
            return ['success' => false, 'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $ex->getMessage()])];
        }
    }
    public function validateFileds($input){
        $validator = \Validator::make($input, [
            'email' => 'required|email|max:255',
            'name' => 'required|max:255',
            'regNo' => 'required|max:255',
        ],[
            'email.required' => trans('srm_tender_rfx.email_required'),
            'email.email'    => trans('srm_tender_rfx.email_invalid'),
            'email.max'      => trans('srm_tender_rfx.email_max'),
            'email.unique'   => trans('srm_tender_rfx.email_exists'),
            'name.required'  => trans('srm_tender_rfx.name_required'),
            'name.max'       => trans('srm_tender_rfx.name_max'),
            'regNo.required' => trans('srm_tender_rfx.regNo_required'),
            'regNo.max'      => trans('srm_tender_rfx.regNo_max'),
        ]);
        if ($validator->fails()) {
            return ['status' => false, 'message' => $validator->messages(), 'code' => 422];
        }


        $email = $input['email'];
        $regNo = $input['regNo'];
        $companyId =$input['companySystemID'];

        $supplierRegLink = SupplierRegistrationLink::select('id','email','registration_number')
            ->where('company_id',$companyId)
            ->where('STATUS',1)
            ->get();

        $emails = $supplierRegLink->pluck('email')->toArray();
        $registrationNumbers = $supplierRegLink->pluck('registration_number')->toArray();

        if (in_array($email, $emails)) {
            return ['status' => false, 'message' => trans('srm_tender_rfx.email_exists'),'code' => 402];
        }

        if (in_array($regNo, $registrationNumbers)) {
            return ['status' => false, 'message' => trans('srm_tender_rfx.regNo_exists'),'code' => 402];
        }


        return ['status' => true, 'message' => trans('srm_tender_rfx.success')];

    }
    public static function deleteAssignedUsers($id, $versionID, $editOrAmend){
        try{
            return DB::transaction(function () use ($id, $versionID, $editOrAmend) {
                $supplier = $editOrAmend ?
                    TenderSupplierAssigneeEditLog::find($id) :
                    TenderSupplierAssignee::find($id);

                if(empty($supplier)){
                    return ['success' => false, 'message' => trans('srm_tender_rfx.assigned_supplier_not_found')];
                }

                if($editOrAmend){
                    TenderSupplierAssigneeEditLog::where('amd_id', $id)->where('version_id', $versionID)->update(['is_deleted' => 1, 'updated_at' => now()]);
                } else {
                    TenderSupplierAssignee::where('id', $id)->delete();
                }
                return ['success' => true, 'message' => trans('srm_tender_rfx.successfully_deleted')];
            });
        } catch (\Exception $ex){
            return ['success' => false, 'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $ex->getMessage()])];
        }
    }
}
