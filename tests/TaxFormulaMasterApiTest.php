<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxFormulaMasterApiTest extends TestCase
{
    use MakeTaxFormulaMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTaxFormulaMaster()
    {
        $taxFormulaMaster = $this->fakeTaxFormulaMasterData();
        $this->json('POST', '/api/v1/taxFormulaMasters', $taxFormulaMaster);

        $this->assertApiResponse($taxFormulaMaster);
    }

    /**
     * @test
     */
    public function testReadTaxFormulaMaster()
    {
        $taxFormulaMaster = $this->makeTaxFormulaMaster();
        $this->json('GET', '/api/v1/taxFormulaMasters/'.$taxFormulaMaster->id);

        $this->assertApiResponse($taxFormulaMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTaxFormulaMaster()
    {
        $taxFormulaMaster = $this->makeTaxFormulaMaster();
        $editedTaxFormulaMaster = $this->fakeTaxFormulaMasterData();

        $this->json('PUT', '/api/v1/taxFormulaMasters/'.$taxFormulaMaster->id, $editedTaxFormulaMaster);

        $this->assertApiResponse($editedTaxFormulaMaster);
    }

    /**
     * @test
     */
    public function testDeleteTaxFormulaMaster()
    {
        $taxFormulaMaster = $this->makeTaxFormulaMaster();
        $this->json('DELETE', '/api/v1/taxFormulaMasters/'.$taxFormulaMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/taxFormulaMasters/'.$taxFormulaMaster->id);

        $this->assertResponseStatus(404);
    }
}
