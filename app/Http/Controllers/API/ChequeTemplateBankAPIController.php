<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateChequeTemplateBankAPIRequest;
use App\Http\Requests\API\UpdateChequeTemplateBankAPIRequest;
use App\Models\ChequeTemplateBank;
use App\Repositories\ChequeTemplateBankRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class ChequeTemplateBankController
 * @package App\Http\Controllers\API
 */

class ChequeTemplateBankAPIController extends AppBaseController
{
    /** @var  ChequeTemplateBankRepository */
    private $chequeTemplateBankRepository;

    public function __construct(ChequeTemplateBankRepository $chequeTemplateBankRepo)
    {
        $this->chequeTemplateBankRepository = $chequeTemplateBankRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeTemplateBanks",
     *      summary="Get a listing of the ChequeTemplateBanks.",
     *      tags={"ChequeTemplateBank"},
     *      description="Get all ChequeTemplateBanks",
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
     *                  @SWG\Items(ref="#/definitions/ChequeTemplateBank")
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
        $this->chequeTemplateBankRepository->pushCriteria(new RequestCriteria($request));
        $this->chequeTemplateBankRepository->pushCriteria(new LimitOffsetCriteria($request));
        $chequeTemplateBanks = $this->chequeTemplateBankRepository->with('template')->get();

        return $this->sendResponse($chequeTemplateBanks->toArray(), trans('custom.cheque_template_banks_retrieved_successfully'));
    }

    /**
     * @param CreateChequeTemplateBankAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/chequeTemplateBanks",
     *      summary="Store a newly created ChequeTemplateBank in storage",
     *      tags={"ChequeTemplateBank"},
     *      description="Store ChequeTemplateBank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeTemplateBank that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeTemplateBank")
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
     *                  ref="#/definitions/ChequeTemplateBank"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateChequeTemplateBankAPIRequest $request)
    {
        $input = $request->all();

        $input['bank_id'] = $input['bankmasterAutoID'];
        $input['created_at'] = date("Y-m-d h:i:s");
        $chequeTemplateBank = $this->chequeTemplateBankRepository->create($input);

        return $this->sendResponse($chequeTemplateBank->toArray(), trans('custom.save', ['attribute' => trans('custom.templates')]));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/chequeTemplateBanks/{id}",
     *      summary="Display the specified ChequeTemplateBank",
     *      tags={"ChequeTemplateBank"},
     *      description="Get ChequeTemplateBank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateBank",
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
     *                  ref="#/definitions/ChequeTemplateBank"
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
        /** @var ChequeTemplateBank $chequeTemplateBank */
        $chequeTemplateBank = $this->chequeTemplateBankRepository->findWithoutFail($id);

        if (empty($chequeTemplateBank)) {
            return $this->sendError(trans('custom.cheque_template_bank_not_found'));
        }

        return $this->sendResponse($chequeTemplateBank->toArray(), trans('custom.cheque_template_bank_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateChequeTemplateBankAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/chequeTemplateBanks/{id}",
     *      summary="Update the specified ChequeTemplateBank in storage",
     *      tags={"ChequeTemplateBank"},
     *      description="Update ChequeTemplateBank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateBank",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="ChequeTemplateBank that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/ChequeTemplateBank")
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
     *                  ref="#/definitions/ChequeTemplateBank"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateChequeTemplateBankAPIRequest $request)
    {
        $input = $request->all();

        /** @var ChequeTemplateBank $chequeTemplateBank */
        $chequeTemplateBank = $this->chequeTemplateBankRepository->findWithoutFail($id);

        if (empty($chequeTemplateBank)) {
            return $this->sendError(trans('custom.cheque_template_bank_not_found'));
        }

        $chequeTemplateBank = $this->chequeTemplateBankRepository->update($input, $id);

        return $this->sendResponse($chequeTemplateBank->toArray(), trans('custom.chequetemplatebank_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/chequeTemplateBanks/{id}",
     *      summary="Remove the specified ChequeTemplateBank from storage",
     *      tags={"ChequeTemplateBank"},
     *      description="Delete ChequeTemplateBank",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of ChequeTemplateBank",
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
        /** @var ChequeTemplateBank $chequeTemplateBank */
        $chequeTemplateBank = $this->chequeTemplateBankRepository->findWithoutFail($id);

        if (empty($chequeTemplateBank)) {
            return $this->sendError(trans('custom.cheque_template_bank_not_found'));
        }

        $chequeTemplateBank->delete();

;
        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.templates')]));
    }

    public function assignedTemplatesByBank(Request $request)
    {
       
        $bankId = $request['bankmasterAutoID'];

        $input = $request->all();

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $itemCompanies = ChequeTemplateBank::with(['template'])
                                           ->whereHas('template')
                                           ->where('bank_id','=',$bankId);


        // return $this->sendResponse($itemCompanies, trans('custom.chequetemplatebank_updated_successfully'));
        // die();

        return \DataTables::of($itemCompanies)
            ->order(function ($query) use ($input) {
                if (request()->has('order') ) {
                    if($input['order'][0]['column'] == 0)
                    {
                        $query->orderBy('erp_cheque_template_bank.id', $input['order'][0]['dir']);
                    }
                }
            })
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }


    public function updateBankAssingTemplate(Request $request)
    {
        $id = $request->get('id');
        $input = $request->all();
        $data['is_active'] = $input['is_active'];
        $chequeTemplateBank = $this->chequeTemplateBankRepository->update($data, $id);

        return $this->sendResponse($chequeTemplateBank->toArray(), trans('custom.update', ['attribute' => trans('custom.templates')]));
    }

    public function getBankTemplates()
    {
        
    }
}
