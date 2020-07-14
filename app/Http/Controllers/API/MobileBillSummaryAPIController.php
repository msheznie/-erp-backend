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

        return $this->sendResponse($mobileBillSummaries->toArray(), 'Mobile Bill Summaries retrieved successfully');
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

        return $this->sendResponse($mobileBillSummary->toArray(), 'Mobile Bill Summary saved successfully');
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
            return $this->sendError('Mobile Bill Summary not found');
        }

        return $this->sendResponse($mobileBillSummary->toArray(), 'Mobile Bill Summary retrieved successfully');
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
            return $this->sendError('Mobile Bill Summary not found');
        }

        $mobileBillSummary = $this->mobileBillSummaryRepository->update($input, $id);

        return $this->sendResponse($mobileBillSummary->toArray(), 'MobileBillSummary updated successfully');
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
            return $this->sendError('Mobile Bill Summary not found');
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
        $path = time(). $extension;
        Storage::disk('local')->put($path, $decodeFile);

        $record = \Excel::selectSheetsByIndex(0)->load(Storage::disk('local')->url('app/' . $path), function ($reader) {})->get();
        $insert_data = [];
        if($record->count() > 0){

            if($input['type'] == 'summary') {
                $tableName = 'hrms_mobilebillsummary';
                foreach($record->toArray() as $key => $value){
                    if($value['mobilenumber'] == null || $value['mobilenumber'] == ''){
                        break;
                    }
                    $insert_data[] = array(// set summary array
                        'mobileMasterID'  => $input['mobilebillMasterID'],
                        'mobileNumber'  => $value['mobilenumber'],
                        'rental'  => $value['rental'],
                        'setUpFee'  => $value['setupfee'],
                        'localCharges'  => $value['localcharges'],
                        'internationalCallCharges'  => $value['internationalcallcharges'],
                        'domesticSMS'  => $value['domesticsms'],
                        'internationalSMS'  => $value['internationalsms'],
                        'domesticMMS'  => $value['domesticmms'],
                        'internationalMMS'  => $value['internationalmms'],
                        'discounts'  => $value['discounts'],
                        'otherCharges'  => $value['othercharges'],
                        'blackberryCharges'  => $value['blackberrycharges'],
                        'roamingCharges'  => $value['roamingcharges'],
                        'GPRSPayG'  => $value['gprspayg'],
                        'GPRSPKG'  => $value['gprspkg'],
                        'totalCurrentCharges'  => $value['totalcurrentcharges'],
                        'billDate'  => (\DateTime::createFromFormat('Y-m-d H:i:s', $value['billdate']) !== FALSE)? Carbon::parse($value['billdate'])->format('Y-m-d'):null,
                        'timestamp'  => Carbon::now()
                    );

                }
            }elseif ($input['type'] == 'detail') {
                $tableName = 'hrms_mobiledetail';

                $employee = Helper::getEmployeeInfo();
                $mobileMaster = MobileBillMaster::find($input['mobilebillMasterID']);
                $period = PeriodMaster::find($mobileMaster->billPeriod);
                foreach($record->toArray() as $key => $value){

                    if($value['mynumber'] == null || $value['mynumber'] == ''){
                        break;
                    }

                    $insert_data[] = array(// set detail array
                        'mobilebillMasterID' => $input['mobilebillMasterID'],
                        'billPeriod' => $mobileMaster->billPeriod,
                        'startDate' => $period->startDate,
                        'EndDate' => $period->endDate,
                        'myNumber' => $value['mynumber'],
                        'DestCountry' => $value['destcountry'],
                        'DestNumber' => $value['destnumber'],
                        'duration' => $value['duration'],
                        'callDate' => $value['calldate'],
                        'cost' => $value['cost'],
                        'currency' => $value['currency'],
                        'Narration' => $value['narration'],
                        'localCurrencyID' => $value['localcurrencyid'],
                        'localCurrencyER' => $value['localcurrencyer'],
                        'localAmount' => $value['localamount'],
                        'rptCurrencyID' => $value['rptcurrencyid'],
                        'rptCurrencyER' => $value['rptcurrencyer'],
                        'rptAmount' => $value['rptamount'],
                        'isOfficial' => $value['isofficial'],
                        'isIDD' => $value['isidd'],
                        'type' => $value['type'],
                        'userComments' => $value['usercomments'],
                        'createDate' => Carbon::now(),
                        'createUserID' => $employee->empID,
                        'createPCID' => gethostname(),
                        'modifiedpc' => gethostname(),
                        'modifiedUser' => $employee->empID,
                        'timestamp' => now()
                    );
                }
            }

            Storage::disk('local')->delete($path);
            $insert = DB::table($tableName)->insert($insert_data);
            if($insert){
                return $this->sendResponse([], 'File Imported successfully');
            }
        }

        return $this->sendError('Unable to upload', 500);
    }
}
