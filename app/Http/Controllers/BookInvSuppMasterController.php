<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookInvSuppMasterRequest;
use App\Http\Requests\UpdateBookInvSuppMasterRequest;
use App\Repositories\BookInvSuppMasterRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BookInvSuppMasterController extends AppBaseController
{
    /** @var  BookInvSuppMasterRepository */
    private $bookInvSuppMasterRepository;

    public function __construct(BookInvSuppMasterRepository $bookInvSuppMasterRepo)
    {
        $this->bookInvSuppMasterRepository = $bookInvSuppMasterRepo;
    }

    /**
     * Display a listing of the BookInvSuppMaster.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bookInvSuppMasterRepository->pushCriteria(new RequestCriteria($request));
        $bookInvSuppMasters = $this->bookInvSuppMasterRepository->all();

        return view('book_inv_supp_masters.index')
            ->with('bookInvSuppMasters', $bookInvSuppMasters);
    }

    /**
     * Show the form for creating a new BookInvSuppMaster.
     *
     * @return Response
     */
    public function create()
    {
        return view('book_inv_supp_masters.create');
    }

    /**
     * Store a newly created BookInvSuppMaster in storage.
     *
     * @param CreateBookInvSuppMasterRequest $request
     *
     * @return Response
     */
    public function store(CreateBookInvSuppMasterRequest $request)
    {
        $input = $request->all();

        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->create($input);

        Flash::success('Book Inv Supp Master saved successfully.');

        return redirect(route('bookInvSuppMasters.index'));
    }

    /**
     * Display the specified BookInvSuppMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            Flash::error('Book Inv Supp Master not found');

            return redirect(route('bookInvSuppMasters.index'));
        }

        return view('book_inv_supp_masters.show')->with('bookInvSuppMaster', $bookInvSuppMaster);
    }

    /**
     * Show the form for editing the specified BookInvSuppMaster.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            Flash::error('Book Inv Supp Master not found');

            return redirect(route('bookInvSuppMasters.index'));
        }

        return view('book_inv_supp_masters.edit')->with('bookInvSuppMaster', $bookInvSuppMaster);
    }

    /**
     * Update the specified BookInvSuppMaster in storage.
     *
     * @param  int              $id
     * @param UpdateBookInvSuppMasterRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBookInvSuppMasterRequest $request)
    {
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            Flash::error('Book Inv Supp Master not found');

            return redirect(route('bookInvSuppMasters.index'));
        }

        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->update($request->all(), $id);

        Flash::success('Book Inv Supp Master updated successfully.');

        return redirect(route('bookInvSuppMasters.index'));
    }

    /**
     * Remove the specified BookInvSuppMaster from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bookInvSuppMaster = $this->bookInvSuppMasterRepository->findWithoutFail($id);

        if (empty($bookInvSuppMaster)) {
            Flash::error('Book Inv Supp Master not found');

            return redirect(route('bookInvSuppMasters.index'));
        }

        $this->bookInvSuppMasterRepository->delete($id);

        Flash::success('Book Inv Supp Master deleted successfully.');

        return redirect(route('bookInvSuppMasters.index'));
    }
}
