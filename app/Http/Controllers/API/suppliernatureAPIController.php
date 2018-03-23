<?php
/**
=============================================
-- File Name : suppliernatureAPIController.php
-- Project Name : ERP
-- Module Name :  supplier nature
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for supplier nature
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreatesuppliernatureAPIRequest;
use App\Http\Requests\API\UpdatesuppliernatureAPIRequest;
use App\Models\suppliernature;
use App\Repositories\suppliernatureRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class suppliernatureController
 * @package App\Http\Controllers\API
 */

class suppliernatureAPIController extends AppBaseController
{
    /** @var  suppliernatureRepository */
    private $suppliernatureRepository;

    public function __construct(suppliernatureRepository $suppliernatureRepo)
    {
        $this->suppliernatureRepository = $suppliernatureRepo;
    }

    /**
     * Display a listing of the suppliernature.
     * GET|HEAD /suppliernatures
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->suppliernatureRepository->pushCriteria(new RequestCriteria($request));
        $this->suppliernatureRepository->pushCriteria(new LimitOffsetCriteria($request));
        $suppliernatures = $this->suppliernatureRepository->all();

        return $this->sendResponse($suppliernatures->toArray(), 'Suppliernatures retrieved successfully');
    }

    /**
     * Store a newly created suppliernature in storage.
     * POST /suppliernatures
     *
     * @param CreatesuppliernatureAPIRequest $request
     *
     * @return Response
     */
    public function store(CreatesuppliernatureAPIRequest $request)
    {
        $input = $request->all();

        $suppliernatures = $this->suppliernatureRepository->create($input);

        return $this->sendResponse($suppliernatures->toArray(), 'Suppliernature saved successfully');
    }

    /**
     * Display the specified suppliernature.
     * GET|HEAD /suppliernatures/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var suppliernature $suppliernature */
        $suppliernature = $this->suppliernatureRepository->findWithoutFail($id);

        if (empty($suppliernature)) {
            return $this->sendError('Suppliernature not found');
        }

        return $this->sendResponse($suppliernature->toArray(), 'Suppliernature retrieved successfully');
    }

    /**
     * Update the specified suppliernature in storage.
     * PUT/PATCH /suppliernatures/{id}
     *
     * @param  int $id
     * @param UpdatesuppliernatureAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatesuppliernatureAPIRequest $request)
    {
        $input = $request->all();

        /** @var suppliernature $suppliernature */
        $suppliernature = $this->suppliernatureRepository->findWithoutFail($id);

        if (empty($suppliernature)) {
            return $this->sendError('Suppliernature not found');
        }

        $suppliernature = $this->suppliernatureRepository->update($input, $id);

        return $this->sendResponse($suppliernature->toArray(), 'suppliernature updated successfully');
    }

    /**
     * Remove the specified suppliernature from storage.
     * DELETE /suppliernatures/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var suppliernature $suppliernature */
        $suppliernature = $this->suppliernatureRepository->findWithoutFail($id);

        if (empty($suppliernature)) {
            return $this->sendError('Suppliernature not found');
        }

        $suppliernature->delete();

        return $this->sendResponse($id, 'Suppliernature deleted successfully');
    }
}
