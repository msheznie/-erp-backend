<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\AppBaseController;
use Response;
use Illuminate\Support\Facades\Storage;
use App\helper\Helper;

/**
 * Class ThirdPartySystemsDocumentUploadAndDownloadController
 * @package App\Http\Controllers\API
 */

class ThirdPartySystemsDocumentUploadAndDownloadAPIController extends AppBaseController
{


    public function __construct()
    {
    }

    /**
     * Display a listing of the YesNoSelectionForMinus.
     * GET|HEAD /yesNoSelectionForMinuses
     *
     * @param Request $request
     * @return Response
     */
    public function documentUpload(Request $request)
    {
        $input = $request->all();

        $validorMessages = [
            'ext.required' => 'File extension is required.',
            'size.required' => 'File size is required.',
            'file.required' => 'File is required.',
            'path.required' => 'Path is required.'
        ];

        $validator = \Validator::make($input, [
            'ext' => 'required',
            'size' => 'required',
            'file' => 'required',
            'path' => 'required',
        ], $validorMessages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $extension = $input['ext'];
        $size = $input['size'];
        $deleteYn = (isset($input['deleteYn']) && $input['deleteYn'] == 1) ? 1 : 0;
        $filePath = isset($input['filePath']) ? $input['filePath'] : null; // if deleteYn is 1 file path is required
        $file = $input['file'];
        $fileName = $input['file_name'];
        $decodeFile = base64_decode($file);
        $pathToUpload = $input['path'];
        $blockExtensions = [
            'ace', 'ade', 'adp', 'ani', 'app', 'asp', 'aspx', 'asx', 'bas', 'bat', 'cla', 'cer', 'chm', 'cmd', 'cnt', 'com',
            'cpl', 'crt', 'csh', 'class', 'der', 'docm', 'exe', 'fxp', 'gadget', 'hlp', 'hpj', 'hta', 'htc', 'inf', 'ins', 'isp', 'its', 'jar',
            'js', 'jse', 'ksh', 'lnk', 'mad', 'maf', 'mag', 'mam', 'maq', 'mar', 'mas', 'mat', 'mau', 'mav', 'maw', 'mda', 'mdb', 'mde', 'mdt',
            'mdw', 'mdz', 'mht', 'mhtml', 'msc', 'msh', 'msh1', 'msh1xml', 'msh2', 'msh2xml', 'mshxml', 'msi', 'msp', 'mst', 'ops', 'osd',
            'ocx', 'pl', 'pcd', 'pif', 'plg', 'prf', 'prg', 'ps1', 'ps1xml', 'ps2', 'ps2xml', 'psc1', 'psc2', 'pst', 'reg', 'scf', 'scr',
            'sct', 'shb', 'shs', 'tmp', 'url', 'vb', 'vbe', 'vbp', 'vbs', 'vsmacros', 'vss', 'vst', 'vsw', 'ws', 'wsc', 'wsf', 'wsh', 'xml',
            'xbap', 'xnk', 'php'
        ];

        try {
            if (in_array($extension, $blockExtensions)) {
                return $this->sendError("This type of file not allow to upload", 500);
            }

            /*  if (isset($size)) {
                if ($size > env('ATTACH_UPLOAD_SIZE_LIMIT')) {
                    return $this->sendError("Maximum allowed file size is exceeded. Please upload lesser than " . \Helper::bytesToHuman(env('ATTACH_UPLOAD_SIZE_LIMIT')), 500);
                }
            } */
            if ($deleteYn == 1) {
                if (!isset($filePath) || empty($filePath)) {
                    return $this->sendError("File path is required", 500);
                }

                if (Storage::disk('s3')->exists($filePath)) {
                    Storage::disk('s3')->delete($filePath);
                }
            }

            Storage::disk('s3')->put($fileName, $decodeFile);
            return $this->sendResponse([], "Document uploaded successfully");
        } catch (\Exception $exception) {
            return $this->sendError($exception, 500);
        }
    }

    public function documentDownload(Request $request)
    {
        $input = $request->all();
        $filePath = $input['filePath'];
        $validorMessages = [
            'filePath.required' => 'File path is required.',
        ];

        $validator = \Validator::make($input, [
            'filePath' => 'required'
        ], $validorMessages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        if (!is_null($filePath)) {
            if (Storage::disk('s3')->exists($filePath)) {
                return Storage::disk('s3')->download($filePath, 'File');
            } else {
                return $this->sendError(trans('custom.attachments_not_found'), 500);
            }
        } else {
            return $this->sendError('Attachment is not attached', 404);
        }
    }

    public function viewDocument(Request $request)
    {
        $input = $request->all();
        $filePath = $input['filePath'];
        $validorMessages = [
            'filePath.required' => 'File path is required.',
        ];

        $validator = \Validator::make($input, [
            'filePath' => 'required'
        ], $validorMessages);

        if ($validator->fails()) {
            return $this->sendError($validator->messages(), 422);
        }

        $documentUrl = "";
        if (Storage::disk('s3')->exists($filePath)) {
            $documentUrl = Helper::getFileUrlFromS3($filePath);
        }
        return $this->sendResponse($documentUrl, "Document retrived successfully");
    }

    public function viewDocumentEmployeeImg(Request $request)
    {
        $input = $request->all();
        $empImage = $input['empImage'];
        $empSignature = $input['empSignature'];
        $managerImg = $input['managerImg'];
        $gender = $input['gender'];
        $empMaster = [];

        if (Storage::disk('s3')->exists($empImage)) {
            $data['employee'] =  Helper::getFileUrlFromS3($empImage);
        } else {
            $img = ($gender == 1) ? 'images/users/male.png' : 'images/users/female.png';
            $data['employee']  = Helper::getFileUrlFromS3($img);
        }

        if (Storage::disk('s3')->exists($managerImg)) {
            $data['managerImg'] =  Helper::getFileUrlFromS3($managerImg);
        } else {
            $img = ($gender == 1) ? 'images/users/male.png' : 'images/users/female.png';
            $data['managerImg']  = Helper::getFileUrlFromS3($img);
        }

        if (Storage::disk('s3')->exists($empSignature)) {
            $data['empSignature'] =  Helper::getFileUrlFromS3($empSignature);
        } else {
            $img = 'images/users/No_Image.png';
            $data['empSignature']  = Helper::getFileUrlFromS3($img);
        }

        array_push($empMaster, $data);
        return $this->sendResponse($empMaster, "Document retrived successfully");
    }

    public function viewDocumentEmployeeImgBulk(Request $request)
    {
        $input = $request->all();
        $empData = $input['empData'];
        $array = [];
        if (!empty($empData)) {
            foreach ($empData as $val) {
                if (Storage::disk('s3')->exists($val['path'])) {
                    $documentUrl = Helper::getFileUrlFromS3($val['path']);
                } else {
                    $img = ($val['gender'] == 1) ? 'images/users/male.png' : 'images/users/female.png';
                    $documentUrl = Helper::getFileUrlFromS3($img);
                }
                $dataImg['empId'] = $val['empId'];
                $dataImg['path'] = $documentUrl;
                array_push($array, $dataImg);
            }
        }

        return $this->sendResponse($array, "Document retrived successfully");
    }

    public function documentUploadDelete(Request $request)
    {
        $input = $request->all();
        $filePath = $input['file_name'];

        if (Storage::disk('s3')->exists($filePath)) {
            Storage::disk('s3')->delete($filePath);
            return $this->sendResponse([], "Attachment deleted successfully");
        } else {
            return $this->sendResponse([], "Attachment not found ");
        }
    }
    public function viewHrDocuments(Request $request)
    {
        $input = $request->all();
        $MappingDataArrFilter = collect($input['hrDocs'])->map(function ($group) {
            $documentUrl = "";
            if (Storage::disk('s3')->exists($group['documentFile'])) {
                $documentUrl = Helper::getFileUrlFromS3($group['documentFile']);
            }
            $group['documentPath'] = $documentUrl;
            return $group;
        });
        return $this->sendResponse($MappingDataArrFilter, "Document retrived successfully");
    }
}
