<?php

namespace App\Repositories;

use App\helper\Helper;
use App\Models\PricingScheduleDetail;
use App\Models\PricingScheduleDetailEditLog;
use App\Models\TenderMainWorks;
use Illuminate\Container\Container as Application;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use InfyOm\Generator\Common\BaseRepository;
use App\Services\SrmDocumentModifyService;

/**
 * Class TenderMainWorksRepository
 * @package App\Repositories
 * @version April 6, 2022, 1:35 pm +04
 *
 * @method TenderMainWorks findWithoutFail($id, $columns = ['*'])
 * @method TenderMainWorks find($id, $columns = ['*'])
 * @method TenderMainWorks first($columns = ['*'])
*/
class TenderMainWorksRepository extends BaseRepository
{
    /**
     * @var array
     */
    protected $fieldSearchable = [
        'tender_id',
        'schedule_id',
        'item',
        'description',
        'created_by',
        'updated_by',
        'company_id'
    ];
    private $srmDocumentModifyService;
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
        return TenderMainWorks::class;
    }
    public function getMainWorkList(Request $request){
        $input = $request->all();
        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tender_id = $input['tender_id'];
        $schedule_id = $input['schedule_id'];

        $requestData = $this->srmDocumentModifyService->checkForEditOrAmendRequest($tender_id);
        $editOrAmend = $requestData['enableRequestChange'] ?? false;

        $mainWorks = $editOrAmend?
            PricingScheduleDetailEditLog::getPricingScheduleDetails($tender_id, $schedule_id, $companyId, $requestData['versionID']) :
            PricingScheduleDetail::getPricingScheduleDetailList($tender_id, $schedule_id, $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $mainWorks = $mainWorks->where(function ($query) use ($search) {
                $query->orWhere('item', 'LIKE', "%{$search}%");
                $query->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::eloquent($mainWorks)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->rawColumns(['description'])
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }
    public function updateWorkOrderDescription($input){
        try{
            return DB::transaction(function () use ($input) {
                $employee = Helper::getEmployeeInfo();
                $versionID = $input['versionID'] ?? 0;
                $editOrAmend = $versionID > 0;

                $pricingScheduleDetail = $editOrAmend && $versionID > 0 ?
                    PricingScheduleDetailEditLog::find($input['id']) :
                    PricingScheduleDetail::find($input['id']);
                if(empty($pricingScheduleDetail)){
                    return ['success' => false, 'message' => trans('srm_tender_rfx.pricing_schedule_detail_not_found')];
                }

                $data['description']=$input['description'];
                $data['updated_by'] = $employee->employeeSystemID;
                $pricingScheduleDetail->update($data);
                return ['success' => true, 'message' => trans('srm_tender_rfx.updated_successfully')];
            });
        } catch (\Exception $exception){
            return ['success' => false, 'message' => trans('srm_tender_rfx.unexpected_error', ['message' => $exception->getMessage()])];
        }
    }
}
