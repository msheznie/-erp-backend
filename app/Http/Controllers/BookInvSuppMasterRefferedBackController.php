<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookInvSuppMasterRefferedBackRequest;
use App\Http\Requests\UpdateBookInvSuppMasterRefferedBackRequest;
use App\Repositories\BookInvSuppMasterRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BookInvSuppMasterRefferedBackController extends AppBaseController
{
    /** @var  BookInvSuppMasterRefferedBackRepository */
    private $bookInvSuppMasterRefferedBackRepository;

    public function __construct(BookInvSuppMasterRefferedBackRepository $bookInvSuppMasterRefferedBackRepo)
    {
        $this->bookInvSuppMasterRefferedBackRepository = $bookInvSuppMasterRefferedBackRepo;
    }

    /**
     * Display a listing of the BookInvSuppMasterRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bookInvSuppMasterRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $bookInvSuppMasterRefferedBacks = $this->bookInvSuppMasterRefferedBackRepository->all();

        return view('book_inv_supp_master_reffered_backs.index')
            ->with('bookInvSuppMasterRefferedBacks', $bookInvSuppMasterRefferedBacks);
    }

    /**
     * Show the form for creating a new BookInvSuppMasterRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('book_inv_supp_master_reffered_backs.create');
    }

    /**
     * Store a newly created BookInvSuppMasterRefferedBack in storage.
     *
     * @param CreateBookInvSuppMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateBookInvSuppMasterRefferedBackRequest $request)
    {
        $input = $request->all();

        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->create($input);

        Flash::success('Book Inv Supp Master Reffered Back saved successfully.');

        return redirect(route('bookInvSuppMasterRefferedBacks.index'));
    }

    /**
     * Display the specified BookInvSuppMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppMasterRefferedBack)) {
            Flash::error('Book Inv Supp Master Reffered Back not found');

            return redirect(route('bookInvSuppMasterRefferedBacks.index'));
        }

        return view('book_inv_supp_master_reffered_backs.show')->with('bookInvSuppMasterRefferedBack', $bookInvSuppMasterRefferedBack);
    }

    /**
     * Show the form for editing the specified BookInvSuppMasterRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppMasterRefferedBack)) {
            Flash::error('Book Inv Supp Master Reffered Back not found');

            return redirect(route('bookInvSuppMasterRefferedBacks.index'));
        }

        return view('book_inv_supp_master_reffered_backs.edit')->with('bookInvSuppMasterRefferedBack', $bookInvSuppMasterRefferedBack);
    }

    /**
     * Update the specified BookInvSuppMasterRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateBookInvSuppMasterRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBookInvSuppMasterRefferedBackRequest $request)
    {
        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppMasterRefferedBack)) {
            Flash::error('Book Inv Supp Master Reffered Back not found');

            return redirect(route('bookInvSuppMasterRefferedBacks.index'));
        }

        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->update($request->all(), $id);

        Flash::success('Book Inv Supp Master Reffered Back updated successfully.');

        return redirect(route('bookInvSuppMasterRefferedBacks.index'));
    }

    /**
     * Remove the specified BookInvSuppMasterRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bookInvSuppMasterRefferedBack = $this->bookInvSuppMasterRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppMasterRefferedBack)) {
            Flash::error('Book Inv Supp Master Reffered Back not found');

            return redirect(route('bookInvSuppMasterRefferedBacks.index'));
        }

        $this->bookInvSuppMasterRefferedBackRepository->delete($id);

        Flash::success('Book Inv Supp Master Reffered Back deleted successfully.');

        return redirect(route('bookInvSuppMasterRefferedBacks.index'));
    }
}
