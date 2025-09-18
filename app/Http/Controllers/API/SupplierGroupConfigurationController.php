<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\SupplierCategory;
use App\Models\User;
use App\Http\Controllers\AppBaseController;
use Illuminate\Support\Facades\Auth;
use App\Models\SupplierGroup;
use App\Models\SupplierMaster;

class SupplierGroupConfigurationController extends AppBaseController
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

        $storeSupplierGroup =  SupplierGroup::create($input);

        if($storeSupplierGroup) {
            return $this->sendResponse($storeSupplierGroup->toArray(), trans('custom.supplier_group_created'));
        }else {
            return $this->sendError(trans('custom.cannot_create_supplier_group'));
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
        $data = SupplierGroup::find($input['id'])->update($input);
       
        if($data) {
          return $this->sendResponse($data, trans('custom.supplier_group_updated'));
        }else {
            return $this->sendError(trans('custom.cannot_find_supplier_group'));
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
            ->addColumn(trans('custom.actions'), trans('custom.actions'), "Actions")
            ->addIndexColumn()
            ->with('orderCondition', $sort)
            ->make(true);
    }

    public function deleteGroup(Request $request) {
        $input = $request->all();

        $id = $input['id'];

        $is_exit = SupplierMaster::where('supplier_group_id',$id)->first();

        if(isset($is_exit))
        {
            return $this->sendError(trans('custom.cannot_delete_group_assigned_to_supplier'));
        }
      

      
        $user = User::with(['employee'])->where('id', Auth::user()->id)->first();
        $data = SupplierGroup::find($input['id']);
        if($data) {
            $data->is_deleted = true;
            $data ->deleted_by = ($user) ? $user->employee_id : 0;
            $data->save();
            return $this->sendResponse($data->toArray(), trans('custom.supplier_group_deleted'));
        }else {
            return $this->sendError(trans('custom.cannot_find_supplier_group'));
        }

    }
}
