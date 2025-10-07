<?php namespace Tests\Repositories;

use App\Models\LogisticModeOfImportTranslations;
use App\Repositories\LogisticModeOfImportTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LogisticModeOfImportTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticModeOfImportTranslationsRepository
     */
    protected $logisticModeOfImportTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->logisticModeOfImportTranslationsRepo = \App::make(LogisticModeOfImportTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_logistic_mode_of_import_translations()
    {
        $logisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->make()->toArray();

        $createdLogisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepo->create($logisticModeOfImportTranslations);

        $createdLogisticModeOfImportTranslations = $createdLogisticModeOfImportTranslations->toArray();
        $this->assertArrayHasKey('id', $createdLogisticModeOfImportTranslations);
        $this->assertNotNull($createdLogisticModeOfImportTranslations['id'], 'Created LogisticModeOfImportTranslations must have id specified');
        $this->assertNotNull(LogisticModeOfImportTranslations::find($createdLogisticModeOfImportTranslations['id']), 'LogisticModeOfImportTranslations with given id must be in DB');
        $this->assertModelData($logisticModeOfImportTranslations, $createdLogisticModeOfImportTranslations);
    }

    /**
     * @test read
     */
    public function test_read_logistic_mode_of_import_translations()
    {
        $logisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->create();

        $dbLogisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepo->find($logisticModeOfImportTranslations->id);

        $dbLogisticModeOfImportTranslations = $dbLogisticModeOfImportTranslations->toArray();
        $this->assertModelData($logisticModeOfImportTranslations->toArray(), $dbLogisticModeOfImportTranslations);
    }

    /**
     * @test update
     */
    public function test_update_logistic_mode_of_import_translations()
    {
        $logisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->create();
        $fakeLogisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->make()->toArray();

        $updatedLogisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepo->update($fakeLogisticModeOfImportTranslations, $logisticModeOfImportTranslations->id);

        $this->assertModelData($fakeLogisticModeOfImportTranslations, $updatedLogisticModeOfImportTranslations->toArray());
        $dbLogisticModeOfImportTranslations = $this->logisticModeOfImportTranslationsRepo->find($logisticModeOfImportTranslations->id);
        $this->assertModelData($fakeLogisticModeOfImportTranslations, $dbLogisticModeOfImportTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_logistic_mode_of_import_translations()
    {
        $logisticModeOfImportTranslations = factory(LogisticModeOfImportTranslations::class)->create();

        $resp = $this->logisticModeOfImportTranslationsRepo->delete($logisticModeOfImportTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(LogisticModeOfImportTranslations::find($logisticModeOfImportTranslations->id), 'LogisticModeOfImportTranslations should not exist in DB');
    }
}
