<?php namespace Tests\Repositories;

use App\Models\MobileMaster;
use App\Repositories\MobileMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MobileMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MobileMasterRepository
     */
    protected $mobileMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->mobileMasterRepo = \App::make(MobileMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mobile_master()
    {
        $mobileMaster = factory(MobileMaster::class)->make()->toArray();

        $createdMobileMaster = $this->mobileMasterRepo->create($mobileMaster);

        $createdMobileMaster = $createdMobileMaster->toArray();
        $this->assertArrayHasKey('id', $createdMobileMaster);
        $this->assertNotNull($createdMobileMaster['id'], 'Created MobileMaster must have id specified');
        $this->assertNotNull(MobileMaster::find($createdMobileMaster['id']), 'MobileMaster with given id must be in DB');
        $this->assertModelData($mobileMaster, $createdMobileMaster);
    }

    /**
     * @test read
     */
    public function test_read_mobile_master()
    {
        $mobileMaster = factory(MobileMaster::class)->create();

        $dbMobileMaster = $this->mobileMasterRepo->find($mobileMaster->id);

        $dbMobileMaster = $dbMobileMaster->toArray();
        $this->assertModelData($mobileMaster->toArray(), $dbMobileMaster);
    }

    /**
     * @test update
     */
    public function test_update_mobile_master()
    {
        $mobileMaster = factory(MobileMaster::class)->create();
        $fakeMobileMaster = factory(MobileMaster::class)->make()->toArray();

        $updatedMobileMaster = $this->mobileMasterRepo->update($fakeMobileMaster, $mobileMaster->id);

        $this->assertModelData($fakeMobileMaster, $updatedMobileMaster->toArray());
        $dbMobileMaster = $this->mobileMasterRepo->find($mobileMaster->id);
        $this->assertModelData($fakeMobileMaster, $dbMobileMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mobile_master()
    {
        $mobileMaster = factory(MobileMaster::class)->create();

        $resp = $this->mobileMasterRepo->delete($mobileMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(MobileMaster::find($mobileMaster->id), 'MobileMaster should not exist in DB');
    }
}
