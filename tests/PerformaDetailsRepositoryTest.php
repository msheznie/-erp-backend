<?php

use App\Models\PerformaDetails;
use App\Repositories\PerformaDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerformaDetailsRepositoryTest extends TestCase
{
    use MakePerformaDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PerformaDetailsRepository
     */
    protected $performaDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->performaDetailsRepo = App::make(PerformaDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePerformaDetails()
    {
        $performaDetails = $this->fakePerformaDetailsData();
        $createdPerformaDetails = $this->performaDetailsRepo->create($performaDetails);
        $createdPerformaDetails = $createdPerformaDetails->toArray();
        $this->assertArrayHasKey('id', $createdPerformaDetails);
        $this->assertNotNull($createdPerformaDetails['id'], 'Created PerformaDetails must have id specified');
        $this->assertNotNull(PerformaDetails::find($createdPerformaDetails['id']), 'PerformaDetails with given id must be in DB');
        $this->assertModelData($performaDetails, $createdPerformaDetails);
    }

    /**
     * @test read
     */
    public function testReadPerformaDetails()
    {
        $performaDetails = $this->makePerformaDetails();
        $dbPerformaDetails = $this->performaDetailsRepo->find($performaDetails->id);
        $dbPerformaDetails = $dbPerformaDetails->toArray();
        $this->assertModelData($performaDetails->toArray(), $dbPerformaDetails);
    }

    /**
     * @test update
     */
    public function testUpdatePerformaDetails()
    {
        $performaDetails = $this->makePerformaDetails();
        $fakePerformaDetails = $this->fakePerformaDetailsData();
        $updatedPerformaDetails = $this->performaDetailsRepo->update($fakePerformaDetails, $performaDetails->id);
        $this->assertModelData($fakePerformaDetails, $updatedPerformaDetails->toArray());
        $dbPerformaDetails = $this->performaDetailsRepo->find($performaDetails->id);
        $this->assertModelData($fakePerformaDetails, $dbPerformaDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePerformaDetails()
    {
        $performaDetails = $this->makePerformaDetails();
        $resp = $this->performaDetailsRepo->delete($performaDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(PerformaDetails::find($performaDetails->id), 'PerformaDetails should not exist in DB');
    }
}
