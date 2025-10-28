<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateFcmTokenAPIRequest;
use App\Http\Requests\API\UpdateFcmTokenAPIRequest;
use App\Models\FcmToken;
use App\Repositories\FcmTokenRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class FcmTokenController
 * @package App\Http\Controllers\API
 */

class FcmTokenAPIController extends AppBaseController
{
    /** @var  FcmTokenRepository */
    private $fcmTokenRepository;

    public function __construct(FcmTokenRepository $fcmTokenRepo)
    {
        $this->fcmTokenRepository = $fcmTokenRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @SWG\Get(
     *      path="/fcmTokens",
     *      summary="Get a listing of the FcmTokens.",
     *      tags={"FcmToken"},
     *      description="Get all FcmTokens",
     *      produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="array",
     *                  @SWG\Items(ref="#/definitions/FcmToken")
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->fcmTokenRepository->pushCriteria(new RequestCriteria($request));
        $this->fcmTokenRepository->pushCriteria(new LimitOffsetCriteria($request));
        $fcmTokens = $this->fcmTokenRepository->all();

        return $this->sendResponse($fcmTokens->toArray(), trans('custom.fcm_tokens_retrieved_successfully'));
    }

    /**
     * @param CreateFcmTokenAPIRequest $request
     * @return Response
     *
     * @SWG\Post(
     *      path="/fcmTokens",
     *      summary="Store a newly created FcmToken in storage",
     *      tags={"FcmToken"},
     *      description="Store FcmToken",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FcmToken that should be stored",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FcmToken")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FcmToken"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateFcmTokenAPIRequest $request)
    {
        $input = $request->all();

        $input = $this->convertArrayToValue($request->all());

        $user_id = \Helper::getEmployeeSystemID();
        
        $validator = \Validator::make(
            $input,
            [
                'fcm_token' => 'required'
            ]
        );

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $existingFcm = FcmToken::where([
                                    'fcm_token' => $input['fcm_token']
                                ])->where('userID', '!=', $user_id)
                                ->exists();

        if ($existingFcm) {
            FcmToken::where(['fcm_token' => $input['fcm_token']])->delete();
        }

        $token = FcmToken::where([
                            'fcm_token' => $input['fcm_token'],
                            'userID' => $user_id
                        ])->first();

        if (empty($token)) {
            FcmToken::where(['fcm_token' => $input['fcm_token']])->delete();
            $input['userID'] = $user_id;
            $fcmTokens = FcmToken::insert($input);

            return $this->sendResponse($fcmTokens, trans('custom.fcm_token_saved_successfully'));
        } else {
            $fcmTokens = $this->fcmTokenRepository->update([
                'fcm_token' => $input['fcm_token']
            ], $token->id);
            return $this->sendResponse([], trans('custom.fcm_token_saved_successfully'));
        }
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Get(
     *      path="/fcmTokens/{id}",
     *      summary="Display the specified FcmToken",
     *      tags={"FcmToken"},
     *      description="Get FcmToken",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FcmToken",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FcmToken"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var FcmToken $fcmToken */
        $fcmToken = $this->fcmTokenRepository->findWithoutFail($id);

        if (empty($fcmToken)) {
            return $this->sendError(trans('custom.fcm_token_not_found'));
        }

        return $this->sendResponse($fcmToken->toArray(), trans('custom.fcm_token_retrieved_successfully'));
    }

    /**
     * @param int $id
     * @param UpdateFcmTokenAPIRequest $request
     * @return Response
     *
     * @SWG\Put(
     *      path="/fcmTokens/{id}",
     *      summary="Update the specified FcmToken in storage",
     *      tags={"FcmToken"},
     *      description="Update FcmToken",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FcmToken",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Parameter(
     *          name="body",
     *          in="body",
     *          description="FcmToken that should be updated",
     *          required=false,
     *          @SWG\Schema(ref="#/definitions/FcmToken")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  ref="#/definitions/FcmToken"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateFcmTokenAPIRequest $request)
    {
        $input = $request->all();

        /** @var FcmToken $fcmToken */
        $fcmToken = $this->fcmTokenRepository->findWithoutFail($id);

        if (empty($fcmToken)) {
            return $this->sendError(trans('custom.fcm_token_not_found'));
        }

        $fcmToken = $this->fcmTokenRepository->update($input, $id);

        return $this->sendResponse($fcmToken->toArray(), trans('custom.fcmtoken_updated_successfully'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @SWG\Delete(
     *      path="/fcmTokens/{id}",
     *      summary="Remove the specified FcmToken from storage",
     *      tags={"FcmToken"},
     *      description="Delete FcmToken",
     *      produces={"application/json"},
     *      @SWG\Parameter(
     *          name="id",
     *          description="id of FcmToken",
     *          type="integer",
     *          required=true,
     *          in="path"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="successful operation",
     *          @SWG\Schema(
     *              type="object",
     *              @SWG\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @SWG\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @SWG\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var FcmToken $fcmToken */
        $fcmToken = $this->fcmTokenRepository->findWithoutFail($id);

        if (empty($fcmToken)) {
            return $this->sendError(trans('custom.fcm_token_not_found'));
        }

        $fcmToken->delete();

        return $this->sendSuccess('Fcm Token deleted successfully');
    }

    public function redirectHome(Request $request)
    {
        try {

            $scheme = request()->secure() ? 'https' : 'http';

            $url = $request->getHttpHost();
            $url_array = explode('.', $url);
            $subDomain = $url_array[0];
            if ($subDomain == 'www') {
                $subDomain = $url_array[1];
            }

            $tenantDomain = (isset(explode('-', $subDomain)[0])) ? explode('-', $subDomain)[0] : "";

            if ($tenantDomain != 'localhost:8000') {
                $homeUrl = $scheme."://".$tenantDomain.".".env('APP_DOMAIAN')."/#/home";
            } else {
                $homeUrl = null;
            }

            return $this->sendResponse(['homeUrl' => $homeUrl], trans('custom.successfully_redirected_to_home'));
        } catch (\Exception $exception) {
            return $this->sendError('Something went wrong');
        }
    }

    public function logoutApiUser(Request $request)
    {
        try {
            $logged_user = \Helper::getEmployeeSystemID();

            if (isset($request['fcm_token']) && $logged_user > 0) {
                FcmToken::where([
                            'fcm_token' => $request['fcm_token'],
                            'userID' => $logged_user
                        ])->delete();
            }


            $scheme = request()->secure() ? 'https' : 'http';

            $url = $request->getHttpHost();
            $url_array = explode('.', $url);
            $subDomain = $url_array[0];
            if ($subDomain == 'www') {
                $subDomain = $url_array[1];
            }

            $tenantDomain = (isset(explode('-', $subDomain)[0])) ? explode('-', $subDomain)[0] : "";

            if ($tenantDomain != 'localhost:8000') {
                 $logoutUrl = $scheme."://".$tenantDomain.".".env('APP_DOMAIAN')."/#/home?logout-from-hr=true";
            } else {
                 $logoutUrl = null;
            }

            $resp = [];
            $logged = Auth::check();
            if ($logged) {
                $resp = $request->user()->token()->revoke();
            }

            return $this->sendResponse([$resp, $logged, 'logoutUrl' => $logoutUrl], 'User logged out Successfully');
        } catch (\Exception $exception) {
            return $this->sendError('Something went wrong');
        }
    }

    public function getPortalRedirectUrl(Request $request)
    {
        try {

            $scheme = request()->secure() ? 'https' : 'http';

            $url = $request->getHttpHost();
            $url_array = explode('.', $url);
            $subDomain = $url_array[0];
            if ($subDomain == 'www') {
                $subDomain = $url_array[1];
            }

            $tenantDomain = (isset(explode('-', $subDomain)[0])) ? explode('-', $subDomain)[0] : "";

            if ($tenantDomain != 'localhost:8000') {
                $portalUrl = $scheme."://".$tenantDomain.".".env('APP_DOMAIAN')."/#/home";
            } else {
                $portalUrl = null;
            }

            return $this->sendResponse(['portalUrl' => $portalUrl], trans('custom.successfully_redirected_to_portal'));
        } catch (\Exception $exception) {
            return $this->sendError('Something went wrong');
        }
    }
}
