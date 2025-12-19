<?php

use App\Models\ItemMaster;
use App\Repositories\ItemMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemMasterRepositoryTest extends TestCase
{
    use MakeItemMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemMasterRepository
     */
    protected $itemMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemMasterRepo = App::make(ItemMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemMaster()
    {
        $itemMaster = $this->fakeItemMasterData();
        $createdItemMaster = $this->itemMasterRepo->create($itemMaster);
        $createdItemMaster = $createdItemMaster->toArray();
        $this->assertArrayHasKey('id', $createdItemMaster);
        $this->assertNotNull($createdItemMaster['id'], 'Created ItemMaster must have id specified');
        $this->assertNotNull(ItemMaster::find($createdItemMaster['id']), 'ItemMaster with given id must be in DB');
        $this->assertModelData($itemMaster, $createdItemMaster);
    }

    /**
     * @test read
     */
    public function testReadItemMaster()
    {
        $itemMaster = $this->makeItemMaster();
        $dbItemMaster = $this->itemMasterRepo->find($itemMaster->id);
        $dbItemMaster = $dbItemMaster->toArray();
        $this->assertModelData($itemMaster->toArray(), $dbItemMaster);
    }

    /**
     * @test update
     */
    public function testUpdateItemMaster()
    {
        $itemMaster = $this->makeItemMaster();
        $fakeItemMaster = $this->fakeItemMasterData();
        $updatedItemMaster = $this->itemMasterRepo->update($fakeItemMaster, $itemMaster->id);
        $this->assertModelData($fakeItemMaster, $updatedItemMaster->toArray());
        $dbItemMaster = $this->itemMasterRepo->find($itemMaster->id);
        $this->assertModelData($fakeItemMaster, $dbItemMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemMaster()
    {
        $itemMaster = $this->makeItemMaster();
        $resp = $this->itemMasterRepo->delete($itemMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemMaster::find($itemMaster->id), 'ItemMaster should not exist in DB');
    }
}
