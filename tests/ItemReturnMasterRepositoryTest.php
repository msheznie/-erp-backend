<?php

use App\Models\ItemReturnMaster;
use App\Repositories\ItemReturnMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemReturnMasterRepositoryTest extends TestCase
{
    use MakeItemReturnMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemReturnMasterRepository
     */
    protected $itemReturnMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemReturnMasterRepo = App::make(ItemReturnMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemReturnMaster()
    {
        $itemReturnMaster = $this->fakeItemReturnMasterData();
        $createdItemReturnMaster = $this->itemReturnMasterRepo->create($itemReturnMaster);
        $createdItemReturnMaster = $createdItemReturnMaster->toArray();
        $this->assertArrayHasKey('id', $createdItemReturnMaster);
        $this->assertNotNull($createdItemReturnMaster['id'], 'Created ItemReturnMaster must have id specified');
        $this->assertNotNull(ItemReturnMaster::find($createdItemReturnMaster['id']), 'ItemReturnMaster with given id must be in DB');
        $this->assertModelData($itemReturnMaster, $createdItemReturnMaster);
    }

    /**
     * @test read
     */
    public function testReadItemReturnMaster()
    {
        $itemReturnMaster = $this->makeItemReturnMaster();
        $dbItemReturnMaster = $this->itemReturnMasterRepo->find($itemReturnMaster->id);
        $dbItemReturnMaster = $dbItemReturnMaster->toArray();
        $this->assertModelData($itemReturnMaster->toArray(), $dbItemReturnMaster);
    }

    /**
     * @test update
     */
    public function testUpdateItemReturnMaster()
    {
        $itemReturnMaster = $this->makeItemReturnMaster();
        $fakeItemReturnMaster = $this->fakeItemReturnMasterData();
        $updatedItemReturnMaster = $this->itemReturnMasterRepo->update($fakeItemReturnMaster, $itemReturnMaster->id);
        $this->assertModelData($fakeItemReturnMaster, $updatedItemReturnMaster->toArray());
        $dbItemReturnMaster = $this->itemReturnMasterRepo->find($itemReturnMaster->id);
        $this->assertModelData($fakeItemReturnMaster, $dbItemReturnMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemReturnMaster()
    {
        $itemReturnMaster = $this->makeItemReturnMaster();
        $resp = $this->itemReturnMasterRepo->delete($itemReturnMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemReturnMaster::find($itemReturnMaster->id), 'ItemReturnMaster should not exist in DB');
    }
}
