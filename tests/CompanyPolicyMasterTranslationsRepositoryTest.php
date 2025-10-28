<?php namespace Tests\Repositories;

use App\Models\CompanyPolicyMasterTranslations;
use App\Repositories\CompanyPolicyMasterTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CompanyPolicyMasterTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyPolicyMasterTranslationsRepository
     */
    protected $companyPolicyMasterTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->companyPolicyMasterTranslationsRepo = \App::make(CompanyPolicyMasterTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_company_policy_master_translations()
    {
        $companyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->make()->toArray();

        $createdCompanyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepo->create($companyPolicyMasterTranslations);

        $createdCompanyPolicyMasterTranslations = $createdCompanyPolicyMasterTranslations->toArray();
        $this->assertArrayHasKey('id', $createdCompanyPolicyMasterTranslations);
        $this->assertNotNull($createdCompanyPolicyMasterTranslations['id'], 'Created CompanyPolicyMasterTranslations must have id specified');
        $this->assertNotNull(CompanyPolicyMasterTranslations::find($createdCompanyPolicyMasterTranslations['id']), 'CompanyPolicyMasterTranslations with given id must be in DB');
        $this->assertModelData($companyPolicyMasterTranslations, $createdCompanyPolicyMasterTranslations);
    }

    /**
     * @test read
     */
    public function test_read_company_policy_master_translations()
    {
        $companyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->create();

        $dbCompanyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepo->find($companyPolicyMasterTranslations->id);

        $dbCompanyPolicyMasterTranslations = $dbCompanyPolicyMasterTranslations->toArray();
        $this->assertModelData($companyPolicyMasterTranslations->toArray(), $dbCompanyPolicyMasterTranslations);
    }

    /**
     * @test update
     */
    public function test_update_company_policy_master_translations()
    {
        $companyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->create();
        $fakeCompanyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->make()->toArray();

        $updatedCompanyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepo->update($fakeCompanyPolicyMasterTranslations, $companyPolicyMasterTranslations->id);

        $this->assertModelData($fakeCompanyPolicyMasterTranslations, $updatedCompanyPolicyMasterTranslations->toArray());
        $dbCompanyPolicyMasterTranslations = $this->companyPolicyMasterTranslationsRepo->find($companyPolicyMasterTranslations->id);
        $this->assertModelData($fakeCompanyPolicyMasterTranslations, $dbCompanyPolicyMasterTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_company_policy_master_translations()
    {
        $companyPolicyMasterTranslations = factory(CompanyPolicyMasterTranslations::class)->create();

        $resp = $this->companyPolicyMasterTranslationsRepo->delete($companyPolicyMasterTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(CompanyPolicyMasterTranslations::find($companyPolicyMasterTranslations->id), 'CompanyPolicyMasterTranslations should not exist in DB');
    }
}
