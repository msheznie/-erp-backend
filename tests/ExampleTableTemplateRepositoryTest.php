<?php namespace Tests\Repositories;

use App\Models\ExampleTableTemplate;
use App\Repositories\ExampleTableTemplateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExampleTableTemplateRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExampleTableTemplateRepository
     */
    protected $exampleTableTemplateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->exampleTableTemplateRepo = \App::make(ExampleTableTemplateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_example_table_template()
    {
        $exampleTableTemplate = factory(ExampleTableTemplate::class)->make()->toArray();

        $createdExampleTableTemplate = $this->exampleTableTemplateRepo->create($exampleTableTemplate);

        $createdExampleTableTemplate = $createdExampleTableTemplate->toArray();
        $this->assertArrayHasKey('id', $createdExampleTableTemplate);
        $this->assertNotNull($createdExampleTableTemplate['id'], 'Created ExampleTableTemplate must have id specified');
        $this->assertNotNull(ExampleTableTemplate::find($createdExampleTableTemplate['id']), 'ExampleTableTemplate with given id must be in DB');
        $this->assertModelData($exampleTableTemplate, $createdExampleTableTemplate);
    }

    /**
     * @test read
     */
    public function test_read_example_table_template()
    {
        $exampleTableTemplate = factory(ExampleTableTemplate::class)->create();

        $dbExampleTableTemplate = $this->exampleTableTemplateRepo->find($exampleTableTemplate->id);

        $dbExampleTableTemplate = $dbExampleTableTemplate->toArray();
        $this->assertModelData($exampleTableTemplate->toArray(), $dbExampleTableTemplate);
    }

    /**
     * @test update
     */
    public function test_update_example_table_template()
    {
        $exampleTableTemplate = factory(ExampleTableTemplate::class)->create();
        $fakeExampleTableTemplate = factory(ExampleTableTemplate::class)->make()->toArray();

        $updatedExampleTableTemplate = $this->exampleTableTemplateRepo->update($fakeExampleTableTemplate, $exampleTableTemplate->id);

        $this->assertModelData($fakeExampleTableTemplate, $updatedExampleTableTemplate->toArray());
        $dbExampleTableTemplate = $this->exampleTableTemplateRepo->find($exampleTableTemplate->id);
        $this->assertModelData($fakeExampleTableTemplate, $dbExampleTableTemplate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_example_table_template()
    {
        $exampleTableTemplate = factory(ExampleTableTemplate::class)->create();

        $resp = $this->exampleTableTemplateRepo->delete($exampleTableTemplate->id);

        $this->assertTrue($resp);
        $this->assertNull(ExampleTableTemplate::find($exampleTableTemplate->id), 'ExampleTableTemplate should not exist in DB');
    }
}
