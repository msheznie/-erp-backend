<?php

use App\Models\RequestRefferedBack;
use App\Repositories\RequestRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RequestRefferedBackRepositoryTest extends TestCase
{
    use MakeRequestRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var RequestRefferedBackRepository
     */
    protected $requestRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->requestRefferedBackRepo = App::make(RequestRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateRequestRefferedBack()
    {
        $requestRefferedBack = $this->fakeRequestRefferedBackData();
        $createdRequestRefferedBack = $this->requestRefferedBackRepo->create($requestRefferedBack);
        $createdRequestRefferedBack = $createdRequestRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdRequestRefferedBack);
        $this->assertNotNull($createdRequestRefferedBack['id'], 'Created RequestRefferedBack must have id specified');
        $this->assertNotNull(RequestRefferedBack::find($createdRequestRefferedBack['id']), 'RequestRefferedBack with given id must be in DB');
        $this->assertModelData($requestRefferedBack, $createdRequestRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadRequestRefferedBack()
    {
        $requestRefferedBack = $this->makeRequestRefferedBack();
        $dbRequestRefferedBack = $this->requestRefferedBackRepo->find($requestRefferedBack->id);
        $dbRequestRefferedBack = $dbRequestRefferedBack->toArray();
        $this->assertModelData($requestRefferedBack->toArray(), $dbRequestRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateRequestRefferedBack()
    {
        $requestRefferedBack = $this->makeRequestRefferedBack();
        $fakeRequestRefferedBack = $this->fakeRequestRefferedBackData();
        $updatedRequestRefferedBack = $this->requestRefferedBackRepo->update($fakeRequestRefferedBack, $requestRefferedBack->id);
        $this->assertModelData($fakeRequestRefferedBack, $updatedRequestRefferedBack->toArray());
        $dbRequestRefferedBack = $this->requestRefferedBackRepo->find($requestRefferedBack->id);
        $this->assertModelData($fakeRequestRefferedBack, $dbRequestRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteRequestRefferedBack()
    {
        $requestRefferedBack = $this->makeRequestRefferedBack();
        $resp = $this->requestRefferedBackRepo->delete($requestRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(RequestRefferedBack::find($requestRefferedBack->id), 'RequestRefferedBack should not exist in DB');
    }
}
