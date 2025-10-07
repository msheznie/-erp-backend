<?php namespace Tests\Repositories;

use App\Models\LogisticShippingModeTranslations;
use App\Repositories\LogisticShippingModeTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class LogisticShippingModeTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var LogisticShippingModeTranslationsRepository
     */
    protected $logisticShippingModeTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->logisticShippingModeTranslationsRepo = \App::make(LogisticShippingModeTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_logistic_shipping_mode_translations()
    {
        $logisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->make()->toArray();

        $createdLogisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepo->create($logisticShippingModeTranslations);

        $createdLogisticShippingModeTranslations = $createdLogisticShippingModeTranslations->toArray();
        $this->assertArrayHasKey('id', $createdLogisticShippingModeTranslations);
        $this->assertNotNull($createdLogisticShippingModeTranslations['id'], 'Created LogisticShippingModeTranslations must have id specified');
        $this->assertNotNull(LogisticShippingModeTranslations::find($createdLogisticShippingModeTranslations['id']), 'LogisticShippingModeTranslations with given id must be in DB');
        $this->assertModelData($logisticShippingModeTranslations, $createdLogisticShippingModeTranslations);
    }

    /**
     * @test read
     */
    public function test_read_logistic_shipping_mode_translations()
    {
        $logisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->create();

        $dbLogisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepo->find($logisticShippingModeTranslations->id);

        $dbLogisticShippingModeTranslations = $dbLogisticShippingModeTranslations->toArray();
        $this->assertModelData($logisticShippingModeTranslations->toArray(), $dbLogisticShippingModeTranslations);
    }

    /**
     * @test update
     */
    public function test_update_logistic_shipping_mode_translations()
    {
        $logisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->create();
        $fakeLogisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->make()->toArray();

        $updatedLogisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepo->update($fakeLogisticShippingModeTranslations, $logisticShippingModeTranslations->id);

        $this->assertModelData($fakeLogisticShippingModeTranslations, $updatedLogisticShippingModeTranslations->toArray());
        $dbLogisticShippingModeTranslations = $this->logisticShippingModeTranslationsRepo->find($logisticShippingModeTranslations->id);
        $this->assertModelData($fakeLogisticShippingModeTranslations, $dbLogisticShippingModeTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_logistic_shipping_mode_translations()
    {
        $logisticShippingModeTranslations = factory(LogisticShippingModeTranslations::class)->create();

        $resp = $this->logisticShippingModeTranslationsRepo->delete($logisticShippingModeTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(LogisticShippingModeTranslations::find($logisticShippingModeTranslations->id), 'LogisticShippingModeTranslations should not exist in DB');
    }
}
