<?php

namespace App\Http\Controllers\API;
/**
=============================================
-- File Name : NavigationMenusAPIController.php
-- Project Name : ERP
-- Module Name :  Navigation Menus
-- Author : Mohamed Fayas
-- Create date : 14 - March 2018
-- Description : This file contains the all CRUD for Navigation Menus
-- REVISION HISTORY
 */
use App\Http\Requests\API\CreateNavigationMenusAPIRequest;
use App\Http\Requests\API\UpdateNavigationMenusAPIRequest;
use App\Models\NavigationMenus;
use App\Repositories\NavigationMenusRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class NavigationMenusController
 * @package App\Http\Controllers\API
 */

class NavigationMenusAPIController extends AppBaseController
{
    /** @var  NavigationMenusRepository */
    private $navigationMenusRepository;

    public function __construct(NavigationMenusRepository $navigationMenusRepo)
    {
        $this->navigationMenusRepository = $navigationMenusRepo;
    }

    /**
     * Display a listing of the NavigationMenus.
     * GET|HEAD /navigationMenuses
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->navigationMenusRepository->pushCriteria(new RequestCriteria($request));
        $this->navigationMenusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $navigationMenuses = $this->navigationMenusRepository->all();

        return $this->sendResponse($navigationMenuses->toArray(), 'Navigation Menuses retrieved successfully');
    }

    /**
     * Store a newly created NavigationMenus in storage.
     * POST /navigationMenuses
     *
     * @param CreateNavigationMenusAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateNavigationMenusAPIRequest $request)
    {
        $input = $request->all();

        $navigationMenuses = $this->navigationMenusRepository->create($input);

        return $this->sendResponse($navigationMenuses->toArray(), 'Navigation Menus saved successfully');
    }

    /**
     * Display the specified NavigationMenus.
     * GET|HEAD /navigationMenuses/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var NavigationMenus $navigationMenus */
        $navigationMenus = $this->navigationMenusRepository->findWithoutFail($id);

        if (empty($navigationMenus)) {
            return $this->sendError('Navigation Menus not found');
        }

        return $this->sendResponse($navigationMenus->toArray(), 'Navigation Menus retrieved successfully');
    }

    /**
     * Update the specified NavigationMenus in storage.
     * PUT/PATCH /navigationMenuses/{id}
     *
     * @param  int $id
     * @param UpdateNavigationMenusAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateNavigationMenusAPIRequest $request)
    {
        $input = $request->all();

        /** @var NavigationMenus $navigationMenus */
        $navigationMenus = $this->navigationMenusRepository->findWithoutFail($id);

        if (empty($navigationMenus)) {
            return $this->sendError('Navigation Menus not found');
        }

        $navigationMenus = $this->navigationMenusRepository->update($input, $id);

        return $this->sendResponse($navigationMenus->toArray(), 'NavigationMenus updated successfully');
    }

    /**
     * Remove the specified NavigationMenus from storage.
     * DELETE /navigationMenuses/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var NavigationMenus $navigationMenus */
        $navigationMenus = $this->navigationMenusRepository->findWithoutFail($id);

        if (empty($navigationMenus)) {
            return $this->sendError('Navigation Menus not found');
        }

        $navigationMenus->delete();

        return $this->sendResponse($id, 'Navigation Menus deleted successfully');
    }

}
