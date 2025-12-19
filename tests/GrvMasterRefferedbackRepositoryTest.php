<?php

use App\Models\GrvMasterRefferedback;
use App\Repositories\GrvMasterRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrvMasterRefferedbackRepositoryTest extends TestCase
{
    use MakeGrvMasterRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GrvMasterRefferedbackRepository
     */
    protected $grvMasterRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->grvMasterRefferedbackRepo = App::make(GrvMasterRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGrvMasterRefferedback()
    {
        $grvMasterRefferedback = $this->fakeGrvMasterRefferedbackData();
        $createdGrvMasterRefferedback = $this->grvMasterRefferedbackRepo->create($grvMasterRefferedback);
        $createdGrvMasterRefferedback = $createdGrvMasterRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdGrvMasterRefferedback);
        $this->assertNotNull($createdGrvMasterRefferedback['id'], 'Created GrvMasterRefferedback must have id specified');
        $this->assertNotNull(GrvMasterRefferedback::find($createdGrvMasterRefferedback['id']), 'GrvMasterRefferedback with given id must be in DB');
        $this->assertModelData($grvMasterRefferedback, $createdGrvMasterRefferedback);
    }

    /**
     * @test read
     */
    public function testReadGrvMasterRefferedback()
    {
        $grvMasterRefferedback = $this->makeGrvMasterRefferedback();
        $dbGrvMasterRefferedback = $this->grvMasterRefferedbackRepo->find($grvMasterRefferedback->id);
        $dbGrvMasterRefferedback = $dbGrvMasterRefferedback->toArray();
        $this->assertModelData($grvMasterRefferedback->toArray(), $dbGrvMasterRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdateGrvMasterRefferedback()
    {
        $grvMasterRefferedback = $this->makeGrvMasterRefferedback();
        $fakeGrvMasterRefferedback = $this->fakeGrvMasterRefferedbackData();
        $updatedGrvMasterRefferedback = $this->grvMasterRefferedbackRepo->update($fakeGrvMasterRefferedback, $grvMasterRefferedback->id);
        $this->assertModelData($fakeGrvMasterRefferedback, $updatedGrvMasterRefferedback->toArray());
        $dbGrvMasterRefferedback = $this->grvMasterRefferedbackRepo->find($grvMasterRefferedback->id);
        $this->assertModelData($fakeGrvMasterRefferedback, $dbGrvMasterRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGrvMasterRefferedback()
    {
        $grvMasterRefferedback = $this->makeGrvMasterRefferedback();
        $resp = $this->grvMasterRefferedbackRepo->delete($grvMasterRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(GrvMasterRefferedback::find($grvMasterRefferedback->id), 'GrvMasterRefferedback should not exist in DB');
    }
}
