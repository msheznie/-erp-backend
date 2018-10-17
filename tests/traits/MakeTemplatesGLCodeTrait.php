<?php

use Faker\Factory as Faker;
use App\Models\TemplatesGLCode;
use App\Repositories\TemplatesGLCodeRepository;

trait MakeTemplatesGLCodeTrait
{
    /**
     * Create fake instance of TemplatesGLCode and save it in database
     *
     * @param array $templatesGLCodeFields
     * @return TemplatesGLCode
     */
    public function makeTemplatesGLCode($templatesGLCodeFields = [])
    {
        /** @var TemplatesGLCodeRepository $templatesGLCodeRepo */
        $templatesGLCodeRepo = App::make(TemplatesGLCodeRepository::class);
        $theme = $this->fakeTemplatesGLCodeData($templatesGLCodeFields);
        return $templatesGLCodeRepo->create($theme);
    }

    /**
     * Get fake instance of TemplatesGLCode
     *
     * @param array $templatesGLCodeFields
     * @return TemplatesGLCode
     */
    public function fakeTemplatesGLCode($templatesGLCodeFields = [])
    {
        return new TemplatesGLCode($this->fakeTemplatesGLCodeData($templatesGLCodeFields));
    }

    /**
     * Get fake data of TemplatesGLCode
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTemplatesGLCodeData($templatesGLCodeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'templateMasterID' => $fake->randomDigitNotNull,
            'templatesDetailsAutoID' => $fake->randomDigitNotNull,
            'chartOfAccountSystemID' => $fake->randomDigitNotNull,
            'glCode' => $fake->word,
            'glDescription' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'erp_templatesglcodecol' => $fake->word
        ], $templatesGLCodeFields);
    }
}
