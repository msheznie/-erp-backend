<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\SoPaymentTerms;

class SoPaymentTermsApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_so_payment_terms()
    {
        $soPaymentTerms = factory(SoPaymentTerms::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/so_payment_terms', $soPaymentTerms
        );

        $this->assertApiResponse($soPaymentTerms);
    }

    /**
     * @test
     */
    public function test_read_so_payment_terms()
    {
        $soPaymentTerms = factory(SoPaymentTerms::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/so_payment_terms/'.$soPaymentTerms->id
        );

        $this->assertApiResponse($soPaymentTerms->toArray());
    }

    /**
     * @test
     */
    public function test_update_so_payment_terms()
    {
        $soPaymentTerms = factory(SoPaymentTerms::class)->create();
        $editedSoPaymentTerms = factory(SoPaymentTerms::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/so_payment_terms/'.$soPaymentTerms->id,
            $editedSoPaymentTerms
        );

        $this->assertApiResponse($editedSoPaymentTerms);
    }

    /**
     * @test
     */
    public function test_delete_so_payment_terms()
    {
        $soPaymentTerms = factory(SoPaymentTerms::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/so_payment_terms/'.$soPaymentTerms->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/so_payment_terms/'.$soPaymentTerms->id
        );

        $this->response->assertStatus(404);
    }
}
