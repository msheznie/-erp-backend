<?php

use App\Models\MaterielRequest;
use App\Repositories\MaterielRequestRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MaterielRequestRepositoryTest extends TestCase
{
    use MakeMaterielRequestTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MaterielRequestRepository
     */
    protected $materielRequestRepo;

    public function setUp()
    {
        parent::setUp();
        $this->materielRequestRepo = App::make(MaterielRequestRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMaterielRequest()
    {
        $materielRequest = $this->fakeMaterielRequestData();
        $createdMaterielRequest = $this->materielRequestRepo->create($materielRequest);
        $createdMaterielRequest = $createdMaterielRequest->toArray();
        $this->assertArrayHasKey('id', $createdMaterielRequest);
        $this->assertNotNull($createdMaterielRequest['id'], 'Created MaterielRequest must have id specified');
        $this->assertNotNull(MaterielRequest::find($createdMaterielRequest['id']), 'MaterielRequest with given id must be in DB');
        $this->assertModelData($materielRequest, $createdMaterielRequest);
    }

    /**
     * @test read
     */
    public function testReadMaterielRequest()
    {
        $materielRequest = $this->makeMaterielRequest();
        $dbMaterielRequest = $this->materielRequestRepo->find($materielRequest->id);
        $dbMaterielRequest = $dbMaterielRequest->toArray();
        $this->assertModelData($materielRequest->toArray(), $dbMaterielRequest);
    }

    /**
     * @test update
     */
    public function testUpdateMaterielRequest()
    {
        $materielRequest = $this->makeMaterielRequest();
        $fakeMaterielRequest = $this->fakeMaterielRequestData();
        $updatedMaterielRequest = $this->materielRequestRepo->update($fakeMaterielRequest, $materielRequest->id);
        $this->assertModelData($fakeMaterielRequest, $updatedMaterielRequest->toArray());
        $dbMaterielRequest = $this->materielRequestRepo->find($materielRequest->id);
        $this->assertModelData($fakeMaterielRequest, $dbMaterielRequest->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMaterielRequest()
    {
        $materielRequest = $this->makeMaterielRequest();
        $resp = $this->materielRequestRepo->delete($materielRequest->id);
        $this->assertTrue($resp);
        $this->assertNull(MaterielRequest::find($materielRequest->id), 'MaterielRequest should not exist in DB');
    }
}
