<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\BidDocumentVerification;

class BidDocumentVerificationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_bid_document_verification()
    {
        $bidDocumentVerification = factory(BidDocumentVerification::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/bid_document_verifications', $bidDocumentVerification
        );

        $this->assertApiResponse($bidDocumentVerification);
    }

    /**
     * @test
     */
    public function test_read_bid_document_verification()
    {
        $bidDocumentVerification = factory(BidDocumentVerification::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/bid_document_verifications/'.$bidDocumentVerification->id
        );

        $this->assertApiResponse($bidDocumentVerification->toArray());
    }

    /**
     * @test
     */
    public function test_update_bid_document_verification()
    {
        $bidDocumentVerification = factory(BidDocumentVerification::class)->create();
        $editedBidDocumentVerification = factory(BidDocumentVerification::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/bid_document_verifications/'.$bidDocumentVerification->id,
            $editedBidDocumentVerification
        );

        $this->assertApiResponse($editedBidDocumentVerification);
    }

    /**
     * @test
     */
    public function test_delete_bid_document_verification()
    {
        $bidDocumentVerification = factory(BidDocumentVerification::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/bid_document_verifications/'.$bidDocumentVerification->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/bid_document_verifications/'.$bidDocumentVerification->id
        );

        $this->response->assertStatus(404);
    }
}
