<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAuditTrailRequest;
use App\Http\Requests\UpdateAuditTrailRequest;
use App\Repositories\AuditTrailRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AuditTrailController extends AppBaseController
{
    /** @var  AuditTrailRepository */
    private $auditTrailRepository;

    public function __construct(AuditTrailRepository $auditTrailRepo)
    {
        $this->auditTrailRepository = $auditTrailRepo;
    }

    /**
     * Display a listing of the AuditTrail.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->auditTrailRepository->pushCriteria(new RequestCriteria($request));
        $auditTrails = $this->auditTrailRepository->all();

        return view('audit_trails.index')
            ->with('auditTrails', $auditTrails);
    }

    /**
     * Show the form for creating a new AuditTrail.
     *
     * @return Response
     */
    public function create()
    {
        return view('audit_trails.create');
    }

    /**
     * Store a newly created AuditTrail in storage.
     *
     * @param CreateAuditTrailRequest $request
     *
     * @return Response
     */
    public function store(CreateAuditTrailRequest $request)
    {
        $input = $request->all();

        $auditTrail = $this->auditTrailRepository->create($input);

        Flash::success('Audit Trail saved successfully.');

        return redirect(route('auditTrails.index'));
    }

    /**
     * Display the specified AuditTrail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $auditTrail = $this->auditTrailRepository->findWithoutFail($id);

        if (empty($auditTrail)) {
            Flash::error('Audit Trail not found');

            return redirect(route('auditTrails.index'));
        }

        return view('audit_trails.show')->with('auditTrail', $auditTrail);
    }

    /**
     * Show the form for editing the specified AuditTrail.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $auditTrail = $this->auditTrailRepository->findWithoutFail($id);

        if (empty($auditTrail)) {
            Flash::error('Audit Trail not found');

            return redirect(route('auditTrails.index'));
        }

        return view('audit_trails.edit')->with('auditTrail', $auditTrail);
    }

    /**
     * Update the specified AuditTrail in storage.
     *
     * @param  int              $id
     * @param UpdateAuditTrailRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAuditTrailRequest $request)
    {
        $auditTrail = $this->auditTrailRepository->findWithoutFail($id);

        if (empty($auditTrail)) {
            Flash::error('Audit Trail not found');

            return redirect(route('auditTrails.index'));
        }

        $auditTrail = $this->auditTrailRepository->update($request->all(), $id);

        Flash::success('Audit Trail updated successfully.');

        return redirect(route('auditTrails.index'));
    }

    /**
     * Remove the specified AuditTrail from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $auditTrail = $this->auditTrailRepository->findWithoutFail($id);

        if (empty($auditTrail)) {
            Flash::error('Audit Trail not found');

            return redirect(route('auditTrails.index'));
        }

        $this->auditTrailRepository->delete($id);

        Flash::success('Audit Trail deleted successfully.');

        return redirect(route('auditTrails.index'));
    }
}
