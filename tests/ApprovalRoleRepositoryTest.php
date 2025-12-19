<?php

use App\Models\ApprovalRole;
use App\Repositories\ApprovalRoleRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApprovalRoleRepositoryTest extends TestCase
{
    use MakeApprovalRoleTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ApprovalRoleRepository
     */
    protected $approvalRoleRepo;

    public function setUp()
    {
        parent::setUp();
        $this->approvalRoleRepo = App::make(ApprovalRoleRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateApprovalRole()
    {
        $approvalRole = $this->fakeApprovalRoleData();
        $createdApprovalRole = $this->approvalRoleRepo->create($approvalRole);
        $createdApprovalRole = $createdApprovalRole->toArray();
        $this->assertArrayHasKey('id', $createdApprovalRole);
        $this->assertNotNull($createdApprovalRole['id'], 'Created ApprovalRole must have id specified');
        $this->assertNotNull(ApprovalRole::find($createdApprovalRole['id']), 'ApprovalRole with given id must be in DB');
        $this->assertModelData($approvalRole, $createdApprovalRole);
    }

    /**
     * @test read
     */
    public function testReadApprovalRole()
    {
        $approvalRole = $this->makeApprovalRole();
        $dbApprovalRole = $this->approvalRoleRepo->find($approvalRole->id);
        $dbApprovalRole = $dbApprovalRole->toArray();
        $this->assertModelData($approvalRole->toArray(), $dbApprovalRole);
    }

    /**
     * @test update
     */
    public function testUpdateApprovalRole()
    {
        $approvalRole = $this->makeApprovalRole();
        $fakeApprovalRole = $this->fakeApprovalRoleData();
        $updatedApprovalRole = $this->approvalRoleRepo->update($fakeApprovalRole, $approvalRole->id);
        $this->assertModelData($fakeApprovalRole, $updatedApprovalRole->toArray());
        $dbApprovalRole = $this->approvalRoleRepo->find($approvalRole->id);
        $this->assertModelData($fakeApprovalRole, $dbApprovalRole->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteApprovalRole()
    {
        $approvalRole = $this->makeApprovalRole();
        $resp = $this->approvalRoleRepo->delete($approvalRole->id);
        $this->assertTrue($resp);
        $this->assertNull(ApprovalRole::find($approvalRole->id), 'ApprovalRole should not exist in DB');
    }
}
