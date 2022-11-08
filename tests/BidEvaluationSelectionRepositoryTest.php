<?php namespace Tests\Repositories;

use App\Models\BidEvaluationSelection;
use App\Repositories\BidEvaluationSelectionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class BidEvaluationSelectionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var BidEvaluationSelectionRepository
     */
    protected $bidEvaluationSelectionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->bidEvaluationSelectionRepo = \App::make(BidEvaluationSelectionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_bid_evaluation_selection()
    {
        $bidEvaluationSelection = factory(BidEvaluationSelection::class)->make()->toArray();

        $createdBidEvaluationSelection = $this->bidEvaluationSelectionRepo->create($bidEvaluationSelection);

        $createdBidEvaluationSelection = $createdBidEvaluationSelection->toArray();
        $this->assertArrayHasKey('id', $createdBidEvaluationSelection);
        $this->assertNotNull($createdBidEvaluationSelection['id'], 'Created BidEvaluationSelection must have id specified');
        $this->assertNotNull(BidEvaluationSelection::find($createdBidEvaluationSelection['id']), 'BidEvaluationSelection with given id must be in DB');
        $this->assertModelData($bidEvaluationSelection, $createdBidEvaluationSelection);
    }

    /**
     * @test read
     */
    public function test_read_bid_evaluation_selection()
    {
        $bidEvaluationSelection = factory(BidEvaluationSelection::class)->create();

        $dbBidEvaluationSelection = $this->bidEvaluationSelectionRepo->find($bidEvaluationSelection->id);

        $dbBidEvaluationSelection = $dbBidEvaluationSelection->toArray();
        $this->assertModelData($bidEvaluationSelection->toArray(), $dbBidEvaluationSelection);
    }

    /**
     * @test update
     */
    public function test_update_bid_evaluation_selection()
    {
        $bidEvaluationSelection = factory(BidEvaluationSelection::class)->create();
        $fakeBidEvaluationSelection = factory(BidEvaluationSelection::class)->make()->toArray();

        $updatedBidEvaluationSelection = $this->bidEvaluationSelectionRepo->update($fakeBidEvaluationSelection, $bidEvaluationSelection->id);

        $this->assertModelData($fakeBidEvaluationSelection, $updatedBidEvaluationSelection->toArray());
        $dbBidEvaluationSelection = $this->bidEvaluationSelectionRepo->find($bidEvaluationSelection->id);
        $this->assertModelData($fakeBidEvaluationSelection, $dbBidEvaluationSelection->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_bid_evaluation_selection()
    {
        $bidEvaluationSelection = factory(BidEvaluationSelection::class)->create();

        $resp = $this->bidEvaluationSelectionRepo->delete($bidEvaluationSelection->id);

        $this->assertTrue($resp);
        $this->assertNull(BidEvaluationSelection::find($bidEvaluationSelection->id), 'BidEvaluationSelection should not exist in DB');
    }
}
