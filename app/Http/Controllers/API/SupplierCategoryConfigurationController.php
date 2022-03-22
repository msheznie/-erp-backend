<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SupplierCategory;
use App\Models\User;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\SupplierGroup;

class SupplierCategoryConfigurationController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input();

        $storeSupplierCategory =  SupplierCategory::create($input);

        if($storeSupplierCategory) {
            return $this->sendResponse($storeSupplierCategory->toArray(), "Supplier Category Created");
        }else {
            return $this->sendError("Cannot create supplier category");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $input = $request->all();
        $data = SupplierCategory::find($input['id'])->update($input);
       
        if($data) {
          return $this->sendResponse($data, "Supplier Category Updated");
        }else {
            return $this->sendError("Cannot find supplier category");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getSupplierCategories(Request $request) {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $data = SupplierCategory::notDeleted();

         return \DataTables::of($data)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function getSupplierGroups(Request $request) {

        $input = $request->all();
        $input = $this->convertArrayToSelectedValue($input, array());

        if (request()->has('order') && $input['order'][0]['column'] == 0 && $input['order'][0]['dir'] === 'asc') {
            $sort = 'asc';
        } else {
            $sort = 'desc';
        }

        $data = SupplierGroup::notDeleted();

         return \DataTables::of($data)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function deleteCategory(Request $request) {
        $input = $request->all();
        $user = User::with(['employee'])->where('id', Auth::user()->id)->first();
        $data = SupplierCategory::find($input['id']);
        if($data) {
            $data->is_deleted = true;
            $data ->deleted_by = ($user) ? $user->employee_id : 0;
            $data->save();
            return $this->sendResponse($data->toArray(), "Supplier Category Deleted");
        }else {
            return $this->sendError("Cannot find supplier category");
        }

    }
}
