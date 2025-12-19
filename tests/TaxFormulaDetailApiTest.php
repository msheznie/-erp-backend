<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxFormulaDetailApiTest extends TestCase
{
    use MakeTaxFormulaDetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTaxFormulaDetail()
    {
        $taxFormulaDetail = $this->fakeTaxFormulaDetailData();
        $this->json('POST', '/api/v1/taxFormulaDetails', $taxFormulaDetail);

        $this->assertApiResponse($taxFormulaDetail);
    }

    /**
     * @test
     */
    public function testReadTaxFormulaDetail()
    {
        $taxFormulaDetail = $this->makeTaxFormulaDetail();
        $this->json('GET', '/api/v1/taxFormulaDetails/'.$taxFormulaDetail->id);

        $this->assertApiResponse($taxFormulaDetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTaxFormulaDetail()
    {
        $taxFormulaDetail = $this->makeTaxFormulaDetail();
        $editedTaxFormulaDetail = $this->fakeTaxFormulaDetailData();

        $this->json('PUT', '/api/v1/taxFormulaDetails/'.$taxFormulaDetail->id, $editedTaxFormulaDetail);

        $this->assertApiResponse($editedTaxFormulaDetail);
    }

    /**
     * @test
     */
    public function testDeleteTaxFormulaDetail()
    {
        $taxFormulaDetail = $this->makeTaxFormulaDetail();
        $this->json('DELETE', '/api/v1/taxFormulaDetails/'.$taxFormulaDetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/taxFormulaDetails/'.$taxFormulaDetail->id);

        $this->assertResponseStatus(404);
    }
}
