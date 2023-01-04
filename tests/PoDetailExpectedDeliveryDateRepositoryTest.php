<?php namespace Tests\Repositories;

use App\Models\PoDetailExpectedDeliveryDate;
use App\Repositories\PoDetailExpectedDeliveryDateRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PoDetailExpectedDeliveryDateRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoDetailExpectedDeliveryDateRepository
     */
    protected $poDetailExpectedDeliveryDateRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->poDetailExpectedDeliveryDateRepo = \App::make(PoDetailExpectedDeliveryDateRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_po_detail_expected_delivery_date()
    {
        $poDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->make()->toArray();

        $createdPoDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepo->create($poDetailExpectedDeliveryDate);

        $createdPoDetailExpectedDeliveryDate = $createdPoDetailExpectedDeliveryDate->toArray();
        $this->assertArrayHasKey('id', $createdPoDetailExpectedDeliveryDate);
        $this->assertNotNull($createdPoDetailExpectedDeliveryDate['id'], 'Created PoDetailExpectedDeliveryDate must have id specified');
        $this->assertNotNull(PoDetailExpectedDeliveryDate::find($createdPoDetailExpectedDeliveryDate['id']), 'PoDetailExpectedDeliveryDate with given id must be in DB');
        $this->assertModelData($poDetailExpectedDeliveryDate, $createdPoDetailExpectedDeliveryDate);
    }

    /**
     * @test read
     */
    public function test_read_po_detail_expected_delivery_date()
    {
        $poDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->create();

        $dbPoDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepo->find($poDetailExpectedDeliveryDate->id);

        $dbPoDetailExpectedDeliveryDate = $dbPoDetailExpectedDeliveryDate->toArray();
        $this->assertModelData($poDetailExpectedDeliveryDate->toArray(), $dbPoDetailExpectedDeliveryDate);
    }

    /**
     * @test update
     */
    public function test_update_po_detail_expected_delivery_date()
    {
        $poDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->create();
        $fakePoDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->make()->toArray();

        $updatedPoDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepo->update($fakePoDetailExpectedDeliveryDate, $poDetailExpectedDeliveryDate->id);

        $this->assertModelData($fakePoDetailExpectedDeliveryDate, $updatedPoDetailExpectedDeliveryDate->toArray());
        $dbPoDetailExpectedDeliveryDate = $this->poDetailExpectedDeliveryDateRepo->find($poDetailExpectedDeliveryDate->id);
        $this->assertModelData($fakePoDetailExpectedDeliveryDate, $dbPoDetailExpectedDeliveryDate->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_po_detail_expected_delivery_date()
    {
        $poDetailExpectedDeliveryDate = factory(PoDetailExpectedDeliveryDate::class)->create();

        $resp = $this->poDetailExpectedDeliveryDateRepo->delete($poDetailExpectedDeliveryDate->id);

        $this->assertTrue($resp);
        $this->assertNull(PoDetailExpectedDeliveryDate::find($poDetailExpectedDeliveryDate->id), 'PoDetailExpectedDeliveryDate should not exist in DB');
    }
}
