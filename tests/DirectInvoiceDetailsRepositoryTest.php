<?php

use App\Models\DirectInvoiceDetails;
use App\Repositories\DirectInvoiceDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectInvoiceDetailsRepositoryTest extends TestCase
{
    use MakeDirectInvoiceDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DirectInvoiceDetailsRepository
     */
    protected $directInvoiceDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->directInvoiceDetailsRepo = App::make(DirectInvoiceDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDirectInvoiceDetails()
    {
        $directInvoiceDetails = $this->fakeDirectInvoiceDetailsData();
        $createdDirectInvoiceDetails = $this->directInvoiceDetailsRepo->create($directInvoiceDetails);
        $createdDirectInvoiceDetails = $createdDirectInvoiceDetails->toArray();
        $this->assertArrayHasKey('id', $createdDirectInvoiceDetails);
        $this->assertNotNull($createdDirectInvoiceDetails['id'], 'Created DirectInvoiceDetails must have id specified');
        $this->assertNotNull(DirectInvoiceDetails::find($createdDirectInvoiceDetails['id']), 'DirectInvoiceDetails with given id must be in DB');
        $this->assertModelData($directInvoiceDetails, $createdDirectInvoiceDetails);
    }

    /**
     * @test read
     */
    public function testReadDirectInvoiceDetails()
    {
        $directInvoiceDetails = $this->makeDirectInvoiceDetails();
        $dbDirectInvoiceDetails = $this->directInvoiceDetailsRepo->find($directInvoiceDetails->id);
        $dbDirectInvoiceDetails = $dbDirectInvoiceDetails->toArray();
        $this->assertModelData($directInvoiceDetails->toArray(), $dbDirectInvoiceDetails);
    }

    /**
     * @test update
     */
    public function testUpdateDirectInvoiceDetails()
    {
        $directInvoiceDetails = $this->makeDirectInvoiceDetails();
        $fakeDirectInvoiceDetails = $this->fakeDirectInvoiceDetailsData();
        $updatedDirectInvoiceDetails = $this->directInvoiceDetailsRepo->update($fakeDirectInvoiceDetails, $directInvoiceDetails->id);
        $this->assertModelData($fakeDirectInvoiceDetails, $updatedDirectInvoiceDetails->toArray());
        $dbDirectInvoiceDetails = $this->directInvoiceDetailsRepo->find($directInvoiceDetails->id);
        $this->assertModelData($fakeDirectInvoiceDetails, $dbDirectInvoiceDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDirectInvoiceDetails()
    {
        $directInvoiceDetails = $this->makeDirectInvoiceDetails();
        $resp = $this->directInvoiceDetailsRepo->delete($directInvoiceDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(DirectInvoiceDetails::find($directInvoiceDetails->id), 'DirectInvoiceDetails should not exist in DB');
    }
}
