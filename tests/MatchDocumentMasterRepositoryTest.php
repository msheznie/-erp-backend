<?php

use App\Models\MatchDocumentMaster;
use App\Repositories\MatchDocumentMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MatchDocumentMasterRepositoryTest extends TestCase
{
    use MakeMatchDocumentMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MatchDocumentMasterRepository
     */
    protected $matchDocumentMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->matchDocumentMasterRepo = App::make(MatchDocumentMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateMatchDocumentMaster()
    {
        $matchDocumentMaster = $this->fakeMatchDocumentMasterData();
        $createdMatchDocumentMaster = $this->matchDocumentMasterRepo->create($matchDocumentMaster);
        $createdMatchDocumentMaster = $createdMatchDocumentMaster->toArray();
        $this->assertArrayHasKey('id', $createdMatchDocumentMaster);
        $this->assertNotNull($createdMatchDocumentMaster['id'], 'Created MatchDocumentMaster must have id specified');
        $this->assertNotNull(MatchDocumentMaster::find($createdMatchDocumentMaster['id']), 'MatchDocumentMaster with given id must be in DB');
        $this->assertModelData($matchDocumentMaster, $createdMatchDocumentMaster);
    }

    /**
     * @test read
     */
    public function testReadMatchDocumentMaster()
    {
        $matchDocumentMaster = $this->makeMatchDocumentMaster();
        $dbMatchDocumentMaster = $this->matchDocumentMasterRepo->find($matchDocumentMaster->id);
        $dbMatchDocumentMaster = $dbMatchDocumentMaster->toArray();
        $this->assertModelData($matchDocumentMaster->toArray(), $dbMatchDocumentMaster);
    }

    /**
     * @test update
     */
    public function testUpdateMatchDocumentMaster()
    {
        $matchDocumentMaster = $this->makeMatchDocumentMaster();
        $fakeMatchDocumentMaster = $this->fakeMatchDocumentMasterData();
        $updatedMatchDocumentMaster = $this->matchDocumentMasterRepo->update($fakeMatchDocumentMaster, $matchDocumentMaster->id);
        $this->assertModelData($fakeMatchDocumentMaster, $updatedMatchDocumentMaster->toArray());
        $dbMatchDocumentMaster = $this->matchDocumentMasterRepo->find($matchDocumentMaster->id);
        $this->assertModelData($fakeMatchDocumentMaster, $dbMatchDocumentMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteMatchDocumentMaster()
    {
        $matchDocumentMaster = $this->makeMatchDocumentMaster();
        $resp = $this->matchDocumentMasterRepo->delete($matchDocumentMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(MatchDocumentMaster::find($matchDocumentMaster->id), 'MatchDocumentMaster should not exist in DB');
    }
}
