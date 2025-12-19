<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ApprovalLevelApiTest extends TestCase
{
    use MakeApprovalLevelTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateApprovalLevel()
    {
        $approvalLevel = $this->fakeApprovalLevelData();
        $this->json('POST', '/api/v1/approvalLevels', $approvalLevel);

        $this->assertApiResponse($approvalLevel);
    }

    /**
     * @test
     */
    public function testReadApprovalLevel()
    {
        $approvalLevel = $this->makeApprovalLevel();
        $this->json('GET', '/api/v1/approvalLevels/'.$approvalLevel->id);

        $this->assertApiResponse($approvalLevel->toArray());
    }

    /**
     * @test
     */
    public function testUpdateApprovalLevel()
    {
        $approvalLevel = $this->makeApprovalLevel();
        $editedApprovalLevel = $this->fakeApprovalLevelData();

        $this->json('PUT', '/api/v1/approvalLevels/'.$approvalLevel->id, $editedApprovalLevel);

        $this->assertApiResponse($editedApprovalLevel);
    }

    /**
     * @test
     */
    public function testDeleteApprovalLevel()
    {
        $approvalLevel = $this->makeApprovalLevel();
        $this->json('DELETE', '/api/v1/approvalLevels/'.$approvalLevel->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/approvalLevels/'.$approvalLevel->id);

        $this->assertResponseStatus(404);
    }
}
