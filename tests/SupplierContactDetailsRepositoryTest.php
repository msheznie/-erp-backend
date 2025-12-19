<?php

use App\Models\SupplierContactDetails;
use App\Repositories\SupplierContactDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SupplierContactDetailsRepositoryTest extends TestCase
{
    use MakeSupplierContactDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierContactDetailsRepository
     */
    protected $supplierContactDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->supplierContactDetailsRepo = App::make(SupplierContactDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSupplierContactDetails()
    {
        $supplierContactDetails = $this->fakeSupplierContactDetailsData();
        $createdSupplierContactDetails = $this->supplierContactDetailsRepo->create($supplierContactDetails);
        $createdSupplierContactDetails = $createdSupplierContactDetails->toArray();
        $this->assertArrayHasKey('id', $createdSupplierContactDetails);
        $this->assertNotNull($createdSupplierContactDetails['id'], 'Created SupplierContactDetails must have id specified');
        $this->assertNotNull(SupplierContactDetails::find($createdSupplierContactDetails['id']), 'SupplierContactDetails with given id must be in DB');
        $this->assertModelData($supplierContactDetails, $createdSupplierContactDetails);
    }

    /**
     * @test read
     */
    public function testReadSupplierContactDetails()
    {
        $supplierContactDetails = $this->makeSupplierContactDetails();
        $dbSupplierContactDetails = $this->supplierContactDetailsRepo->find($supplierContactDetails->id);
        $dbSupplierContactDetails = $dbSupplierContactDetails->toArray();
        $this->assertModelData($supplierContactDetails->toArray(), $dbSupplierContactDetails);
    }

    /**
     * @test update
     */
    public function testUpdateSupplierContactDetails()
    {
        $supplierContactDetails = $this->makeSupplierContactDetails();
        $fakeSupplierContactDetails = $this->fakeSupplierContactDetailsData();
        $updatedSupplierContactDetails = $this->supplierContactDetailsRepo->update($fakeSupplierContactDetails, $supplierContactDetails->id);
        $this->assertModelData($fakeSupplierContactDetails, $updatedSupplierContactDetails->toArray());
        $dbSupplierContactDetails = $this->supplierContactDetailsRepo->find($supplierContactDetails->id);
        $this->assertModelData($fakeSupplierContactDetails, $dbSupplierContactDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSupplierContactDetails()
    {
        $supplierContactDetails = $this->makeSupplierContactDetails();
        $resp = $this->supplierContactDetailsRepo->delete($supplierContactDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(SupplierContactDetails::find($supplierContactDetails->id), 'SupplierContactDetails should not exist in DB');
    }
}
