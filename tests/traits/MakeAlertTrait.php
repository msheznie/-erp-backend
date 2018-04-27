<?php

use Faker\Factory as Faker;
use App\Models\Alert;
use App\Repositories\AlertRepository;

trait MakeAlertTrait
{
    /**
     * Create fake instance of Alert and save it in database
     *
     * @param array $alertFields
     * @return Alert
     */
    public function makeAlert($alertFields = [])
    {
        /** @var AlertRepository $alertRepo */
        $alertRepo = App::make(AlertRepository::class);
        $theme = $this->fakeAlertData($alertFields);
        return $alertRepo->create($theme);
    }

    /**
     * Get fake instance of Alert
     *
     * @param array $alertFields
     * @return Alert
     */
    public function fakeAlert($alertFields = [])
    {
        return new Alert($this->fakeAlertData($alertFields));
    }

    /**
     * Get fake data of Alert
     *
     * @param array $postFields
     * @return array
     */
    public function fakeAlertData($alertFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'companyID' => $fake->word,
            'empID' => $fake->word,
            'docID' => $fake->word,
            'docApprovedYN' => $fake->randomDigitNotNull,
            'docSystemCode' => $fake->randomDigitNotNull,
            'docCode' => $fake->word,
            'alertMessage' => $fake->text,
            'alertDateTime' => $fake->date('Y-m-d H:i:s'),
            'alertViewedYN' => $fake->randomDigitNotNull,
            'alertViewedDateTime' => $fake->date('Y-m-d H:i:s'),
            'empName' => $fake->word,
            'empEmail' => $fake->word,
            'ccEmailID' => $fake->word,
            'emailAlertMessage' => $fake->text,
            'isEmailSend' => $fake->randomDigitNotNull,
            'attachmentFileName' => $fake->word,
            'timeStamp' => $fake->date('Y-m-d H:i:s')
        ], $alertFields);
    }
}
