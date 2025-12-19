<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ChequeRegisterDetail;
use App\Repositories\ChequeRegisterDetailRepository;

trait MakeChequeRegisterDetailTrait
{
    /**
     * Create fake instance of ChequeRegisterDetail and save it in database
     *
     * @param array $chequeRegisterDetailFields
     * @return ChequeRegisterDetail
     */
    public function makeChequeRegisterDetail($chequeRegisterDetailFields = [])
    {
        /** @var ChequeRegisterDetailRepository $chequeRegisterDetailRepo */
        $chequeRegisterDetailRepo = \App::make(ChequeRegisterDetailRepository::class);
        $theme = $this->fakeChequeRegisterDetailData($chequeRegisterDetailFields);
        return $chequeRegisterDetailRepo->create($theme);
    }

    /**
     * Get fake instance of ChequeRegisterDetail
     *
     * @param array $chequeRegisterDetailFields
     * @return ChequeRegisterDetail
     */
    public function fakeChequeRegisterDetail($chequeRegisterDetailFields = [])
    {
        return new ChequeRegisterDetail($this->fakeChequeRegisterDetailData($chequeRegisterDetailFields));
    }

    /**
     * Get fake data of ChequeRegisterDetail
     *
     * @param array $chequeRegisterDetailFields
     * @return array
     */
    public function fakeChequeRegisterDetailData($chequeRegisterDetailFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'cheque_register_master_id' => $fake->randomDigitNotNull,
            'cheque_no' => $fake->word,
            'description' => $fake->word,
            'created_at' => $fake->date('Y-m-d H:i:s'),
            'created_by' => $fake->randomDigitNotNull,
            'created_pc' => $fake->word,
            'updated_at' => $fake->date('Y-m-d H:i:s'),
            'updated_by' => $fake->randomDigitNotNull,
            'updated_pc' => $fake->word,
            'company_id' => $fake->randomDigitNotNull,
            'document_id' => $fake->randomDigitNotNull,
            'document_master_id' => $fake->randomDigitNotNull,
            'status' => $fake->randomDigitNotNull
        ], $chequeRegisterDetailFields);
    }
}
