<?php namespace Tests\Repositories;

use App\Models\SMECompanyPolicyValue;
use App\Repositories\SMECompanyPolicyValueRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMECompanyPolicyValueRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMECompanyPolicyValueRepository
     */
    protected $sMECompanyPolicyValueRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMECompanyPolicyValueRepo = \App::make(SMECompanyPolicyValueRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_company_policy_value()
    {
        $sMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->make()->toArray();

        $createdSMECompanyPolicyValue = $this->sMECompanyPolicyValueRepo->create($sMECompanyPolicyValue);

        $createdSMECompanyPolicyValue = $createdSMECompanyPolicyValue->toArray();
        $this->assertArrayHasKey('id', $createdSMECompanyPolicyValue);
        $this->assertNotNull($createdSMECompanyPolicyValue['id'], 'Created SMECompanyPolicyValue must have id specified');
        $this->assertNotNull(SMECompanyPolicyValue::find($createdSMECompanyPolicyValue['id']), 'SMECompanyPolicyValue with given id must be in DB');
        $this->assertModelData($sMECompanyPolicyValue, $createdSMECompanyPolicyValue);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_company_policy_value()
    {
        $sMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->create();

        $dbSMECompanyPolicyValue = $this->sMECompanyPolicyValueRepo->find($sMECompanyPolicyValue->id);

        $dbSMECompanyPolicyValue = $dbSMECompanyPolicyValue->toArray();
        $this->assertModelData($sMECompanyPolicyValue->toArray(), $dbSMECompanyPolicyValue);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_company_policy_value()
    {
        $sMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->create();
        $fakeSMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->make()->toArray();

        $updatedSMECompanyPolicyValue = $this->sMECompanyPolicyValueRepo->update($fakeSMECompanyPolicyValue, $sMECompanyPolicyValue->id);

        $this->assertModelData($fakeSMECompanyPolicyValue, $updatedSMECompanyPolicyValue->toArray());
        $dbSMECompanyPolicyValue = $this->sMECompanyPolicyValueRepo->find($sMECompanyPolicyValue->id);
        $this->assertModelData($fakeSMECompanyPolicyValue, $dbSMECompanyPolicyValue->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_company_policy_value()
    {
        $sMECompanyPolicyValue = factory(SMECompanyPolicyValue::class)->create();

        $resp = $this->sMECompanyPolicyValueRepo->delete($sMECompanyPolicyValue->id);

        $this->assertTrue($resp);
        $this->assertNull(SMECompanyPolicyValue::find($sMECompanyPolicyValue->id), 'SMECompanyPolicyValue should not exist in DB');
    }
}
