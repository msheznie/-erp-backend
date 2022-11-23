<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BidSubmissionMaster;

class BidSubmissionMasterApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bid_submission_master()
    {
        $bidSubmissionMaster = factory(BidSubmissionMaster::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bid_submission_masters', $bidSubmissionMaster
        );

        $this->assertApiResponse($bidSubmissionMaster);
    }

    /**
     * @test
     */
    public function test_read_bid_submission_master()
    {
        $bidSubmissionMaster = factory(BidSubmissionMaster::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bid_submission_masters/'.$bidSubmissionMaster->id
        );

        $this->assertApiResponse($bidSubmissionMaster->toArray());
    }

    /**
     * @test
     */
    public function test_update_bid_submission_master()
    {
        $bidSubmissionMaster = factory(BidSubmissionMaster::class)->create();
        $editedBidSubmissionMaster = factory(BidSubmissionMaster::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bid_submission_masters/'.$bidSubmissionMaster->id,
            $editedBidSubmissionMaster
        );

        $this->assertApiResponse($editedBidSubmissionMaster);
    }

    /**
     * @test
     */
    public function test_delete_bid_submission_master()
    {
        $bidSubmissionMaster = factory(BidSubmissionMaster::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bid_submission_masters/'.$bidSubmissionMaster->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bid_submission_masters/'.$bidSubmissionMaster->id
        );

        $this->response->assertStatus(404);
    }
}
