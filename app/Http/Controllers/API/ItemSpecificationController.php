<?php

namespace App\Http\Controllers\API;

use App\Models\ItemSpecification;
use Illuminate\Http\Request;
use App\Http\Requests\API\CreateItemReturnMasterRefferedBackAPIRequest;
use App\Http\Requests\API\UpdateItemReturnMasterRefferedBackAPIRequest;
use App\Models\ItemReturnMasterRefferedBack;
use App\Repositories\ItemReturnMasterRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class ItemSpecificationController extends AppBaseController
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
        $specification = ItemSpecification::where('item_id',$input['item_id'])->where('sub_cat_id',$input['sub_cat_id'])->first();

        if($specification) {
            $specification->html = $input['html'];
            $specification->save();
            return $this->sendResponse($specification->toArray(), trans('custom.item_specification_updated_successfully'));

        }else {
            $data = ItemSpecification::create($request->input());
            return $this->sendResponse($data->toArray(), trans('custom.item_specification_created_successfully'));
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\ItemSpecification  $itemSpecification
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = ItemSpecification::where('item_id',$id)->first();
        if(empty($data)) {
            return $this->sendError(trans('custom.not_found', ['attribute' => trans('custom.item_specification')]));
        }
        return $this->sendResponse($data->toArray(), trans('custom.retrieve', ['attribute' => trans('custom.item_specification')]));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\ItemSpecification  $itemSpecification
     * @return \Illuminate\Http\Response
     */
    public function edit(ItemSpecification $itemSpecification)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\ItemSpecification  $itemSpecification
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ItemSpecification $itemSpecification)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\ItemSpecification  $itemSpecification
     * @return \Illuminate\Http\Response
     */
    public function destroy(ItemSpecification $itemSpecification)
    {
        //
    }
}
