<?php

use App\Models\PerformaMaster;
use App\Repositories\PerformaMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PerformaMasterRepositoryTest extends TestCase
{
    use MakePerformaMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PerformaMasterRepository
     */
    protected $performaMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->performaMasterRepo = App::make(PerformaMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePerformaMaster()
    {
        $performaMaster = $this->fakePerformaMasterData();
        $createdPerformaMaster = $this->performaMasterRepo->create($performaMaster);
        $createdPerformaMaster = $createdPerformaMaster->toArray();
        $this->assertArrayHasKey('id', $createdPerformaMaster);
        $this->assertNotNull($createdPerformaMaster['id'], 'Created PerformaMaster must have id specified');
        $this->assertNotNull(PerformaMaster::find($createdPerformaMaster['id']), 'PerformaMaster with given id must be in DB');
        $this->assertModelData($performaMaster, $createdPerformaMaster);
    }

    /**
     * @test read
     */
    public function testReadPerformaMaster()
    {
        $performaMaster = $this->makePerformaMaster();
        $dbPerformaMaster = $this->performaMasterRepo->find($performaMaster->id);
        $dbPerformaMaster = $dbPerformaMaster->toArray();
        $this->assertModelData($performaMaster->toArray(), $dbPerformaMaster);
    }

    /**
     * @test update
     */
    public function testUpdatePerformaMaster()
    {
        $performaMaster = $this->makePerformaMaster();
        $fakePerformaMaster = $this->fakePerformaMasterData();
        $updatedPerformaMaster = $this->performaMasterRepo->update($fakePerformaMaster, $performaMaster->id);
        $this->assertModelData($fakePerformaMaster, $updatedPerformaMaster->toArray());
        $dbPerformaMaster = $this->performaMasterRepo->find($performaMaster->id);
        $this->assertModelData($fakePerformaMaster, $dbPerformaMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePerformaMaster()
    {
        $performaMaster = $this->makePerformaMaster();
        $resp = $this->performaMasterRepo->delete($performaMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(PerformaMaster::find($performaMaster->id), 'PerformaMaster should not exist in DB');
    }
}
