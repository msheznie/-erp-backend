<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBookInvSuppDetRequest;
use App\Http\Requests\UpdateBookInvSuppDetRequest;
use App\Repositories\BookInvSuppDetRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class BookInvSuppDetController extends AppBaseController
{
    /** @var  BookInvSuppDetRepository */
    private $bookInvSuppDetRepository;

    public function __construct(BookInvSuppDetRepository $bookInvSuppDetRepo)
    {
        $this->bookInvSuppDetRepository = $bookInvSuppDetRepo;
    }

    /**
     * Display a listing of the BookInvSuppDet.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->bookInvSuppDetRepository->pushCriteria(new RequestCriteria($request));
        $bookInvSuppDets = $this->bookInvSuppDetRepository->all();

        return view('book_inv_supp_dets.index')
            ->with('bookInvSuppDets', $bookInvSuppDets);
    }

    /**
     * Show the form for creating a new BookInvSuppDet.
     *
     * @return Response
     */
    public function create()
    {
        return view('book_inv_supp_dets.create');
    }

    /**
     * Store a newly created BookInvSuppDet in storage.
     *
     * @param CreateBookInvSuppDetRequest $request
     *
     * @return Response
     */
    public function store(CreateBookInvSuppDetRequest $request)
    {
        $input = $request->all();

        $bookInvSuppDet = $this->bookInvSuppDetRepository->create($input);

        Flash::success('Book Inv Supp Det saved successfully.');

        return redirect(route('bookInvSuppDets.index'));
    }

    /**
     * Display the specified BookInvSuppDet.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            Flash::error('Book Inv Supp Det not found');

            return redirect(route('bookInvSuppDets.index'));
        }

        return view('book_inv_supp_dets.show')->with('bookInvSuppDet', $bookInvSuppDet);
    }

    /**
     * Show the form for editing the specified BookInvSuppDet.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            Flash::error('Book Inv Supp Det not found');

            return redirect(route('bookInvSuppDets.index'));
        }

        return view('book_inv_supp_dets.edit')->with('bookInvSuppDet', $bookInvSuppDet);
    }

    /**
     * Update the specified BookInvSuppDet in storage.
     *
     * @param  int              $id
     * @param UpdateBookInvSuppDetRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateBookInvSuppDetRequest $request)
    {
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            Flash::error('Book Inv Supp Det not found');

            return redirect(route('bookInvSuppDets.index'));
        }

        $bookInvSuppDet = $this->bookInvSuppDetRepository->update($request->all(), $id);

        Flash::success('Book Inv Supp Det updated successfully.');

        return redirect(route('bookInvSuppDets.index'));
    }

    /**
     * Remove the specified BookInvSuppDet from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $bookInvSuppDet = $this->bookInvSuppDetRepository->findWithoutFail($id);

        if (empty($bookInvSuppDet)) {
            Flash::error('Book Inv Supp Det not found');

            return redirect(route('bookInvSuppDets.index'));
        }

        $this->bookInvSuppDetRepository->delete($id);

        Flash::success('Book Inv Supp Det deleted successfully.');

        return redirect(route('bookInvSuppDets.index'));
    }
}
