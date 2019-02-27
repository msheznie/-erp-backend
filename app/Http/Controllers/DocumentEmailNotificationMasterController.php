<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentEmailNotificationMasterRequest;
use App\Http\Requests\UpdateDocumentEmailNotificationMasterRequest;
use App\Repositories\DocumentEmailNotificationMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentEmailNotificationMasterController extends AppBaseController
{
    /** @var  DocumentEmailNotificationMasterRepository */
    private $documentEmailNotificationMasterRepository;

    public function __construct(DocumentEmailNotificationMasterRepository $documentEmailNotificationMasterRepo)
    {
        $this->documentEmailNotificationMasterRepository = $documentEmailNotificationMasterRepo;
    }

    /**
     * Display a listing of the DocumentEmailNotificationMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentEmailNotificationMasterRepository->pushCriteria(new RequestCriteria($request));
        $documentEmailNotificationMasters = $this->documentEmailNotificationMasterRepository->all();

        return view('document_email_notification_masters.index')
            ->with('documentEmailNotificationMasters', $documentEmailNotificationMasters);
    }

    /**
     * Show the form for creating a new DocumentEmailNotificationMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_email_notification_masters.create');
    }

    /**
     * Store a newly created DocumentEmailNotificationMaster in storage.
     *
     * @param CreateDocumentEmailNotificationMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentEmailNotificationMasterRequest $request)
    {
        $input = $request->all();

        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->create($input);

        Flash::success('Document Email Notification Master saved successfully.');

        return redirect(route('documentEmailNotificationMasters.index'));
    }

    /**
     * Display the specified DocumentEmailNotificationMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMaster)) {
            Flash::error('Document Email Notification Master not found');

            return redirect(route('documentEmailNotificationMasters.index'));
        }

        return view('document_email_notification_masters.show')->with('documentEmailNotificationMaster', $documentEmailNotificationMaster);
    }

    /**
     * Show the form for editing the specified DocumentEmailNotificationMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMaster)) {
            Flash::error('Document Email Notification Master not found');

            return redirect(route('documentEmailNotificationMasters.index'));
        }

        return view('document_email_notification_masters.edit')->with('documentEmailNotificationMaster', $documentEmailNotificationMaster);
    }

    /**
     * Update the specified DocumentEmailNotificationMaster in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentEmailNotificationMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentEmailNotificationMasterRequest $request)
    {
        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMaster)) {
            Flash::error('Document Email Notification Master not found');

            return redirect(route('documentEmailNotificationMasters.index'));
        }

        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->update($request->all(), $id);

        Flash::success('Document Email Notification Master updated successfully.');

        return redirect(route('documentEmailNotificationMasters.index'));
    }

    /**
     * Remove the specified DocumentEmailNotificationMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentEmailNotificationMaster = $this->documentEmailNotificationMasterRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationMaster)) {
            Flash::error('Document Email Notification Master not found');

            return redirect(route('documentEmailNotificationMasters.index'));
        }

        $this->documentEmailNotificationMasterRepository->delete($id);

        Flash::success('Document Email Notification Master deleted successfully.');

        return redirect(route('documentEmailNotificationMasters.index'));
    }
}
