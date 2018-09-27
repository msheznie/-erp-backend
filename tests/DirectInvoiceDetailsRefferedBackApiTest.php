<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectInvoiceDetailsRefferedBackApiTest extends TestCase
{
    use MakeDirectInvoiceDetailsRefferedBackTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateDirectInvoiceDetailsRefferedBack()
    {
        $directInvoiceDetailsRefferedBack = $this->fakeDirectInvoiceDetailsRefferedBackData();
        $this->json('POST', '/api/v1/directInvoiceDetailsRefferedBacks', $directInvoiceDetailsRefferedBack);

        $this->assertApiResponse($directInvoiceDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testReadDirectInvoiceDetailsRefferedBack()
    {
        $directInvoiceDetailsRefferedBack = $this->makeDirectInvoiceDetailsRefferedBack();
        $this->json('GET', '/api/v1/directInvoiceDetailsRefferedBacks/'.$directInvoiceDetailsRefferedBack->id);

        $this->assertApiResponse($directInvoiceDetailsRefferedBack->toArray());
    }

    /**
     * @test
     */
    public function testUpdateDirectInvoiceDetailsRefferedBack()
    {
        $directInvoiceDetailsRefferedBack = $this->makeDirectInvoiceDetailsRefferedBack();
        $editedDirectInvoiceDetailsRefferedBack = $this->fakeDirectInvoiceDetailsRefferedBackData();

        $this->json('PUT', '/api/v1/directInvoiceDetailsRefferedBacks/'.$directInvoiceDetailsRefferedBack->id, $editedDirectInvoiceDetailsRefferedBack);

        $this->assertApiResponse($editedDirectInvoiceDetailsRefferedBack);
    }

    /**
     * @test
     */
    public function testDeleteDirectInvoiceDetailsRefferedBack()
    {
        $directInvoiceDetailsRefferedBack = $this->makeDirectInvoiceDetailsRefferedBack();
        $this->json('DELETE', '/api/v1/directInvoiceDetailsRefferedBacks/'.$directInvoiceDetailsRefferedBack->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/directInvoiceDetailsRefferedBacks/'.$directInvoiceDetailsRefferedBack->id);

        $this->assertResponseStatus(404);
    }
}
