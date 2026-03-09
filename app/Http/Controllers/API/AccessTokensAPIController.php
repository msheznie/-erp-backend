<?php
/**
 * =============================================
 * -- File Name : AccessTokensAPIController.php
 * -- Project Name : ERP
 * -- Module Name :  Access Tokens
 * -- Author : Mohamed Fayas
 * -- Create date : 04- May 2018
 * -- Description : This file contains the all CRUD for Access Tokens
 * -- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateAccessTokensAPIRequest;
use App\Http\Requests\API\UpdateAccessTokensAPIRequest;
use App\Models\AccessTokens;
use App\Repositories\AccessTokensRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use App\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class AccessTokensController
 * @package App\Http\Controllers\API
 */

class AccessTokensAPIController extends AppBaseController
{
    /** @var  AccessTokensRepository */
    private $accessTokensRepository;

    public function __construct(AccessTokensRepository $accessTokensRepo)
    {
        $this->accessTokensRepository = $accessTokensRepo;
    }

    /**
     * Display a listing of the AccessTokens.
     * GET|HEAD /accessTokens
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->accessTokensRepository->pushCriteria(new RequestCriteria($request));
        $this->accessTokensRepository->pushCriteria(new LimitOffsetCriteria($request));
        $accessTokens = $this->accessTokensRepository->all();

        return $this->sendResponse($accessTokens->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.access_token')]));
    }

    /**
     * Store a newly created AccessTokens in storage.
     * POST /accessTokens
     *
     * @param CreateAccessTokensAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateAccessTokensAPIRequest $request)
    {
        $input = $request->all();

        $accessTokens = $this->accessTokensRepository->create($input);

        return $this->sendResponse($accessTokens->toArray(), trans('custom.save', ['attribute' => trans('custom.access_token')]));
    }

    /**
     * Display the specified AccessTokens.
     * GET|HEAD /accessTokens/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var AccessTokens $accessTokens */
        $accessTokens = $this->accessTokensRepository->findWithoutFail($id);

        if (empty($accessTokens)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.access_token')]));
        }

        return $this->sendResponse($accessTokens->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.access_token')]));
    }

    /**
     * Update the specified AccessTokens in storage.
     * PUT/PATCH /accessTokens/{id}
     *
     * @param  int $id
     * @param UpdateAccessTokensAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccessTokensAPIRequest $request)
    {
        $input = $request->all();

        /** @var AccessTokens $accessTokens */
        $accessTokens = $this->accessTokensRepository->findWithoutFail($id);

        if (empty($accessTokens)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.access_token')]));
        }

        $accessTokens = $this->accessTokensRepository->update($input, $id);

        return $this->sendResponse($accessTokens->toArray(), trans('custom.update', ['attribute' => trans('custom.access_token')]));
    }

    /**
     * Remove the specified AccessTokens from storage.
     * DELETE /accessTokens/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var AccessTokens $accessTokens */
        $accessTokens = $this->accessTokensRepository->findWithoutFail($id);

        if (empty($accessTokens)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.access_token')]));
        }

        $accessTokens->delete();

        return $this->sendResponse($id, trans('custom.delete', ['attribute' => trans('custom.access_token')]));
    }
}
