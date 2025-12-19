<?php

use App\Models\HRMSJvDetails;
use App\Repositories\HRMSJvDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class HRMSJvDetailsRepositoryTest extends TestCase
{
    use MakeHRMSJvDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRMSJvDetailsRepository
     */
    protected $hRMSJvDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->hRMSJvDetailsRepo = App::make(HRMSJvDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateHRMSJvDetails()
    {
        $hRMSJvDetails = $this->fakeHRMSJvDetailsData();
        $createdHRMSJvDetails = $this->hRMSJvDetailsRepo->create($hRMSJvDetails);
        $createdHRMSJvDetails = $createdHRMSJvDetails->toArray();
        $this->assertArrayHasKey('id', $createdHRMSJvDetails);
        $this->assertNotNull($createdHRMSJvDetails['id'], 'Created HRMSJvDetails must have id specified');
        $this->assertNotNull(HRMSJvDetails::find($createdHRMSJvDetails['id']), 'HRMSJvDetails with given id must be in DB');
        $this->assertModelData($hRMSJvDetails, $createdHRMSJvDetails);
    }

    /**
     * @test read
     */
    public function testReadHRMSJvDetails()
    {
        $hRMSJvDetails = $this->makeHRMSJvDetails();
        $dbHRMSJvDetails = $this->hRMSJvDetailsRepo->find($hRMSJvDetails->id);
        $dbHRMSJvDetails = $dbHRMSJvDetails->toArray();
        $this->assertModelData($hRMSJvDetails->toArray(), $dbHRMSJvDetails);
    }

    /**
     * @test update
     */
    public function testUpdateHRMSJvDetails()
    {
        $hRMSJvDetails = $this->makeHRMSJvDetails();
        $fakeHRMSJvDetails = $this->fakeHRMSJvDetailsData();
        $updatedHRMSJvDetails = $this->hRMSJvDetailsRepo->update($fakeHRMSJvDetails, $hRMSJvDetails->id);
        $this->assertModelData($fakeHRMSJvDetails, $updatedHRMSJvDetails->toArray());
        $dbHRMSJvDetails = $this->hRMSJvDetailsRepo->find($hRMSJvDetails->id);
        $this->assertModelData($fakeHRMSJvDetails, $dbHRMSJvDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteHRMSJvDetails()
    {
        $hRMSJvDetails = $this->makeHRMSJvDetails();
        $resp = $this->hRMSJvDetailsRepo->delete($hRMSJvDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(HRMSJvDetails::find($hRMSJvDetails->id), 'HRMSJvDetails should not exist in DB');
    }
}
