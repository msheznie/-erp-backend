<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\B2BSubmissionFileDetail;

class B2BSubmissionFileDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_b2_b_submission_file_detail()
    {
        $b2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/b2_b_submission_file_details', $b2BSubmissionFileDetail
        );

        $this->assertApiResponse($b2BSubmissionFileDetail);
    }

    /**
     * @test
     */
    public function test_read_b2_b_submission_file_detail()
    {
        $b2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/b2_b_submission_file_details/'.$b2BSubmissionFileDetail->id
        );

        $this->assertApiResponse($b2BSubmissionFileDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_b2_b_submission_file_detail()
    {
        $b2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->create();
        $editedB2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/b2_b_submission_file_details/'.$b2BSubmissionFileDetail->id,
            $editedB2BSubmissionFileDetail
        );

        $this->assertApiResponse($editedB2BSubmissionFileDetail);
    }

    /**
     * @test
     */
    public function test_delete_b2_b_submission_file_detail()
    {
        $b2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/b2_b_submission_file_details/'.$b2BSubmissionFileDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/b2_b_submission_file_details/'.$b2BSubmissionFileDetail->id
        );

        $this->response->assertStatus(404);
    }
}
