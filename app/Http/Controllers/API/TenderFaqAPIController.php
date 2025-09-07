<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderFaqAPIRequest;
use App\Http\Requests\API\UpdateTenderFaqAPIRequest;
use App\Models\TenderFaq;
use App\Repositories\TenderFaqRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Models\TenderMaster;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderFaqController
 * @package App\Http\Controllers\API
 */

class TenderFaqAPIController extends AppBaseController
{
    /** @var  TenderFaqRepository */
    private $tenderFaqRepository;

    public function __construct(TenderFaqRepository $tenderFaqRepo)
    {
        $this->tenderFaqRepository = $tenderFaqRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderFaqs",
     *      summary="Get a listing of the TenderFaqs.",
     *      tags={"TenderFaq"},
     *      description="Get all TenderFaqs",
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
     *                  @SWG\Items(ref="#/definitions/TenderFaq")
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
        $this->tenderFaqRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderFaqRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderFaqs = $this->tenderFaqRepository->all();

        return $this->sendResponse($tenderFaqs->toArray(), trans('custom.tender_faqs_retrieved_successfully'));
    }

    /**
     * @param CreateTenderFaqAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderFaqs",
     *      summary="Store a newly created TenderFaq in storage",
     *      tags={"TenderFaq"},
     *      description="Store TenderFaq",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderFaq that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderFaq")
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
     *                  ref="#/definitions/TenderFaq"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderFaqAPIRequest $request)
    {
        $input = $request->all();

        $tenderFaq = $this->tenderFaqRepository->create($input);

        return $this->sendResponse($tenderFaq->toArray(), trans('custom.tender_faq_saved_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderFaqs/{id}",
     *      summary="Display the specified TenderFaq",
     *      tags={"TenderFaq"},
     *      description="Get TenderFaq",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderFaq",
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
     *                  ref="#/definitions/TenderFaq"
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
        /** @var TenderFaq $tenderFaq */
        $tenderFaq = $this->tenderFaqRepository->findWithoutFail($id);

        if (empty($tenderFaq)) {
            return $this->sendError(trans('custom.tender_faq_not_found'));
        }

        return $this->sendResponse($tenderFaq->toArray(), trans('custom.tender_faq_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateTenderFaqAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderFaqs/{id}",
     *      summary="Update the specified TenderFaq in storage",
     *      tags={"TenderFaq"},
     *      description="Update TenderFaq",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderFaq",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderFaq that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderFaq")
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
     *                  ref="#/definitions/TenderFaq"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderFaqAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderFaq $tenderFaq */
        $tenderFaq = $this->tenderFaqRepository->findWithoutFail($id);

        if (empty($tenderFaq)) {
            return $this->sendError(trans('custom.tender_faq_not_found'));
        }

        $tenderFaq = $this->tenderFaqRepository->update($input, $id);

        return $this->sendResponse($tenderFaq->toArray(), trans('custom.tenderfaq_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderFaqs/{id}",
     *      summary="Remove the specified TenderFaq from storage",
     *      tags={"TenderFaq"},
     *      description="Delete TenderFaq",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderFaq",
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
        /** @var TenderFaq $tenderFaq */
        $tenderFaq = $this->tenderFaqRepository->findWithoutFail($id);

        if (empty($tenderFaq)) {
            return $this->sendError(trans('custom.tender_faq_not_found'));
        }

        $tenderFaq->delete();

        return $this->sendSuccess('Tender Faq deleted successfully');
    }
    public function createFaq(Request $request)
    { 
        $input = $this->convertArrayToSelectedValue($request->all(), array('tender_master_id'));
        //$input =$request->all();
        $date_time = Carbon::now();
        $employee = \Helper::getEmployeeInfo();
        $tenderMasterId = $input['tender_master_id'];
        $answer = $input['answer'];
        $companySystemID = $input['companySystemID'];
        $question = $input['question'];
        DB::beginTransaction();
        try {
            $data['tender_master_id'] = $tenderMasterId;
            $data['question'] = $question;
            $data['answer'] = $answer;
            $data['created_by'] = $employee->employeeSystemID;
            $data['company_id'] = $companySystemID;

            if ($input['autoID'] > 0) {
                $data['updated_by'] = $employee->employeeSystemID;
                $data['updated_at'] = $date_time;
                $result =  TenderFaq::where('id', $input['id'])
                    ->update($data);
            } else {
                $result = TenderFaq::create($data);
            }
            
            if ($result) {
                DB::commit();
                return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
    public function getFaqList(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companySystemID'];
        $tenderId = isset($input['tender']) ? $input['tender'] : 0;
        $data['tenderFaqList'] = TenderMaster::with(['tenderFaq' => function ($q) use ($companyId) {
            $q->where('company_id', $companyId);
            $q->with(['employee' => function ($q2) {
                $q2->with(['profilepic']);
            }]);
        }])->where('company_id', $companyId)
            ->whereHas('tenderFaq', function ($q) use ($companyId) {
                $q->where('company_id', '=', $companyId);
            })->when(($tenderId > 0), function ($query) use ($tenderId) {
                $query->where('id', $tenderId);
            })
            ->get();
        return $data;
    }
    public function getFaq(Request $request)
    {
        $input = $request->all();
        $companyId = $input['companyId'];
        $id = $input['id'];
        return TenderFaq::where('company_id', $companyId)
            ->where('id', $id)
            ->first();
    }
    public function deleteFaq(Request $request){ 
        $input = $request->all();
        $id = $input['id'];

        $tenderFaq = $this->tenderFaqRepository->findWithoutFail($id);

        if (empty($tenderFaq)) {
            return $this->sendError(trans('custom.not_found_1'));
        } 
        $tenderFaq->delete(); 
        return $this->sendResponse($id,trans('custom.file_deleted'));
    }
}
