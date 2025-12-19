<?php

use App\Models\ApprovalLevel;
use App\Repositories\ApprovalLevelRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApprovalLevelRepositoryTest extends TestCase
{
    use MakeApprovalLevelTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ApprovalLevelRepository
     */
    protected $approvalLevelRepo;

    public function setUp()
    {
        parent::setUp();
        $this->approvalLevelRepo = App::make(ApprovalLevelRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateApprovalLevel()
    {
        $approvalLevel = $this->fakeApprovalLevelData();
        $createdApprovalLevel = $this->approvalLevelRepo->create($approvalLevel);
        $createdApprovalLevel = $createdApprovalLevel->toArray();
        $this->assertArrayHasKey('id', $createdApprovalLevel);
        $this->assertNotNull($createdApprovalLevel['id'], 'Created ApprovalLevel must have id specified');
        $this->assertNotNull(ApprovalLevel::find($createdApprovalLevel['id']), 'ApprovalLevel with given id must be in DB');
        $this->assertModelData($approvalLevel, $createdApprovalLevel);
    }

    /**
     * @test read
     */
    public function testReadApprovalLevel()
    {
        $approvalLevel = $this->makeApprovalLevel();
        $dbApprovalLevel = $this->approvalLevelRepo->find($approvalLevel->id);
        $dbApprovalLevel = $dbApprovalLevel->toArray();
        $this->assertModelData($approvalLevel->toArray(), $dbApprovalLevel);
    }

    /**
     * @test update
     */
    public function testUpdateApprovalLevel()
    {
        $approvalLevel = $this->makeApprovalLevel();
        $fakeApprovalLevel = $this->fakeApprovalLevelData();
        $updatedApprovalLevel = $this->approvalLevelRepo->update($fakeApprovalLevel, $approvalLevel->id);
        $this->assertModelData($fakeApprovalLevel, $updatedApprovalLevel->toArray());
        $dbApprovalLevel = $this->approvalLevelRepo->find($approvalLevel->id);
        $this->assertModelData($fakeApprovalLevel, $dbApprovalLevel->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteApprovalLevel()
    {
        $approvalLevel = $this->makeApprovalLevel();
        $resp = $this->approvalLevelRepo->delete($approvalLevel->id);
        $this->assertTrue($resp);
        $this->assertNull(ApprovalLevel::find($approvalLevel->id), 'ApprovalLevel should not exist in DB');
    }
}
