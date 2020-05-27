<?php namespace Tests\Repositories;

use App\Models\DeliveryOrderDetail;
use App\Repositories\DeliveryOrderDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DeliveryOrderDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DeliveryOrderDetailRepository
     */
    protected $deliveryOrderDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->deliveryOrderDetailRepo = \App::make(DeliveryOrderDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_delivery_order_detail()
    {
        $deliveryOrderDetail = factory(DeliveryOrderDetail::class)->make()->toArray();

        $createdDeliveryOrderDetail = $this->deliveryOrderDetailRepo->create($deliveryOrderDetail);

        $createdDeliveryOrderDetail = $createdDeliveryOrderDetail->toArray();
        $this->assertArrayHasKey('id', $createdDeliveryOrderDetail);
        $this->assertNotNull($createdDeliveryOrderDetail['id'], 'Created DeliveryOrderDetail must have id specified');
        $this->assertNotNull(DeliveryOrderDetail::find($createdDeliveryOrderDetail['id']), 'DeliveryOrderDetail with given id must be in DB');
        $this->assertModelData($deliveryOrderDetail, $createdDeliveryOrderDetail);
    }

    /**
     * @test read
     */
    public function test_read_delivery_order_detail()
    {
        $deliveryOrderDetail = factory(DeliveryOrderDetail::class)->create();

        $dbDeliveryOrderDetail = $this->deliveryOrderDetailRepo->find($deliveryOrderDetail->id);

        $dbDeliveryOrderDetail = $dbDeliveryOrderDetail->toArray();
        $this->assertModelData($deliveryOrderDetail->toArray(), $dbDeliveryOrderDetail);
    }

    /**
     * @test update
     */
    public function test_update_delivery_order_detail()
    {
        $deliveryOrderDetail = factory(DeliveryOrderDetail::class)->create();
        $fakeDeliveryOrderDetail = factory(DeliveryOrderDetail::class)->make()->toArray();

        $updatedDeliveryOrderDetail = $this->deliveryOrderDetailRepo->update($fakeDeliveryOrderDetail, $deliveryOrderDetail->id);

        $this->assertModelData($fakeDeliveryOrderDetail, $updatedDeliveryOrderDetail->toArray());
        $dbDeliveryOrderDetail = $this->deliveryOrderDetailRepo->find($deliveryOrderDetail->id);
        $this->assertModelData($fakeDeliveryOrderDetail, $dbDeliveryOrderDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_delivery_order_detail()
    {
        $deliveryOrderDetail = factory(DeliveryOrderDetail::class)->create();

        $resp = $this->deliveryOrderDetailRepo->delete($deliveryOrderDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(DeliveryOrderDetail::find($deliveryOrderDetail->id), 'DeliveryOrderDetail should not exist in DB');
    }
}
