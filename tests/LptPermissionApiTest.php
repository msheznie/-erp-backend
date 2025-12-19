<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeLptPermissionTrait;
use Tests\ApiTestTrait;

class LptPermissionApiTest extends TestCase
{
    use MakeLptPermissionTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_lpt_permission()
    {
        $lptPermission = $this->fakeLptPermissionData();
        $this->response = $this->json('POST', '/api/lptPermissions', $lptPermission);

        $this->assertApiResponse($lptPermission);
    }

    /**
     * @test
     */
    public function test_read_lpt_permission()
    {
        $lptPermission = $this->makeLptPermission();
        $this->response = $this->json('GET', '/api/lptPermissions/'.$lptPermission->id);

        $this->assertApiResponse($lptPermission->toArray());
    }

    /**
     * @test
     */
    public function test_update_lpt_permission()
    {
        $lptPermission = $this->makeLptPermission();
        $editedLptPermission = $this->fakeLptPermissionData();

        $this->response = $this->json('PUT', '/api/lptPermissions/'.$lptPermission->id, $editedLptPermission);

        $this->assertApiResponse($editedLptPermission);
    }

    /**
     * @test
     */
    public function test_delete_lpt_permission()
    {
        $lptPermission = $this->makeLptPermission();
        $this->response = $this->json('DELETE', '/api/lptPermissions/'.$lptPermission->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/lptPermissions/'.$lptPermission->id);

        $this->response->assertStatus(404);
    }
}
