<?php namespace Tests\Repositories;

use App\Models\PricingScheduleMaster;
use App\Repositories\PricingScheduleMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PricingScheduleMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PricingScheduleMasterRepository
     */
    protected $pricingScheduleMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pricingScheduleMasterRepo = \App::make(PricingScheduleMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pricing_schedule_master()
    {
        $pricingScheduleMaster = factory(PricingScheduleMaster::class)->make()->toArray();

        $createdPricingScheduleMaster = $this->pricingScheduleMasterRepo->create($pricingScheduleMaster);

        $createdPricingScheduleMaster = $createdPricingScheduleMaster->toArray();
        $this->assertArrayHasKey('id', $createdPricingScheduleMaster);
        $this->assertNotNull($createdPricingScheduleMaster['id'], 'Created PricingScheduleMaster must have id specified');
        $this->assertNotNull(PricingScheduleMaster::find($createdPricingScheduleMaster['id']), 'PricingScheduleMaster with given id must be in DB');
        $this->assertModelData($pricingScheduleMaster, $createdPricingScheduleMaster);
    }

    /**
     * @test read
     */
    public function test_read_pricing_schedule_master()
    {
        $pricingScheduleMaster = factory(PricingScheduleMaster::class)->create();

        $dbPricingScheduleMaster = $this->pricingScheduleMasterRepo->find($pricingScheduleMaster->id);

        $dbPricingScheduleMaster = $dbPricingScheduleMaster->toArray();
        $this->assertModelData($pricingScheduleMaster->toArray(), $dbPricingScheduleMaster);
    }

    /**
     * @test update
     */
    public function test_update_pricing_schedule_master()
    {
        $pricingScheduleMaster = factory(PricingScheduleMaster::class)->create();
        $fakePricingScheduleMaster = factory(PricingScheduleMaster::class)->make()->toArray();

        $updatedPricingScheduleMaster = $this->pricingScheduleMasterRepo->update($fakePricingScheduleMaster, $pricingScheduleMaster->id);

        $this->assertModelData($fakePricingScheduleMaster, $updatedPricingScheduleMaster->toArray());
        $dbPricingScheduleMaster = $this->pricingScheduleMasterRepo->find($pricingScheduleMaster->id);
        $this->assertModelData($fakePricingScheduleMaster, $dbPricingScheduleMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pricing_schedule_master()
    {
        $pricingScheduleMaster = factory(PricingScheduleMaster::class)->create();

        $resp = $this->pricingScheduleMasterRepo->delete($pricingScheduleMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(PricingScheduleMaster::find($pricingScheduleMaster->id), 'PricingScheduleMaster should not exist in DB');
    }
}
