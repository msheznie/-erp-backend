<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ExampleTableTemplate;

class ExampleTableTemplateApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_example_table_template()
    {
        $exampleTableTemplate = factory(ExampleTableTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/example_table_templates', $exampleTableTemplate
        );

        $this->assertApiResponse($exampleTableTemplate);
    }

    /**
     * @test
     */
    public function test_read_example_table_template()
    {
        $exampleTableTemplate = factory(ExampleTableTemplate::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/example_table_templates/'.$exampleTableTemplate->id
        );

        $this->assertApiResponse($exampleTableTemplate->toArray());
    }

    /**
     * @test
     */
    public function test_update_example_table_template()
    {
        $exampleTableTemplate = factory(ExampleTableTemplate::class)->create();
        $editedExampleTableTemplate = factory(ExampleTableTemplate::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/example_table_templates/'.$exampleTableTemplate->id,
            $editedExampleTableTemplate
        );

        $this->assertApiResponse($editedExampleTableTemplate);
    }

    /**
     * @test
     */
    public function test_delete_example_table_template()
    {
        $exampleTableTemplate = factory(ExampleTableTemplate::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/example_table_templates/'.$exampleTableTemplate->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/example_table_templates/'.$exampleTableTemplate->id
        );

        $this->response->assertStatus(404);
    }
}
