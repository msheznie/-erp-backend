<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FinanceItemCategoryMasterApiTest extends TestCase
{
    use MakeFinanceItemCategoryMasterTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFinanceItemCategoryMaster()
    {
        $financeItemCategoryMaster = $this->fakeFinanceItemCategoryMasterData();
        $this->json('POST', '/api/v1/financeItemCategoryMasters', $financeItemCategoryMaster);

        $this->assertApiResponse($financeItemCategoryMaster);
    }

    /**
     * @test
     */
    public function testReadFinanceItemCategoryMaster()
    {
        $financeItemCategoryMaster = $this->makeFinanceItemCategoryMaster();
        $this->json('GET', '/api/v1/financeItemCategoryMasters/'.$financeItemCategoryMaster->id);

        $this->assertApiResponse($financeItemCategoryMaster->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFinanceItemCategoryMaster()
    {
        $financeItemCategoryMaster = $this->makeFinanceItemCategoryMaster();
        $editedFinanceItemCategoryMaster = $this->fakeFinanceItemCategoryMasterData();

        $this->json('PUT', '/api/v1/financeItemCategoryMasters/'.$financeItemCategoryMaster->id, $editedFinanceItemCategoryMaster);

        $this->assertApiResponse($editedFinanceItemCategoryMaster);
    }

    /**
     * @test
     */
    public function testDeleteFinanceItemCategoryMaster()
    {
        $financeItemCategoryMaster = $this->makeFinanceItemCategoryMaster();
        $this->json('DELETE', '/api/v1/financeItemCategoryMasters/'.$financeItemCategoryMaster->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/financeItemCategoryMasters/'.$financeItemCategoryMaster->id);

        $this->assertResponseStatus(404);
    }
}
