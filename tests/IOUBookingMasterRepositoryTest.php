<?php namespace Tests\Repositories;

use App\Models\IOUBookingMaster;
use App\Repositories\IOUBookingMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class IOUBookingMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var IOUBookingMasterRepository
     */
    protected $iOUBookingMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->iOUBookingMasterRepo = \App::make(IOUBookingMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_i_o_u_booking_master()
    {
        $iOUBookingMaster = factory(IOUBookingMaster::class)->make()->toArray();

        $createdIOUBookingMaster = $this->iOUBookingMasterRepo->create($iOUBookingMaster);

        $createdIOUBookingMaster = $createdIOUBookingMaster->toArray();
        $this->assertArrayHasKey('id', $createdIOUBookingMaster);
        $this->assertNotNull($createdIOUBookingMaster['id'], 'Created IOUBookingMaster must have id specified');
        $this->assertNotNull(IOUBookingMaster::find($createdIOUBookingMaster['id']), 'IOUBookingMaster with given id must be in DB');
        $this->assertModelData($iOUBookingMaster, $createdIOUBookingMaster);
    }

    /**
     * @test read
     */
    public function test_read_i_o_u_booking_master()
    {
        $iOUBookingMaster = factory(IOUBookingMaster::class)->create();

        $dbIOUBookingMaster = $this->iOUBookingMasterRepo->find($iOUBookingMaster->id);

        $dbIOUBookingMaster = $dbIOUBookingMaster->toArray();
        $this->assertModelData($iOUBookingMaster->toArray(), $dbIOUBookingMaster);
    }

    /**
     * @test update
     */
    public function test_update_i_o_u_booking_master()
    {
        $iOUBookingMaster = factory(IOUBookingMaster::class)->create();
        $fakeIOUBookingMaster = factory(IOUBookingMaster::class)->make()->toArray();

        $updatedIOUBookingMaster = $this->iOUBookingMasterRepo->update($fakeIOUBookingMaster, $iOUBookingMaster->id);

        $this->assertModelData($fakeIOUBookingMaster, $updatedIOUBookingMaster->toArray());
        $dbIOUBookingMaster = $this->iOUBookingMasterRepo->find($iOUBookingMaster->id);
        $this->assertModelData($fakeIOUBookingMaster, $dbIOUBookingMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_i_o_u_booking_master()
    {
        $iOUBookingMaster = factory(IOUBookingMaster::class)->create();

        $resp = $this->iOUBookingMasterRepo->delete($iOUBookingMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(IOUBookingMaster::find($iOUBookingMaster->id), 'IOUBookingMaster should not exist in DB');
    }
}
