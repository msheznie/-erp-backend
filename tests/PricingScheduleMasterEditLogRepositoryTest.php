<?php namespace Tests\Repositories;

use App\Models\PricingScheduleMasterEditLog;
use App\Repositories\PricingScheduleMasterEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PricingScheduleMasterEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PricingScheduleMasterEditLogRepository
     */
    protected $pricingScheduleMasterEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pricingScheduleMasterEditLogRepo = \App::make(PricingScheduleMasterEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pricing_schedule_master_edit_log()
    {
        $pricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->make()->toArray();

        $createdPricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepo->create($pricingScheduleMasterEditLog);

        $createdPricingScheduleMasterEditLog = $createdPricingScheduleMasterEditLog->toArray();
        $this->assertArrayHasKey('id', $createdPricingScheduleMasterEditLog);
        $this->assertNotNull($createdPricingScheduleMasterEditLog['id'], 'Created PricingScheduleMasterEditLog must have id specified');
        $this->assertNotNull(PricingScheduleMasterEditLog::find($createdPricingScheduleMasterEditLog['id']), 'PricingScheduleMasterEditLog with given id must be in DB');
        $this->assertModelData($pricingScheduleMasterEditLog, $createdPricingScheduleMasterEditLog);
    }

    /**
     * @test read
     */
    public function test_read_pricing_schedule_master_edit_log()
    {
        $pricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->create();

        $dbPricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepo->find($pricingScheduleMasterEditLog->id);

        $dbPricingScheduleMasterEditLog = $dbPricingScheduleMasterEditLog->toArray();
        $this->assertModelData($pricingScheduleMasterEditLog->toArray(), $dbPricingScheduleMasterEditLog);
    }

    /**
     * @test update
     */
    public function test_update_pricing_schedule_master_edit_log()
    {
        $pricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->create();
        $fakePricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->make()->toArray();

        $updatedPricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepo->update($fakePricingScheduleMasterEditLog, $pricingScheduleMasterEditLog->id);

        $this->assertModelData($fakePricingScheduleMasterEditLog, $updatedPricingScheduleMasterEditLog->toArray());
        $dbPricingScheduleMasterEditLog = $this->pricingScheduleMasterEditLogRepo->find($pricingScheduleMasterEditLog->id);
        $this->assertModelData($fakePricingScheduleMasterEditLog, $dbPricingScheduleMasterEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pricing_schedule_master_edit_log()
    {
        $pricingScheduleMasterEditLog = factory(PricingScheduleMasterEditLog::class)->create();

        $resp = $this->pricingScheduleMasterEditLogRepo->delete($pricingScheduleMasterEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(PricingScheduleMasterEditLog::find($pricingScheduleMasterEditLog->id), 'PricingScheduleMasterEditLog should not exist in DB');
    }
}
