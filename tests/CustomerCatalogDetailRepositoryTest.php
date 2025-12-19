<?php namespace Tests\Repositories;

use App\Models\CustomerCatalogDetail;
use App\Repositories\CustomerCatalogDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCustomerCatalogDetailTrait;
use Tests\ApiTestTrait;

class CustomerCatalogDetailRepositoryTest extends TestCase
{
    use MakeCustomerCatalogDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerCatalogDetailRepository
     */
    protected $customerCatalogDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customerCatalogDetailRepo = \App::make(CustomerCatalogDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_customer_catalog_detail()
    {
        $customerCatalogDetail = $this->fakeCustomerCatalogDetailData();
        $createdCustomerCatalogDetail = $this->customerCatalogDetailRepo->create($customerCatalogDetail);
        $createdCustomerCatalogDetail = $createdCustomerCatalogDetail->toArray();
        $this->assertArrayHasKey('id', $createdCustomerCatalogDetail);
        $this->assertNotNull($createdCustomerCatalogDetail['id'], 'Created CustomerCatalogDetail must have id specified');
        $this->assertNotNull(CustomerCatalogDetail::find($createdCustomerCatalogDetail['id']), 'CustomerCatalogDetail with given id must be in DB');
        $this->assertModelData($customerCatalogDetail, $createdCustomerCatalogDetail);
    }

    /**
     * @test read
     */
    public function test_read_customer_catalog_detail()
    {
        $customerCatalogDetail = $this->makeCustomerCatalogDetail();
        $dbCustomerCatalogDetail = $this->customerCatalogDetailRepo->find($customerCatalogDetail->id);
        $dbCustomerCatalogDetail = $dbCustomerCatalogDetail->toArray();
        $this->assertModelData($customerCatalogDetail->toArray(), $dbCustomerCatalogDetail);
    }

    /**
     * @test update
     */
    public function test_update_customer_catalog_detail()
    {
        $customerCatalogDetail = $this->makeCustomerCatalogDetail();
        $fakeCustomerCatalogDetail = $this->fakeCustomerCatalogDetailData();
        $updatedCustomerCatalogDetail = $this->customerCatalogDetailRepo->update($fakeCustomerCatalogDetail, $customerCatalogDetail->id);
        $this->assertModelData($fakeCustomerCatalogDetail, $updatedCustomerCatalogDetail->toArray());
        $dbCustomerCatalogDetail = $this->customerCatalogDetailRepo->find($customerCatalogDetail->id);
        $this->assertModelData($fakeCustomerCatalogDetail, $dbCustomerCatalogDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_customer_catalog_detail()
    {
        $customerCatalogDetail = $this->makeCustomerCatalogDetail();
        $resp = $this->customerCatalogDetailRepo->delete($customerCatalogDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerCatalogDetail::find($customerCatalogDetail->id), 'CustomerCatalogDetail should not exist in DB');
    }
}
