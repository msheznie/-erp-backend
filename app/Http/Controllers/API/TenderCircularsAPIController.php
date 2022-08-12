<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateTenderCircularsAPIRequest;
use App\Http\Requests\API\UpdateTenderCircularsAPIRequest;
use App\Models\DocumentAttachments;
use App\Models\TenderCirculars;
use App\Repositories\TenderCircularsRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class TenderCircularsController
 * @package App\Http\Controllers\API
 */

class TenderCircularsAPIController extends AppBaseController
{
    /** @var  TenderCircularsRepository */
    private $tenderCircularsRepository;

    public function __construct(TenderCircularsRepository $tenderCircularsRepo)
    {
        $this->tenderCircularsRepository = $tenderCircularsRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderCirculars",
     *      summary="Get a listing of the TenderCirculars.",
     *      tags={"TenderCirculars"},
     *      description="Get all TenderCirculars",
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
     *                  @SWG\Items(ref="#/definitions/TenderCirculars")
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
        $this->tenderCircularsRepository->pushCriteria(new RequestCriteria($request));
        $this->tenderCircularsRepository->pushCriteria(new LimitOffsetCriteria($request));
        $tenderCirculars = $this->tenderCircularsRepository->all();

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars retrieved successfully');
    }

    /**
     * @param CreateTenderCircularsAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/tenderCirculars",
     *      summary="Store a newly created TenderCirculars in storage",
     *      tags={"TenderCirculars"},
     *      description="Store TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderCirculars that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderCirculars")
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
     *                  ref="#/definitions/TenderCirculars"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateTenderCircularsAPIRequest $request)
    {
        $input = $request->all();

        $tenderCirculars = $this->tenderCircularsRepository->create($input);

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/tenderCirculars/{id}",
     *      summary="Display the specified TenderCirculars",
     *      tags={"TenderCirculars"},
     *      description="Get TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
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
     *                  ref="#/definitions/TenderCirculars"
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
        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        return $this->sendResponse($tenderCirculars->toArray(), 'Tender Circulars retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateTenderCircularsAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/tenderCirculars/{id}",
     *      summary="Update the specified TenderCirculars in storage",
     *      tags={"TenderCirculars"},
     *      description="Update TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="TenderCirculars that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/TenderCirculars")
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
     *                  ref="#/definitions/TenderCirculars"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateTenderCircularsAPIRequest $request)
    {
        $input = $request->all();

        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        $tenderCirculars = $this->tenderCircularsRepository->update($input, $id);

        return $this->sendResponse($tenderCirculars->toArray(), 'TenderCirculars updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/tenderCirculars/{id}",
     *      summary="Remove the specified TenderCirculars from storage",
     *      tags={"TenderCirculars"},
     *      description="Delete TenderCirculars",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of TenderCirculars",
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
        /** @var TenderCirculars $tenderCirculars */
        $tenderCirculars = $this->tenderCircularsRepository->findWithoutFail($id);

        if (empty($tenderCirculars)) {
            return $this->sendError('Tender Circulars not found');
        }

        $tenderCirculars->delete();

        return $this->sendSuccess('Tender Circulars deleted successfully');
    }

    public function getTenderCircularList(Request $request)
    {
        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $companyId = $request['companyId'];
        $tender_id = $input['tender_id'];



        $tenderMaster = TenderCirculars::with(['document_attachments'])->where('tender_id', $tender_id)->where('company_id', $companyId);

        $search = $request->input('search.value');
        if ($search) {
            $tenderMaster = $tenderMaster->where(function ($query) use ($search) {
                $query->orWhere('circular_name', 'LIKE', "%{$search}%");
                $query->orWhere('description', 'LIKE', "%{$search}%");
            });
        }


        return \DataTables::eloquent($tenderMaster)
            ->order(function ($query) use ($input) {
                if (request()->has('order')) {
                    if ($input['order'][0]['column'] == 0) {
                        $query->orderBy('id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getAttachmentDropCircular(Request $request)
    {
        $input = $request->all();
        $attachment = TenderCirculars::where('tender_id',$input['tenderMasterId'])->get();
        $attchArray = array();
        if(count($attachment) > 0){
            $attchArray = $attachment->pluck('attachment_id');
            $attchArray = $attchArray->filter();
        }

        $data['attachmentDrop'] = DocumentAttachments::whereNotIn('attachmentID',$attchArray)
            ->where('documentSystemID',108)
            ->where('attachmentType',3)
            ->where('parent_id', null)
            ->where('documentSystemCode',$input['tenderMasterId'])->get();

        if(isset($input['circularId']) && $input['circularId'] > 0){
           $circular = TenderCirculars::where('id',$input['circularId'])->first();
           if($circular['attachment_id']>0){
               $attachment = DocumentAttachments::where('attachmentID',$circular['attachment_id'])->first();
               $data['attachmentDrop'][] = $attachment;
           }

        }

        return $data;
    }

    public function addCircular(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($request->all(), array('attachment_id'));

        if(!isset($input['description']) && !isset($input['attachment_id'])){
            return ['success' => false, 'message' => 'Description or Attachment is required'];
        }

        if(isset($input['id'])) {
            $exist = TenderCirculars::where('id','!=',$input['id'])->where('tender_id', $input['tenderMasterId'])->where('circular_name', $input['circular_name'])->where('company_id', $input['companySystemID'])->first();

            if(!empty($exist)){
                return ['success' => false, 'message' => 'Circular name can not be duplicated'];
            }
        }else{
            $exist = TenderCirculars::where('circular_name', $input['circular_name'])->where('tender_id', $input['tenderMasterId'])->where('company_id', $input['companySystemID'])->first();

            if(!empty($exist)){
                return ['success' => false, 'message' => 'Circular name can not be duplicated'];
            }
        }

        if(isset($input['attachment_id'])){
            if(isset($input['id'])) {
                $exist = TenderCirculars::where('id','!=',$input['id'])->where('tender_id', $input['tenderMasterId'])->where('attachment_id', $input['attachment_id'])->where('company_id', $input['companySystemID'])->first();

                if(!empty($exist)){
                    return ['success' => false, 'message' => 'Selected Attachment has been used in a different circular'];
                }
            }else{
               $exist = TenderCirculars::where('attachment_id', $input['attachment_id'])->where('tender_id', $input['tenderMasterId'])->where('company_id', $input['companySystemID'])->first();

                if(!empty($exist)){
                    return ['success' => false, 'message' => 'Selected Attachment has been used in a different circular'];
                }
            }
        }

        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $data['tender_id']=$input['tenderMasterId'];
            $data['circular_name']=$input['circular_name'];
            if(isset($input['description'])){
                $data['description']=$input['description'];
            }else{
                $data['description']=null;
            }
            if(isset($input['attachment_id'])){
                $data['attachment_id']=$input['attachment_id'];
            }else{
                $data['attachment_id']=null;
            }
            $data['company_id']=$input['companySystemID'];

            if(isset($input['id'])){
                $data['updated_by'] = $employee->employeeSystemID;
                $data['updated_at'] = Carbon::now();
                $result = TenderCirculars::where('id',$input['id'])->update($data);
                if($result){
                    DB::commit();
                    return ['success' => true, 'message' => 'Successfully updated', 'data' => $result];
                }
            }else{
                $data['created_by'] = $employee->employeeSystemID;
                $data['created_at'] = Carbon::now();
                $result = TenderCirculars::create($data);
                if($result){
                    DB::commit();
                    return ['success' => true, 'message' => 'Successfully saved', 'data' => $result];
                }
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }

    public function getCircularMaster(Request $request)
    {
        $input = $request->all();
        return TenderCirculars::where('id',$input['id'])->first();
    }

    public function deleteTenderCircular(Request $request)
    {
        $input = $request->all();
        DB::beginTransaction();
        try {
            $result = TenderCirculars::where('id',$input['id'])->delete();
            if($result){
                DB::commit();
                return ['success' => true, 'message' => 'Successfully deleted', 'data' => $result];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }

    }

    public function tenderCircularPublish(Request $request)
    {
        $input = $request->all();
        $employee = \Helper::getEmployeeInfo();
        DB::beginTransaction();
        try {
            $att['updated_by'] = $employee->employeeSystemID;
            $att['status'] = 1;
            $result = TenderCirculars::where('id', $input['id'])->update($att);

            if ($result) {
                DB::commit();
                return ['success' => true, 'message' => 'Successfully Published'];
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::error($this->failed($e));
            return ['success' => false, 'message' => $e];
        }
    }
}
