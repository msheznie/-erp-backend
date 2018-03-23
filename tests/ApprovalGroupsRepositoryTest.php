<?php

use App\Models\ApprovalGroups;
use App\Repositories\ApprovalGroupsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApprovalGroupsRepositoryTest extends TestCase
{
    use MakeApprovalGroupsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ApprovalGroupsRepository
     */
    protected $approvalGroupsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->approvalGroupsRepo = App::make(ApprovalGroupsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateApprovalGroups()
    {
        $approvalGroups = $this->fakeApprovalGroupsData();
        $createdApprovalGroups = $this->approvalGroupsRepo->create($approvalGroups);
        $createdApprovalGroups = $createdApprovalGroups->toArray();
        $this->assertArrayHasKey('id', $createdApprovalGroups);
        $this->assertNotNull($createdApprovalGroups['id'], 'Created ApprovalGroups must have id specified');
        $this->assertNotNull(ApprovalGroups::find($createdApprovalGroups['id']), 'ApprovalGroups with given id must be in DB');
        $this->assertModelData($approvalGroups, $createdApprovalGroups);
    }

    /**
     * @test read
     */
    public function testReadApprovalGroups()
    {
        $approvalGroups = $this->makeApprovalGroups();
        $dbApprovalGroups = $this->approvalGroupsRepo->find($approvalGroups->id);
        $dbApprovalGroups = $dbApprovalGroups->toArray();
        $this->assertModelData($approvalGroups->toArray(), $dbApprovalGroups);
    }

    /**
     * @test update
     */
    public function testUpdateApprovalGroups()
    {
        $approvalGroups = $this->makeApprovalGroups();
        $fakeApprovalGroups = $this->fakeApprovalGroupsData();
        $updatedApprovalGroups = $this->approvalGroupsRepo->update($fakeApprovalGroups, $approvalGroups->id);
        $this->assertModelData($fakeApprovalGroups, $updatedApprovalGroups->toArray());
        $dbApprovalGroups = $this->approvalGroupsRepo->find($approvalGroups->id);
        $this->assertModelData($fakeApprovalGroups, $dbApprovalGroups->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteApprovalGroups()
    {
        $approvalGroups = $this->makeApprovalGroups();
        $resp = $this->approvalGroupsRepo->delete($approvalGroups->id);
        $this->assertTrue($resp);
        $this->assertNull(ApprovalGroups::find($approvalGroups->id), 'ApprovalGroups should not exist in DB');
    }
}
