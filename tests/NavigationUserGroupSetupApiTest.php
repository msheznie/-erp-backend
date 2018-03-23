<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NavigationUserGroupSetupApiTest extends TestCase
{
    use MakeNavigationUserGroupSetupTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateNavigationUserGroupSetup()
    {
        $navigationUserGroupSetup = $this->fakeNavigationUserGroupSetupData();
        $this->json('POST', '/api/v1/navigationUserGroupSetups', $navigationUserGroupSetup);

        $this->assertApiResponse($navigationUserGroupSetup);
    }

    /**
     * @test
     */
    public function testReadNavigationUserGroupSetup()
    {
        $navigationUserGroupSetup = $this->makeNavigationUserGroupSetup();
        $this->json('GET', '/api/v1/navigationUserGroupSetups/'.$navigationUserGroupSetup->id);

        $this->assertApiResponse($navigationUserGroupSetup->toArray());
    }

    /**
     * @test
     */
    public function testUpdateNavigationUserGroupSetup()
    {
        $navigationUserGroupSetup = $this->makeNavigationUserGroupSetup();
        $editedNavigationUserGroupSetup = $this->fakeNavigationUserGroupSetupData();

        $this->json('PUT', '/api/v1/navigationUserGroupSetups/'.$navigationUserGroupSetup->id, $editedNavigationUserGroupSetup);

        $this->assertApiResponse($editedNavigationUserGroupSetup);
    }

    /**
     * @test
     */
    public function testDeleteNavigationUserGroupSetup()
    {
        $navigationUserGroupSetup = $this->makeNavigationUserGroupSetup();
        $this->json('DELETE', '/api/v1/navigationUserGroupSetups/'.$navigationUserGroupSetup->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/navigationUserGroupSetups/'.$navigationUserGroupSetup->id);

        $this->assertResponseStatus(404);
    }
}
