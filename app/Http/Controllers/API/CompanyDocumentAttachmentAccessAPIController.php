<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\API\CreateCompanyDocumentAttachmentAccessAPIRequest;
use App\Http\Requests\API\UpdateCompanyDocumentAttachmentAccessAPIRequest;
use App\Models\CompanyDocumentAttachmentAccess;
use App\Repositories\CompanyDocumentAttachmentAccessRepository;
use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use InfyOm\Generator\Criteria\LimitOffsetCriteria;
use Prettus\Repository\Criteria\RequestCriteria;
use Response;

/**
 * Class CompanyDocumentAttachmentAccessController
 * @package App\Http\Controllers\API
 */

class CompanyDocumentAttachmentAccessAPIController extends AppBaseController
{
    /** @var  CompanyDocumentAttachmentAccessRepository */
    private $companyDocumentAttachmentAccessRepository;

    public function __construct(CompanyDocumentAttachmentAccessRepository $companyDocumentAttachmentAccessRepo)
    {
        $this->companyDocumentAttachmentAccessRepository = $companyDocumentAttachmentAccessRepo;
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Get(
     *      path="/companyDocumentAttachmentAccesses",
     *      summary="getCompanyDocumentAttachmentAccessList",
     *      tags={"CompanyDocumentAttachmentAccess"},
     *      description="Get all CompanyDocumentAttachmentAccesses",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="array",
     *                  @OA\Items(ref="#/definitions/CompanyDocumentAttachmentAccess")
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function index(Request $request)
    {
        $this->companyDocumentAttachmentAccessRepository->pushCriteria(new RequestCriteria($request));
        $this->companyDocumentAttachmentAccessRepository->pushCriteria(new LimitOffsetCriteria($request));
        $companyDocumentAttachmentAccesses = $this->companyDocumentAttachmentAccessRepository->all();

        return $this->sendResponse($companyDocumentAttachmentAccesses->toArray(), trans('custom.company_document_attachment_accesses_retrieved_suc'));
    }

    /**
     * @param Request $request
     * @return Response
     *
     * @OA\Post(
     *      path="/companyDocumentAttachmentAccesses",
     *      summary="createCompanyDocumentAttachmentAccess",
     *      tags={"CompanyDocumentAttachmentAccess"},
     *      description="Create CompanyDocumentAttachmentAccess",
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CompanyDocumentAttachmentAccess"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function store(CreateCompanyDocumentAttachmentAccessAPIRequest $request)
    {
        $input = $request->all();

        $companyDocumentAttachmentAccess = $this->companyDocumentAttachmentAccessRepository->create($input);

        return $this->sendResponse($companyDocumentAttachmentAccess->toArray(), trans('custom.company_document_attachment_access_saved_successfu'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Get(
     *      path="/companyDocumentAttachmentAccesses/{id}",
     *      summary="getCompanyDocumentAttachmentAccessItem",
     *      tags={"CompanyDocumentAttachmentAccess"},
     *      description="Get CompanyDocumentAttachmentAccess",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyDocumentAttachmentAccess",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CompanyDocumentAttachmentAccess"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function show($id)
    {
        /** @var CompanyDocumentAttachmentAccess $companyDocumentAttachmentAccess */
        $companyDocumentAttachmentAccess = $this->companyDocumentAttachmentAccessRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachmentAccess)) {
            return $this->sendError(trans('custom.company_document_attachment_access_not_found'));
        }

        return $this->sendResponse($companyDocumentAttachmentAccess->toArray(), trans('custom.company_document_attachment_access_retrieved_succe'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @return Response
     *
     * @OA\Put(
     *      path="/companyDocumentAttachmentAccesses/{id}",
     *      summary="updateCompanyDocumentAttachmentAccess",
     *      tags={"CompanyDocumentAttachmentAccess"},
     *      description="Update CompanyDocumentAttachmentAccess",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyDocumentAttachmentAccess",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\RequestBody(
     *        required=true,
     *        @OA\MediaType(
     *            mediaType="application/x-www-form-urlencoded",
     *            @OA\Schema(
     *                type="object",
     *                required={""},
     *                @OA\Property(
     *                    property="name",
     *                    description="desc",
     *                    type="string"
     *                )
     *            )
     *        )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  ref="#/definitions/CompanyDocumentAttachmentAccess"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function update($id, UpdateCompanyDocumentAttachmentAccessAPIRequest $request)
    {
        $input = $request->all();

        /** @var CompanyDocumentAttachmentAccess $companyDocumentAttachmentAccess */
        $companyDocumentAttachmentAccess = $this->companyDocumentAttachmentAccessRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachmentAccess)) {
            return $this->sendError(trans('custom.company_document_attachment_access_not_found'));
        }

        $companyDocumentAttachmentAccess = $this->companyDocumentAttachmentAccessRepository->update($input, $id);

        return $this->sendResponse($companyDocumentAttachmentAccess->toArray(), trans('custom.companydocumentattachmentaccess_updated_successful'));
    }

    /**
     * @param int $id
     * @return Response
     *
     * @OA\Delete(
     *      path="/companyDocumentAttachmentAccesses/{id}",
     *      summary="deleteCompanyDocumentAttachmentAccess",
     *      tags={"CompanyDocumentAttachmentAccess"},
     *      description="Delete CompanyDocumentAttachmentAccess",
     *      @OA\Parameter(
     *          name="id",
     *          description="id of CompanyDocumentAttachmentAccess",
     *           @OA\Schema(
     *             type="integer"
     *          ),
     *          required=true,
     *          in="path"
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\Schema(
     *              type="object",
     *              @OA\Property(
     *                  property="success",
     *                  type="boolean"
     *              ),
     *              @OA\Property(
     *                  property="data",
     *                  type="string"
     *              ),
     *              @OA\Property(
     *                  property="message",
     *                  type="string"
     *              )
     *          )
     *      )
     * )
     */
    public function destroy($id)
    {
        /** @var CompanyDocumentAttachmentAccess $companyDocumentAttachmentAccess */
        $companyDocumentAttachmentAccess = $this->companyDocumentAttachmentAccessRepository->findWithoutFail($id);

        if (empty($companyDocumentAttachmentAccess)) {
            return $this->sendError(trans('custom.company_document_attachment_access_not_found'));
        }

        $companyDocumentAttachmentAccess->delete();

        return $this->sendSuccess('Company Document Attachment Access deleted successfully');
    }
}
