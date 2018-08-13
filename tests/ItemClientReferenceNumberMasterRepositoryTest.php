<?php

use App\Models\ItemClientReferenceNumberMaster;
use App\Repositories\ItemClientReferenceNumberMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemClientReferenceNumberMasterRepositoryTest extends TestCase
{
    use MakeItemClientReferenceNumberMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemClientReferenceNumberMasterRepository
     */
    protected $itemClientReferenceNumberMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemClientReferenceNumberMasterRepo = App::make(ItemClientReferenceNumberMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemClientReferenceNumberMaster()
    {
        $itemClientReferenceNumberMaster = $this->fakeItemClientReferenceNumberMasterData();
        $createdItemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepo->create($itemClientReferenceNumberMaster);
        $createdItemClientReferenceNumberMaster = $createdItemClientReferenceNumberMaster->toArray();
        $this->assertArrayHasKey('id', $createdItemClientReferenceNumberMaster);
        $this->assertNotNull($createdItemClientReferenceNumberMaster['id'], 'Created ItemClientReferenceNumberMaster must have id specified');
        $this->assertNotNull(ItemClientReferenceNumberMaster::find($createdItemClientReferenceNumberMaster['id']), 'ItemClientReferenceNumberMaster with given id must be in DB');
        $this->assertModelData($itemClientReferenceNumberMaster, $createdItemClientReferenceNumberMaster);
    }

    /**
     * @test read
     */
    public function testReadItemClientReferenceNumberMaster()
    {
        $itemClientReferenceNumberMaster = $this->makeItemClientReferenceNumberMaster();
        $dbItemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepo->find($itemClientReferenceNumberMaster->id);
        $dbItemClientReferenceNumberMaster = $dbItemClientReferenceNumberMaster->toArray();
        $this->assertModelData($itemClientReferenceNumberMaster->toArray(), $dbItemClientReferenceNumberMaster);
    }

    /**
     * @test update
     */
    public function testUpdateItemClientReferenceNumberMaster()
    {
        $itemClientReferenceNumberMaster = $this->makeItemClientReferenceNumberMaster();
        $fakeItemClientReferenceNumberMaster = $this->fakeItemClientReferenceNumberMasterData();
        $updatedItemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepo->update($fakeItemClientReferenceNumberMaster, $itemClientReferenceNumberMaster->id);
        $this->assertModelData($fakeItemClientReferenceNumberMaster, $updatedItemClientReferenceNumberMaster->toArray());
        $dbItemClientReferenceNumberMaster = $this->itemClientReferenceNumberMasterRepo->find($itemClientReferenceNumberMaster->id);
        $this->assertModelData($fakeItemClientReferenceNumberMaster, $dbItemClientReferenceNumberMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemClientReferenceNumberMaster()
    {
        $itemClientReferenceNumberMaster = $this->makeItemClientReferenceNumberMaster();
        $resp = $this->itemClientReferenceNumberMasterRepo->delete($itemClientReferenceNumberMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemClientReferenceNumberMaster::find($itemClientReferenceNumberMaster->id), 'ItemClientReferenceNumberMaster should not exist in DB');
    }
}
