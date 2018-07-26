<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePoAddonsRefferedBackRequest;
use App\Http\Requests\UpdatePoAddonsRefferedBackRequest;
use App\Repositories\PoAddonsRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PoAddonsRefferedBackController extends AppBaseController
{
    /** @var  PoAddonsRefferedBackRepository */
    private $poAddonsRefferedBackRepository;

    public function __construct(PoAddonsRefferedBackRepository $poAddonsRefferedBackRepo)
    {
        $this->poAddonsRefferedBackRepository = $poAddonsRefferedBackRepo;
    }

    /**
     * Display a listing of the PoAddonsRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poAddonsRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $poAddonsRefferedBacks = $this->poAddonsRefferedBackRepository->all();

        return view('po_addons_reffered_backs.index')
            ->with('poAddonsRefferedBacks', $poAddonsRefferedBacks);
    }

    /**
     * Show the form for creating a new PoAddonsRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('po_addons_reffered_backs.create');
    }

    /**
     * Store a newly created PoAddonsRefferedBack in storage.
     *
     * @param CreatePoAddonsRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreatePoAddonsRefferedBackRequest $request)
    {
        $input = $request->all();

        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->create($input);

        Flash::success('Po Addons Reffered Back saved successfully.');

        return redirect(route('poAddonsRefferedBacks.index'));
    }

    /**
     * Display the specified PoAddonsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->findWithoutFail($id);

        if (empty($poAddonsRefferedBack)) {
            Flash::error('Po Addons Reffered Back not found');

            return redirect(route('poAddonsRefferedBacks.index'));
        }

        return view('po_addons_reffered_backs.show')->with('poAddonsRefferedBack', $poAddonsRefferedBack);
    }

    /**
     * Show the form for editing the specified PoAddonsRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->findWithoutFail($id);

        if (empty($poAddonsRefferedBack)) {
            Flash::error('Po Addons Reffered Back not found');

            return redirect(route('poAddonsRefferedBacks.index'));
        }

        return view('po_addons_reffered_backs.edit')->with('poAddonsRefferedBack', $poAddonsRefferedBack);
    }

    /**
     * Update the specified PoAddonsRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdatePoAddonsRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoAddonsRefferedBackRequest $request)
    {
        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->findWithoutFail($id);

        if (empty($poAddonsRefferedBack)) {
            Flash::error('Po Addons Reffered Back not found');

            return redirect(route('poAddonsRefferedBacks.index'));
        }

        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->update($request->all(), $id);

        Flash::success('Po Addons Reffered Back updated successfully.');

        return redirect(route('poAddonsRefferedBacks.index'));
    }

    /**
     * Remove the specified PoAddonsRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $poAddonsRefferedBack = $this->poAddonsRefferedBackRepository->findWithoutFail($id);

        if (empty($poAddonsRefferedBack)) {
            Flash::error('Po Addons Reffered Back not found');

            return redirect(route('poAddonsRefferedBacks.index'));
        }

        $this->poAddonsRefferedBackRepository->delete($id);

        Flash::success('Po Addons Reffered Back deleted successfully.');

        return redirect(route('poAddonsRefferedBacks.index'));
    }
}
