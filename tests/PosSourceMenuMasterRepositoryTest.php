<?php namespace Tests\Repositories;

use App\Models\PosSourceMenuMaster;
use App\Repositories\PosSourceMenuMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PosSourceMenuMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PosSourceMenuMasterRepository
     */
    protected $posSourceMenuMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->posSourceMenuMasterRepo = \App::make(PosSourceMenuMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pos_source_menu_master()
    {
        $posSourceMenuMaster = factory(PosSourceMenuMaster::class)->make()->toArray();

        $createdPosSourceMenuMaster = $this->posSourceMenuMasterRepo->create($posSourceMenuMaster);

        $createdPosSourceMenuMaster = $createdPosSourceMenuMaster->toArray();
        $this->assertArrayHasKey('id', $createdPosSourceMenuMaster);
        $this->assertNotNull($createdPosSourceMenuMaster['id'], 'Created PosSourceMenuMaster must have id specified');
        $this->assertNotNull(PosSourceMenuMaster::find($createdPosSourceMenuMaster['id']), 'PosSourceMenuMaster with given id must be in DB');
        $this->assertModelData($posSourceMenuMaster, $createdPosSourceMenuMaster);
    }

    /**
     * @test read
     */
    public function test_read_pos_source_menu_master()
    {
        $posSourceMenuMaster = factory(PosSourceMenuMaster::class)->create();

        $dbPosSourceMenuMaster = $this->posSourceMenuMasterRepo->find($posSourceMenuMaster->id);

        $dbPosSourceMenuMaster = $dbPosSourceMenuMaster->toArray();
        $this->assertModelData($posSourceMenuMaster->toArray(), $dbPosSourceMenuMaster);
    }

    /**
     * @test update
     */
    public function test_update_pos_source_menu_master()
    {
        $posSourceMenuMaster = factory(PosSourceMenuMaster::class)->create();
        $fakePosSourceMenuMaster = factory(PosSourceMenuMaster::class)->make()->toArray();

        $updatedPosSourceMenuMaster = $this->posSourceMenuMasterRepo->update($fakePosSourceMenuMaster, $posSourceMenuMaster->id);

        $this->assertModelData($fakePosSourceMenuMaster, $updatedPosSourceMenuMaster->toArray());
        $dbPosSourceMenuMaster = $this->posSourceMenuMasterRepo->find($posSourceMenuMaster->id);
        $this->assertModelData($fakePosSourceMenuMaster, $dbPosSourceMenuMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pos_source_menu_master()
    {
        $posSourceMenuMaster = factory(PosSourceMenuMaster::class)->create();

        $resp = $this->posSourceMenuMasterRepo->delete($posSourceMenuMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(PosSourceMenuMaster::find($posSourceMenuMaster->id), 'PosSourceMenuMaster should not exist in DB');
    }
}
