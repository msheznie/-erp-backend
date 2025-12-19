<?php

use Faker\Factory as Faker;
use App\Models\TemplatesDetails;
use App\Repositories\TemplatesDetailsRepository;

trait MakeTemplatesDetailsTrait
{
    /**
     * Create fake instance of TemplatesDetails and save it in database
     *
     * @param array $templatesDetailsFields
     * @return TemplatesDetails
     */
    public function makeTemplatesDetails($templatesDetailsFields = [])
    {
        /** @var TemplatesDetailsRepository $templatesDetailsRepo */
        $templatesDetailsRepo = App::make(TemplatesDetailsRepository::class);
        $theme = $this->fakeTemplatesDetailsData($templatesDetailsFields);
        return $templatesDetailsRepo->create($theme);
    }

    /**
     * Get fake instance of TemplatesDetails
     *
     * @param array $templatesDetailsFields
     * @return TemplatesDetails
     */
    public function fakeTemplatesDetails($templatesDetailsFields = [])
    {
        return new TemplatesDetails($this->fakeTemplatesDetailsData($templatesDetailsFields));
    }

    /**
     * Get fake data of TemplatesDetails
     *
     * @param array $postFields
     * @return array
     */
    public function fakeTemplatesDetailsData($templatesDetailsFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'templatesMasterAutoID' => $fake->randomDigitNotNull,
            'templateDetailDescription' => $fake->word,
            'controlAccountID' => $fake->word,
            'controlAccountSubID' => $fake->randomDigitNotNull,
            'sortOrder' => $fake->randomDigitNotNull,
            'cashflowid' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $templatesDetailsFields);
    }
}
