<?php

use App\Models\CustomerReceivePaymentDetail;
use App\Repositories\CustomerReceivePaymentDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerReceivePaymentDetailRepositoryTest extends TestCase
{
    use MakeCustomerReceivePaymentDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerReceivePaymentDetailRepository
     */
    protected $customerReceivePaymentDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerReceivePaymentDetailRepo = App::make(CustomerReceivePaymentDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerReceivePaymentDetail()
    {
        $customerReceivePaymentDetail = $this->fakeCustomerReceivePaymentDetailData();
        $createdCustomerReceivePaymentDetail = $this->customerReceivePaymentDetailRepo->create($customerReceivePaymentDetail);
        $createdCustomerReceivePaymentDetail = $createdCustomerReceivePaymentDetail->toArray();
        $this->assertArrayHasKey('id', $createdCustomerReceivePaymentDetail);
        $this->assertNotNull($createdCustomerReceivePaymentDetail['id'], 'Created CustomerReceivePaymentDetail must have id specified');
        $this->assertNotNull(CustomerReceivePaymentDetail::find($createdCustomerReceivePaymentDetail['id']), 'CustomerReceivePaymentDetail with given id must be in DB');
        $this->assertModelData($customerReceivePaymentDetail, $createdCustomerReceivePaymentDetail);
    }

    /**
     * @test read
     */
    public function testReadCustomerReceivePaymentDetail()
    {
        $customerReceivePaymentDetail = $this->makeCustomerReceivePaymentDetail();
        $dbCustomerReceivePaymentDetail = $this->customerReceivePaymentDetailRepo->find($customerReceivePaymentDetail->id);
        $dbCustomerReceivePaymentDetail = $dbCustomerReceivePaymentDetail->toArray();
        $this->assertModelData($customerReceivePaymentDetail->toArray(), $dbCustomerReceivePaymentDetail);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerReceivePaymentDetail()
    {
        $customerReceivePaymentDetail = $this->makeCustomerReceivePaymentDetail();
        $fakeCustomerReceivePaymentDetail = $this->fakeCustomerReceivePaymentDetailData();
        $updatedCustomerReceivePaymentDetail = $this->customerReceivePaymentDetailRepo->update($fakeCustomerReceivePaymentDetail, $customerReceivePaymentDetail->id);
        $this->assertModelData($fakeCustomerReceivePaymentDetail, $updatedCustomerReceivePaymentDetail->toArray());
        $dbCustomerReceivePaymentDetail = $this->customerReceivePaymentDetailRepo->find($customerReceivePaymentDetail->id);
        $this->assertModelData($fakeCustomerReceivePaymentDetail, $dbCustomerReceivePaymentDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerReceivePaymentDetail()
    {
        $customerReceivePaymentDetail = $this->makeCustomerReceivePaymentDetail();
        $resp = $this->customerReceivePaymentDetailRepo->delete($customerReceivePaymentDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerReceivePaymentDetail::find($customerReceivePaymentDetail->id), 'CustomerReceivePaymentDetail should not exist in DB');
    }
}
