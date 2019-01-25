<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposInvoicePaymentsApiTest extends TestCase
{
    use MakeGposInvoicePaymentsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGposInvoicePayments()
    {
        $gposInvoicePayments = $this->fakeGposInvoicePaymentsData();
        $this->json('POST', '/api/v1/gposInvoicePayments', $gposInvoicePayments);

        $this->assertApiResponse($gposInvoicePayments);
    }

    /**
     * @test
     */
    public function testReadGposInvoicePayments()
    {
        $gposInvoicePayments = $this->makeGposInvoicePayments();
        $this->json('GET', '/api/v1/gposInvoicePayments/'.$gposInvoicePayments->id);

        $this->assertApiResponse($gposInvoicePayments->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGposInvoicePayments()
    {
        $gposInvoicePayments = $this->makeGposInvoicePayments();
        $editedGposInvoicePayments = $this->fakeGposInvoicePaymentsData();

        $this->json('PUT', '/api/v1/gposInvoicePayments/'.$gposInvoicePayments->id, $editedGposInvoicePayments);

        $this->assertApiResponse($editedGposInvoicePayments);
    }

    /**
     * @test
     */
    public function testDeleteGposInvoicePayments()
    {
        $gposInvoicePayments = $this->makeGposInvoicePayments();
        $this->json('DELETE', '/api/v1/gposInvoicePayments/'.$gposInvoicePayments->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/gposInvoicePayments/'.$gposInvoicePayments->id);

        $this->assertResponseStatus(404);
    }
}
