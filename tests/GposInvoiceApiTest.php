<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class GposInvoiceApiTest extends TestCase
{
    use MakeGposInvoiceTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateGposInvoice()
    {
        $gposInvoice = $this->fakeGposInvoiceData();
        $this->json('POST', '/api/v1/gposInvoices', $gposInvoice);

        $this->assertApiResponse($gposInvoice);
    }

    /**
     * @test
     */
    public function testReadGposInvoice()
    {
        $gposInvoice = $this->makeGposInvoice();
        $this->json('GET', '/api/v1/gposInvoices/'.$gposInvoice->id);

        $this->assertApiResponse($gposInvoice->toArray());
    }

    /**
     * @test
     */
    public function testUpdateGposInvoice()
    {
        $gposInvoice = $this->makeGposInvoice();
        $editedGposInvoice = $this->fakeGposInvoiceData();

        $this->json('PUT', '/api/v1/gposInvoices/'.$gposInvoice->id, $editedGposInvoice);

        $this->assertApiResponse($editedGposInvoice);
    }

    /**
     * @test
     */
    public function testDeleteGposInvoice()
    {
        $gposInvoice = $this->makeGposInvoice();
        $this->json('DELETE', '/api/v1/gposInvoices/'.$gposInvoice->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/gposInvoices/'.$gposInvoice->id);

        $this->assertResponseStatus(404);
    }
}
