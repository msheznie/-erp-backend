<?php namespace Tests\Repositories;

use App\Models\CompanyDigitalStamp;
use App\Repositories\CompanyDigitalStampRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CompanyDigitalStampRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyDigitalStampRepository
     */
    protected $companyDigitalStampRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->companyDigitalStampRepo = \App::make(CompanyDigitalStampRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_company_digital_stamp()
    {
        $companyDigitalStamp = factory(CompanyDigitalStamp::class)->make()->toArray();

        $createdCompanyDigitalStamp = $this->companyDigitalStampRepo->create($companyDigitalStamp);

        $createdCompanyDigitalStamp = $createdCompanyDigitalStamp->toArray();
        $this->assertArrayHasKey('id', $createdCompanyDigitalStamp);
        $this->assertNotNull($createdCompanyDigitalStamp['id'], 'Created CompanyDigitalStamp must have id specified');
        $this->assertNotNull(CompanyDigitalStamp::find($createdCompanyDigitalStamp['id']), 'CompanyDigitalStamp with given id must be in DB');
        $this->assertModelData($companyDigitalStamp, $createdCompanyDigitalStamp);
    }

    /**
     * @test read
     */
    public function test_read_company_digital_stamp()
    {
        $companyDigitalStamp = factory(CompanyDigitalStamp::class)->create();

        $dbCompanyDigitalStamp = $this->companyDigitalStampRepo->find($companyDigitalStamp->id);

        $dbCompanyDigitalStamp = $dbCompanyDigitalStamp->toArray();
        $this->assertModelData($companyDigitalStamp->toArray(), $dbCompanyDigitalStamp);
    }

    /**
     * @test update
     */
    public function test_update_company_digital_stamp()
    {
        $companyDigitalStamp = factory(CompanyDigitalStamp::class)->create();
        $fakeCompanyDigitalStamp = factory(CompanyDigitalStamp::class)->make()->toArray();

        $updatedCompanyDigitalStamp = $this->companyDigitalStampRepo->update($fakeCompanyDigitalStamp, $companyDigitalStamp->id);

        $this->assertModelData($fakeCompanyDigitalStamp, $updatedCompanyDigitalStamp->toArray());
        $dbCompanyDigitalStamp = $this->companyDigitalStampRepo->find($companyDigitalStamp->id);
        $this->assertModelData($fakeCompanyDigitalStamp, $dbCompanyDigitalStamp->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_company_digital_stamp()
    {
        $companyDigitalStamp = factory(CompanyDigitalStamp::class)->create();

        $resp = $this->companyDigitalStampRepo->delete($companyDigitalStamp->id);

        $this->assertTrue($resp);
        $this->assertNull(CompanyDigitalStamp::find($companyDigitalStamp->id), 'CompanyDigitalStamp should not exist in DB');
    }
}
