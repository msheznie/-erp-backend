<?php namespace Tests\Repositories;

use App\Models\CalenderMaster;
use App\Repositories\CalenderMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeCalenderMasterTrait;
use Tests\ApiTestTrait;

class CalenderMasterRepositoryTest extends TestCase
{
    use MakeCalenderMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var CalenderMasterRepository
     */
    protected $calenderMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->calenderMasterRepo = \App::make(CalenderMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_calender_master()
    {
        $calenderMaster = $this->fakeCalenderMasterData();
        $createdCalenderMaster = $this->calenderMasterRepo->create($calenderMaster);
        $createdCalenderMaster = $createdCalenderMaster->toArray();
        $this->assertArrayHasKey('id', $createdCalenderMaster);
        $this->assertNotNull($createdCalenderMaster['id'], 'Created CalenderMaster must have id specified');
        $this->assertNotNull(CalenderMaster::find($createdCalenderMaster['id']), 'CalenderMaster with given id must be in DB');
        $this->assertModelData($calenderMaster, $createdCalenderMaster);
    }

    /**
     * @test read
     */
    public function test_read_calender_master()
    {
        $calenderMaster = $this->makeCalenderMaster();
        $dbCalenderMaster = $this->calenderMasterRepo->find($calenderMaster->id);
        $dbCalenderMaster = $dbCalenderMaster->toArray();
        $this->assertModelData($calenderMaster->toArray(), $dbCalenderMaster);
    }

    /**
     * @test update
     */
    public function test_update_calender_master()
    {
        $calenderMaster = $this->makeCalenderMaster();
        $fakeCalenderMaster = $this->fakeCalenderMasterData();
        $updatedCalenderMaster = $this->calenderMasterRepo->update($fakeCalenderMaster, $calenderMaster->id);
        $this->assertModelData($fakeCalenderMaster, $updatedCalenderMaster->toArray());
        $dbCalenderMaster = $this->calenderMasterRepo->find($calenderMaster->id);
        $this->assertModelData($fakeCalenderMaster, $dbCalenderMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_calender_master()
    {
        $calenderMaster = $this->makeCalenderMaster();
        $resp = $this->calenderMasterRepo->delete($calenderMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(CalenderMaster::find($calenderMaster->id), 'CalenderMaster should not exist in DB');
    }
}
