<?php

use App\Models\MaterielRequestDetails;
use App\Repositories\MaterielRequestDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MaterielRequestDetailsRepositoryTest extends TestCase
{
    use MakeMaterielRequestDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MaterielRequestDetailsRepository
     */
    protected $materielRequestDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->materielRequestDetailsRepo = App::make(MaterielRequestDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMaterielRequestDetails()
    {
        $materielRequestDetails = $this->fakeMaterielRequestDetailsData();
        $createdMaterielRequestDetails = $this->materielRequestDetailsRepo->create($materielRequestDetails);
        $createdMaterielRequestDetails = $createdMaterielRequestDetails->toArray();
        $this->assertArrayHasKey('id', $createdMaterielRequestDetails);
        $this->assertNotNull($createdMaterielRequestDetails['id'], 'Created MaterielRequestDetails must have id specified');
        $this->assertNotNull(MaterielRequestDetails::find($createdMaterielRequestDetails['id']), 'MaterielRequestDetails with given id must be in DB');
        $this->assertModelData($materielRequestDetails, $createdMaterielRequestDetails);
    }

    /**
     * @test read
     */
    public function testReadMaterielRequestDetails()
    {
        $materielRequestDetails = $this->makeMaterielRequestDetails();
        $dbMaterielRequestDetails = $this->materielRequestDetailsRepo->find($materielRequestDetails->id);
        $dbMaterielRequestDetails = $dbMaterielRequestDetails->toArray();
        $this->assertModelData($materielRequestDetails->toArray(), $dbMaterielRequestDetails);
    }

    /**
     * @test update
     */
    public function testUpdateMaterielRequestDetails()
    {
        $materielRequestDetails = $this->makeMaterielRequestDetails();
        $fakeMaterielRequestDetails = $this->fakeMaterielRequestDetailsData();
        $updatedMaterielRequestDetails = $this->materielRequestDetailsRepo->update($fakeMaterielRequestDetails, $materielRequestDetails->id);
        $this->assertModelData($fakeMaterielRequestDetails, $updatedMaterielRequestDetails->toArray());
        $dbMaterielRequestDetails = $this->materielRequestDetailsRepo->find($materielRequestDetails->id);
        $this->assertModelData($fakeMaterielRequestDetails, $dbMaterielRequestDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMaterielRequestDetails()
    {
        $materielRequestDetails = $this->makeMaterielRequestDetails();
        $resp = $this->materielRequestDetailsRepo->delete($materielRequestDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(MaterielRequestDetails::find($materielRequestDetails->id), 'MaterielRequestDetails should not exist in DB');
    }
}
