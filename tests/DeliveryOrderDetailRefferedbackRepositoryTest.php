<?php namespace Tests\Repositories;

use App\Models\DeliveryOrderDetailRefferedback;
use App\Repositories\DeliveryOrderDetailRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DeliveryOrderDetailRefferedbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DeliveryOrderDetailRefferedbackRepository
     */
    protected $deliveryOrderDetailRefferedbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->deliveryOrderDetailRefferedbackRepo = \App::make(DeliveryOrderDetailRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_delivery_order_detail_refferedback()
    {
        $deliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->make()->toArray();

        $createdDeliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepo->create($deliveryOrderDetailRefferedback);

        $createdDeliveryOrderDetailRefferedback = $createdDeliveryOrderDetailRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdDeliveryOrderDetailRefferedback);
        $this->assertNotNull($createdDeliveryOrderDetailRefferedback['id'], 'Created DeliveryOrderDetailRefferedback must have id specified');
        $this->assertNotNull(DeliveryOrderDetailRefferedback::find($createdDeliveryOrderDetailRefferedback['id']), 'DeliveryOrderDetailRefferedback with given id must be in DB');
        $this->assertModelData($deliveryOrderDetailRefferedback, $createdDeliveryOrderDetailRefferedback);
    }

    /**
     * @test read
     */
    public function test_read_delivery_order_detail_refferedback()
    {
        $deliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->create();

        $dbDeliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepo->find($deliveryOrderDetailRefferedback->id);

        $dbDeliveryOrderDetailRefferedback = $dbDeliveryOrderDetailRefferedback->toArray();
        $this->assertModelData($deliveryOrderDetailRefferedback->toArray(), $dbDeliveryOrderDetailRefferedback);
    }

    /**
     * @test update
     */
    public function test_update_delivery_order_detail_refferedback()
    {
        $deliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->create();
        $fakeDeliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->make()->toArray();

        $updatedDeliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepo->update($fakeDeliveryOrderDetailRefferedback, $deliveryOrderDetailRefferedback->id);

        $this->assertModelData($fakeDeliveryOrderDetailRefferedback, $updatedDeliveryOrderDetailRefferedback->toArray());
        $dbDeliveryOrderDetailRefferedback = $this->deliveryOrderDetailRefferedbackRepo->find($deliveryOrderDetailRefferedback->id);
        $this->assertModelData($fakeDeliveryOrderDetailRefferedback, $dbDeliveryOrderDetailRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_delivery_order_detail_refferedback()
    {
        $deliveryOrderDetailRefferedback = factory(DeliveryOrderDetailRefferedback::class)->create();

        $resp = $this->deliveryOrderDetailRefferedbackRepo->delete($deliveryOrderDetailRefferedback->id);

        $this->assertTrue($resp);
        $this->assertNull(DeliveryOrderDetailRefferedback::find($deliveryOrderDetailRefferedback->id), 'DeliveryOrderDetailRefferedback should not exist in DB');
    }
}
