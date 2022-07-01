<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BidSubmissionDetail;

class BidSubmissionDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bid_submission_detail()
    {
        $bidSubmissionDetail = factory(BidSubmissionDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bid_submission_details', $bidSubmissionDetail
        );

        $this->assertApiResponse($bidSubmissionDetail);
    }

    /**
     * @test
     */
    public function test_read_bid_submission_detail()
    {
        $bidSubmissionDetail = factory(BidSubmissionDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bid_submission_details/'.$bidSubmissionDetail->id
        );

        $this->assertApiResponse($bidSubmissionDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_bid_submission_detail()
    {
        $bidSubmissionDetail = factory(BidSubmissionDetail::class)->create();
        $editedBidSubmissionDetail = factory(BidSubmissionDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bid_submission_details/'.$bidSubmissionDetail->id,
            $editedBidSubmissionDetail
        );

        $this->assertApiResponse($editedBidSubmissionDetail);
    }

    /**
     * @test
     */
    public function test_delete_bid_submission_detail()
    {
        $bidSubmissionDetail = factory(BidSubmissionDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bid_submission_details/'.$bidSubmissionDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bid_submission_details/'.$bidSubmissionDetail->id
        );

        $this->response->assertStatus(404);
    }
}
