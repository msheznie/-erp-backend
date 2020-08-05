<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\CustomFiltersColumn;

class CustomFiltersColumnApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_custom_filters_column()
    {
        $customFiltersColumn = factory(CustomFiltersColumn::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/custom_filters_columns', $customFiltersColumn
        );

        $this->assertApiResponse($customFiltersColumn);
    }

    /**
     * @test
     */
    public function test_read_custom_filters_column()
    {
        $customFiltersColumn = factory(CustomFiltersColumn::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/custom_filters_columns/'.$customFiltersColumn->id
        );

        $this->assertApiResponse($customFiltersColumn->toArray());
    }

    /**
     * @test
     */
    public function test_update_custom_filters_column()
    {
        $customFiltersColumn = factory(CustomFiltersColumn::class)->create();
        $editedCustomFiltersColumn = factory(CustomFiltersColumn::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/custom_filters_columns/'.$customFiltersColumn->id,
            $editedCustomFiltersColumn
        );

        $this->assertApiResponse($editedCustomFiltersColumn);
    }

    /**
     * @test
     */
    public function test_delete_custom_filters_column()
    {
        $customFiltersColumn = factory(CustomFiltersColumn::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/custom_filters_columns/'.$customFiltersColumn->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/custom_filters_columns/'.$customFiltersColumn->id
        );

        $this->response->assertStatus(404);
    }
}
