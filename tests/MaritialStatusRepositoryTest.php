<?php namespace Tests\Repositories;

use App\Models\MaritialStatus;
use App\Repositories\MaritialStatusRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeMaritialStatusTrait;
use Tests\ApiTestTrait;

class MaritialStatusRepositoryTest extends TestCase
{
    use MakeMaritialStatusTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var MaritialStatusRepository
     */
    protected $maritialStatusRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->maritialStatusRepo = \App::make(MaritialStatusRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_maritial_status()
    {
        $maritialStatus = $this->fakeMaritialStatusData();
        $createdMaritialStatus = $this->maritialStatusRepo->create($maritialStatus);
        $createdMaritialStatus = $createdMaritialStatus->toArray();
        $this->assertArrayHasKey('id', $createdMaritialStatus);
        $this->assertNotNull($createdMaritialStatus['id'], 'Created MaritialStatus must have id specified');
        $this->assertNotNull(MaritialStatus::find($createdMaritialStatus['id']), 'MaritialStatus with given id must be in DB');
        $this->assertModelData($maritialStatus, $createdMaritialStatus);
    }

    /**
     * @test read
     */
    public function test_read_maritial_status()
    {
        $maritialStatus = $this->makeMaritialStatus();
        $dbMaritialStatus = $this->maritialStatusRepo->find($maritialStatus->id);
        $dbMaritialStatus = $dbMaritialStatus->toArray();
        $this->assertModelData($maritialStatus->toArray(), $dbMaritialStatus);
    }

    /**
     * @test update
     */
    public function test_update_maritial_status()
    {
        $maritialStatus = $this->makeMaritialStatus();
        $fakeMaritialStatus = $this->fakeMaritialStatusData();
        $updatedMaritialStatus = $this->maritialStatusRepo->update($fakeMaritialStatus, $maritialStatus->id);
        $this->assertModelData($fakeMaritialStatus, $updatedMaritialStatus->toArray());
        $dbMaritialStatus = $this->maritialStatusRepo->find($maritialStatus->id);
        $this->assertModelData($fakeMaritialStatus, $dbMaritialStatus->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_maritial_status()
    {
        $maritialStatus = $this->makeMaritialStatus();
        $resp = $this->maritialStatusRepo->delete($maritialStatus->id);
        $this->assertTrue($resp);
        $this->assertNull(MaritialStatus::find($maritialStatus->id), 'MaritialStatus should not exist in DB');
    }
}
