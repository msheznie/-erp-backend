<?php namespace Tests\Repositories;

use App\Models\BidMainWork;
use App\Repositories\BidMainWorkRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BidMainWorkRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BidMainWorkRepository
     */
    protected $bidMainWorkRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bidMainWorkRepo = \App::make(BidMainWorkRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bid_main_work()
    {
        $bidMainWork = factory(BidMainWork::class)->make()->toArray();

        $createdBidMainWork = $this->bidMainWorkRepo->create($bidMainWork);

        $createdBidMainWork = $createdBidMainWork->toArray();
        $this->assertArrayHasKey('id', $createdBidMainWork);
        $this->assertNotNull($createdBidMainWork['id'], 'Created BidMainWork must have id specified');
        $this->assertNotNull(BidMainWork::find($createdBidMainWork['id']), 'BidMainWork with given id must be in DB');
        $this->assertModelData($bidMainWork, $createdBidMainWork);
    }

    /**
     * @test read
     */
    public function test_read_bid_main_work()
    {
        $bidMainWork = factory(BidMainWork::class)->create();

        $dbBidMainWork = $this->bidMainWorkRepo->find($bidMainWork->id);

        $dbBidMainWork = $dbBidMainWork->toArray();
        $this->assertModelData($bidMainWork->toArray(), $dbBidMainWork);
    }

    /**
     * @test update
     */
    public function test_update_bid_main_work()
    {
        $bidMainWork = factory(BidMainWork::class)->create();
        $fakeBidMainWork = factory(BidMainWork::class)->make()->toArray();

        $updatedBidMainWork = $this->bidMainWorkRepo->update($fakeBidMainWork, $bidMainWork->id);

        $this->assertModelData($fakeBidMainWork, $updatedBidMainWork->toArray());
        $dbBidMainWork = $this->bidMainWorkRepo->find($bidMainWork->id);
        $this->assertModelData($fakeBidMainWork, $dbBidMainWork->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bid_main_work()
    {
        $bidMainWork = factory(BidMainWork::class)->create();

        $resp = $this->bidMainWorkRepo->delete($bidMainWork->id);

        $this->assertTrue($resp);
        $this->assertNull(BidMainWork::find($bidMainWork->id), 'BidMainWork should not exist in DB');
    }
}
