<?php

use App\Models\ItemIssueMaster;
use App\Repositories\ItemIssueMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueMasterRepositoryTest extends TestCase
{
    use MakeItemIssueMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemIssueMasterRepository
     */
    protected $itemIssueMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemIssueMasterRepo = App::make(ItemIssueMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemIssueMaster()
    {
        $itemIssueMaster = $this->fakeItemIssueMasterData();
        $createdItemIssueMaster = $this->itemIssueMasterRepo->create($itemIssueMaster);
        $createdItemIssueMaster = $createdItemIssueMaster->toArray();
        $this->assertArrayHasKey('id', $createdItemIssueMaster);
        $this->assertNotNull($createdItemIssueMaster['id'], 'Created ItemIssueMaster must have id specified');
        $this->assertNotNull(ItemIssueMaster::find($createdItemIssueMaster['id']), 'ItemIssueMaster with given id must be in DB');
        $this->assertModelData($itemIssueMaster, $createdItemIssueMaster);
    }

    /**
     * @test read
     */
    public function testReadItemIssueMaster()
    {
        $itemIssueMaster = $this->makeItemIssueMaster();
        $dbItemIssueMaster = $this->itemIssueMasterRepo->find($itemIssueMaster->id);
        $dbItemIssueMaster = $dbItemIssueMaster->toArray();
        $this->assertModelData($itemIssueMaster->toArray(), $dbItemIssueMaster);
    }

    /**
     * @test update
     */
    public function testUpdateItemIssueMaster()
    {
        $itemIssueMaster = $this->makeItemIssueMaster();
        $fakeItemIssueMaster = $this->fakeItemIssueMasterData();
        $updatedItemIssueMaster = $this->itemIssueMasterRepo->update($fakeItemIssueMaster, $itemIssueMaster->id);
        $this->assertModelData($fakeItemIssueMaster, $updatedItemIssueMaster->toArray());
        $dbItemIssueMaster = $this->itemIssueMasterRepo->find($itemIssueMaster->id);
        $this->assertModelData($fakeItemIssueMaster, $dbItemIssueMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemIssueMaster()
    {
        $itemIssueMaster = $this->makeItemIssueMaster();
        $resp = $this->itemIssueMasterRepo->delete($itemIssueMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemIssueMaster::find($itemIssueMaster->id), 'ItemIssueMaster should not exist in DB');
    }
}
