<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Requests\API\CreateMobileBillSummaryAPIRequest;
use App\Http\Requests\API\UpdateMobileBillSummaryAPIRequest;
use App\Models\MobileBillMaster;
use App\Models\MobileBillSummary;
use App\Models\PeriodMaster;
use App\Repositories\MobileBillSummaryRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Maatwebsite\Excel\Excel;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class MobileBillSummaryController
 * @package App\Http\Controllers\API
 */

class MobileBillSummaryAPIController extends AppBaseController
{
    /** @var  MobileBillSummaryRepository */
    private $mobileBillSummaryRepository;

    public function __construct(MobileBillSummaryRepository $mobileBillSummaryRepo)
    {
        $this->mobileBillSummaryRepository = $mobileBillSummaryRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileBillSummaries",
     *      summary="Get a listing of the MobileBillSummaries.",
     *      tags={"MobileBillSummary"},
     *      description="Get all MobileBillSummaries",
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
     *                  @SWG\Items(ref="#/definitions/MobileBillSummary")
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
        $this->mobileBillSummaryRepository->pushCriteria(new RequestCriteria($request));
        $this->mobileBillSummaryRepository->pushCriteria(new LimitOffsetCriteria($request));
        $mobileBillSummaries = $this->mobileBillSummaryRepository->all();

        return $this->sendResponse($mobileBillSummaries->toArray(), trans('custom.mobile_bill_summaries_retrieved_successfully'));
    }

    /**
     * @param CreateMobileBillSummaryAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/mobileBillSummaries",
     *      summary="Store a newly created MobileBillSummary in storage",
     *      tags={"MobileBillSummary"},
     *      description="Store MobileBillSummary",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileBillSummary that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileBillSummary")
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
     *                  ref="#/definitions/MobileBillSummary"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateMobileBillSummaryAPIRequest $request)
    {
        $input = $request->all();

        $mobileBillSummary = $this->mobileBillSummaryRepository->create($input);

        return $this->sendResponse($mobileBillSummary->toArray(), trans('custom.mobile_bill_summary_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/mobileBillSummaries/{id}",
     *      summary="Display the specified MobileBillSummary",
     *      tags={"MobileBillSummary"},
     *      description="Get MobileBillSummary",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileBillSummary",
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
     *                  ref="#/definitions/MobileBillSummary"
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
        /** @var MobileBillSummary $mobileBillSummary */
        $mobileBillSummary = $this->mobileBillSummaryRepository->findWithoutFail($id);

        if (empty($mobileBillSummary)) {
            return $this->sendError(trans('custom.mobile_bill_summary_not_found'));
        }

        return $this->sendResponse($mobileBillSummary->toArray(), trans('custom.mobile_bill_summary_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateMobileBillSummaryAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/mobileBillSummaries/{id}",
     *      summary="Update the specified MobileBillSummary in storage",
     *      tags={"MobileBillSummary"},
     *      description="Update MobileBillSummary",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileBillSummary",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="MobileBillSummary that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/MobileBillSummary")
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
     *                  ref="#/definitions/MobileBillSummary"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateMobileBillSummaryAPIRequest $request)
    {
        $input = $request->all();

        /** @var MobileBillSummary $mobileBillSummary */
        $mobileBillSummary = $this->mobileBillSummaryRepository->findWithoutFail($id);

        if (empty($mobileBillSummary)) {
            return $this->sendError(trans('custom.mobile_bill_summary_not_found'));
        }

        $mobileBillSummary = $this->mobileBillSummaryRepository->update($input, $id);

        return $this->sendResponse($mobileBillSummary->toArray(), trans('custom.mobilebillsummary_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/mobileBillSummaries/{id}",
     *      summary="Remove the specified MobileBillSummary from storage",
     *      tags={"MobileBillSummary"},
     *      description="Delete MobileBillSummary",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of MobileBillSummary",
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
        /** @var MobileBillSummary $mobileBillSummary */
        $mobileBillSummary = $this->mobileBillSummaryRepository->findWithoutFail($id);

        if (empty($mobileBillSummary)) {
            return $this->sendError(trans('custom.mobile_bill_summary_not_found'));
        }

        $mobileBillSummary->delete();

        return $this->sendSuccess('Mobile Bill Summary deleted successfully');
    }

    public function importMobileBillDocument(Request $request)
    {

        $input = $request->all();
        $extension = $input['fileType'];
        $allowExtensions = ['xlsx'];

        if (!in_array($extension, $allowExtensions))
        {
            return $this->sendError('This type of file not allow to upload.',500);
        }


        if(isset($input['size'])){
            if ($input['size'] > 31457280) {
                return $this->sendError("Maximum allowed file size is 30 MB. Please upload lesser than 30 MB.",500);
            }
        }

        $validator = \Validator::make($input, [
            'file' => 'required',
            'mobilebillMasterID' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $file = $request->request->get('file');
        $decodeFile = base64_decode($file);
        $path = time().'.'.$extension;
        Storage::disk('local')->put($path, $decodeFile);
        $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk('local')->url('app/' . $path), function ($reader) {})->get();
        $insert_data = [];

        if($record->count() > 0){

            if($input['type'] == 'summary') {
                $tableName = 'hrms_mobilebillsummary';
                foreach($record as $value){

                    if (!$value->filter()->isEmpty()) {
                        // do something
                        $insert_data[] = array(// set summary array
                            'mobileMasterID' => $input['mobilebillMasterID'],
                            'mobileNumber' => isset($value['mobilenumber'])?$value['mobilenumber']:'',
                            'rental' => isset($value['rental'])?$value['rental']:'',
                            'setUpFee' => isset($value['setupfee'])?$value['setupfee']:'',
                            'localCharges' => isset($value['localcharges'])?$value['localcharges']:'',
                            'internationalCallCharges' => isset($value['internationalcallcharges'])?$value['internationalcallcharges']:'',
                            'domesticSMS' => isset($value['domesticsms'])?$value['domesticsms']:'',
                            'internationalSMS' => isset($value['internationalsms'])?$value['internationalsms']:'',
                            'domesticMMS' => isset($value['domesticmms'])?$value['domesticmms']:'',
                            'internationalMMS' => isset($value['internationalmms'])?$value['internationalmms']:'',
                            'discounts' => isset($value['discounts'])?$value['discounts']:'',
                            'otherCharges' => isset($value['othercharges'])?$value['othercharges']:'',
                            'blackberryCharges' => isset($value['blackberrycharges'])?$value['blackberrycharges']:'',
                            'roamingCharges' => isset($value['roamingcharges'])?$value['roamingcharges']:'',
                            'GPRSPayG' => isset($value['gprspayg'])?$value['gprspayg']:'',
                            'GPRSPKG' => isset($value['gprspkg'])?$value['gprspkg']:'',
                            'totalCurrentCharges' => isset($value['totalcurrentcharges'])?$value['totalcurrentcharges']:'',
                            'billDate' => isset($value['billdate'])?$value['billdate']:'',
                            'timestamp' => Carbon::now()
                        );
                    }

                }
            }elseif ($input['type'] == 'detail') {
                $tableName = 'hrms_mobiledetail';

                $employee = Helper::getEmployeeInfo();
                $mobileMaster = MobileBillMaster::find($input['mobilebillMasterID']);
                $period = PeriodMaster::find($mobileMaster->billPeriod);
                foreach($record as $value){

                    if (!$value->filter()->isEmpty()) {
                        $insert_data[] = array(// set detail array
                            'mobilebillMasterID' => $input['mobilebillMasterID'],
                            'billPeriod' => isset($mobileMaster->billPeriod)?$mobileMaster->billPeriod:'',
                            'startDate' => isset($period->startDate)?$period->startDate:'',
                            'EndDate' => isset($period->endDate)?$period->endDate:'',
                            'myNumber' => isset($value['mynumber'])?$value['mynumber']:'',
                            'DestCountry' => isset($value['destcountry'])?$value['destcountry']:'',
                            'DestNumber' => isset($value['destnumber'])?$value['destnumber']:'',
                            'duration' => isset($value['duration']) ? $value['duration'] : '',
                            'callDate' => isset($value['calldate'])?$value['calldate']:'',
                            'cost' => isset($value['cost'])?$value['cost']:'',
                            'currency' => isset($value['currency'])?$value['currency']:'',
                            'Narration' => isset($value['narration'])?$value['narration']:'',
                            'localCurrencyID' => isset($value['localcurrencyid'])?$value['localcurrencyid']:'',
                            'localCurrencyER' => isset($value['localcurrencyer'])?$value['localcurrencyer']:'',
                            'localAmount' => isset($value['localamount'])?$value['localamount']:'',
                            'rptCurrencyID' => isset($value['rptcurrencyid'])?$value['rptcurrencyid']:'',
                            'rptCurrencyER' => isset($value['rptcurrencyer'])?$value['rptcurrencyer']:'',
                            'rptAmount' => isset($value['rptamount'])?$value['rptamount']:'',
                            'isOfficial' => isset($value['isofficial'])?$value['isofficial']:'',
                            'isIDD' => isset($value['isidd'])?$value['isidd']:'',
                            'type' => isset($value['type'])?$value['type']:'',
                            'userComments' => isset($value['usercomments'])?$value['usercomments']:'',
                            'createDate' => Carbon::now(),
                            'createUserID' => $employee->empID,
                            'createPCID' => gethostname(),
                            'modifiedpc' => gethostname(),
                            'modifiedUser' => $employee->empID,
                            'timestamp' => now()
                        );
                    }
                }
            }

            Storage::disk('local')->delete($path);
            $insert = DB::table($tableName)->insert($insert_data);
            if($insert){
                return $this->sendResponse([], trans('custom.file_imported_successfully'));
            }
        }

        return $this->sendError('Unable to upload', 500);
    }

    public function getAllMobileBillSummaries(Request $request){
        $input = $request->all();
        $id = isset($input['id'])?$input['id']:0;

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $mobileMaster = MobileBillSummary::where('mobileMasterID',$id)->with(['mobile_pool.mobile_master.employee']);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $mobileMaster = $mobileMaster->where(function ($query) use ($search) {
                $query->where('mobileNumber', 'LIKE', "%{$search}%")
                    ->orWhereHas('mobile_pool', function ($query) use ($search){
                        $query->whereHas('mobile_master', function ($q) use ($search){
                            $q->whereHas('employee', function ($q1) use ($search){
                                $q1->where('empID', 'LIKE', "%{$search}%")
                                    ->orWhere('empName', 'LIKE', "%{$search}%");
                            });
                        });
                    });
            });
        }

        return \DataTables::eloquent($mobileMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('mobileBillSummaryID', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function downloadSummaryTemplate(Request $request)
    {
        $input = $request->all();
        $disk = isset($input['companySystemID']) ? Helper::policyWiseDisk($input['companySystemID'], 'local_public') : 'local_public';
        if (Storage::disk($disk)->exists('mobile_bill_templates/summary_template.xlsx')) {
            return Storage::disk($disk)->download('mobile_bill_templates/summary_template.xlsx', 'summary_template.xlsx');
        } else {
            return $this->sendError(trans('custom.summary_template_not_found'), 500);
        }
    }
}
