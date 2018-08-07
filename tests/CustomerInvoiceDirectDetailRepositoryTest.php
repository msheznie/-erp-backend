<?php

use App\Models\CustomerInvoiceDirectDetail;
use App\Repositories\CustomerInvoiceDirectDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerInvoiceDirectDetailRepositoryTest extends TestCase
{
    use MakeCustomerInvoiceDirectDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerInvoiceDirectDetailRepository
     */
    protected $customerInvoiceDirectDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerInvoiceDirectDetailRepo = App::make(CustomerInvoiceDirectDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerInvoiceDirectDetail()
    {
        $customerInvoiceDirectDetail = $this->fakeCustomerInvoiceDirectDetailData();
        $createdCustomerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepo->create($customerInvoiceDirectDetail);
        $createdCustomerInvoiceDirectDetail = $createdCustomerInvoiceDirectDetail->toArray();
        $this->assertArrayHasKey('id', $createdCustomerInvoiceDirectDetail);
        $this->assertNotNull($createdCustomerInvoiceDirectDetail['id'], 'Created CustomerInvoiceDirectDetail must have id specified');
        $this->assertNotNull(CustomerInvoiceDirectDetail::find($createdCustomerInvoiceDirectDetail['id']), 'CustomerInvoiceDirectDetail with given id must be in DB');
        $this->assertModelData($customerInvoiceDirectDetail, $createdCustomerInvoiceDirectDetail);
    }

    /**
     * @test read
     */
    public function testReadCustomerInvoiceDirectDetail()
    {
        $customerInvoiceDirectDetail = $this->makeCustomerInvoiceDirectDetail();
        $dbCustomerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepo->find($customerInvoiceDirectDetail->id);
        $dbCustomerInvoiceDirectDetail = $dbCustomerInvoiceDirectDetail->toArray();
        $this->assertModelData($customerInvoiceDirectDetail->toArray(), $dbCustomerInvoiceDirectDetail);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerInvoiceDirectDetail()
    {
        $customerInvoiceDirectDetail = $this->makeCustomerInvoiceDirectDetail();
        $fakeCustomerInvoiceDirectDetail = $this->fakeCustomerInvoiceDirectDetailData();
        $updatedCustomerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepo->update($fakeCustomerInvoiceDirectDetail, $customerInvoiceDirectDetail->id);
        $this->assertModelData($fakeCustomerInvoiceDirectDetail, $updatedCustomerInvoiceDirectDetail->toArray());
        $dbCustomerInvoiceDirectDetail = $this->customerInvoiceDirectDetailRepo->find($customerInvoiceDirectDetail->id);
        $this->assertModelData($fakeCustomerInvoiceDirectDetail, $dbCustomerInvoiceDirectDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerInvoiceDirectDetail()
    {
        $customerInvoiceDirectDetail = $this->makeCustomerInvoiceDirectDetail();
        $resp = $this->customerInvoiceDirectDetailRepo->delete($customerInvoiceDirectDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerInvoiceDirectDetail::find($customerInvoiceDirectDetail->id), 'CustomerInvoiceDirectDetail should not exist in DB');
    }
}
