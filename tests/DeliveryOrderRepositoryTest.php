<?php namespace Tests\Repositories;

use App\Models\DeliveryOrder;
use App\Repositories\DeliveryOrderRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DeliveryOrderRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DeliveryOrderRepository
     */
    protected $deliveryOrderRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->deliveryOrderRepo = \App::make(DeliveryOrderRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_delivery_order()
    {
        $deliveryOrder = factory(DeliveryOrder::class)->make()->toArray();

        $createdDeliveryOrder = $this->deliveryOrderRepo->create($deliveryOrder);

        $createdDeliveryOrder = $createdDeliveryOrder->toArray();
        $this->assertArrayHasKey('id', $createdDeliveryOrder);
        $this->assertNotNull($createdDeliveryOrder['id'], 'Created DeliveryOrder must have id specified');
        $this->assertNotNull(DeliveryOrder::find($createdDeliveryOrder['id']), 'DeliveryOrder with given id must be in DB');
        $this->assertModelData($deliveryOrder, $createdDeliveryOrder);
    }

    /**
     * @test read
     */
    public function test_read_delivery_order()
    {
        $deliveryOrder = factory(DeliveryOrder::class)->create();

        $dbDeliveryOrder = $this->deliveryOrderRepo->find($deliveryOrder->id);

        $dbDeliveryOrder = $dbDeliveryOrder->toArray();
        $this->assertModelData($deliveryOrder->toArray(), $dbDeliveryOrder);
    }

    /**
     * @test update
     */
    public function test_update_delivery_order()
    {
        $deliveryOrder = factory(DeliveryOrder::class)->create();
        $fakeDeliveryOrder = factory(DeliveryOrder::class)->make()->toArray();

        $updatedDeliveryOrder = $this->deliveryOrderRepo->update($fakeDeliveryOrder, $deliveryOrder->id);

        $this->assertModelData($fakeDeliveryOrder, $updatedDeliveryOrder->toArray());
        $dbDeliveryOrder = $this->deliveryOrderRepo->find($deliveryOrder->id);
        $this->assertModelData($fakeDeliveryOrder, $dbDeliveryOrder->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_delivery_order()
    {
        $deliveryOrder = factory(DeliveryOrder::class)->create();

        $resp = $this->deliveryOrderRepo->delete($deliveryOrder->id);

        $this->assertTrue($resp);
        $this->assertNull(DeliveryOrder::find($deliveryOrder->id), 'DeliveryOrder should not exist in DB');
    }
}
