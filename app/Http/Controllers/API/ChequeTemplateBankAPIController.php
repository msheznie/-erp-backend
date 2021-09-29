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
        $chequeTemplateBanks = $this->chequeTemplateBankRepository->all();

        return $this->sendResponse($chequeTemplateBanks->toArray(), 'Cheque Template Banks retrieved successfully');
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

        return $this->sendResponse($chequeTemplateBank->toArray(), 'Cheque Template Bank saved successfully');
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
            return $this->sendError('Cheque Template Bank not found');
        }

        return $this->sendResponse($chequeTemplateBank->toArray(), 'Cheque Template Bank retrieved successfully');
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
            return $this->sendError('Cheque Template Bank not found');
        }

        $chequeTemplateBank = $this->chequeTemplateBankRepository->update($input, $id);

        return $this->sendResponse($chequeTemplateBank->toArray(), 'ChequeTemplateBank updated successfully');
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
            return $this->sendError('Cheque Template Bank not found');
        }

        $chequeTemplateBank->delete();

        return $this->sendSuccess('Cheque Template Bank deleted successfully');
    }
}
