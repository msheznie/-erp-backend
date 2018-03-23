<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class NavigationMenusApiTest extends TestCase
{
    use MakeNavigationMenusTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateNavigationMenus()
    {
        $navigationMenus = $this->fakeNavigationMenusData();
        $this->json('POST', '/api/v1/navigationMenuses', $navigationMenus);

        $this->assertApiResponse($navigationMenus);
    }

    /**
     * @test
     */
    public function testReadNavigationMenus()
    {
        $navigationMenus = $this->makeNavigationMenus();
        $this->json('GET', '/api/v1/navigationMenuses/'.$navigationMenus->id);

        $this->assertApiResponse($navigationMenus->toArray());
    }

    /**
     * @test
     */
    public function testUpdateNavigationMenus()
    {
        $navigationMenus = $this->makeNavigationMenus();
        $editedNavigationMenus = $this->fakeNavigationMenusData();

        $this->json('PUT', '/api/v1/navigationMenuses/'.$navigationMenus->id, $editedNavigationMenus);

        $this->assertApiResponse($editedNavigationMenus);
    }

    /**
     * @test
     */
    public function testDeleteNavigationMenus()
    {
        $navigationMenus = $this->makeNavigationMenus();
        $this->json('DELETE', '/api/v1/navigationMenuses/'.$navigationMenus->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/navigationMenuses/'.$navigationMenus->id);

        $this->assertResponseStatus(404);
    }
}
