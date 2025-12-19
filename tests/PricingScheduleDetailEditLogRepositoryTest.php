<?php namespace Tests\Repositories;

use App\Models\PricingScheduleDetailEditLog;
use App\Repositories\PricingScheduleDetailEditLogRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PricingScheduleDetailEditLogRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PricingScheduleDetailEditLogRepository
     */
    protected $pricingScheduleDetailEditLogRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pricingScheduleDetailEditLogRepo = \App::make(PricingScheduleDetailEditLogRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pricing_schedule_detail_edit_log()
    {
        $pricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->make()->toArray();

        $createdPricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepo->create($pricingScheduleDetailEditLog);

        $createdPricingScheduleDetailEditLog = $createdPricingScheduleDetailEditLog->toArray();
        $this->assertArrayHasKey('id', $createdPricingScheduleDetailEditLog);
        $this->assertNotNull($createdPricingScheduleDetailEditLog['id'], 'Created PricingScheduleDetailEditLog must have id specified');
        $this->assertNotNull(PricingScheduleDetailEditLog::find($createdPricingScheduleDetailEditLog['id']), 'PricingScheduleDetailEditLog with given id must be in DB');
        $this->assertModelData($pricingScheduleDetailEditLog, $createdPricingScheduleDetailEditLog);
    }

    /**
     * @test read
     */
    public function test_read_pricing_schedule_detail_edit_log()
    {
        $pricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->create();

        $dbPricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepo->find($pricingScheduleDetailEditLog->id);

        $dbPricingScheduleDetailEditLog = $dbPricingScheduleDetailEditLog->toArray();
        $this->assertModelData($pricingScheduleDetailEditLog->toArray(), $dbPricingScheduleDetailEditLog);
    }

    /**
     * @test update
     */
    public function test_update_pricing_schedule_detail_edit_log()
    {
        $pricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->create();
        $fakePricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->make()->toArray();

        $updatedPricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepo->update($fakePricingScheduleDetailEditLog, $pricingScheduleDetailEditLog->id);

        $this->assertModelData($fakePricingScheduleDetailEditLog, $updatedPricingScheduleDetailEditLog->toArray());
        $dbPricingScheduleDetailEditLog = $this->pricingScheduleDetailEditLogRepo->find($pricingScheduleDetailEditLog->id);
        $this->assertModelData($fakePricingScheduleDetailEditLog, $dbPricingScheduleDetailEditLog->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pricing_schedule_detail_edit_log()
    {
        $pricingScheduleDetailEditLog = factory(PricingScheduleDetailEditLog::class)->create();

        $resp = $this->pricingScheduleDetailEditLogRepo->delete($pricingScheduleDetailEditLog->id);

        $this->assertTrue($resp);
        $this->assertNull(PricingScheduleDetailEditLog::find($pricingScheduleDetailEditLog->id), 'PricingScheduleDetailEditLog should not exist in DB');
    }
}
