<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateFcmTokenRequest;
use App\Http\Requests\UpdateFcmTokenRequest;
use App\Repositories\FcmTokenRepository;
use App\Http\Controllers\AppBaseController;
use Illuminate\Http\Request;
use Flash;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

class FcmTokenController extends AppBaseController
{
    /** @var  FcmTokenRepository */
    private $fcmTokenRepository;

    public function __construct(FcmTokenRepository $fcmTokenRepo)
    {
        $this->fcmTokenRepository = $fcmTokenRepo;
    }

    /**
     * Display a listing of the FcmToken.
     *
     * @param Request $request
     * @return Response
     */
    public function index(Request $request)
    {
        $this->fcmTokenRepository->pushCriteria(new RequestCriteria($request));
        $fcmTokens = $this->fcmTokenRepository->all();

        return view('fcm_tokens.index')
            ->with('fcmTokens', $fcmTokens);
    }

    /**
     * Show the form for creating a new FcmToken.
     *
     * @return Response
     */
    public function create()
    {
        return view('fcm_tokens.create');
    }

    /**
     * Store a newly created FcmToken in storage.
     *
     * @param CreateFcmTokenRequest $request
     *
     * @return Response
     */
    public function store(CreateFcmTokenRequest $request)
    {
        $input = $request->all();

        $fcmToken = $this->fcmTokenRepository->create($input);

        Flash::success('Fcm Token saved successfully.');

        return redirect(route('fcmTokens.index'));
    }

    /**
     * Display the specified FcmToken.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function show($id)
    {
        $fcmToken = $this->fcmTokenRepository->findWithoutFail($id);

        if (empty($fcmToken)) {
            Flash::error('Fcm Token not found');

            return redirect(route('fcmTokens.index'));
        }

        return view('fcm_tokens.show')->with('fcmToken', $fcmToken);
    }

    /**
     * Show the form for editing the specified FcmToken.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function edit($id)
    {
        $fcmToken = $this->fcmTokenRepository->findWithoutFail($id);

        if (empty($fcmToken)) {
            Flash::error('Fcm Token not found');

            return redirect(route('fcmTokens.index'));
        }

        return view('fcm_tokens.edit')->with('fcmToken', $fcmToken);
    }

    /**
     * Update the specified FcmToken in storage.
     *
     * @param  int              $id
     * @param UpdateFcmTokenRequest $request
     *
     * @return Response
     */
    public function update($id, UpdateFcmTokenRequest $request)
    {
        $fcmToken = $this->fcmTokenRepository->findWithoutFail($id);

        if (empty($fcmToken)) {
            Flash::error('Fcm Token not found');

            return redirect(route('fcmTokens.index'));
        }

        $fcmToken = $this->fcmTokenRepository->update($request->all(), $id);

        Flash::success('Fcm Token updated successfully.');

        return redirect(route('fcmTokens.index'));
    }

    /**
     * Remove the specified FcmToken from storage.
     *
     * @param  int $id
     *
     * @return Response
     */
    public function destroy($id)
    {
        $fcmToken = $this->fcmTokenRepository->findWithoutFail($id);

        if (empty($fcmToken)) {
            Flash::error('Fcm Token not found');

            return redirect(route('fcmTokens.index'));
        }

        $this->fcmTokenRepository->delete($id);

        Flash::success('Fcm Token deleted successfully.');

        return redirect(route('fcmTokens.index'));
    }
}
