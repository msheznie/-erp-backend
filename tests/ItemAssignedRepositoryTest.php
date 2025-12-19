<?php

use App\Models\ItemAssigned;
use App\Repositories\ItemAssignedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemAssignedRepositoryTest extends TestCase
{
    use MakeItemAssignedTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemAssignedRepository
     */
    protected $itemAssignedRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemAssignedRepo = App::make(ItemAssignedRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemAssigned()
    {
        $itemAssigned = $this->fakeItemAssignedData();
        $createdItemAssigned = $this->itemAssignedRepo->create($itemAssigned);
        $createdItemAssigned = $createdItemAssigned->toArray();
        $this->assertArrayHasKey('id', $createdItemAssigned);
        $this->assertNotNull($createdItemAssigned['id'], 'Created ItemAssigned must have id specified');
        $this->assertNotNull(ItemAssigned::find($createdItemAssigned['id']), 'ItemAssigned with given id must be in DB');
        $this->assertModelData($itemAssigned, $createdItemAssigned);
    }

    /**
     * @test read
     */
    public function testReadItemAssigned()
    {
        $itemAssigned = $this->makeItemAssigned();
        $dbItemAssigned = $this->itemAssignedRepo->find($itemAssigned->id);
        $dbItemAssigned = $dbItemAssigned->toArray();
        $this->assertModelData($itemAssigned->toArray(), $dbItemAssigned);
    }

    /**
     * @test update
     */
    public function testUpdateItemAssigned()
    {
        $itemAssigned = $this->makeItemAssigned();
        $fakeItemAssigned = $this->fakeItemAssignedData();
        $updatedItemAssigned = $this->itemAssignedRepo->update($fakeItemAssigned, $itemAssigned->id);
        $this->assertModelData($fakeItemAssigned, $updatedItemAssigned->toArray());
        $dbItemAssigned = $this->itemAssignedRepo->find($itemAssigned->id);
        $this->assertModelData($fakeItemAssigned, $dbItemAssigned->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemAssigned()
    {
        $itemAssigned = $this->makeItemAssigned();
        $resp = $this->itemAssignedRepo->delete($itemAssigned->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemAssigned::find($itemAssigned->id), 'ItemAssigned should not exist in DB');
    }
}
