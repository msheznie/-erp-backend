<?php
/**
=============================================
-- File Name : CompanyNavigationMenusAPIController.php
-- Project Name : ERP
-- Module Name :  Company assign
-- Author : Mohamed Mubashir
-- Create date : 14 - March 2018
-- Description : This file contains assigning navigation to company
-- REVISION HISTORY
 */
namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyNavigationMenusAPIRequest;
use App\Http\Requests\API\UpdateCompanyNavigationMenusAPIRequest;
use App\Models\CompanyNavigationMenus;
use App\Models\NavigationMenus;
use App\Models\Company;
use App\Repositories\CompanyNavigationMenusRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Illuminate\Support\Facades\DB;
use Response;

/**
 * Class CompanyNavigationMenusController
 * @package App\Http\Controllers\API
 */
class CompanyNavigationMenusAPIController extends AppBaseController
{
    /** @var  CompanyNavigationMenusRepository */
    private $companyNavigationMenusRepository;

    public function __construct(CompanyNavigationMenusRepository $companyNavigationMenusRepo)
    {
        $this->companyNavigationMenusRepository = $companyNavigationMenusRepo;
    }

    /**
     * Display a listing of the CompanyNavigationMenus.
     * GET|HEAD /companyNavigationMenuses
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->companyNavigationMenusRepository->pushCriteria(new RequestCriteria($request));
        $this->companyNavigationMenusRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyNavigationMenuses = $this->companyNavigationMenusRepository->all();

        return $this->sendResponse($companyNavigationMenuses->toArray(), 'Company Navigation Menuses retrieved successfully');
    }

    /**
     * Store a newly created CompanyNavigationMenus in storage.
     * POST /companyNavigationMenuses
     *
     * @param CreateCompanyNavigationMenusAPIRequest $request
     *
     * @return Response
     */
    public function store(CreateCompanyNavigationMenusAPIRequest $request)
    {
        $input = $request->all();
        DB::table('srp_erp_companynavigationmenus')->where('companyID', '=', $input["companyID"])->delete();
        $navigation = array();
        if ($input["navigation"]) {
            foreach ($input["navigation"] as $val) {
                if($val["isChecked"]) {
                    $navigation[] = array("navigationMenuID" => $val["navigationMenuID"], "description" => $val["description"], "companyID" => $input["companyID"], "masterID" => $val["masterID"], "languageID" => $val["languageID"], "url" => $val["url"], "pageID" => $val["pageID"], "pageTitle" => $val["pageTitle"], "pageIcon" => $val["pageIcon"], "levelNo" => $val["levelNo"], "sortOrder" => $val["sortOrder"], "isSubExist" => $val["isSubExist"], "isAddon" => $val["isAddon"],"isPortalYN" => $val["isPortalYN"],"externalLink" => $val["externalLink"]);
                }
                if (isset($val["children"])) {
                    $children1 = $val["children"];
                    foreach ($children1 as $val2) {
                        if($val2["isChecked"]) {
                            $navigation[] = array("navigationMenuID" => $val2["navigationMenuID"], "description" => $val2["description"], "companyID" => $input["companyID"], "masterID" => $val2["masterID"], "languageID" => $val2["languageID"], "url" => $val2["url"], "pageID" => $val2["pageID"], "pageTitle" => $val2["pageTitle"], "pageIcon" => $val2["pageIcon"], "levelNo" => $val2["levelNo"], "sortOrder" => $val2["sortOrder"], "isSubExist" => $val2["isSubExist"], "isAddon" => $val2["isAddon"],"isPortalYN" => $val2["isPortalYN"],"externalLink" => $val2["externalLink"]);
                        }
                        if (isset($val2["children"])) {
                            $children2 = $val2["children"];
                            foreach ($children2 as $val3) {
                                if($val3["isChecked"]) {
                                    $navigation[] = array("navigationMenuID" => $val3["navigationMenuID"], "description" => $val3["description"], "companyID" => $input["companyID"], "masterID" => $val3["masterID"], "languageID" => $val3["languageID"], "url" => $val3["url"], "pageID" => $val3["pageID"], "pageTitle" => $val3["pageTitle"], "pageIcon" => $val3["pageIcon"], "levelNo" => $val3["levelNo"], "sortOrder" => $val3["sortOrder"], "isSubExist" => $val3["isSubExist"], "isAddon" => $val3["isAddon"],"isPortalYN" => $val3["isPortalYN"],"externalLink" => $val3["externalLink"]);
                                }
                            }
                        }
                    }
                }
            }
        }
        $companyNavigationMenuses = CompanyNavigationMenus::insert($navigation);
        return $this->sendResponse(array(), 'Company Navigation Menus saved successfully');
    }

    /**
     * Display the specified CompanyNavigationMenus.
     * GET|HEAD /companyNavigationMenuses/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        /** @var CompanyNavigationMenus $companyNavigationMenus */
        $companyNavigationMenus = $this->companyNavigationMenusRepository->findWithoutFail($id);

        if (empty($companyNavigationMenus)) {
            return $this->sendError('Company Navigation Menus not found');
        }

        return $this->sendResponse($companyNavigationMenus->toArray(), 'Company Navigation Menus retrieved successfully');
    }

    /**
     * Update the specified CompanyNavigationMenus in storage.
     * PUT/PATCH /companyNavigationMenuses/{id}
     *
     * @param  int $id
     * @param UpdateCompanyNavigationMenusAPIRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateCompanyNavigationMenusAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyNavigationMenus $companyNavigationMenus */
        $companyNavigationMenus = $this->companyNavigationMenusRepository->findWithoutFail($id);

        if (empty($companyNavigationMenus)) {
            return $this->sendError('Company Navigation Menus not found');
        }

        $companyNavigationMenus = $this->companyNavigationMenusRepository->update($input, $id);

        return $this->sendResponse($companyNavigationMenus->toArray(), 'CompanyNavigationMenus updated successfully');
    }

    /**
     * Remove the specified CompanyNavigationMenus from storage.
     * DELETE /companyNavigationMenuses/{id}
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        /** @var CompanyNavigationMenus $companyNavigationMenus */
        $companyNavigationMenus = $this->companyNavigationMenusRepository->findWithoutFail($id);

        if (empty($companyNavigationMenus)) {
            return $this->sendError('Company Navigation Menus not found');
        }

        $companyNavigationMenus->delete();

        return $this->sendResponse($id, 'Company Navigation Menus deleted successfully');
    }

    public function getGroupCompany(Request $request)
    {
        $selectedCompanyId = $request['selectedCompanyId'];
        $companiesByGroup = "";
        if(\Helper::checkIsCompanyGroup($selectedCompanyId)){
            $companiesByGroup = \Helper::getGroupCompany($selectedCompanyId);
        }else{
            $companiesByGroup = (array)$selectedCompanyId;
        }
        $groupCompany = Company::whereIN('companySystemID',$companiesByGroup)->get();
        return $this->sendResponse($groupCompany, 'Record retrieved successfully');
    }

    public function getCompanyNavigation(Request $request)
    {

        //return $navigationMenu = NavigationMenus::with(['child'])->get();
        $companyID = $request['companyID'];
        //$navigationMenu = NavigationMenus::all()->toArray();
        //DB::enableQueryLog();
        $navigationMenu = DB::table('srp_erp_navigationmenus')->select(DB::raw('srp_erp_navigationmenus.*,if(srp_erp_companynavigationmenus.navigationMenuID = srp_erp_navigationmenus.navigationMenuID,1,0) as isChecked'))
            ->leftJoin('srp_erp_companynavigationmenus', function ($join) use ($companyID) {
                $join->on('srp_erp_navigationmenus.navigationMenuID', '=', 'srp_erp_companynavigationmenus.navigationMenuID')
                    ->where('srp_erp_companynavigationmenus.companyID', '=', $companyID)
                    ->orderBy('srp_erp_navigationmenus.sortOrder');
            })
            ->orderBy('srp_erp_navigationmenus.sortOrder')
            ->get();
        //dd(DB::getQueryLog());
        $tree = buildTree($navigationMenu);
        //$navigationMenu = DB::table("srp_erp_companynavigationmenus")->where("companyID",$companyID)->get();
        return $this->sendResponse($tree, 'Record retrieved successfully');
    }


}


function buildTree($elements, $parentId = null)
{
    $branch = array();
    foreach ($elements as $element) {
        if ($element->masterID == $parentId) {
            $children = buildTree($elements, $element->navigationMenuID);
            if ($children) {
                $element->children = $children;
            }
            $branch[] = $element;
        }
    }
    return $branch;
}