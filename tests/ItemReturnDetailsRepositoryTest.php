<?php

use App\Models\ItemReturnDetails;
use App\Repositories\ItemReturnDetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemReturnDetailsRepositoryTest extends TestCase
{
    use MakeItemReturnDetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemReturnDetailsRepository
     */
    protected $itemReturnDetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->itemReturnDetailsRepo = App::make(ItemReturnDetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateItemReturnDetails()
    {
        $itemReturnDetails = $this->fakeItemReturnDetailsData();
        $createdItemReturnDetails = $this->itemReturnDetailsRepo->create($itemReturnDetails);
        $createdItemReturnDetails = $createdItemReturnDetails->toArray();
        $this->assertArrayHasKey('id', $createdItemReturnDetails);
        $this->assertNotNull($createdItemReturnDetails['id'], 'Created ItemReturnDetails must have id specified');
        $this->assertNotNull(ItemReturnDetails::find($createdItemReturnDetails['id']), 'ItemReturnDetails with given id must be in DB');
        $this->assertModelData($itemReturnDetails, $createdItemReturnDetails);
    }

    /**
     * @test read
     */
    public function testReadItemReturnDetails()
    {
        $itemReturnDetails = $this->makeItemReturnDetails();
        $dbItemReturnDetails = $this->itemReturnDetailsRepo->find($itemReturnDetails->id);
        $dbItemReturnDetails = $dbItemReturnDetails->toArray();
        $this->assertModelData($itemReturnDetails->toArray(), $dbItemReturnDetails);
    }

    /**
     * @test update
     */
    public function testUpdateItemReturnDetails()
    {
        $itemReturnDetails = $this->makeItemReturnDetails();
        $fakeItemReturnDetails = $this->fakeItemReturnDetailsData();
        $updatedItemReturnDetails = $this->itemReturnDetailsRepo->update($fakeItemReturnDetails, $itemReturnDetails->id);
        $this->assertModelData($fakeItemReturnDetails, $updatedItemReturnDetails->toArray());
        $dbItemReturnDetails = $this->itemReturnDetailsRepo->find($itemReturnDetails->id);
        $this->assertModelData($fakeItemReturnDetails, $dbItemReturnDetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteItemReturnDetails()
    {
        $itemReturnDetails = $this->makeItemReturnDetails();
        $resp = $this->itemReturnDetailsRepo->delete($itemReturnDetails->id);
        $this->assertTrue($resp);
        $this->assertNull(ItemReturnDetails::find($itemReturnDetails->id), 'ItemReturnDetails should not exist in DB');
    }
}
