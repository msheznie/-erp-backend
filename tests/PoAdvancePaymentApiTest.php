<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoAdvancePaymentApiTest extends TestCase
{
    use MakePoAdvancePaymentTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePoAdvancePayment()
    {
        $poAdvancePayment = $this->fakePoAdvancePaymentData();
        $this->json('POST', '/api/v1/poAdvancePayments', $poAdvancePayment);

        $this->assertApiResponse($poAdvancePayment);
    }

    /**
     * @test
     */
    public function testReadPoAdvancePayment()
    {
        $poAdvancePayment = $this->makePoAdvancePayment();
        $this->json('GET', '/api/v1/poAdvancePayments/'.$poAdvancePayment->id);

        $this->assertApiResponse($poAdvancePayment->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePoAdvancePayment()
    {
        $poAdvancePayment = $this->makePoAdvancePayment();
        $editedPoAdvancePayment = $this->fakePoAdvancePaymentData();

        $this->json('PUT', '/api/v1/poAdvancePayments/'.$poAdvancePayment->id, $editedPoAdvancePayment);

        $this->assertApiResponse($editedPoAdvancePayment);
    }

    /**
     * @test
     */
    public function testDeletePoAdvancePayment()
    {
        $poAdvancePayment = $this->makePoAdvancePayment();
        $this->json('DELETE', '/api/v1/poAdvancePayments/'.$poAdvancePayment->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/poAdvancePayments/'.$poAdvancePayment->id);

        $this->assertResponseStatus(404);
    }
}
