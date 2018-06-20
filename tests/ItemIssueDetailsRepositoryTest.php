<?php

use App\Models\ItemIssueDetails;
use App\Repositories\ItemIssueDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemIssueDetailsRepositoryTest extends TestCase
{
    use MakeItemIssueDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemIssueDetailsRepository
     */
    protected $itemIssueDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemIssueDetailsRepo = App::make(ItemIssueDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemIssueDetails()
    {
        $itemIssueDetails = $this->fakeItemIssueDetailsData();
        $createdItemIssueDetails = $this->itemIssueDetailsRepo->create($itemIssueDetails);
        $createdItemIssueDetails = $createdItemIssueDetails->toArray();
        $this->assertArrayHasKey('id', $createdItemIssueDetails);
        $this->assertNotNull($createdItemIssueDetails['id'], 'Created ItemIssueDetails must have id specified');
        $this->assertNotNull(ItemIssueDetails::find($createdItemIssueDetails['id']), 'ItemIssueDetails with given id must be in DB');
        $this->assertModelData($itemIssueDetails, $createdItemIssueDetails);
    }

    /**
     * @test read
     */
    public function testReadItemIssueDetails()
    {
        $itemIssueDetails = $this->makeItemIssueDetails();
        $dbItemIssueDetails = $this->itemIssueDetailsRepo->find($itemIssueDetails->id);
        $dbItemIssueDetails = $dbItemIssueDetails->toArray();
        $this->assertModelData($itemIssueDetails->toArray(), $dbItemIssueDetails);
    }

    /**
     * @test update
     */
    public function testUpdateItemIssueDetails()
    {
        $itemIssueDetails = $this->makeItemIssueDetails();
        $fakeItemIssueDetails = $this->fakeItemIssueDetailsData();
        $updatedItemIssueDetails = $this->itemIssueDetailsRepo->update($fakeItemIssueDetails, $itemIssueDetails->id);
        $this->assertModelData($fakeItemIssueDetails, $updatedItemIssueDetails->toArray());
        $dbItemIssueDetails = $this->itemIssueDetailsRepo->find($itemIssueDetails->id);
        $this->assertModelData($fakeItemIssueDetails, $dbItemIssueDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemIssueDetails()
    {
        $itemIssueDetails = $this->makeItemIssueDetails();
        $resp = $this->itemIssueDetailsRepo->delete($itemIssueDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemIssueDetails::find($itemIssueDetails->id), 'ItemIssueDetails should not exist in DB');
    }
}
