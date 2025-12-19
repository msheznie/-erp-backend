<?php

use App\Models\GRVDetails;
use App\Repositories\GRVDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GRVDetailsRepositoryTest extends TestCase
{
    use MakeGRVDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GRVDetailsRepository
     */
    protected $gRVDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->gRVDetailsRepo = App::make(GRVDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGRVDetails()
    {
        $gRVDetails = $this->fakeGRVDetailsData();
        $createdGRVDetails = $this->gRVDetailsRepo->create($gRVDetails);
        $createdGRVDetails = $createdGRVDetails->toArray();
        $this->assertArrayHasKey('id', $createdGRVDetails);
        $this->assertNotNull($createdGRVDetails['id'], 'Created GRVDetails must have id specified');
        $this->assertNotNull(GRVDetails::find($createdGRVDetails['id']), 'GRVDetails with given id must be in DB');
        $this->assertModelData($gRVDetails, $createdGRVDetails);
    }

    /**
     * @test read
     */
    public function testReadGRVDetails()
    {
        $gRVDetails = $this->makeGRVDetails();
        $dbGRVDetails = $this->gRVDetailsRepo->find($gRVDetails->id);
        $dbGRVDetails = $dbGRVDetails->toArray();
        $this->assertModelData($gRVDetails->toArray(), $dbGRVDetails);
    }

    /**
     * @test update
     */
    public function testUpdateGRVDetails()
    {
        $gRVDetails = $this->makeGRVDetails();
        $fakeGRVDetails = $this->fakeGRVDetailsData();
        $updatedGRVDetails = $this->gRVDetailsRepo->update($fakeGRVDetails, $gRVDetails->id);
        $this->assertModelData($fakeGRVDetails, $updatedGRVDetails->toArray());
        $dbGRVDetails = $this->gRVDetailsRepo->find($gRVDetails->id);
        $this->assertModelData($fakeGRVDetails, $dbGRVDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGRVDetails()
    {
        $gRVDetails = $this->makeGRVDetails();
        $resp = $this->gRVDetailsRepo->delete($gRVDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(GRVDetails::find($gRVDetails->id), 'GRVDetails should not exist in DB');
    }
}
