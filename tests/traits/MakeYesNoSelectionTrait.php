<?php

use Faker\Factory as Faker;
use App\Models\YesNoSelection;
use App\Repositories\YesNoSelectionRepository;

trait MakeYesNoSelectionTrait
{
    /**
     * Create fake instance of YesNoSelection and save it in database
     *
     * @param array $yesNoSelectionFields
     * @return YesNoSelection
     */
    public function makeYesNoSelection($yesNoSelectionFields = [])
    {
        /** @var YesNoSelectionRepository $yesNoSelectionRepo */
        $yesNoSelectionRepo = App::make(YesNoSelectionRepository::class);
        $theme = $this->fakeYesNoSelectionData($yesNoSelectionFields);
        return $yesNoSelectionRepo->create($theme);
    }

    /**
     * Get fake instance of YesNoSelection
     *
     * @param array $yesNoSelectionFields
     * @return YesNoSelection
     */
    public function fakeYesNoSelection($yesNoSelectionFields = [])
    {
        return new YesNoSelection($this->fakeYesNoSelectionData($yesNoSelectionFields));
    }

    /**
     * Get fake data of YesNoSelection
     *
     * @param array $postFields
     * @return array
     */
    public function fakeYesNoSelectionData($yesNoSelectionFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'YesNo' => $fake->word
        ], $yesNoSelectionFields);
    }
}
