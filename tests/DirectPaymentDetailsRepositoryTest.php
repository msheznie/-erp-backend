<?php

use App\Models\DirectPaymentDetails;
use App\Repositories\DirectPaymentDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectPaymentDetailsRepositoryTest extends TestCase
{
    use MakeDirectPaymentDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DirectPaymentDetailsRepository
     */
    protected $directPaymentDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->directPaymentDetailsRepo = App::make(DirectPaymentDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDirectPaymentDetails()
    {
        $directPaymentDetails = $this->fakeDirectPaymentDetailsData();
        $createdDirectPaymentDetails = $this->directPaymentDetailsRepo->create($directPaymentDetails);
        $createdDirectPaymentDetails = $createdDirectPaymentDetails->toArray();
        $this->assertArrayHasKey('id', $createdDirectPaymentDetails);
        $this->assertNotNull($createdDirectPaymentDetails['id'], 'Created DirectPaymentDetails must have id specified');
        $this->assertNotNull(DirectPaymentDetails::find($createdDirectPaymentDetails['id']), 'DirectPaymentDetails with given id must be in DB');
        $this->assertModelData($directPaymentDetails, $createdDirectPaymentDetails);
    }

    /**
     * @test read
     */
    public function testReadDirectPaymentDetails()
    {
        $directPaymentDetails = $this->makeDirectPaymentDetails();
        $dbDirectPaymentDetails = $this->directPaymentDetailsRepo->find($directPaymentDetails->id);
        $dbDirectPaymentDetails = $dbDirectPaymentDetails->toArray();
        $this->assertModelData($directPaymentDetails->toArray(), $dbDirectPaymentDetails);
    }

    /**
     * @test update
     */
    public function testUpdateDirectPaymentDetails()
    {
        $directPaymentDetails = $this->makeDirectPaymentDetails();
        $fakeDirectPaymentDetails = $this->fakeDirectPaymentDetailsData();
        $updatedDirectPaymentDetails = $this->directPaymentDetailsRepo->update($fakeDirectPaymentDetails, $directPaymentDetails->id);
        $this->assertModelData($fakeDirectPaymentDetails, $updatedDirectPaymentDetails->toArray());
        $dbDirectPaymentDetails = $this->directPaymentDetailsRepo->find($directPaymentDetails->id);
        $this->assertModelData($fakeDirectPaymentDetails, $dbDirectPaymentDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDirectPaymentDetails()
    {
        $directPaymentDetails = $this->makeDirectPaymentDetails();
        $resp = $this->directPaymentDetailsRepo->delete($directPaymentDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(DirectPaymentDetails::find($directPaymentDetails->id), 'DirectPaymentDetails should not exist in DB');
    }
}
