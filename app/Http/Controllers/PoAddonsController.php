<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePoAddonsRequest;
use App\Http\Requests\UpdatePoAddonsRequest;
use App\Repositories\PoAddonsRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PoAddonsController extends AppBaseController
{
    /** @var  PoAddonsRepository */
    private $poAddonsRepository;

    public function __construct(PoAddonsRepository $poAddonsRepo)
    {
        $this->poAddonsRepository = $poAddonsRepo;
    }

    /**
     * Display a listing of the PoAddons.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poAddonsRepository->pushCriteria(new RequestCriteria($request));
        $poAddons = $this->poAddonsRepository->all();

        return view('po_addons.index')
            ->with('poAddons', $poAddons);
    }

    /**
     * Show the form for creating a new PoAddons.
     *
     * @return Response
     */
    public function create()
    {
        return view('po_addons.create');
    }

    /**
     * Store a newly created PoAddons in storage.
     *
     * @param CreatePoAddonsRequest $request
     *
     * @return Response
     */
    public function store(CreatePoAddonsRequest $request)
    {
        $input = $request->all();

        $poAddons = $this->poAddonsRepository->create($input);

        Flash::success('Po Addons saved successfully.');

        return redirect(route('poAddons.index'));
    }

    /**
     * Display the specified PoAddons.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $poAddons = $this->poAddonsRepository->findWithoutFail($id);

        if (empty($poAddons)) {
            Flash::error('Po Addons not found');

            return redirect(route('poAddons.index'));
        }

        return view('po_addons.show')->with('poAddons', $poAddons);
    }

    /**
     * Show the form for editing the specified PoAddons.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $poAddons = $this->poAddonsRepository->findWithoutFail($id);

        if (empty($poAddons)) {
            Flash::error('Po Addons not found');

            return redirect(route('poAddons.index'));
        }

        return view('po_addons.edit')->with('poAddons', $poAddons);
    }

    /**
     * Update the specified PoAddons in storage.
     *
     * @param  int              $id
     * @param UpdatePoAddonsRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoAddonsRequest $request)
    {
        $poAddons = $this->poAddonsRepository->findWithoutFail($id);

        if (empty($poAddons)) {
            Flash::error('Po Addons not found');

            return redirect(route('poAddons.index'));
        }

        $poAddons = $this->poAddonsRepository->update($request->all(), $id);

        Flash::success('Po Addons updated successfully.');

        return redirect(route('poAddons.index'));
    }

    /**
     * Remove the specified PoAddons from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $poAddons = $this->poAddonsRepository->findWithoutFail($id);

        if (empty($poAddons)) {
            Flash::error('Po Addons not found');

            return redirect(route('poAddons.index'));
        }

        $this->poAddonsRepository->delete($id);

        Flash::success('Po Addons deleted successfully.');

        return redirect(route('poAddons.index'));
    }
}
