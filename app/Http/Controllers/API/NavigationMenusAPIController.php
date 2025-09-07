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

        return $this->sendResponse($navigationMenuses->toArray(), trans('custom.navigation_menuses_retrieved_successfully'));
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

        return $this->sendResponse($navigationMenuses->toArray(), trans('custom.navigation_menus_saved_successfully'));
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
            return $this->sendError(trans('custom.navigation_menus_not_found'));
        }

        return $this->sendResponse($navigationMenus->toArray(), trans('custom.navigation_menus_retrieved_successfully'));
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
            return $this->sendError(trans('custom.navigation_menus_not_found'));
        }

        $navigationMenus = $this->navigationMenusRepository->update($input, $id);

        return $this->sendResponse($navigationMenus->toArray(), trans('custom.navigationmenus_updated_successfully'));
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
            return $this->sendError(trans('custom.navigation_menus_not_found'));
        }

        $navigationMenus->delete();

        return $this->sendResponse($id, trans('custom.navigation_menus_deleted_successfully'));
    }

}
