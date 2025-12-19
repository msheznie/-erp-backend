<?php

use App\Models\Designation;
use App\Repositories\DesignationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DesignationRepositoryTest extends TestCase
{
    use MakeDesignationTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DesignationRepository
     */
    protected $designationRepo;

    public function setUp()
    {
        parent::setUp();
        $this->designationRepo = App::make(DesignationRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDesignation()
    {
        $designation = $this->fakeDesignationData();
        $createdDesignation = $this->designationRepo->create($designation);
        $createdDesignation = $createdDesignation->toArray();
        $this->assertArrayHasKey('id', $createdDesignation);
        $this->assertNotNull($createdDesignation['id'], 'Created Designation must have id specified');
        $this->assertNotNull(Designation::find($createdDesignation['id']), 'Designation with given id must be in DB');
        $this->assertModelData($designation, $createdDesignation);
    }

    /**
     * @test read
     */
    public function testReadDesignation()
    {
        $designation = $this->makeDesignation();
        $dbDesignation = $this->designationRepo->find($designation->id);
        $dbDesignation = $dbDesignation->toArray();
        $this->assertModelData($designation->toArray(), $dbDesignation);
    }

    /**
     * @test update
     */
    public function testUpdateDesignation()
    {
        $designation = $this->makeDesignation();
        $fakeDesignation = $this->fakeDesignationData();
        $updatedDesignation = $this->designationRepo->update($fakeDesignation, $designation->id);
        $this->assertModelData($fakeDesignation, $updatedDesignation->toArray());
        $dbDesignation = $this->designationRepo->find($designation->id);
        $this->assertModelData($fakeDesignation, $dbDesignation->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDesignation()
    {
        $designation = $this->makeDesignation();
        $resp = $this->designationRepo->delete($designation->id);
        $this->assertTrue($resp);
        $this->assertNull(Designation::find($designation->id), 'Designation should not exist in DB');
    }
}
