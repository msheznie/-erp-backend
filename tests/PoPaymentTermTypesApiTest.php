<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoPaymentTermTypesApiTest extends TestCase
{
    use MakePoPaymentTermTypesTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreatePoPaymentTermTypes()
    {
        $poPaymentTermTypes = $this->fakePoPaymentTermTypesData();
        $this->json('POST', '/api/v1/poPaymentTermTypes', $poPaymentTermTypes);

        $this->assertApiResponse($poPaymentTermTypes);
    }

    /**
     * @test
     */
    public function testReadPoPaymentTermTypes()
    {
        $poPaymentTermTypes = $this->makePoPaymentTermTypes();
        $this->json('GET', '/api/v1/poPaymentTermTypes/'.$poPaymentTermTypes->id);

        $this->assertApiResponse($poPaymentTermTypes->toArray());
    }

    /**
     * @test
     */
    public function testUpdatePoPaymentTermTypes()
    {
        $poPaymentTermTypes = $this->makePoPaymentTermTypes();
        $editedPoPaymentTermTypes = $this->fakePoPaymentTermTypesData();

        $this->json('PUT', '/api/v1/poPaymentTermTypes/'.$poPaymentTermTypes->id, $editedPoPaymentTermTypes);

        $this->assertApiResponse($editedPoPaymentTermTypes);
    }

    /**
     * @test
     */
    public function testDeletePoPaymentTermTypes()
    {
        $poPaymentTermTypes = $this->makePoPaymentTermTypes();
        $this->json('DELETE', '/api/v1/poPaymentTermTypes/'.$poPaymentTermTypes->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/poPaymentTermTypes/'.$poPaymentTermTypes->id);

        $this->assertResponseStatus(404);
    }
}
