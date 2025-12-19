<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FinanceItemCategorySubApiTest extends TestCase
{
    use MakeFinanceItemCategorySubTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateFinanceItemCategorySub()
    {
        $financeItemCategorySub = $this->fakeFinanceItemCategorySubData();
        $this->json('POST', '/api/v1/financeItemCategorySubs', $financeItemCategorySub);

        $this->assertApiResponse($financeItemCategorySub);
    }

    /**
     * @test
     */
    public function testReadFinanceItemCategorySub()
    {
        $financeItemCategorySub = $this->makeFinanceItemCategorySub();
        $this->json('GET', '/api/v1/financeItemCategorySubs/'.$financeItemCategorySub->id);

        $this->assertApiResponse($financeItemCategorySub->toArray());
    }

    /**
     * @test
     */
    public function testUpdateFinanceItemCategorySub()
    {
        $financeItemCategorySub = $this->makeFinanceItemCategorySub();
        $editedFinanceItemCategorySub = $this->fakeFinanceItemCategorySubData();

        $this->json('PUT', '/api/v1/financeItemCategorySubs/'.$financeItemCategorySub->id, $editedFinanceItemCategorySub);

        $this->assertApiResponse($editedFinanceItemCategorySub);
    }

    /**
     * @test
     */
    public function testDeleteFinanceItemCategorySub()
    {
        $financeItemCategorySub = $this->makeFinanceItemCategorySub();
        $this->json('DELETE', '/api/v1/financeItemCategorySubs/'.$financeItemCategorySub->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/financeItemCategorySubs/'.$financeItemCategorySub->id);

        $this->assertResponseStatus(404);
    }
}
