<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class TaxAuthorityApiTest extends TestCase
{
    use MakeTaxAuthorityTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateTaxAuthority()
    {
        $taxAuthority = $this->fakeTaxAuthorityData();
        $this->json('POST', '/api/v1/taxAuthorities', $taxAuthority);

        $this->assertApiResponse($taxAuthority);
    }

    /**
     * @test
     */
    public function testReadTaxAuthority()
    {
        $taxAuthority = $this->makeTaxAuthority();
        $this->json('GET', '/api/v1/taxAuthorities/'.$taxAuthority->id);

        $this->assertApiResponse($taxAuthority->toArray());
    }

    /**
     * @test
     */
    public function testUpdateTaxAuthority()
    {
        $taxAuthority = $this->makeTaxAuthority();
        $editedTaxAuthority = $this->fakeTaxAuthorityData();

        $this->json('PUT', '/api/v1/taxAuthorities/'.$taxAuthority->id, $editedTaxAuthority);

        $this->assertApiResponse($editedTaxAuthority);
    }

    /**
     * @test
     */
    public function testDeleteTaxAuthority()
    {
        $taxAuthority = $this->makeTaxAuthority();
        $this->json('DELETE', '/api/v1/taxAuthorities/'.$taxAuthority->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/taxAuthorities/'.$taxAuthority->id);

        $this->assertResponseStatus(404);
    }
}
