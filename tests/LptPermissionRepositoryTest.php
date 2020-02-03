<?php namespace Tests\Repositories;

use App\Models\LptPermission;
use App\Repositories\LptPermissionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLptPermissionTrait;
use Tests\ApiTestTrait;

class LptPermissionRepositoryTest extends TestCase
{
    use MakeLptPermissionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var LptPermissionRepository
     */
    protected $lptPermissionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->lptPermissionRepo = \App::make(LptPermissionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_lpt_permission()
    {
        $lptPermission = $this->fakeLptPermissionData();
        $createdLptPermission = $this->lptPermissionRepo->create($lptPermission);
        $createdLptPermission = $createdLptPermission->toArray();
        $this->assertArrayHasKey('id', $createdLptPermission);
        $this->assertNotNull($createdLptPermission['id'], 'Created LptPermission must have id specified');
        $this->assertNotNull(LptPermission::find($createdLptPermission['id']), 'LptPermission with given id must be in DB');
        $this->assertModelData($lptPermission, $createdLptPermission);
    }

    /**
     * @test read
     */
    public function test_read_lpt_permission()
    {
        $lptPermission = $this->makeLptPermission();
        $dbLptPermission = $this->lptPermissionRepo->find($lptPermission->id);
        $dbLptPermission = $dbLptPermission->toArray();
        $this->assertModelData($lptPermission->toArray(), $dbLptPermission);
    }

    /**
     * @test update
     */
    public function test_update_lpt_permission()
    {
        $lptPermission = $this->makeLptPermission();
        $fakeLptPermission = $this->fakeLptPermissionData();
        $updatedLptPermission = $this->lptPermissionRepo->update($fakeLptPermission, $lptPermission->id);
        $this->assertModelData($fakeLptPermission, $updatedLptPermission->toArray());
        $dbLptPermission = $this->lptPermissionRepo->find($lptPermission->id);
        $this->assertModelData($fakeLptPermission, $dbLptPermission->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_lpt_permission()
    {
        $lptPermission = $this->makeLptPermission();
        $resp = $this->lptPermissionRepo->delete($lptPermission->id);
        $this->assertTrue($resp);
        $this->assertNull(LptPermission::find($lptPermission->id), 'LptPermission should not exist in DB');
    }
}
