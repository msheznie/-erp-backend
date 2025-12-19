<?php

use App\Models\GrvDetailsRefferedback;
use App\Repositories\GrvDetailsRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GrvDetailsRefferedbackRepositoryTest extends TestCase
{
    use MakeGrvDetailsRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GrvDetailsRefferedbackRepository
     */
    protected $grvDetailsRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->grvDetailsRefferedbackRepo = App::make(GrvDetailsRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateGrvDetailsRefferedback()
    {
        $grvDetailsRefferedback = $this->fakeGrvDetailsRefferedbackData();
        $createdGrvDetailsRefferedback = $this->grvDetailsRefferedbackRepo->create($grvDetailsRefferedback);
        $createdGrvDetailsRefferedback = $createdGrvDetailsRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdGrvDetailsRefferedback);
        $this->assertNotNull($createdGrvDetailsRefferedback['id'], 'Created GrvDetailsRefferedback must have id specified');
        $this->assertNotNull(GrvDetailsRefferedback::find($createdGrvDetailsRefferedback['id']), 'GrvDetailsRefferedback with given id must be in DB');
        $this->assertModelData($grvDetailsRefferedback, $createdGrvDetailsRefferedback);
    }

    /**
     * @test read
     */
    public function testReadGrvDetailsRefferedback()
    {
        $grvDetailsRefferedback = $this->makeGrvDetailsRefferedback();
        $dbGrvDetailsRefferedback = $this->grvDetailsRefferedbackRepo->find($grvDetailsRefferedback->id);
        $dbGrvDetailsRefferedback = $dbGrvDetailsRefferedback->toArray();
        $this->assertModelData($grvDetailsRefferedback->toArray(), $dbGrvDetailsRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdateGrvDetailsRefferedback()
    {
        $grvDetailsRefferedback = $this->makeGrvDetailsRefferedback();
        $fakeGrvDetailsRefferedback = $this->fakeGrvDetailsRefferedbackData();
        $updatedGrvDetailsRefferedback = $this->grvDetailsRefferedbackRepo->update($fakeGrvDetailsRefferedback, $grvDetailsRefferedback->id);
        $this->assertModelData($fakeGrvDetailsRefferedback, $updatedGrvDetailsRefferedback->toArray());
        $dbGrvDetailsRefferedback = $this->grvDetailsRefferedbackRepo->find($grvDetailsRefferedback->id);
        $this->assertModelData($fakeGrvDetailsRefferedback, $dbGrvDetailsRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteGrvDetailsRefferedback()
    {
        $grvDetailsRefferedback = $this->makeGrvDetailsRefferedback();
        $resp = $this->grvDetailsRefferedbackRepo->delete($grvDetailsRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(GrvDetailsRefferedback::find($grvDetailsRefferedback->id), 'GrvDetailsRefferedback should not exist in DB');
    }
}
