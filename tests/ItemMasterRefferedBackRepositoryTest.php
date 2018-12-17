<?php

use App\Models\ItemMasterRefferedBack;
use App\Repositories\ItemMasterRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemMasterRefferedBackRepositoryTest extends TestCase
{
    use MakeItemMasterRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemMasterRefferedBackRepository
     */
    protected $itemMasterRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemMasterRefferedBackRepo = App::make(ItemMasterRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemMasterRefferedBack()
    {
        $itemMasterRefferedBack = $this->fakeItemMasterRefferedBackData();
        $createdItemMasterRefferedBack = $this->itemMasterRefferedBackRepo->create($itemMasterRefferedBack);
        $createdItemMasterRefferedBack = $createdItemMasterRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdItemMasterRefferedBack);
        $this->assertNotNull($createdItemMasterRefferedBack['id'], 'Created ItemMasterRefferedBack must have id specified');
        $this->assertNotNull(ItemMasterRefferedBack::find($createdItemMasterRefferedBack['id']), 'ItemMasterRefferedBack with given id must be in DB');
        $this->assertModelData($itemMasterRefferedBack, $createdItemMasterRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadItemMasterRefferedBack()
    {
        $itemMasterRefferedBack = $this->makeItemMasterRefferedBack();
        $dbItemMasterRefferedBack = $this->itemMasterRefferedBackRepo->find($itemMasterRefferedBack->id);
        $dbItemMasterRefferedBack = $dbItemMasterRefferedBack->toArray();
        $this->assertModelData($itemMasterRefferedBack->toArray(), $dbItemMasterRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateItemMasterRefferedBack()
    {
        $itemMasterRefferedBack = $this->makeItemMasterRefferedBack();
        $fakeItemMasterRefferedBack = $this->fakeItemMasterRefferedBackData();
        $updatedItemMasterRefferedBack = $this->itemMasterRefferedBackRepo->update($fakeItemMasterRefferedBack, $itemMasterRefferedBack->id);
        $this->assertModelData($fakeItemMasterRefferedBack, $updatedItemMasterRefferedBack->toArray());
        $dbItemMasterRefferedBack = $this->itemMasterRefferedBackRepo->find($itemMasterRefferedBack->id);
        $this->assertModelData($fakeItemMasterRefferedBack, $dbItemMasterRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemMasterRefferedBack()
    {
        $itemMasterRefferedBack = $this->makeItemMasterRefferedBack();
        $resp = $this->itemMasterRefferedBackRepo->delete($itemMasterRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemMasterRefferedBack::find($itemMasterRefferedBack->id), 'ItemMasterRefferedBack should not exist in DB');
    }
}
