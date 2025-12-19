<?php

use App\Models\PoAddonsRefferedBack;
use App\Repositories\PoAddonsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoAddonsRefferedBackRepositoryTest extends TestCase
{
    use MakePoAddonsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoAddonsRefferedBackRepository
     */
    protected $poAddonsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->poAddonsRefferedBackRepo = App::make(PoAddonsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePoAddonsRefferedBack()
    {
        $poAddonsRefferedBack = $this->fakePoAddonsRefferedBackData();
        $createdPoAddonsRefferedBack = $this->poAddonsRefferedBackRepo->create($poAddonsRefferedBack);
        $createdPoAddonsRefferedBack = $createdPoAddonsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdPoAddonsRefferedBack);
        $this->assertNotNull($createdPoAddonsRefferedBack['id'], 'Created PoAddonsRefferedBack must have id specified');
        $this->assertNotNull(PoAddonsRefferedBack::find($createdPoAddonsRefferedBack['id']), 'PoAddonsRefferedBack with given id must be in DB');
        $this->assertModelData($poAddonsRefferedBack, $createdPoAddonsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadPoAddonsRefferedBack()
    {
        $poAddonsRefferedBack = $this->makePoAddonsRefferedBack();
        $dbPoAddonsRefferedBack = $this->poAddonsRefferedBackRepo->find($poAddonsRefferedBack->id);
        $dbPoAddonsRefferedBack = $dbPoAddonsRefferedBack->toArray();
        $this->assertModelData($poAddonsRefferedBack->toArray(), $dbPoAddonsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdatePoAddonsRefferedBack()
    {
        $poAddonsRefferedBack = $this->makePoAddonsRefferedBack();
        $fakePoAddonsRefferedBack = $this->fakePoAddonsRefferedBackData();
        $updatedPoAddonsRefferedBack = $this->poAddonsRefferedBackRepo->update($fakePoAddonsRefferedBack, $poAddonsRefferedBack->id);
        $this->assertModelData($fakePoAddonsRefferedBack, $updatedPoAddonsRefferedBack->toArray());
        $dbPoAddonsRefferedBack = $this->poAddonsRefferedBackRepo->find($poAddonsRefferedBack->id);
        $this->assertModelData($fakePoAddonsRefferedBack, $dbPoAddonsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePoAddonsRefferedBack()
    {
        $poAddonsRefferedBack = $this->makePoAddonsRefferedBack();
        $resp = $this->poAddonsRefferedBackRepo->delete($poAddonsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(PoAddonsRefferedBack::find($poAddonsRefferedBack->id), 'PoAddonsRefferedBack should not exist in DB');
    }
}
