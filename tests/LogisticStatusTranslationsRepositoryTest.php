<?php namespace Tests\Repositories;

use App\Models\LogisticStatusTranslations;
use App\Repositories\LogisticStatusTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LogisticStatusTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticStatusTranslationsRepository
     */
    protected $logisticStatusTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->logisticStatusTranslationsRepo = \App::make(LogisticStatusTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_logistic_status_translations()
    {
        $logisticStatusTranslations = factory(LogisticStatusTranslations::class)->make()->toArray();

        $createdLogisticStatusTranslations = $this->logisticStatusTranslationsRepo->create($logisticStatusTranslations);

        $createdLogisticStatusTranslations = $createdLogisticStatusTranslations->toArray();
        $this->assertArrayHasKey('id', $createdLogisticStatusTranslations);
        $this->assertNotNull($createdLogisticStatusTranslations['id'], 'Created LogisticStatusTranslations must have id specified');
        $this->assertNotNull(LogisticStatusTranslations::find($createdLogisticStatusTranslations['id']), 'LogisticStatusTranslations with given id must be in DB');
        $this->assertModelData($logisticStatusTranslations, $createdLogisticStatusTranslations);
    }

    /**
     * @test read
     */
    public function test_read_logistic_status_translations()
    {
        $logisticStatusTranslations = factory(LogisticStatusTranslations::class)->create();

        $dbLogisticStatusTranslations = $this->logisticStatusTranslationsRepo->find($logisticStatusTranslations->id);

        $dbLogisticStatusTranslations = $dbLogisticStatusTranslations->toArray();
        $this->assertModelData($logisticStatusTranslations->toArray(), $dbLogisticStatusTranslations);
    }

    /**
     * @test update
     */
    public function test_update_logistic_status_translations()
    {
        $logisticStatusTranslations = factory(LogisticStatusTranslations::class)->create();
        $fakeLogisticStatusTranslations = factory(LogisticStatusTranslations::class)->make()->toArray();

        $updatedLogisticStatusTranslations = $this->logisticStatusTranslationsRepo->update($fakeLogisticStatusTranslations, $logisticStatusTranslations->id);

        $this->assertModelData($fakeLogisticStatusTranslations, $updatedLogisticStatusTranslations->toArray());
        $dbLogisticStatusTranslations = $this->logisticStatusTranslationsRepo->find($logisticStatusTranslations->id);
        $this->assertModelData($fakeLogisticStatusTranslations, $dbLogisticStatusTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_logistic_status_translations()
    {
        $logisticStatusTranslations = factory(LogisticStatusTranslations::class)->create();

        $resp = $this->logisticStatusTranslationsRepo->delete($logisticStatusTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(LogisticStatusTranslations::find($logisticStatusTranslations->id), 'LogisticStatusTranslations should not exist in DB');
    }
}
