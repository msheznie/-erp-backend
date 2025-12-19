<?php

use Faker\Factory as Faker;
use App\Models\DocumentMaster;
use App\Repositories\DocumentMasterRepository;

trait MakeDocumentMasterTrait
{
    /**
     * Create fake instance of DocumentMaster and save it in database
     *
     * @param array $documentMasterFields
     * @return DocumentMaster
     */
    public function makeDocumentMaster($documentMasterFields = [])
    {
        /** @var DocumentMasterRepository $documentMasterRepo */
        $documentMasterRepo = App::make(DocumentMasterRepository::class);
        $theme = $this->fakeDocumentMasterData($documentMasterFields);
        return $documentMasterRepo->create($theme);
    }

    /**
     * Get fake instance of DocumentMaster
     *
     * @param array $documentMasterFields
     * @return DocumentMaster
     */
    public function fakeDocumentMaster($documentMasterFields = [])
    {
        return new DocumentMaster($this->fakeDocumentMasterData($documentMasterFields));
    }

    /**
     * Get fake data of DocumentMaster
     *
     * @param array $postFields
     * @return array
     */
    public function fakeDocumentMasterData($documentMasterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'documentID' => $fake->word,
            'documentDescription' => $fake->word,
            'departmentID' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $documentMasterFields);
    }
}
