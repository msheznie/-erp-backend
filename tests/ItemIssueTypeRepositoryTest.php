<?php

use App\Models\ItemIssueType;
use App\Repositories\ItemIssueTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueTypeRepositoryTest extends TestCase
{
    use MakeItemIssueTypeTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemIssueTypeRepository
     */
    protected $itemIssueTypeRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemIssueTypeRepo = App::make(ItemIssueTypeRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemIssueType()
    {
        $itemIssueType = $this->fakeItemIssueTypeData();
        $createdItemIssueType = $this->itemIssueTypeRepo->create($itemIssueType);
        $createdItemIssueType = $createdItemIssueType->toArray();
        $this->assertArrayHasKey('id', $createdItemIssueType);
        $this->assertNotNull($createdItemIssueType['id'], 'Created ItemIssueType must have id specified');
        $this->assertNotNull(ItemIssueType::find($createdItemIssueType['id']), 'ItemIssueType with given id must be in DB');
        $this->assertModelData($itemIssueType, $createdItemIssueType);
    }

    /**
     * @test read
     */
    public function testReadItemIssueType()
    {
        $itemIssueType = $this->makeItemIssueType();
        $dbItemIssueType = $this->itemIssueTypeRepo->find($itemIssueType->id);
        $dbItemIssueType = $dbItemIssueType->toArray();
        $this->assertModelData($itemIssueType->toArray(), $dbItemIssueType);
    }

    /**
     * @test update
     */
    public function testUpdateItemIssueType()
    {
        $itemIssueType = $this->makeItemIssueType();
        $fakeItemIssueType = $this->fakeItemIssueTypeData();
        $updatedItemIssueType = $this->itemIssueTypeRepo->update($fakeItemIssueType, $itemIssueType->id);
        $this->assertModelData($fakeItemIssueType, $updatedItemIssueType->toArray());
        $dbItemIssueType = $this->itemIssueTypeRepo->find($itemIssueType->id);
        $this->assertModelData($fakeItemIssueType, $dbItemIssueType->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemIssueType()
    {
        $itemIssueType = $this->makeItemIssueType();
        $resp = $this->itemIssueTypeRepo->delete($itemIssueType->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemIssueType::find($itemIssueType->id), 'ItemIssueType should not exist in DB');
    }
}
