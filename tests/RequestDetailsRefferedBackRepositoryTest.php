<?php

use App\Models\RequestDetailsRefferedBack;
use App\Repositories\RequestDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class RequestDetailsRefferedBackRepositoryTest extends TestCase
{
    use MakeRequestDetailsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var RequestDetailsRefferedBackRepository
     */
    protected $requestDetailsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->requestDetailsRefferedBackRepo = App::make(RequestDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateRequestDetailsRefferedBack()
    {
        $requestDetailsRefferedBack = $this->fakeRequestDetailsRefferedBackData();
        $createdRequestDetailsRefferedBack = $this->requestDetailsRefferedBackRepo->create($requestDetailsRefferedBack);
        $createdRequestDetailsRefferedBack = $createdRequestDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdRequestDetailsRefferedBack);
        $this->assertNotNull($createdRequestDetailsRefferedBack['id'], 'Created RequestDetailsRefferedBack must have id specified');
        $this->assertNotNull(RequestDetailsRefferedBack::find($createdRequestDetailsRefferedBack['id']), 'RequestDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($requestDetailsRefferedBack, $createdRequestDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadRequestDetailsRefferedBack()
    {
        $requestDetailsRefferedBack = $this->makeRequestDetailsRefferedBack();
        $dbRequestDetailsRefferedBack = $this->requestDetailsRefferedBackRepo->find($requestDetailsRefferedBack->id);
        $dbRequestDetailsRefferedBack = $dbRequestDetailsRefferedBack->toArray();
        $this->assertModelData($requestDetailsRefferedBack->toArray(), $dbRequestDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateRequestDetailsRefferedBack()
    {
        $requestDetailsRefferedBack = $this->makeRequestDetailsRefferedBack();
        $fakeRequestDetailsRefferedBack = $this->fakeRequestDetailsRefferedBackData();
        $updatedRequestDetailsRefferedBack = $this->requestDetailsRefferedBackRepo->update($fakeRequestDetailsRefferedBack, $requestDetailsRefferedBack->id);
        $this->assertModelData($fakeRequestDetailsRefferedBack, $updatedRequestDetailsRefferedBack->toArray());
        $dbRequestDetailsRefferedBack = $this->requestDetailsRefferedBackRepo->find($requestDetailsRefferedBack->id);
        $this->assertModelData($fakeRequestDetailsRefferedBack, $dbRequestDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteRequestDetailsRefferedBack()
    {
        $requestDetailsRefferedBack = $this->makeRequestDetailsRefferedBack();
        $resp = $this->requestDetailsRefferedBackRepo->delete($requestDetailsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(RequestDetailsRefferedBack::find($requestDetailsRefferedBack->id), 'RequestDetailsRefferedBack should not exist in DB');
    }
}
