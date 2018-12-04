<?php

use App\Models\ItemIssueDetailsRefferedBack;
use App\Repositories\ItemIssueDetailsRefferedBackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueDetailsRefferedBackRepositoryTest extends TestCase
{
    use MakeItemIssueDetailsRefferedBackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemIssueDetailsRefferedBackRepository
     */
    protected $itemIssueDetailsRefferedBackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemIssueDetailsRefferedBackRepo = App::make(ItemIssueDetailsRefferedBackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemIssueDetailsRefferedBack()
    {
        $itemIssueDetailsRefferedBack = $this->fakeItemIssueDetailsRefferedBackData();
        $createdItemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepo->create($itemIssueDetailsRefferedBack);
        $createdItemIssueDetailsRefferedBack = $createdItemIssueDetailsRefferedBack->toArray();
        $this->assertArrayHasKey('id', $createdItemIssueDetailsRefferedBack);
        $this->assertNotNull($createdItemIssueDetailsRefferedBack['id'], 'Created ItemIssueDetailsRefferedBack must have id specified');
        $this->assertNotNull(ItemIssueDetailsRefferedBack::find($createdItemIssueDetailsRefferedBack['id']), 'ItemIssueDetailsRefferedBack with given id must be in DB');
        $this->assertModelData($itemIssueDetailsRefferedBack, $createdItemIssueDetailsRefferedBack);
    }

    /**
     * @test read
     */
    public function testReadItemIssueDetailsRefferedBack()
    {
        $itemIssueDetailsRefferedBack = $this->makeItemIssueDetailsRefferedBack();
        $dbItemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepo->find($itemIssueDetailsRefferedBack->id);
        $dbItemIssueDetailsRefferedBack = $dbItemIssueDetailsRefferedBack->toArray();
        $this->assertModelData($itemIssueDetailsRefferedBack->toArray(), $dbItemIssueDetailsRefferedBack);
    }

    /**
     * @test update
     */
    public function testUpdateItemIssueDetailsRefferedBack()
    {
        $itemIssueDetailsRefferedBack = $this->makeItemIssueDetailsRefferedBack();
        $fakeItemIssueDetailsRefferedBack = $this->fakeItemIssueDetailsRefferedBackData();
        $updatedItemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepo->update($fakeItemIssueDetailsRefferedBack, $itemIssueDetailsRefferedBack->id);
        $this->assertModelData($fakeItemIssueDetailsRefferedBack, $updatedItemIssueDetailsRefferedBack->toArray());
        $dbItemIssueDetailsRefferedBack = $this->itemIssueDetailsRefferedBackRepo->find($itemIssueDetailsRefferedBack->id);
        $this->assertModelData($fakeItemIssueDetailsRefferedBack, $dbItemIssueDetailsRefferedBack->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemIssueDetailsRefferedBack()
    {
        $itemIssueDetailsRefferedBack = $this->makeItemIssueDetailsRefferedBack();
        $resp = $this->itemIssueDetailsRefferedBackRepo->delete($itemIssueDetailsRefferedBack->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemIssueDetailsRefferedBack::find($itemIssueDetailsRefferedBack->id), 'ItemIssueDetailsRefferedBack should not exist in DB');
    }
}
