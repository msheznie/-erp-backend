<?php

use Faker\Factory as Faker;
use App\Models\YesNoSelectionForMinus;
use App\Repositories\YesNoSelectionForMinusRepository;

trait MakeYesNoSelectionForMinusTrait
{
    /**
     * Create fake instance of YesNoSelectionForMinus and save it in database
     *
     * @param array $yesNoSelectionForMinusFields
     * @return YesNoSelectionForMinus
     */
    public function makeYesNoSelectionForMinus($yesNoSelectionForMinusFields = [])
    {
        /** @var YesNoSelectionForMinusRepository $yesNoSelectionForMinusRepo */
        $yesNoSelectionForMinusRepo = App::make(YesNoSelectionForMinusRepository::class);
        $theme = $this->fakeYesNoSelectionForMinusData($yesNoSelectionForMinusFields);
        return $yesNoSelectionForMinusRepo->create($theme);
    }

    /**
     * Get fake instance of YesNoSelectionForMinus
     *
     * @param array $yesNoSelectionForMinusFields
     * @return YesNoSelectionForMinus
     */
    public function fakeYesNoSelectionForMinus($yesNoSelectionForMinusFields = [])
    {
        return new YesNoSelectionForMinus($this->fakeYesNoSelectionForMinusData($yesNoSelectionForMinusFields));
    }

    /**
     * Get fake data of YesNoSelectionForMinus
     *
     * @param array $postFields
     * @return array
     */
    public function fakeYesNoSelectionForMinusData($yesNoSelectionForMinusFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'selection' => $fake->word
        ], $yesNoSelectionForMinusFields);
    }
}
