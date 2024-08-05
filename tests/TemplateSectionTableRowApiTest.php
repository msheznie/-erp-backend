<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\TemplateSectionTableRow;

class TemplateSectionTableRowApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_template_section_table_row()
    {
        $templateSectionTableRow = factory(TemplateSectionTableRow::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/template_section_table_rows', $templateSectionTableRow
        );

        $this->assertApiResponse($templateSectionTableRow);
    }

    /**
     * @test
     */
    public function test_read_template_section_table_row()
    {
        $templateSectionTableRow = factory(TemplateSectionTableRow::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/template_section_table_rows/'.$templateSectionTableRow->id
        );

        $this->assertApiResponse($templateSectionTableRow->toArray());
    }

    /**
     * @test
     */
    public function test_update_template_section_table_row()
    {
        $templateSectionTableRow = factory(TemplateSectionTableRow::class)->create();
        $editedTemplateSectionTableRow = factory(TemplateSectionTableRow::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/template_section_table_rows/'.$templateSectionTableRow->id,
            $editedTemplateSectionTableRow
        );

        $this->assertApiResponse($editedTemplateSectionTableRow);
    }

    /**
     * @test
     */
    public function test_delete_template_section_table_row()
    {
        $templateSectionTableRow = factory(TemplateSectionTableRow::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/template_section_table_rows/'.$templateSectionTableRow->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/template_section_table_rows/'.$templateSectionTableRow->id
        );

        $this->response->assertStatus(404);
    }
}
