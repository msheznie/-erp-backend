<?php namespace Tests\Repositories;

use App\Models\CustomerCatalogMaster;
use App\Repositories\CustomerCatalogMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerCatalogMasterTrait;
use Tests\ApiTestTrait;

class CustomerCatalogMasterRepositoryTest extends TestCase
{
    use MakeCustomerCatalogMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerCatalogMasterRepository
     */
    protected $customerCatalogMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerCatalogMasterRepo = \App::make(CustomerCatalogMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_catalog_master()
    {
        $customerCatalogMaster = $this->fakeCustomerCatalogMasterData();
        $createdCustomerCatalogMaster = $this->customerCatalogMasterRepo->create($customerCatalogMaster);
        $createdCustomerCatalogMaster = $createdCustomerCatalogMaster->toArray();
        $this->assertArrayHasKey('id', $createdCustomerCatalogMaster);
        $this->assertNotNull($createdCustomerCatalogMaster['id'], 'Created CustomerCatalogMaster must have id specified');
        $this->assertNotNull(CustomerCatalogMaster::find($createdCustomerCatalogMaster['id']), 'CustomerCatalogMaster with given id must be in DB');
        $this->assertModelData($customerCatalogMaster, $createdCustomerCatalogMaster);
    }

    /**
     * @test read
     */
    public function test_read_customer_catalog_master()
    {
        $customerCatalogMaster = $this->makeCustomerCatalogMaster();
        $dbCustomerCatalogMaster = $this->customerCatalogMasterRepo->find($customerCatalogMaster->id);
        $dbCustomerCatalogMaster = $dbCustomerCatalogMaster->toArray();
        $this->assertModelData($customerCatalogMaster->toArray(), $dbCustomerCatalogMaster);
    }

    /**
     * @test update
     */
    public function test_update_customer_catalog_master()
    {
        $customerCatalogMaster = $this->makeCustomerCatalogMaster();
        $fakeCustomerCatalogMaster = $this->fakeCustomerCatalogMasterData();
        $updatedCustomerCatalogMaster = $this->customerCatalogMasterRepo->update($fakeCustomerCatalogMaster, $customerCatalogMaster->id);
        $this->assertModelData($fakeCustomerCatalogMaster, $updatedCustomerCatalogMaster->toArray());
        $dbCustomerCatalogMaster = $this->customerCatalogMasterRepo->find($customerCatalogMaster->id);
        $this->assertModelData($fakeCustomerCatalogMaster, $dbCustomerCatalogMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_catalog_master()
    {
        $customerCatalogMaster = $this->makeCustomerCatalogMaster();
        $resp = $this->customerCatalogMasterRepo->delete($customerCatalogMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerCatalogMaster::find($customerCatalogMaster->id), 'CustomerCatalogMaster should not exist in DB');
    }
}
