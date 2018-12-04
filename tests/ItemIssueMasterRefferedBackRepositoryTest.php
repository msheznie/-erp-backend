<?php

use App\Models\ItemIssueMasterRefferedBack;
use App\Repositories\ItemIssueMasterRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueMasterRefferedBackRepositoryTest extends TestCase
{
    use MakeItemIssueMasterRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemIssueMasterRefferedBackRepository
     */
    protected $itemIssueMasterRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemIssueMasterRefferedBackRepo = App::make(ItemIssueMasterRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemIssueMasterRefferedBack()
    {
        $itemIssueMasterRefferedBack = $this->fakeItemIssueMasterRefferedBackData();
        $createdItemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepo->create($itemIssueMasterRefferedBack);
        $createdItemIssueMasterRefferedBack = $createdItemIssueMasterRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdItemIssueMasterRefferedBack);
        $this->assertNotNull($createdItemIssueMasterRefferedBack['id'], 'Created ItemIssueMasterRefferedBack must have id specified');
        $this->assertNotNull(ItemIssueMasterRefferedBack::find($createdItemIssueMasterRefferedBack['id']), 'ItemIssueMasterRefferedBack with given id must be in DB');
        $this->assertModelData($itemIssueMasterRefferedBack, $createdItemIssueMasterRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadItemIssueMasterRefferedBack()
    {
        $itemIssueMasterRefferedBack = $this->makeItemIssueMasterRefferedBack();
        $dbItemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepo->find($itemIssueMasterRefferedBack->id);
        $dbItemIssueMasterRefferedBack = $dbItemIssueMasterRefferedBack->toArray();
        $this->assertModelData($itemIssueMasterRefferedBack->toArray(), $dbItemIssueMasterRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateItemIssueMasterRefferedBack()
    {
        $itemIssueMasterRefferedBack = $this->makeItemIssueMasterRefferedBack();
        $fakeItemIssueMasterRefferedBack = $this->fakeItemIssueMasterRefferedBackData();
        $updatedItemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepo->update($fakeItemIssueMasterRefferedBack, $itemIssueMasterRefferedBack->id);
        $this->assertModelData($fakeItemIssueMasterRefferedBack, $updatedItemIssueMasterRefferedBack->toArray());
        $dbItemIssueMasterRefferedBack = $this->itemIssueMasterRefferedBackRepo->find($itemIssueMasterRefferedBack->id);
        $this->assertModelData($fakeItemIssueMasterRefferedBack, $dbItemIssueMasterRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemIssueMasterRefferedBack()
    {
        $itemIssueMasterRefferedBack = $this->makeItemIssueMasterRefferedBack();
        $resp = $this->itemIssueMasterRefferedBackRepo->delete($itemIssueMasterRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemIssueMasterRefferedBack::find($itemIssueMasterRefferedBack->id), 'ItemIssueMasterRefferedBack should not exist in DB');
    }
}
