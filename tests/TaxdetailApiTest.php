<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxdetailApiTest extends TestCase
{
    use MakeTaxdetailTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTaxdetail()
    {
        $taxdetail = $this->fakeTaxdetailData();
        $this->json('POST', '/api/v1/taxdetails', $taxdetail);

        $this->assertApiResponse($taxdetail);
    }

    /**
     * @test
     */
    public function testReadTaxdetail()
    {
        $taxdetail = $this->makeTaxdetail();
        $this->json('GET', '/api/v1/taxdetails/'.$taxdetail->id);

        $this->assertApiResponse($taxdetail->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTaxdetail()
    {
        $taxdetail = $this->makeTaxdetail();
        $editedTaxdetail = $this->fakeTaxdetailData();

        $this->json('PUT', '/api/v1/taxdetails/'.$taxdetail->id, $editedTaxdetail);

        $this->assertApiResponse($editedTaxdetail);
    }

    /**
     * @test
     */
    public function testDeleteTaxdetail()
    {
        $taxdetail = $this->makeTaxdetail();
        $this->json('DELETE', '/api/v1/taxdetails/'.$taxdetail->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/taxdetails/'.$taxdetail->id);

        $this->assertResponseStatus(404);
    }
}
