<?php namespace Tests\Repositories;

use App\Models\CompanyPolicyCategoryTranslations;
use App\Repositories\CompanyPolicyCategoryTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CompanyPolicyCategoryTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyPolicyCategoryTranslationsRepository
     */
    protected $companyPolicyCategoryTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->companyPolicyCategoryTranslationsRepo = \App::make(CompanyPolicyCategoryTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_company_policy_category_translations()
    {
        $companyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->make()->toArray();

        $createdCompanyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepo->create($companyPolicyCategoryTranslations);

        $createdCompanyPolicyCategoryTranslations = $createdCompanyPolicyCategoryTranslations->toArray();
        $this->assertArrayHasKey('id', $createdCompanyPolicyCategoryTranslations);
        $this->assertNotNull($createdCompanyPolicyCategoryTranslations['id'], 'Created CompanyPolicyCategoryTranslations must have id specified');
        $this->assertNotNull(CompanyPolicyCategoryTranslations::find($createdCompanyPolicyCategoryTranslations['id']), 'CompanyPolicyCategoryTranslations with given id must be in DB');
        $this->assertModelData($companyPolicyCategoryTranslations, $createdCompanyPolicyCategoryTranslations);
    }

    /**
     * @test read
     */
    public function test_read_company_policy_category_translations()
    {
        $companyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->create();

        $dbCompanyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepo->find($companyPolicyCategoryTranslations->id);

        $dbCompanyPolicyCategoryTranslations = $dbCompanyPolicyCategoryTranslations->toArray();
        $this->assertModelData($companyPolicyCategoryTranslations->toArray(), $dbCompanyPolicyCategoryTranslations);
    }

    /**
     * @test update
     */
    public function test_update_company_policy_category_translations()
    {
        $companyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->create();
        $fakeCompanyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->make()->toArray();

        $updatedCompanyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepo->update($fakeCompanyPolicyCategoryTranslations, $companyPolicyCategoryTranslations->id);

        $this->assertModelData($fakeCompanyPolicyCategoryTranslations, $updatedCompanyPolicyCategoryTranslations->toArray());
        $dbCompanyPolicyCategoryTranslations = $this->companyPolicyCategoryTranslationsRepo->find($companyPolicyCategoryTranslations->id);
        $this->assertModelData($fakeCompanyPolicyCategoryTranslations, $dbCompanyPolicyCategoryTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_company_policy_category_translations()
    {
        $companyPolicyCategoryTranslations = factory(CompanyPolicyCategoryTranslations::class)->create();

        $resp = $this->companyPolicyCategoryTranslationsRepo->delete($companyPolicyCategoryTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(CompanyPolicyCategoryTranslations::find($companyPolicyCategoryTranslations->id), 'CompanyPolicyCategoryTranslations should not exist in DB');
    }
}
