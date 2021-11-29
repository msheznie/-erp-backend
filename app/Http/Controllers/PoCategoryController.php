<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreatePoCategoryRequest;
use App\Http\Requests\UpdatePoCategoryRequest;
use App\Repositories\PoCategoryRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class PoCategoryController extends AppBaseController
{
    /** @var  PoCategoryRepository */
    private $poCategoryRepository;

    public function __construct(PoCategoryRepository $poCategoryRepo)
    {
        $this->poCategoryRepository = $poCategoryRepo;
    }

    /**
     * Display a listing of the PoCategory.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->poCategoryRepository->pushCriteria(new RequestCriteria($request));
        $poCategories = $this->poCategoryRepository->all();

        return view('po_categories.index')
            ->with('poCategories', $poCategories);
    }

    /**
     * Show the form for creating a new PoCategory.
     *
     * @return Response
     */
    public function create()
    {
        return view('po_categories.create');
    }

    /**
     * Store a newly created PoCategory in storage.
     *
     * @param CreatePoCategoryRequest $request
     *
     * @return Response
     */
    public function store(CreatePoCategoryRequest $request)
    {
        $input = $request->all();

        $poCategory = $this->poCategoryRepository->create($input);

        Flash::success('Po Category saved successfully.');

        return redirect(route('poCategories.index'));
    }

    /**
     * Display the specified PoCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $poCategory = $this->poCategoryRepository->findWithoutFail($id);

        if (empty($poCategory)) {
            Flash::error('Po Category not found');

            return redirect(route('poCategories.index'));
        }

        return view('po_categories.show')->with('poCategory', $poCategory);
    }

    /**
     * Show the form for editing the specified PoCategory.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $poCategory = $this->poCategoryRepository->findWithoutFail($id);

        if (empty($poCategory)) {
            Flash::error('Po Category not found');

            return redirect(route('poCategories.index'));
        }

        return view('po_categories.edit')->with('poCategory', $poCategory);
    }

    /**
     * Update the specified PoCategory in storage.
     *
     * @param  int              $id
     * @param UpdatePoCategoryRequest $request
     *
     * @return Response
     */
    public function update($id, UpdatePoCategoryRequest $request)
    {
        $poCategory = $this->poCategoryRepository->findWithoutFail($id);

        if (empty($poCategory)) {
            Flash::error('Po Category not found');

            return redirect(route('poCategories.index'));
        }

        $poCategory = $this->poCategoryRepository->update($request->all(), $id);

        Flash::success('Po Category updated successfully.');

        return redirect(route('poCategories.index'));
    }

    /**
     * Remove the specified PoCategory from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $poCategory = $this->poCategoryRepository->findWithoutFail($id);

        if (empty($poCategory)) {
            Flash::error('Po Category not found');

            return redirect(route('poCategories.index'));
        }

        $this->poCategoryRepository->delete($id);

        Flash::success('Po Category deleted successfully.');

        return redirect(route('poCategories.index'));
    }
}
