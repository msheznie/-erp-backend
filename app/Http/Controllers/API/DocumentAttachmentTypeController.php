<?php

namespace App\Http\Controllers\API;

use App\helper\Helper;
use App\Http\Controllers\AppBaseController;
use App\Models\TenderDocumentTypes;
use App\Repositories\TenderDocumentTypesRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class DocumentAttachmentTypeController extends AppBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    private $tenderDocumentTypesRepository;

    public function __construct(TenderDocumentTypesRepository $tenderDocumentTypesRepo)
    {
        $this->tenderDocumentTypesRepository = $tenderDocumentTypesRepo;
    }
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
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Prettus\Validator\Exceptions\ValidatorException
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $input = $this->convertArrayToValue($input);
        $companySystemID = $request->input('companySystemID');
        $validator = \Validator::make($input, [
            'srm_action' => 'required|numeric|min:0',
            'document_type' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $attachmentTypeExist = TenderDocumentTypes::select('id', 'document_type')
            ->where('document_type', '=', $input['document_type'])->first();

        if (!empty($attachmentTypeExist)) {
            return $this->sendError(trans('srm_masters.document_type_already_exists', [
                'code' => $input['document_type'],
            ]));
        }

        $sort = TenderDocumentTypes::getSortOrder();

        $input['sort_order'] = $sort;
        $input['created_at'] = Carbon::now();
        $input['created_by'] = Helper::getEmployeeSystemID();
        $input['company_id'] = $companySystemID;
        $attachmentType = $this->tenderDocumentTypesRepository->create($input);
        return $this->sendResponse($attachmentType->toArray(), trans('srm_masters.document_type_saved_successfully'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $attachmentType = TenderDocumentTypes::find($id);

        if (empty($attachmentType)) {
            return $this->sendError('Document Type not found');
        }

        return $this->sendResponse($attachmentType->toArray(), 'Document Type retrieved successfully');
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
    public function update(Request $request, $id)
    {
        $input = $request->all();
        $attachmentType = TenderDocumentTypes::find($id);

        if (empty($attachmentType)) {
            return $this->sendError(trans('srm_masters.document_type_not_found'));
        }

        $input = $this->convertArrayToValue($input);

        if (isset($input['attachments'])) {
            unset($input['attachments']);
        }

        $validator = \Validator::make($input, [
            'srm_action' => 'required|numeric|min:0',
            'document_type' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $attachmentTypeExist = TenderDocumentTypes::select('id', 'document_type')
            ->where('document_type', '=', $input['document_type'])
            ->where('id', '!=', $id)
            ->first();

        if (!empty($attachmentTypeExist)) {
            return $this->sendError(trans('srm_masters.document_type_already_exists', [
                'code' => $input['document_type'],
            ]));
        }

        $input['updated_by'] = Helper::getEmployeeSystemID();
        $input['updated_at'] = Carbon::now();

        $attachmentType->update($input);

        return $this->sendResponse($attachmentType, trans('srm_masters.document_type_updated_successfully'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function removeDocumentAttachmentType(Request $request)
    {
        $attachmentType = TenderDocumentTypes::find($request[0]);

        if (empty($attachmentType)) {
            return $this->sendError(trans('srm_masters.document_type_not_found'));
        }

        $attachmentType->delete();

        return $this->sendResponse($request[0], trans('srm_masters.document_type_deleted_successfully'));
    }

    public function getAllDocumentAttachmentTypes(Request $request)
    {
        $input = $request->all();
        $attachmentTypes = TenderDocumentTypes::getTenderDocumentTypes($input);
        $search = $request->input('search.value');
        if ($search) {
            $search = str_replace("\\", "\\\\", $search);
            $attachmentTypes = $attachmentTypes->where(function ($query) use ($search) {
                $query->where('document_type', 'LIKE', "%{$search}%");
            });
        }

        return \DataTables::of($attachmentTypes)
            ->addColumn('Actions', 'Actions', "Actions")
            ->addIndexColumn()
            ->make(true);
    }
}
