<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ChequeRegister;
use App\Repositories\ChequeRegisterRepository;

trait MakeChequeRegisterTrait
{
    /**
     * Create fake instance of ChequeRegister and save it in database
     *
     * @param array $chequeRegisterFields
     * @return ChequeRegister
     */
    public function makeChequeRegister($chequeRegisterFields = [])
    {
        /** @var ChequeRegisterRepository $chequeRegisterRepo */
        $chequeRegisterRepo = \App::make(ChequeRegisterRepository::class);
        $theme = $this->fakeChequeRegisterData($chequeRegisterFields);
        return $chequeRegisterRepo->create($theme);
    }

    /**
     * Get fake instance of ChequeRegister
     *
     * @param array $chequeRegisterFields
     * @return ChequeRegister
     */
    public function fakeChequeRegister($chequeRegisterFields = [])
    {
        return new ChequeRegister($this->fakeChequeRegisterData($chequeRegisterFields));
    }

    /**
     * Get fake data of ChequeRegister
     *
     * @param array $chequeRegisterFields
     * @return array
     */
    public function fakeChequeRegisterData($chequeRegisterFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'master_description' => $fake->word,
            'bank_id' => $fake->randomDigitNotNull,
            'bank_account_id' => $fake->randomDigitNotNull,
            'no_of_cheques' => $fake->randomDigitNotNull,
            'started_cheque_no' => $fake->word,
            'ended_cheque_no' => $fake->word,
            'company_id' => $fake->randomDigitNotNull,
            'document_id' => $fake->randomDigitNotNull,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'created_by' => $fake->randomDigitNotNull,
            'created_pc' => $fake->word,
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'updated_by' => $fake->randomDigitNotNull,
            'updated_pc' => $fake->word
        ], $chequeRegisterFields);
    }
}
