<?php

use Faker\Factory as Faker;
use App\Models\PerformaTemp;
use App\Repositories\PerformaTempRepository;

trait MakePerformaTempTrait
{
    /**
     * Create fake instance of PerformaTemp and save it in database
     *
     * @param array $performaTempFields
     * @return PerformaTemp
     */
    public function makePerformaTemp($performaTempFields = [])
    {
        /** @var PerformaTempRepository $performaTempRepo */
        $performaTempRepo = App::make(PerformaTempRepository::class);
        $theme = $this->fakePerformaTempData($performaTempFields);
        return $performaTempRepo->create($theme);
    }

    /**
     * Get fake instance of PerformaTemp
     *
     * @param array $performaTempFields
     * @return PerformaTemp
     */
    public function fakePerformaTemp($performaTempFields = [])
    {
        return new PerformaTemp($this->fakePerformaTempData($performaTempFields));
    }

    /**
     * Get fake data of PerformaTemp
     *
     * @param array $postFields
     * @return array
     */
    public function fakePerformaTempData($performaTempFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'performaMasterID' => $fake->randomDigitNotNull,
            'myStdTitle' => $fake->word,
            'companyID' => $fake->word,
            'contractid' => $fake->word,
            'performaInvoiceNo' => $fake->randomDigitNotNull,
            'sumofsumofStandbyAmount' => $fake->randomDigitNotNull,
            'TicketNo' => $fake->randomDigitNotNull,
            'myTicketNo' => $fake->word,
            'clientID' => $fake->word,
            'performaDate' => $fake->date('Y-m-d H:i:s'),
            'performaFinanceConfirmed' => $fake->randomDigitNotNull,
            'PerformaOpConfirmed' => $fake->randomDigitNotNull,
            'performaFinanceConfirmedBy' => $fake->word,
            'performaOpConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'performaFinanceConfirmedDate' => $fake->date('Y-m-d H:i:s'),
            'stdGLcode' => $fake->word,
            'sortOrder' => $fake->randomDigitNotNull,
            'timestamp' => $fake->date('Y-m-d H:i:s'),
            'proformaComment' => $fake->word,
            'isDiscount' => $fake->randomDigitNotNull,
            'discountDescription' => $fake->word,
            'DiscountPercentage' => $fake->randomDigitNotNull
        ], $performaTempFields);
    }
}
