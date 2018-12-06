<?php

use App\Models\ItemReturnMasterRefferedBack;
use App\Repositories\ItemReturnMasterRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemReturnMasterRefferedBackRepositoryTest extends TestCase
{
    use MakeItemReturnMasterRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemReturnMasterRefferedBackRepository
     */
    protected $itemReturnMasterRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemReturnMasterRefferedBackRepo = App::make(ItemReturnMasterRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemReturnMasterRefferedBack()
    {
        $itemReturnMasterRefferedBack = $this->fakeItemReturnMasterRefferedBackData();
        $createdItemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepo->create($itemReturnMasterRefferedBack);
        $createdItemReturnMasterRefferedBack = $createdItemReturnMasterRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdItemReturnMasterRefferedBack);
        $this->assertNotNull($createdItemReturnMasterRefferedBack['id'], 'Created ItemReturnMasterRefferedBack must have id specified');
        $this->assertNotNull(ItemReturnMasterRefferedBack::find($createdItemReturnMasterRefferedBack['id']), 'ItemReturnMasterRefferedBack with given id must be in DB');
        $this->assertModelData($itemReturnMasterRefferedBack, $createdItemReturnMasterRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadItemReturnMasterRefferedBack()
    {
        $itemReturnMasterRefferedBack = $this->makeItemReturnMasterRefferedBack();
        $dbItemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepo->find($itemReturnMasterRefferedBack->id);
        $dbItemReturnMasterRefferedBack = $dbItemReturnMasterRefferedBack->toArray();
        $this->assertModelData($itemReturnMasterRefferedBack->toArray(), $dbItemReturnMasterRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateItemReturnMasterRefferedBack()
    {
        $itemReturnMasterRefferedBack = $this->makeItemReturnMasterRefferedBack();
        $fakeItemReturnMasterRefferedBack = $this->fakeItemReturnMasterRefferedBackData();
        $updatedItemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepo->update($fakeItemReturnMasterRefferedBack, $itemReturnMasterRefferedBack->id);
        $this->assertModelData($fakeItemReturnMasterRefferedBack, $updatedItemReturnMasterRefferedBack->toArray());
        $dbItemReturnMasterRefferedBack = $this->itemReturnMasterRefferedBackRepo->find($itemReturnMasterRefferedBack->id);
        $this->assertModelData($fakeItemReturnMasterRefferedBack, $dbItemReturnMasterRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemReturnMasterRefferedBack()
    {
        $itemReturnMasterRefferedBack = $this->makeItemReturnMasterRefferedBack();
        $resp = $this->itemReturnMasterRefferedBackRepo->delete($itemReturnMasterRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemReturnMasterRefferedBack::find($itemReturnMasterRefferedBack->id), 'ItemReturnMasterRefferedBack should not exist in DB');
    }
}
