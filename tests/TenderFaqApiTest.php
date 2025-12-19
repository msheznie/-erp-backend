<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TenderFaq;

class TenderFaqApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_tender_faq()
    {
        $tenderFaq = factory(TenderFaq::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/tender_faqs', $tenderFaq
        );

        $this->assertApiResponse($tenderFaq);
    }

    /**
     * @test
     */
    public function test_read_tender_faq()
    {
        $tenderFaq = factory(TenderFaq::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/tender_faqs/'.$tenderFaq->id
        );

        $this->assertApiResponse($tenderFaq->toArray());
    }

    /**
     * @test
     */
    public function test_update_tender_faq()
    {
        $tenderFaq = factory(TenderFaq::class)->create();
        $editedTenderFaq = factory(TenderFaq::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/tender_faqs/'.$tenderFaq->id,
            $editedTenderFaq
        );

        $this->assertApiResponse($editedTenderFaq);
    }

    /**
     * @test
     */
    public function test_delete_tender_faq()
    {
        $tenderFaq = factory(TenderFaq::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/tender_faqs/'.$tenderFaq->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/tender_faqs/'.$tenderFaq->id
        );

        $this->response->assertStatus(404);
    }
}
