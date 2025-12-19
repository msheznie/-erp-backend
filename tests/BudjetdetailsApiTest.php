<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudjetdetailsApiTest extends TestCase
{
    use MakeBudjetdetailsTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateBudjetdetails()
    {
        $budjetdetails = $this->fakeBudjetdetailsData();
        $this->json('POST', '/api/v1/budjetdetails', $budjetdetails);

        $this->assertApiResponse($budjetdetails);
    }

    /**
     * @test
     */
    public function testReadBudjetdetails()
    {
        $budjetdetails = $this->makeBudjetdetails();
        $this->json('GET', '/api/v1/budjetdetails/'.$budjetdetails->id);

        $this->assertApiResponse($budjetdetails->toArray());
    }

    /**
     * @test
     */
    public function testUpdateBudjetdetails()
    {
        $budjetdetails = $this->makeBudjetdetails();
        $editedBudjetdetails = $this->fakeBudjetdetailsData();

        $this->json('PUT', '/api/v1/budjetdetails/'.$budjetdetails->id, $editedBudjetdetails);

        $this->assertApiResponse($editedBudjetdetails);
    }

    /**
     * @test
     */
    public function testDeleteBudjetdetails()
    {
        $budjetdetails = $this->makeBudjetdetails();
        $this->json('DELETE', '/api/v1/budjetdetails/'.$budjetdetails->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/budjetdetails/'.$budjetdetails->id);

        $this->assertResponseStatus(404);
    }
}
