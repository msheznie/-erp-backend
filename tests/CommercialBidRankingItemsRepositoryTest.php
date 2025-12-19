<?php namespace Tests\Repositories;

use App\Models\CommercialBidRankingItems;
use App\Repositories\CommercialBidRankingItemsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CommercialBidRankingItemsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CommercialBidRankingItemsRepository
     */
    protected $commercialBidRankingItemsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->commercialBidRankingItemsRepo = \App::make(CommercialBidRankingItemsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_commercial_bid_ranking_items()
    {
        $commercialBidRankingItems = factory(CommercialBidRankingItems::class)->make()->toArray();

        $createdCommercialBidRankingItems = $this->commercialBidRankingItemsRepo->create($commercialBidRankingItems);

        $createdCommercialBidRankingItems = $createdCommercialBidRankingItems->toArray();
        $this->assertArrayHasKey('id', $createdCommercialBidRankingItems);
        $this->assertNotNull($createdCommercialBidRankingItems['id'], 'Created CommercialBidRankingItems must have id specified');
        $this->assertNotNull(CommercialBidRankingItems::find($createdCommercialBidRankingItems['id']), 'CommercialBidRankingItems with given id must be in DB');
        $this->assertModelData($commercialBidRankingItems, $createdCommercialBidRankingItems);
    }

    /**
     * @test read
     */
    public function test_read_commercial_bid_ranking_items()
    {
        $commercialBidRankingItems = factory(CommercialBidRankingItems::class)->create();

        $dbCommercialBidRankingItems = $this->commercialBidRankingItemsRepo->find($commercialBidRankingItems->id);

        $dbCommercialBidRankingItems = $dbCommercialBidRankingItems->toArray();
        $this->assertModelData($commercialBidRankingItems->toArray(), $dbCommercialBidRankingItems);
    }

    /**
     * @test update
     */
    public function test_update_commercial_bid_ranking_items()
    {
        $commercialBidRankingItems = factory(CommercialBidRankingItems::class)->create();
        $fakeCommercialBidRankingItems = factory(CommercialBidRankingItems::class)->make()->toArray();

        $updatedCommercialBidRankingItems = $this->commercialBidRankingItemsRepo->update($fakeCommercialBidRankingItems, $commercialBidRankingItems->id);

        $this->assertModelData($fakeCommercialBidRankingItems, $updatedCommercialBidRankingItems->toArray());
        $dbCommercialBidRankingItems = $this->commercialBidRankingItemsRepo->find($commercialBidRankingItems->id);
        $this->assertModelData($fakeCommercialBidRankingItems, $dbCommercialBidRankingItems->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_commercial_bid_ranking_items()
    {
        $commercialBidRankingItems = factory(CommercialBidRankingItems::class)->create();

        $resp = $this->commercialBidRankingItemsRepo->delete($commercialBidRankingItems->id);

        $this->assertTrue($resp);
        $this->assertNull(CommercialBidRankingItems::find($commercialBidRankingItems->id), 'CommercialBidRankingItems should not exist in DB');
    }
}
