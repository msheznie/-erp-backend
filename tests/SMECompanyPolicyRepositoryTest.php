<?php namespace Tests\Repositories;

use App\Models\SMECompanyPolicy;
use App\Repositories\SMECompanyPolicyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMECompanyPolicyRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMECompanyPolicyRepository
     */
    protected $sMECompanyPolicyRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMECompanyPolicyRepo = \App::make(SMECompanyPolicyRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_company_policy()
    {
        $sMECompanyPolicy = factory(SMECompanyPolicy::class)->make()->toArray();

        $createdSMECompanyPolicy = $this->sMECompanyPolicyRepo->create($sMECompanyPolicy);

        $createdSMECompanyPolicy = $createdSMECompanyPolicy->toArray();
        $this->assertArrayHasKey('id', $createdSMECompanyPolicy);
        $this->assertNotNull($createdSMECompanyPolicy['id'], 'Created SMECompanyPolicy must have id specified');
        $this->assertNotNull(SMECompanyPolicy::find($createdSMECompanyPolicy['id']), 'SMECompanyPolicy with given id must be in DB');
        $this->assertModelData($sMECompanyPolicy, $createdSMECompanyPolicy);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_company_policy()
    {
        $sMECompanyPolicy = factory(SMECompanyPolicy::class)->create();

        $dbSMECompanyPolicy = $this->sMECompanyPolicyRepo->find($sMECompanyPolicy->id);

        $dbSMECompanyPolicy = $dbSMECompanyPolicy->toArray();
        $this->assertModelData($sMECompanyPolicy->toArray(), $dbSMECompanyPolicy);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_company_policy()
    {
        $sMECompanyPolicy = factory(SMECompanyPolicy::class)->create();
        $fakeSMECompanyPolicy = factory(SMECompanyPolicy::class)->make()->toArray();

        $updatedSMECompanyPolicy = $this->sMECompanyPolicyRepo->update($fakeSMECompanyPolicy, $sMECompanyPolicy->id);

        $this->assertModelData($fakeSMECompanyPolicy, $updatedSMECompanyPolicy->toArray());
        $dbSMECompanyPolicy = $this->sMECompanyPolicyRepo->find($sMECompanyPolicy->id);
        $this->assertModelData($fakeSMECompanyPolicy, $dbSMECompanyPolicy->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_company_policy()
    {
        $sMECompanyPolicy = factory(SMECompanyPolicy::class)->create();

        $resp = $this->sMECompanyPolicyRepo->delete($sMECompanyPolicy->id);

        $this->assertTrue($resp);
        $this->assertNull(SMECompanyPolicy::find($sMECompanyPolicy->id), 'SMECompanyPolicy should not exist in DB');
    }
}
