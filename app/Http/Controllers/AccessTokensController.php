<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateAccessTokensRequest;
use App\Http\Requests\UpdateAccessTokensRequest;
use App\Repositories\AccessTokensRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class AccessTokensController extends AppBaseController
{
    /** @var  AccessTokensRepository */
    private $accessTokensRepository;

    public function __construct(AccessTokensRepository $accessTokensRepo)
    {
        $this->accessTokensRepository = $accessTokensRepo;
    }

    /**
     * Display a listing of the AccessTokens.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->accessTokensRepository->pushCriteria(new RequestCriteria($request));
        $accessTokens = $this->accessTokensRepository->all();

        return view('access_tokens.index')
            ->with('accessTokens', $accessTokens);
    }

    /**
     * Show the form for creating a new AccessTokens.
     *
     * @return Response
     */
    public function create()
    {
        return view('access_tokens.create');
    }

    /**
     * Store a newly created AccessTokens in storage.
     *
     * @param CreateAccessTokensRequest $request
     *
     * @return Response
     */
    public function store(CreateAccessTokensRequest $request)
    {
        $input = $request->all();

        $accessTokens = $this->accessTokensRepository->create($input);

        Flash::success('Access Tokens saved successfully.');

        return redirect(route('accessTokens.index'));
    }

    /**
     * Display the specified AccessTokens.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $accessTokens = $this->accessTokensRepository->findWithoutFail($id);

        if (empty($accessTokens)) {
            Flash::error('Access Tokens not found');

            return redirect(route('accessTokens.index'));
        }

        return view('access_tokens.show')->with('accessTokens', $accessTokens);
    }

    /**
     * Show the form for editing the specified AccessTokens.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $accessTokens = $this->accessTokensRepository->findWithoutFail($id);

        if (empty($accessTokens)) {
            Flash::error('Access Tokens not found');

            return redirect(route('accessTokens.index'));
        }

        return view('access_tokens.edit')->with('accessTokens', $accessTokens);
    }

    /**
     * Update the specified AccessTokens in storage.
     *
     * @param  int              $id
     * @param UpdateAccessTokensRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateAccessTokensRequest $request)
    {
        $accessTokens = $this->accessTokensRepository->findWithoutFail($id);

        if (empty($accessTokens)) {
            Flash::error('Access Tokens not found');

            return redirect(route('accessTokens.index'));
        }

        $accessTokens = $this->accessTokensRepository->update($request->all(), $id);

        Flash::success('Access Tokens updated successfully.');

        return redirect(route('accessTokens.index'));
    }

    /**
     * Remove the specified AccessTokens from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $accessTokens = $this->accessTokensRepository->findWithoutFail($id);

        if (empty($accessTokens)) {
            Flash::error('Access Tokens not found');

            return redirect(route('accessTokens.index'));
        }

        $this->accessTokensRepository->delete($id);

        Flash::success('Access Tokens deleted successfully.');

        return redirect(route('accessTokens.index'));
    }
}
