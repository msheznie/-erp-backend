<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookInvSuppDetRefferedBackRequest;
use App\Http\Requests\UpdateBookInvSuppDetRefferedBackRequest;
use App\Repositories\BookInvSuppDetRefferedBackRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BookInvSuppDetRefferedBackController extends AppBaseController
{
    /** @var  BookInvSuppDetRefferedBackRepository */
    private $bookInvSuppDetRefferedBackRepository;

    public function __construct(BookInvSuppDetRefferedBackRepository $bookInvSuppDetRefferedBackRepo)
    {
        $this->bookInvSuppDetRefferedBackRepository = $bookInvSuppDetRefferedBackRepo;
    }

    /**
     * Display a listing of the BookInvSuppDetRefferedBack.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bookInvSuppDetRefferedBackRepository->pushCriteria(new RequestCriteria($request));
        $bookInvSuppDetRefferedBacks = $this->bookInvSuppDetRefferedBackRepository->all();

        return view('book_inv_supp_det_reffered_backs.index')
            ->with('bookInvSuppDetRefferedBacks', $bookInvSuppDetRefferedBacks);
    }

    /**
     * Show the form for creating a new BookInvSuppDetRefferedBack.
     *
     * @return Response
     */
    public function create()
    {
        return view('book_inv_supp_det_reffered_backs.create');
    }

    /**
     * Store a newly created BookInvSuppDetRefferedBack in storage.
     *
     * @param CreateBookInvSuppDetRefferedBackRequest $request
     *
     * @return Response
     */
    public function store(CreateBookInvSuppDetRefferedBackRequest $request)
    {
        $input = $request->all();

        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->create($input);

        Flash::success('Book Inv Supp Det Reffered Back saved successfully.');

        return redirect(route('bookInvSuppDetRefferedBacks.index'));
    }

    /**
     * Display the specified BookInvSuppDetRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppDetRefferedBack)) {
            Flash::error('Book Inv Supp Det Reffered Back not found');

            return redirect(route('bookInvSuppDetRefferedBacks.index'));
        }

        return view('book_inv_supp_det_reffered_backs.show')->with('bookInvSuppDetRefferedBack', $bookInvSuppDetRefferedBack);
    }

    /**
     * Show the form for editing the specified BookInvSuppDetRefferedBack.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppDetRefferedBack)) {
            Flash::error('Book Inv Supp Det Reffered Back not found');

            return redirect(route('bookInvSuppDetRefferedBacks.index'));
        }

        return view('book_inv_supp_det_reffered_backs.edit')->with('bookInvSuppDetRefferedBack', $bookInvSuppDetRefferedBack);
    }

    /**
     * Update the specified BookInvSuppDetRefferedBack in storage.
     *
     * @param  int              $id
     * @param UpdateBookInvSuppDetRefferedBackRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBookInvSuppDetRefferedBackRequest $request)
    {
        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppDetRefferedBack)) {
            Flash::error('Book Inv Supp Det Reffered Back not found');

            return redirect(route('bookInvSuppDetRefferedBacks.index'));
        }

        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->update($request->all(), $id);

        Flash::success('Book Inv Supp Det Reffered Back updated successfully.');

        return redirect(route('bookInvSuppDetRefferedBacks.index'));
    }

    /**
     * Remove the specified BookInvSuppDetRefferedBack from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bookInvSuppDetRefferedBack = $this->bookInvSuppDetRefferedBackRepository->findWithoutFail($id);

        if (empty($bookInvSuppDetRefferedBack)) {
            Flash::error('Book Inv Supp Det Reffered Back not found');

            return redirect(route('bookInvSuppDetRefferedBacks.index'));
        }

        $this->bookInvSuppDetRefferedBackRepository->delete($id);

        Flash::success('Book Inv Supp Det Reffered Back deleted successfully.');

        return redirect(route('bookInvSuppDetRefferedBacks.index'));
    }
}
