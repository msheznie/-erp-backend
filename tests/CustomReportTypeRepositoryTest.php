<?php namespace Tests\Repositories;

use App\Models\CustomReportType;
use App\Repositories\CustomReportTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomReportTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomReportTypeRepository
     */
    protected $customReportTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customReportTypeRepo = \App::make(CustomReportTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_custom_report_type()
    {
        $customReportType = factory(CustomReportType::class)->make()->toArray();

        $createdCustomReportType = $this->customReportTypeRepo->create($customReportType);

        $createdCustomReportType = $createdCustomReportType->toArray();
        $this->assertArrayHasKey('id', $createdCustomReportType);
        $this->assertNotNull($createdCustomReportType['id'], 'Created CustomReportType must have id specified');
        $this->assertNotNull(CustomReportType::find($createdCustomReportType['id']), 'CustomReportType with given id must be in DB');
        $this->assertModelData($customReportType, $createdCustomReportType);
    }

    /**
     * @test read
     */
    public function test_read_custom_report_type()
    {
        $customReportType = factory(CustomReportType::class)->create();

        $dbCustomReportType = $this->customReportTypeRepo->find($customReportType->id);

        $dbCustomReportType = $dbCustomReportType->toArray();
        $this->assertModelData($customReportType->toArray(), $dbCustomReportType);
    }

    /**
     * @test update
     */
    public function test_update_custom_report_type()
    {
        $customReportType = factory(CustomReportType::class)->create();
        $fakeCustomReportType = factory(CustomReportType::class)->make()->toArray();

        $updatedCustomReportType = $this->customReportTypeRepo->update($fakeCustomReportType, $customReportType->id);

        $this->assertModelData($fakeCustomReportType, $updatedCustomReportType->toArray());
        $dbCustomReportType = $this->customReportTypeRepo->find($customReportType->id);
        $this->assertModelData($fakeCustomReportType, $dbCustomReportType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_custom_report_type()
    {
        $customReportType = factory(CustomReportType::class)->create();

        $resp = $this->customReportTypeRepo->delete($customReportType->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomReportType::find($customReportType->id), 'CustomReportType should not exist in DB');
    }
}
