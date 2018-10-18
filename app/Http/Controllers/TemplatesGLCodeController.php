<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateTemplatesGLCodeRequest;
use App\Http\Requests\UpdateTemplatesGLCodeRequest;
use App\Repositories\TemplatesGLCodeRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class TemplatesGLCodeController extends AppBaseController
{
    /** @var  TemplatesGLCodeRepository */
    private $templatesGLCodeRepository;

    public function __construct(TemplatesGLCodeRepository $templatesGLCodeRepo)
    {
        $this->templatesGLCodeRepository = $templatesGLCodeRepo;
    }

    /**
     * Display a listing of the TemplatesGLCode.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->templatesGLCodeRepository->pushCriteria(new RequestCriteria($request));
        $templatesGLCodes = $this->templatesGLCodeRepository->all();

        return view('templates_g_l_codes.index')
            ->with('templatesGLCodes', $templatesGLCodes);
    }

    /**
     * Show the form for creating a new TemplatesGLCode.
     *
     * @return Response
     */
    public function create()
    {
        return view('templates_g_l_codes.create');
    }

    /**
     * Store a newly created TemplatesGLCode in storage.
     *
     * @param CreateTemplatesGLCodeRequest $request
     *
     * @return Response
     */
    public function store(CreateTemplatesGLCodeRequest $request)
    {
        $input = $request->all();

        $templatesGLCode = $this->templatesGLCodeRepository->create($input);

        Flash::success('Templates G L Code saved successfully.');

        return redirect(route('templatesGLCodes.index'));
    }

    /**
     * Display the specified TemplatesGLCode.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $templatesGLCode = $this->templatesGLCodeRepository->findWithoutFail($id);

        if (empty($templatesGLCode)) {
            Flash::error('Templates G L Code not found');

            return redirect(route('templatesGLCodes.index'));
        }

        return view('templates_g_l_codes.show')->with('templatesGLCode', $templatesGLCode);
    }

    /**
     * Show the form for editing the specified TemplatesGLCode.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $templatesGLCode = $this->templatesGLCodeRepository->findWithoutFail($id);

        if (empty($templatesGLCode)) {
            Flash::error('Templates G L Code not found');

            return redirect(route('templatesGLCodes.index'));
        }

        return view('templates_g_l_codes.edit')->with('templatesGLCode', $templatesGLCode);
    }

    /**
     * Update the specified TemplatesGLCode in storage.
     *
     * @param  int              $id
     * @param UpdateTemplatesGLCodeRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateTemplatesGLCodeRequest $request)
    {
        $templatesGLCode = $this->templatesGLCodeRepository->findWithoutFail($id);

        if (empty($templatesGLCode)) {
            Flash::error('Templates G L Code not found');

            return redirect(route('templatesGLCodes.index'));
        }

        $templatesGLCode = $this->templatesGLCodeRepository->update($request->all(), $id);

        Flash::success('Templates G L Code updated successfully.');

        return redirect(route('templatesGLCodes.index'));
    }

    /**
     * Remove the specified TemplatesGLCode from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $templatesGLCode = $this->templatesGLCodeRepository->findWithoutFail($id);

        if (empty($templatesGLCode)) {
            Flash::error('Templates G L Code not found');

            return redirect(route('templatesGLCodes.index'));
        }

        $this->templatesGLCodeRepository->delete($id);

        Flash::success('Templates G L Code deleted successfully.');

        return redirect(route('templatesGLCodes.index'));
    }
}
