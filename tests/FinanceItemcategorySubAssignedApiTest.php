<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FinanceItemcategorySubAssignedApiTest extends TestCase
{
    use MakeFinanceItemcategorySubAssignedTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFinanceItemcategorySubAssigned()
    {
        $financeItemcategorySubAssigned = $this->fakeFinanceItemcategorySubAssignedData();
        $this->json('POST', '/api/v1/financeItemcategorySubAssigneds', $financeItemcategorySubAssigned);

        $this->assertApiResponse($financeItemcategorySubAssigned);
    }

    /**
     * @test
     */
    public function testReadFinanceItemcategorySubAssigned()
    {
        $financeItemcategorySubAssigned = $this->makeFinanceItemcategorySubAssigned();
        $this->json('GET', '/api/v1/financeItemcategorySubAssigneds/'.$financeItemcategorySubAssigned->id);

        $this->assertApiResponse($financeItemcategorySubAssigned->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFinanceItemcategorySubAssigned()
    {
        $financeItemcategorySubAssigned = $this->makeFinanceItemcategorySubAssigned();
        $editedFinanceItemcategorySubAssigned = $this->fakeFinanceItemcategorySubAssignedData();

        $this->json('PUT', '/api/v1/financeItemcategorySubAssigneds/'.$financeItemcategorySubAssigned->id, $editedFinanceItemcategorySubAssigned);

        $this->assertApiResponse($editedFinanceItemcategorySubAssigned);
    }

    /**
     * @test
     */
    public function testDeleteFinanceItemcategorySubAssigned()
    {
        $financeItemcategorySubAssigned = $this->makeFinanceItemcategorySubAssigned();
        $this->json('DELETE', '/api/v1/financeItemcategorySubAssigneds/'.$financeItemcategorySubAssigned->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/financeItemcategorySubAssigneds/'.$financeItemcategorySubAssigned->id);

        $this->assertResponseStatus(404);
    }
}
