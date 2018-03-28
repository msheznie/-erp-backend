<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateYesNoSelectionForMinusRequest;
use App\Http\Requests\UpdateYesNoSelectionForMinusRequest;
use App\Repositories\YesNoSelectionForMinusRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class YesNoSelectionForMinusController extends AppBaseController
{
    /** @var  YesNoSelectionForMinusRepository */
    private $yesNoSelectionForMinusRepository;

    public function __construct(YesNoSelectionForMinusRepository $yesNoSelectionForMinusRepo)
    {
        $this->yesNoSelectionForMinusRepository = $yesNoSelectionForMinusRepo;
    }

    /**
     * Display a listing of the YesNoSelectionForMinus.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->yesNoSelectionForMinusRepository->pushCriteria(new RequestCriteria($request));
        $yesNoSelectionForMinuses = $this->yesNoSelectionForMinusRepository->all();

        return view('yes_no_selection_for_minuses.index')
            ->with('yesNoSelectionForMinuses', $yesNoSelectionForMinuses);
    }

    /**
     * Show the form for creating a new YesNoSelectionForMinus.
     *
     * @return Response
     */
    public function create()
    {
        return view('yes_no_selection_for_minuses.create');
    }

    /**
     * Store a newly created YesNoSelectionForMinus in storage.
     *
     * @param CreateYesNoSelectionForMinusRequest $request
     *
     * @return Response
     */
    public function store(CreateYesNoSelectionForMinusRequest $request)
    {
        $input = $request->all();

        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->create($input);

        Flash::success('Yes No Selection For Minus saved successfully.');

        return redirect(route('yesNoSelectionForMinuses.index'));
    }

    /**
     * Display the specified YesNoSelectionForMinus.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->findWithoutFail($id);

        if (empty($yesNoSelectionForMinus)) {
            Flash::error('Yes No Selection For Minus not found');

            return redirect(route('yesNoSelectionForMinuses.index'));
        }

        return view('yes_no_selection_for_minuses.show')->with('yesNoSelectionForMinus', $yesNoSelectionForMinus);
    }

    /**
     * Show the form for editing the specified YesNoSelectionForMinus.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->findWithoutFail($id);

        if (empty($yesNoSelectionForMinus)) {
            Flash::error('Yes No Selection For Minus not found');

            return redirect(route('yesNoSelectionForMinuses.index'));
        }

        return view('yes_no_selection_for_minuses.edit')->with('yesNoSelectionForMinus', $yesNoSelectionForMinus);
    }

    /**
     * Update the specified YesNoSelectionForMinus in storage.
     *
     * @param  int              $id
     * @param UpdateYesNoSelectionForMinusRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateYesNoSelectionForMinusRequest $request)
    {
        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->findWithoutFail($id);

        if (empty($yesNoSelectionForMinus)) {
            Flash::error('Yes No Selection For Minus not found');

            return redirect(route('yesNoSelectionForMinuses.index'));
        }

        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->update($request->all(), $id);

        Flash::success('Yes No Selection For Minus updated successfully.');

        return redirect(route('yesNoSelectionForMinuses.index'));
    }

    /**
     * Remove the specified YesNoSelectionForMinus from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $yesNoSelectionForMinus = $this->yesNoSelectionForMinusRepository->findWithoutFail($id);

        if (empty($yesNoSelectionForMinus)) {
            Flash::error('Yes No Selection For Minus not found');

            return redirect(route('yesNoSelectionForMinuses.index'));
        }

        $this->yesNoSelectionForMinusRepository->delete($id);

        Flash::success('Yes No Selection For Minus deleted successfully.');

        return redirect(route('yesNoSelectionForMinuses.index'));
    }
}
