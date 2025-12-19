<?php namespace Tests\Repositories;

use App\Models\DeliveryTermsMaster;
use App\Repositories\DeliveryTermsMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DeliveryTermsMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DeliveryTermsMasterRepository
     */
    protected $deliveryTermsMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->deliveryTermsMasterRepo = \App::make(DeliveryTermsMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_delivery_terms_master()
    {
        $deliveryTermsMaster = factory(DeliveryTermsMaster::class)->make()->toArray();

        $createdDeliveryTermsMaster = $this->deliveryTermsMasterRepo->create($deliveryTermsMaster);

        $createdDeliveryTermsMaster = $createdDeliveryTermsMaster->toArray();
        $this->assertArrayHasKey('id', $createdDeliveryTermsMaster);
        $this->assertNotNull($createdDeliveryTermsMaster['id'], 'Created DeliveryTermsMaster must have id specified');
        $this->assertNotNull(DeliveryTermsMaster::find($createdDeliveryTermsMaster['id']), 'DeliveryTermsMaster with given id must be in DB');
        $this->assertModelData($deliveryTermsMaster, $createdDeliveryTermsMaster);
    }

    /**
     * @test read
     */
    public function test_read_delivery_terms_master()
    {
        $deliveryTermsMaster = factory(DeliveryTermsMaster::class)->create();

        $dbDeliveryTermsMaster = $this->deliveryTermsMasterRepo->find($deliveryTermsMaster->id);

        $dbDeliveryTermsMaster = $dbDeliveryTermsMaster->toArray();
        $this->assertModelData($deliveryTermsMaster->toArray(), $dbDeliveryTermsMaster);
    }

    /**
     * @test update
     */
    public function test_update_delivery_terms_master()
    {
        $deliveryTermsMaster = factory(DeliveryTermsMaster::class)->create();
        $fakeDeliveryTermsMaster = factory(DeliveryTermsMaster::class)->make()->toArray();

        $updatedDeliveryTermsMaster = $this->deliveryTermsMasterRepo->update($fakeDeliveryTermsMaster, $deliveryTermsMaster->id);

        $this->assertModelData($fakeDeliveryTermsMaster, $updatedDeliveryTermsMaster->toArray());
        $dbDeliveryTermsMaster = $this->deliveryTermsMasterRepo->find($deliveryTermsMaster->id);
        $this->assertModelData($fakeDeliveryTermsMaster, $dbDeliveryTermsMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_delivery_terms_master()
    {
        $deliveryTermsMaster = factory(DeliveryTermsMaster::class)->create();

        $resp = $this->deliveryTermsMasterRepo->delete($deliveryTermsMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(DeliveryTermsMaster::find($deliveryTermsMaster->id), 'DeliveryTermsMaster should not exist in DB');
    }
}
