<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateDocumentEmailNotificationDetailRequest;
use App\Http\Requests\UpdateDocumentEmailNotificationDetailRequest;
use App\Repositories\DocumentEmailNotificationDetailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class DocumentEmailNotificationDetailController extends AppBaseController
{
    /** @var  DocumentEmailNotificationDetailRepository */
    private $documentEmailNotificationDetailRepository;

    public function __construct(DocumentEmailNotificationDetailRepository $documentEmailNotificationDetailRepo)
    {
        $this->documentEmailNotificationDetailRepository = $documentEmailNotificationDetailRepo;
    }

    /**
     * Display a listing of the DocumentEmailNotificationDetail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->documentEmailNotificationDetailRepository->pushCriteria(new RequestCriteria($request));
        $documentEmailNotificationDetails = $this->documentEmailNotificationDetailRepository->all();

        return view('document_email_notification_details.index')
            ->with('documentEmailNotificationDetails', $documentEmailNotificationDetails);
    }

    /**
     * Show the form for creating a new DocumentEmailNotificationDetail.
     *
     * @return Response
     */
    public function create()
    {
        return view('document_email_notification_details.create');
    }

    /**
     * Store a newly created DocumentEmailNotificationDetail in storage.
     *
     * @param CreateDocumentEmailNotificationDetailRequest $request
     *
     * @return Response
     */
    public function store(CreateDocumentEmailNotificationDetailRequest $request)
    {
        $input = $request->all();

        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->create($input);

        Flash::success('Document Email Notification Detail saved successfully.');

        return redirect(route('documentEmailNotificationDetails.index'));
    }

    /**
     * Display the specified DocumentEmailNotificationDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationDetail)) {
            Flash::error('Document Email Notification Detail not found');

            return redirect(route('documentEmailNotificationDetails.index'));
        }

        return view('document_email_notification_details.show')->with('documentEmailNotificationDetail', $documentEmailNotificationDetail);
    }

    /**
     * Show the form for editing the specified DocumentEmailNotificationDetail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationDetail)) {
            Flash::error('Document Email Notification Detail not found');

            return redirect(route('documentEmailNotificationDetails.index'));
        }

        return view('document_email_notification_details.edit')->with('documentEmailNotificationDetail', $documentEmailNotificationDetail);
    }

    /**
     * Update the specified DocumentEmailNotificationDetail in storage.
     *
     * @param  int              $id
     * @param UpdateDocumentEmailNotificationDetailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateDocumentEmailNotificationDetailRequest $request)
    {
        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationDetail)) {
            Flash::error('Document Email Notification Detail not found');

            return redirect(route('documentEmailNotificationDetails.index'));
        }

        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->update($request->all(), $id);

        Flash::success('Document Email Notification Detail updated successfully.');

        return redirect(route('documentEmailNotificationDetails.index'));
    }

    /**
     * Remove the specified DocumentEmailNotificationDetail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $documentEmailNotificationDetail = $this->documentEmailNotificationDetailRepository->findWithoutFail($id);

        if (empty($documentEmailNotificationDetail)) {
            Flash::error('Document Email Notification Detail not found');

            return redirect(route('documentEmailNotificationDetails.index'));
        }

        $this->documentEmailNotificationDetailRepository->delete($id);

        Flash::success('Document Email Notification Detail deleted successfully.');

        return redirect(route('documentEmailNotificationDetails.index'));
    }
}
