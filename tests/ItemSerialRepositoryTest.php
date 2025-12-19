<?php namespace Tests\Repositories;

use App\Models\ItemSerial;
use App\Repositories\ItemSerialRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ItemSerialRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ItemSerialRepository
     */
    protected $itemSerialRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->itemSerialRepo = \App::make(ItemSerialRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_item_serial()
    {
        $itemSerial = factory(ItemSerial::class)->make()->toArray();

        $createdItemSerial = $this->itemSerialRepo->create($itemSerial);

        $createdItemSerial = $createdItemSerial->toArray();
        $this->assertArrayHasKey('id', $createdItemSerial);
        $this->assertNotNull($createdItemSerial['id'], 'Created ItemSerial must have id specified');
        $this->assertNotNull(ItemSerial::find($createdItemSerial['id']), 'ItemSerial with given id must be in DB');
        $this->assertModelData($itemSerial, $createdItemSerial);
    }

    /**
     * @test read
     */
    public function test_read_item_serial()
    {
        $itemSerial = factory(ItemSerial::class)->create();

        $dbItemSerial = $this->itemSerialRepo->find($itemSerial->id);

        $dbItemSerial = $dbItemSerial->toArray();
        $this->assertModelData($itemSerial->toArray(), $dbItemSerial);
    }

    /**
     * @test update
     */
    public function test_update_item_serial()
    {
        $itemSerial = factory(ItemSerial::class)->create();
        $fakeItemSerial = factory(ItemSerial::class)->make()->toArray();

        $updatedItemSerial = $this->itemSerialRepo->update($fakeItemSerial, $itemSerial->id);

        $this->assertModelData($fakeItemSerial, $updatedItemSerial->toArray());
        $dbItemSerial = $this->itemSerialRepo->find($itemSerial->id);
        $this->assertModelData($fakeItemSerial, $dbItemSerial->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_item_serial()
    {
        $itemSerial = factory(ItemSerial::class)->create();

        $resp = $this->itemSerialRepo->delete($itemSerial->id);

        $this->assertTrue($resp);
        $this->assertNull(ItemSerial::find($itemSerial->id), 'ItemSerial should not exist in DB');
    }
}
