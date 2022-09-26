<?php namespace Tests\Repositories;

use App\Models\PricingScheduleDetail;
use App\Repositories\PricingScheduleDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PricingScheduleDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PricingScheduleDetailRepository
     */
    protected $pricingScheduleDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pricingScheduleDetailRepo = \App::make(PricingScheduleDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pricing_schedule_detail()
    {
        $pricingScheduleDetail = factory(PricingScheduleDetail::class)->make()->toArray();

        $createdPricingScheduleDetail = $this->pricingScheduleDetailRepo->create($pricingScheduleDetail);

        $createdPricingScheduleDetail = $createdPricingScheduleDetail->toArray();
        $this->assertArrayHasKey('id', $createdPricingScheduleDetail);
        $this->assertNotNull($createdPricingScheduleDetail['id'], 'Created PricingScheduleDetail must have id specified');
        $this->assertNotNull(PricingScheduleDetail::find($createdPricingScheduleDetail['id']), 'PricingScheduleDetail with given id must be in DB');
        $this->assertModelData($pricingScheduleDetail, $createdPricingScheduleDetail);
    }

    /**
     * @test read
     */
    public function test_read_pricing_schedule_detail()
    {
        $pricingScheduleDetail = factory(PricingScheduleDetail::class)->create();

        $dbPricingScheduleDetail = $this->pricingScheduleDetailRepo->find($pricingScheduleDetail->id);

        $dbPricingScheduleDetail = $dbPricingScheduleDetail->toArray();
        $this->assertModelData($pricingScheduleDetail->toArray(), $dbPricingScheduleDetail);
    }

    /**
     * @test update
     */
    public function test_update_pricing_schedule_detail()
    {
        $pricingScheduleDetail = factory(PricingScheduleDetail::class)->create();
        $fakePricingScheduleDetail = factory(PricingScheduleDetail::class)->make()->toArray();

        $updatedPricingScheduleDetail = $this->pricingScheduleDetailRepo->update($fakePricingScheduleDetail, $pricingScheduleDetail->id);

        $this->assertModelData($fakePricingScheduleDetail, $updatedPricingScheduleDetail->toArray());
        $dbPricingScheduleDetail = $this->pricingScheduleDetailRepo->find($pricingScheduleDetail->id);
        $this->assertModelData($fakePricingScheduleDetail, $dbPricingScheduleDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pricing_schedule_detail()
    {
        $pricingScheduleDetail = factory(PricingScheduleDetail::class)->create();

        $resp = $this->pricingScheduleDetailRepo->delete($pricingScheduleDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(PricingScheduleDetail::find($pricingScheduleDetail->id), 'PricingScheduleDetail should not exist in DB');
    }
}
