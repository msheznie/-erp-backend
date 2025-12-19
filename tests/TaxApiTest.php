<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxApiTest extends TestCase
{
    use MakeTaxTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTax()
    {
        $tax = $this->fakeTaxData();
        $this->json('POST', '/api/v1/taxes', $tax);

        $this->assertApiResponse($tax);
    }

    /**
     * @test
     */
    public function testReadTax()
    {
        $tax = $this->makeTax();
        $this->json('GET', '/api/v1/taxes/'.$tax->id);

        $this->assertApiResponse($tax->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTax()
    {
        $tax = $this->makeTax();
        $editedTax = $this->fakeTaxData();

        $this->json('PUT', '/api/v1/taxes/'.$tax->id, $editedTax);

        $this->assertApiResponse($editedTax);
    }

    /**
     * @test
     */
    public function testDeleteTax()
    {
        $tax = $this->makeTax();
        $this->json('DELETE', '/api/v1/taxes/'.$tax->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/taxes/'.$tax->id);

        $this->assertResponseStatus(404);
    }
}
