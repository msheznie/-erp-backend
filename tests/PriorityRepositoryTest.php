<?php

use App\Models\Priority;
use App\Repositories\PriorityRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PriorityRepositoryTest extends TestCase
{
    use MakePriorityTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PriorityRepository
     */
    protected $priorityRepo;

    public function setUp()
    {
        parent::setUp();
        $this->priorityRepo = App::make(PriorityRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePriority()
    {
        $priority = $this->fakePriorityData();
        $createdPriority = $this->priorityRepo->create($priority);
        $createdPriority = $createdPriority->toArray();
        $this->assertArrayHasKey('id', $createdPriority);
        $this->assertNotNull($createdPriority['id'], 'Created Priority must have id specified');
        $this->assertNotNull(Priority::find($createdPriority['id']), 'Priority with given id must be in DB');
        $this->assertModelData($priority, $createdPriority);
    }

    /**
     * @test read
     */
    public function testReadPriority()
    {
        $priority = $this->makePriority();
        $dbPriority = $this->priorityRepo->find($priority->id);
        $dbPriority = $dbPriority->toArray();
        $this->assertModelData($priority->toArray(), $dbPriority);
    }

    /**
     * @test update
     */
    public function testUpdatePriority()
    {
        $priority = $this->makePriority();
        $fakePriority = $this->fakePriorityData();
        $updatedPriority = $this->priorityRepo->update($fakePriority, $priority->id);
        $this->assertModelData($fakePriority, $updatedPriority->toArray());
        $dbPriority = $this->priorityRepo->find($priority->id);
        $this->assertModelData($fakePriority, $dbPriority->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePriority()
    {
        $priority = $this->makePriority();
        $resp = $this->priorityRepo->delete($priority->id);
        $this->assertTrue($resp);
        $this->assertNull(Priority::find($priority->id), 'Priority should not exist in DB');
    }
}
