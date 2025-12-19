<?php

use App\Models\CompanyNavigationMenus;
use App\Repositories\CompanyNavigationMenusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyNavigationMenusRepositoryTest extends TestCase
{
    use MakeCompanyNavigationMenusTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CompanyNavigationMenusRepository
     */
    protected $companyNavigationMenusRepo;

    public function setUp()
    {
        parent::setUp();
        $this->companyNavigationMenusRepo = App::make(CompanyNavigationMenusRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCompanyNavigationMenus()
    {
        $companyNavigationMenus = $this->fakeCompanyNavigationMenusData();
        $createdCompanyNavigationMenus = $this->companyNavigationMenusRepo->create($companyNavigationMenus);
        $createdCompanyNavigationMenus = $createdCompanyNavigationMenus->toArray();
        $this->assertArrayHasKey('id', $createdCompanyNavigationMenus);
        $this->assertNotNull($createdCompanyNavigationMenus['id'], 'Created CompanyNavigationMenus must have id specified');
        $this->assertNotNull(CompanyNavigationMenus::find($createdCompanyNavigationMenus['id']), 'CompanyNavigationMenus with given id must be in DB');
        $this->assertModelData($companyNavigationMenus, $createdCompanyNavigationMenus);
    }

    /**
     * @test read
     */
    public function testReadCompanyNavigationMenus()
    {
        $companyNavigationMenus = $this->makeCompanyNavigationMenus();
        $dbCompanyNavigationMenus = $this->companyNavigationMenusRepo->find($companyNavigationMenus->id);
        $dbCompanyNavigationMenus = $dbCompanyNavigationMenus->toArray();
        $this->assertModelData($companyNavigationMenus->toArray(), $dbCompanyNavigationMenus);
    }

    /**
     * @test update
     */
    public function testUpdateCompanyNavigationMenus()
    {
        $companyNavigationMenus = $this->makeCompanyNavigationMenus();
        $fakeCompanyNavigationMenus = $this->fakeCompanyNavigationMenusData();
        $updatedCompanyNavigationMenus = $this->companyNavigationMenusRepo->update($fakeCompanyNavigationMenus, $companyNavigationMenus->id);
        $this->assertModelData($fakeCompanyNavigationMenus, $updatedCompanyNavigationMenus->toArray());
        $dbCompanyNavigationMenus = $this->companyNavigationMenusRepo->find($companyNavigationMenus->id);
        $this->assertModelData($fakeCompanyNavigationMenus, $dbCompanyNavigationMenus->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCompanyNavigationMenus()
    {
        $companyNavigationMenus = $this->makeCompanyNavigationMenus();
        $resp = $this->companyNavigationMenusRepo->delete($companyNavigationMenus->id);
        $this->assertTrue($resp);
        $this->assertNull(CompanyNavigationMenus::find($companyNavigationMenus->id), 'CompanyNavigationMenus should not exist in DB');
    }
}
