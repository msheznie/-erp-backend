<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\FinanceItemCategoryTypes;

class FinanceItemCategoryTypesApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_finance_item_category_types()
    {
        $financeItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/finance_item_category_types', $financeItemCategoryTypes
        );

        $this->assertApiResponse($financeItemCategoryTypes);
    }

    /**
     * @test
     */
    public function test_read_finance_item_category_types()
    {
        $financeItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/finance_item_category_types/'.$financeItemCategoryTypes->id
        );

        $this->assertApiResponse($financeItemCategoryTypes->toArray());
    }

    /**
     * @test
     */
    public function test_update_finance_item_category_types()
    {
        $financeItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->create();
        $editedFinanceItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/finance_item_category_types/'.$financeItemCategoryTypes->id,
            $editedFinanceItemCategoryTypes
        );

        $this->assertApiResponse($editedFinanceItemCategoryTypes);
    }

    /**
     * @test
     */
    public function test_delete_finance_item_category_types()
    {
        $financeItemCategoryTypes = factory(FinanceItemCategoryTypes::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/finance_item_category_types/'.$financeItemCategoryTypes->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/finance_item_category_types/'.$financeItemCategoryTypes->id
        );

        $this->response->assertStatus(404);
    }
}
