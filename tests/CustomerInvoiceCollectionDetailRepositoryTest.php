<?php

use App\Models\CustomerInvoiceCollectionDetail;
use App\Repositories\CustomerInvoiceCollectionDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceCollectionDetailRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceCollectionDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceCollectionDetailRepository
     */
    protected $customerInvoiceCollectionDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerInvoiceCollectionDetailRepo = App::make(CustomerInvoiceCollectionDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerInvoiceCollectionDetail()
    {
        $customerInvoiceCollectionDetail = $this->fakeCustomerInvoiceCollectionDetailData();
        $createdCustomerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepo->create($customerInvoiceCollectionDetail);
        $createdCustomerInvoiceCollectionDetail = $createdCustomerInvoiceCollectionDetail->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceCollectionDetail);
        $this->assertNotNull($createdCustomerInvoiceCollectionDetail['id'], 'Created CustomerInvoiceCollectionDetail must have id specified');
        $this->assertNotNull(CustomerInvoiceCollectionDetail::find($createdCustomerInvoiceCollectionDetail['id']), 'CustomerInvoiceCollectionDetail with given id must be in DB');
        $this->assertModelData($customerInvoiceCollectionDetail, $createdCustomerInvoiceCollectionDetail);
    }

    /**
     * @test read
     */
    public function testReadCustomerInvoiceCollectionDetail()
    {
        $customerInvoiceCollectionDetail = $this->makeCustomerInvoiceCollectionDetail();
        $dbCustomerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepo->find($customerInvoiceCollectionDetail->id);
        $dbCustomerInvoiceCollectionDetail = $dbCustomerInvoiceCollectionDetail->toArray();
        $this->assertModelData($customerInvoiceCollectionDetail->toArray(), $dbCustomerInvoiceCollectionDetail);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerInvoiceCollectionDetail()
    {
        $customerInvoiceCollectionDetail = $this->makeCustomerInvoiceCollectionDetail();
        $fakeCustomerInvoiceCollectionDetail = $this->fakeCustomerInvoiceCollectionDetailData();
        $updatedCustomerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepo->update($fakeCustomerInvoiceCollectionDetail, $customerInvoiceCollectionDetail->id);
        $this->assertModelData($fakeCustomerInvoiceCollectionDetail, $updatedCustomerInvoiceCollectionDetail->toArray());
        $dbCustomerInvoiceCollectionDetail = $this->customerInvoiceCollectionDetailRepo->find($customerInvoiceCollectionDetail->id);
        $this->assertModelData($fakeCustomerInvoiceCollectionDetail, $dbCustomerInvoiceCollectionDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerInvoiceCollectionDetail()
    {
        $customerInvoiceCollectionDetail = $this->makeCustomerInvoiceCollectionDetail();
        $resp = $this->customerInvoiceCollectionDetailRepo->delete($customerInvoiceCollectionDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceCollectionDetail::find($customerInvoiceCollectionDetail->id), 'CustomerInvoiceCollectionDetail should not exist in DB');
    }
}
