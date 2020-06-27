<?php namespace Tests\Repositories;

use App\Models\DeliveryOrderRefferedback;
use App\Repositories\DeliveryOrderRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DeliveryOrderRefferedbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DeliveryOrderRefferedbackRepository
     */
    protected $deliveryOrderRefferedbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->deliveryOrderRefferedbackRepo = \App::make(DeliveryOrderRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_delivery_order_refferedback()
    {
        $deliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->make()->toArray();

        $createdDeliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepo->create($deliveryOrderRefferedback);

        $createdDeliveryOrderRefferedback = $createdDeliveryOrderRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdDeliveryOrderRefferedback);
        $this->assertNotNull($createdDeliveryOrderRefferedback['id'], 'Created DeliveryOrderRefferedback must have id specified');
        $this->assertNotNull(DeliveryOrderRefferedback::find($createdDeliveryOrderRefferedback['id']), 'DeliveryOrderRefferedback with given id must be in DB');
        $this->assertModelData($deliveryOrderRefferedback, $createdDeliveryOrderRefferedback);
    }

    /**
     * @test read
     */
    public function test_read_delivery_order_refferedback()
    {
        $deliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->create();

        $dbDeliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepo->find($deliveryOrderRefferedback->id);

        $dbDeliveryOrderRefferedback = $dbDeliveryOrderRefferedback->toArray();
        $this->assertModelData($deliveryOrderRefferedback->toArray(), $dbDeliveryOrderRefferedback);
    }

    /**
     * @test update
     */
    public function test_update_delivery_order_refferedback()
    {
        $deliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->create();
        $fakeDeliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->make()->toArray();

        $updatedDeliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepo->update($fakeDeliveryOrderRefferedback, $deliveryOrderRefferedback->id);

        $this->assertModelData($fakeDeliveryOrderRefferedback, $updatedDeliveryOrderRefferedback->toArray());
        $dbDeliveryOrderRefferedback = $this->deliveryOrderRefferedbackRepo->find($deliveryOrderRefferedback->id);
        $this->assertModelData($fakeDeliveryOrderRefferedback, $dbDeliveryOrderRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_delivery_order_refferedback()
    {
        $deliveryOrderRefferedback = factory(DeliveryOrderRefferedback::class)->create();

        $resp = $this->deliveryOrderRefferedbackRepo->delete($deliveryOrderRefferedback->id);

        $this->assertTrue($resp);
        $this->assertNull(DeliveryOrderRefferedback::find($deliveryOrderRefferedback->id), 'DeliveryOrderRefferedback should not exist in DB');
    }
}
