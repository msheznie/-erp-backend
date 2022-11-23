<?php namespace Tests\Repositories;

use App\Models\PosStagMenuMaster;
use App\Repositories\PosStagMenuMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PosStagMenuMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PosStagMenuMasterRepository
     */
    protected $posStagMenuMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->posStagMenuMasterRepo = \App::make(PosStagMenuMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pos_stag_menu_master()
    {
        $posStagMenuMaster = factory(PosStagMenuMaster::class)->make()->toArray();

        $createdPosStagMenuMaster = $this->posStagMenuMasterRepo->create($posStagMenuMaster);

        $createdPosStagMenuMaster = $createdPosStagMenuMaster->toArray();
        $this->assertArrayHasKey('id', $createdPosStagMenuMaster);
        $this->assertNotNull($createdPosStagMenuMaster['id'], 'Created PosStagMenuMaster must have id specified');
        $this->assertNotNull(PosStagMenuMaster::find($createdPosStagMenuMaster['id']), 'PosStagMenuMaster with given id must be in DB');
        $this->assertModelData($posStagMenuMaster, $createdPosStagMenuMaster);
    }

    /**
     * @test read
     */
    public function test_read_pos_stag_menu_master()
    {
        $posStagMenuMaster = factory(PosStagMenuMaster::class)->create();

        $dbPosStagMenuMaster = $this->posStagMenuMasterRepo->find($posStagMenuMaster->id);

        $dbPosStagMenuMaster = $dbPosStagMenuMaster->toArray();
        $this->assertModelData($posStagMenuMaster->toArray(), $dbPosStagMenuMaster);
    }

    /**
     * @test update
     */
    public function test_update_pos_stag_menu_master()
    {
        $posStagMenuMaster = factory(PosStagMenuMaster::class)->create();
        $fakePosStagMenuMaster = factory(PosStagMenuMaster::class)->make()->toArray();

        $updatedPosStagMenuMaster = $this->posStagMenuMasterRepo->update($fakePosStagMenuMaster, $posStagMenuMaster->id);

        $this->assertModelData($fakePosStagMenuMaster, $updatedPosStagMenuMaster->toArray());
        $dbPosStagMenuMaster = $this->posStagMenuMasterRepo->find($posStagMenuMaster->id);
        $this->assertModelData($fakePosStagMenuMaster, $dbPosStagMenuMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pos_stag_menu_master()
    {
        $posStagMenuMaster = factory(PosStagMenuMaster::class)->create();

        $resp = $this->posStagMenuMasterRepo->delete($posStagMenuMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(PosStagMenuMaster::find($posStagMenuMaster->id), 'PosStagMenuMaster should not exist in DB');
    }
}
