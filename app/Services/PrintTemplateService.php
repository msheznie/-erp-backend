<?php

namespace App\Services;

use App\Models\PrintTemplate;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Throwable;

class PrintTemplateService
{
    // print template related variables
    private $tempTemplateFile = 'print-templates/temp/';
    private $templateName = 'template.html';

    public function __construct()
    {

    }

    /**
     * get default set template from DB
     * @param $documentId
     * @return LengthAwarePaginator|Collection|mixed
     */
    public function getDefaultTemplateByDocumentId($documentId){
        return PrintTemplate::where('document_id', $documentId)
            ->defaultTemplate()
            ->first();
    }

    /**
     * save file
     * @param $config ['file', 'path', 'fileName', 'storageDriver']
     * @return string
     * @throws Exception
     */
    public function saveFile($config): string {
        try{
            $content        = $config['content'];
            $fileName       = $config['fileName'] ?? $this->templateName;
            $path           = $config['path'] ? $config['path'].$fileName : $fileName;
            $storageDriver  = $config['storageDriver'] ?? 'resource';

            $isUpload = Storage::disk($storageDriver)->put($path, $content);

            if(!$isUpload){
                throw new Exception("File couldn't uploaded");
            }

            // return Storage::disk($storageDriver)->url($path);
            // return Helper::getFileUrlFromS3($path);
            return $path;
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * remove file
     * @param $path
     * @param string $storageDriver
     * @return string
     * @throws Exception
     */
    public function removeFile($path, string $storageDriver = 'resource'): string {
        try{
            return Storage::disk($storageDriver)->delete($path);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * get file
     * @param $path
     * @param string $storageDriver
     * @return string
     * @throws Exception
     */
    public function getFile($path, string $storageDriver = 's3'): string {
        try{
            if(!Storage::disk($storageDriver)->exists($path)){
                throw new Exception("File doesn't exists");
            }

            return Storage::disk($storageDriver)->get($path);
        }catch(Exception $e){
            throw new Exception($e->getMessage());
        }
    }

    /**
     * get default template with data
     * @param $documentSystemID: 'document id for get template from db'
     * @param $data 'data for pass to view for render html'
     * @throws Throwable
     */
    public function getDefaultTemplateSource($documentSystemID, $data): ?string
    {
        $defaultTemplate = $this->getDefaultTemplateByDocumentId($documentSystemID);

        if(!empty($defaultTemplate)){
            // get file content from aws s3 for create temp file in local
            $fileContent = $this->getFile($defaultTemplate->template_url);

            $identifyName = time();
            $fileName = $identifyName.'.blade.php';

            $this->saveFile([
                'content' => $fileContent,
                'fileName' => $fileName,
                'path' => $this->tempTemplateFile
            ]);

            // get render file with data and store variable
            $html = view(str_replace('/',  '.', ($this->tempTemplateFile.$identifyName)), $data)->render();

            // after getting rendered html with data we stored that on a $file variable then remove that temp file
            $this->removeFile($this->tempTemplateFile.$fileName);

            return $html;
        }

        return null;
    }
}