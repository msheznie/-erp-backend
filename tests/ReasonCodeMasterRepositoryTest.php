<?php namespace Tests\Repositories;

use App\Models\ReasonCodeMaster;
use App\Repositories\ReasonCodeMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ReasonCodeMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ReasonCodeMasterRepository
     */
    protected $reasonCodeMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->reasonCodeMasterRepo = \App::make(ReasonCodeMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_reason_code_master()
    {
        $reasonCodeMaster = factory(ReasonCodeMaster::class)->make()->toArray();

        $createdReasonCodeMaster = $this->reasonCodeMasterRepo->create($reasonCodeMaster);

        $createdReasonCodeMaster = $createdReasonCodeMaster->toArray();
        $this->assertArrayHasKey('id', $createdReasonCodeMaster);
        $this->assertNotNull($createdReasonCodeMaster['id'], 'Created ReasonCodeMaster must have id specified');
        $this->assertNotNull(ReasonCodeMaster::find($createdReasonCodeMaster['id']), 'ReasonCodeMaster with given id must be in DB');
        $this->assertModelData($reasonCodeMaster, $createdReasonCodeMaster);
    }

    /**
     * @test read
     */
    public function test_read_reason_code_master()
    {
        $reasonCodeMaster = factory(ReasonCodeMaster::class)->create();

        $dbReasonCodeMaster = $this->reasonCodeMasterRepo->find($reasonCodeMaster->id);

        $dbReasonCodeMaster = $dbReasonCodeMaster->toArray();
        $this->assertModelData($reasonCodeMaster->toArray(), $dbReasonCodeMaster);
    }

    /**
     * @test update
     */
    public function test_update_reason_code_master()
    {
        $reasonCodeMaster = factory(ReasonCodeMaster::class)->create();
        $fakeReasonCodeMaster = factory(ReasonCodeMaster::class)->make()->toArray();

        $updatedReasonCodeMaster = $this->reasonCodeMasterRepo->update($fakeReasonCodeMaster, $reasonCodeMaster->id);

        $this->assertModelData($fakeReasonCodeMaster, $updatedReasonCodeMaster->toArray());
        $dbReasonCodeMaster = $this->reasonCodeMasterRepo->find($reasonCodeMaster->id);
        $this->assertModelData($fakeReasonCodeMaster, $dbReasonCodeMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_reason_code_master()
    {
        $reasonCodeMaster = factory(ReasonCodeMaster::class)->create();

        $resp = $this->reasonCodeMasterRepo->delete($reasonCodeMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ReasonCodeMaster::find($reasonCodeMaster->id), 'ReasonCodeMaster should not exist in DB');
    }
}
