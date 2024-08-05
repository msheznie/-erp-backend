<?php namespace Tests\Repositories;

use App\Models\TemplateSectionTableRow;
use App\Repositories\TemplateSectionTableRowRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class TemplateSectionTableRowRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var TemplateSectionTableRowRepository
     */
    protected $templateSectionTableRowRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->templateSectionTableRowRepo = \App::make(TemplateSectionTableRowRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_template_section_table_row()
    {
        $templateSectionTableRow = factory(TemplateSectionTableRow::class)->make()->toArray();

        $createdTemplateSectionTableRow = $this->templateSectionTableRowRepo->create($templateSectionTableRow);

        $createdTemplateSectionTableRow = $createdTemplateSectionTableRow->toArray();
        $this->assertArrayHasKey('id', $createdTemplateSectionTableRow);
        $this->assertNotNull($createdTemplateSectionTableRow['id'], 'Created TemplateSectionTableRow must have id specified');
        $this->assertNotNull(TemplateSectionTableRow::find($createdTemplateSectionTableRow['id']), 'TemplateSectionTableRow with given id must be in DB');
        $this->assertModelData($templateSectionTableRow, $createdTemplateSectionTableRow);
    }

    /**
     * @test read
     */
    public function test_read_template_section_table_row()
    {
        $templateSectionTableRow = factory(TemplateSectionTableRow::class)->create();

        $dbTemplateSectionTableRow = $this->templateSectionTableRowRepo->find($templateSectionTableRow->id);

        $dbTemplateSectionTableRow = $dbTemplateSectionTableRow->toArray();
        $this->assertModelData($templateSectionTableRow->toArray(), $dbTemplateSectionTableRow);
    }

    /**
     * @test update
     */
    public function test_update_template_section_table_row()
    {
        $templateSectionTableRow = factory(TemplateSectionTableRow::class)->create();
        $fakeTemplateSectionTableRow = factory(TemplateSectionTableRow::class)->make()->toArray();

        $updatedTemplateSectionTableRow = $this->templateSectionTableRowRepo->update($fakeTemplateSectionTableRow, $templateSectionTableRow->id);

        $this->assertModelData($fakeTemplateSectionTableRow, $updatedTemplateSectionTableRow->toArray());
        $dbTemplateSectionTableRow = $this->templateSectionTableRowRepo->find($templateSectionTableRow->id);
        $this->assertModelData($fakeTemplateSectionTableRow, $dbTemplateSectionTableRow->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_template_section_table_row()
    {
        $templateSectionTableRow = factory(TemplateSectionTableRow::class)->create();

        $resp = $this->templateSectionTableRowRepo->delete($templateSectionTableRow->id);

        $this->assertTrue($resp);
        $this->assertNull(TemplateSectionTableRow::find($templateSectionTableRow->id), 'TemplateSectionTableRow should not exist in DB');
    }
}
