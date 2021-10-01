<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChequeTemplateMasterAPIRequest;
use App\Http\Requests\API\UpdateChequeTemplateMasterAPIRequest;
use App\Models\ChequeTemplateMaster;
use App\Repositories\ChequeTemplateMasterRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use App\Models\ChequeTemplateBank;
use App\Criteria\FilterActiveRecordsCriteria;
use App\Criteria\FilterNonAssignedTemplateCriteria;
/**
 * Class ChequeTemplateMasterController
 * @package App\Http\Controllers\API
 */

class ChequeTemplateMasterAPIController extends AppBaseController
{
    /** @var  ChequeTemplateMasterRepository */
    private $chequeTemplateMasterRepository;

    public function __construct(ChequeTemplateMasterRepository $chequeTemplateMasterRepo)
    {
        $this->chequeTemplateMasterRepository = $chequeTemplateMasterRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeTemplateMasters",
     *      summary="Get a listing of the ChequeTemplateMasters.",
     *      tags={"ChequeTemplateMaster"},
     *      description="Get all ChequeTemplateMasters",
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
     *                  @SWG\Items(ref="#/definitions/ChequeTemplateMaster")
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
        $bank_id = $request->get('bank_id');
        $this->chequeTemplateMasterRepository->pushCriteria(new RequestCriteria($request));
        $this->chequeTemplateMasterRepository->pushCriteria(new LimitOffsetCriteria($request));
        $this->chequeTemplateMasterRepository->pushCriteria(new FilterActiveRecordsCriteria($request));
        $chequeTemplateMasters = $this->chequeTemplateMasterRepository->get()->toArray();

        $tr = false;

   

        foreach($chequeTemplateMasters as $key=>$val)
        {

      
            $template_banks = ChequeTemplateBank::get();
            foreach($template_banks as $template_bank)
            {
               if($template_bank->cheque_template_master_id == $val['id'] && $template_bank->bank_id == $bank_id)
               {
                unset($chequeTemplateMasters[$key]);
        
                // array_splice($chequeTemplateMasters, $key, 1);
                // break;
               
               }

            }
      
        }
        $data = [];
        foreach($chequeTemplateMasters as $chequeTemplateMaster)
        {
            array_push($data,$chequeTemplateMaster);

        }
      

        return $this->sendResponse($data, trans('custom.retrieve', ['attribute' => trans('custom.templates')]));
    }

    /**
     * @param CreateChequeTemplateMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chequeTemplateMasters",
     *      summary="Store a newly created ChequeTemplateMaster in storage",
     *      tags={"ChequeTemplateMaster"},
     * 
     *      description="Store ChequeTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeTemplateMaster that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeTemplateMaster")
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
     *                  ref="#/definitions/ChequeTemplateMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChequeTemplateMasterAPIRequest $request)
    {
        $input = $request->all();

        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->create($input);

        return $this->sendResponse($chequeTemplateMaster->toArray(), 'Cheque Template Master saved successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeTemplateMasters/{id}",
     *      summary="Display the specified ChequeTemplateMaster",
     *      tags={"ChequeTemplateMaster"},
     *      description="Get ChequeTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateMaster",
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
     *                  ref="#/definitions/ChequeTemplateMaster"
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
        /** @var ChequeTemplateMaster $chequeTemplateMaster */
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            return $this->sendError('Cheque Template Master not found');
        }

        return $this->sendResponse($chequeTemplateMaster->toArray(), 'Cheque Template Master retrieved successfully');
    }

    /**
     * @param int $id
     * @param UpdateChequeTemplateMasterAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chequeTemplateMasters/{id}",
     *      summary="Update the specified ChequeTemplateMaster in storage",
     *      tags={"ChequeTemplateMaster"},
     *      description="Update ChequeTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateMaster",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeTemplateMaster that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeTemplateMaster")
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
     *                  ref="#/definitions/ChequeTemplateMaster"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChequeTemplateMasterAPIRequest $request)
    {
        $input = $request->all();

        /** @var ChequeTemplateMaster $chequeTemplateMaster */
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            return $this->sendError('Cheque Template Master not found');
        }

        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->update($input, $id);

        return $this->sendResponse($chequeTemplateMaster->toArray(), 'ChequeTemplateMaster updated successfully');
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chequeTemplateMasters/{id}",
     *      summary="Remove the specified ChequeTemplateMaster from storage",
     *      tags={"ChequeTemplateMaster"},
     *      description="Delete ChequeTemplateMaster",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateMaster",
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
        /** @var ChequeTemplateMaster $chequeTemplateMaster */
        $chequeTemplateMaster = $this->chequeTemplateMasterRepository->findWithoutFail($id);

        if (empty($chequeTemplateMaster)) {
            return $this->sendError('Cheque Template Master not found');
        }

        $chequeTemplateMaster->delete();

        return $this->sendSuccess('Cheque Template Master deleted successfully');
    }
}
