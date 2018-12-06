<?php

use App\Models\ItemReturnDetailsRefferedBack;
use App\Repositories\ItemReturnDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemReturnDetailsRefferedBackRepositoryTest extends TestCase
{
    use MakeItemReturnDetailsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemReturnDetailsRefferedBackRepository
     */
    protected $itemReturnDetailsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemReturnDetailsRefferedBackRepo = App::make(ItemReturnDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemReturnDetailsRefferedBack()
    {
        $itemReturnDetailsRefferedBack = $this->fakeItemReturnDetailsRefferedBackData();
        $createdItemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepo->create($itemReturnDetailsRefferedBack);
        $createdItemReturnDetailsRefferedBack = $createdItemReturnDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdItemReturnDetailsRefferedBack);
        $this->assertNotNull($createdItemReturnDetailsRefferedBack['id'], 'Created ItemReturnDetailsRefferedBack must have id specified');
        $this->assertNotNull(ItemReturnDetailsRefferedBack::find($createdItemReturnDetailsRefferedBack['id']), 'ItemReturnDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($itemReturnDetailsRefferedBack, $createdItemReturnDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadItemReturnDetailsRefferedBack()
    {
        $itemReturnDetailsRefferedBack = $this->makeItemReturnDetailsRefferedBack();
        $dbItemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepo->find($itemReturnDetailsRefferedBack->id);
        $dbItemReturnDetailsRefferedBack = $dbItemReturnDetailsRefferedBack->toArray();
        $this->assertModelData($itemReturnDetailsRefferedBack->toArray(), $dbItemReturnDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateItemReturnDetailsRefferedBack()
    {
        $itemReturnDetailsRefferedBack = $this->makeItemReturnDetailsRefferedBack();
        $fakeItemReturnDetailsRefferedBack = $this->fakeItemReturnDetailsRefferedBackData();
        $updatedItemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepo->update($fakeItemReturnDetailsRefferedBack, $itemReturnDetailsRefferedBack->id);
        $this->assertModelData($fakeItemReturnDetailsRefferedBack, $updatedItemReturnDetailsRefferedBack->toArray());
        $dbItemReturnDetailsRefferedBack = $this->itemReturnDetailsRefferedBackRepo->find($itemReturnDetailsRefferedBack->id);
        $this->assertModelData($fakeItemReturnDetailsRefferedBack, $dbItemReturnDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemReturnDetailsRefferedBack()
    {
        $itemReturnDetailsRefferedBack = $this->makeItemReturnDetailsRefferedBack();
        $resp = $this->itemReturnDetailsRefferedBackRepo->delete($itemReturnDetailsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemReturnDetailsRefferedBack::find($itemReturnDetailsRefferedBack->id), 'ItemReturnDetailsRefferedBack should not exist in DB');
    }
}
