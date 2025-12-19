<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxTypeApiTest extends TestCase
{
    use MakeTaxTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTaxType()
    {
        $taxType = $this->fakeTaxTypeData();
        $this->json('POST', '/api/v1/taxTypes', $taxType);

        $this->assertApiResponse($taxType);
    }

    /**
     * @test
     */
    public function testReadTaxType()
    {
        $taxType = $this->makeTaxType();
        $this->json('GET', '/api/v1/taxTypes/'.$taxType->id);

        $this->assertApiResponse($taxType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTaxType()
    {
        $taxType = $this->makeTaxType();
        $editedTaxType = $this->fakeTaxTypeData();

        $this->json('PUT', '/api/v1/taxTypes/'.$taxType->id, $editedTaxType);

        $this->assertApiResponse($editedTaxType);
    }

    /**
     * @test
     */
    public function testDeleteTaxType()
    {
        $taxType = $this->makeTaxType();
        $this->json('DELETE', '/api/v1/taxTypes/'.$taxType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/taxTypes/'.$taxType->id);

        $this->assertResponseStatus(404);
    }
}
