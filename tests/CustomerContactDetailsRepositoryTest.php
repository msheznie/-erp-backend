<?php

use App\Models\CustomerContactDetails;
use App\Repositories\CustomerContactDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CustomerContactDetailsRepositoryTest extends TestCase
{
    use MakeCustomerContactDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomerContactDetailsRepository
     */
    protected $customerContactDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->customerContactDetailsRepo = App::make(CustomerContactDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateCustomerContactDetails()
    {
        $customerContactDetails = $this->fakeCustomerContactDetailsData();
        $createdCustomerContactDetails = $this->customerContactDetailsRepo->create($customerContactDetails);
        $createdCustomerContactDetails = $createdCustomerContactDetails->toArray();
        $this->assertArrayHasKey('id', $createdCustomerContactDetails);
        $this->assertNotNull($createdCustomerContactDetails['id'], 'Created CustomerContactDetails must have id specified');
        $this->assertNotNull(CustomerContactDetails::find($createdCustomerContactDetails['id']), 'CustomerContactDetails with given id must be in DB');
        $this->assertModelData($customerContactDetails, $createdCustomerContactDetails);
    }

    /**
     * @test read
     */
    public function testReadCustomerContactDetails()
    {
        $customerContactDetails = $this->makeCustomerContactDetails();
        $dbCustomerContactDetails = $this->customerContactDetailsRepo->find($customerContactDetails->id);
        $dbCustomerContactDetails = $dbCustomerContactDetails->toArray();
        $this->assertModelData($customerContactDetails->toArray(), $dbCustomerContactDetails);
    }

    /**
     * @test update
     */
    public function testUpdateCustomerContactDetails()
    {
        $customerContactDetails = $this->makeCustomerContactDetails();
        $fakeCustomerContactDetails = $this->fakeCustomerContactDetailsData();
        $updatedCustomerContactDetails = $this->customerContactDetailsRepo->update($fakeCustomerContactDetails, $customerContactDetails->id);
        $this->assertModelData($fakeCustomerContactDetails, $updatedCustomerContactDetails->toArray());
        $dbCustomerContactDetails = $this->customerContactDetailsRepo->find($customerContactDetails->id);
        $this->assertModelData($fakeCustomerContactDetails, $dbCustomerContactDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteCustomerContactDetails()
    {
        $customerContactDetails = $this->makeCustomerContactDetails();
        $resp = $this->customerContactDetailsRepo->delete($customerContactDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(CustomerContactDetails::find($customerContactDetails->id), 'CustomerContactDetails should not exist in DB');
    }
}
