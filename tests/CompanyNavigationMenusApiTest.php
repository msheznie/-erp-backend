<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class CompanyNavigationMenusApiTest extends TestCase
{
    use MakeCompanyNavigationMenusTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateCompanyNavigationMenus()
    {
        $companyNavigationMenus = $this->fakeCompanyNavigationMenusData();
        $this->json('POST', '/api/v1/companyNavigationMenuses', $companyNavigationMenus);

        $this->assertApiResponse($companyNavigationMenus);
    }

    /**
     * @test
     */
    public function testReadCompanyNavigationMenus()
    {
        $companyNavigationMenus = $this->makeCompanyNavigationMenus();
        $this->json('GET', '/api/v1/companyNavigationMenuses/'.$companyNavigationMenus->id);

        $this->assertApiResponse($companyNavigationMenus->toArray());
    }

    /**
     * @test
     */
    public function testUpdateCompanyNavigationMenus()
    {
        $companyNavigationMenus = $this->makeCompanyNavigationMenus();
        $editedCompanyNavigationMenus = $this->fakeCompanyNavigationMenusData();

        $this->json('PUT', '/api/v1/companyNavigationMenuses/'.$companyNavigationMenus->id, $editedCompanyNavigationMenus);

        $this->assertApiResponse($editedCompanyNavigationMenus);
    }

    /**
     * @test
     */
    public function testDeleteCompanyNavigationMenus()
    {
        $companyNavigationMenus = $this->makeCompanyNavigationMenus();
        $this->json('DELETE', '/api/v1/companyNavigationMenuses/'.$companyNavigationMenus->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/companyNavigationMenuses/'.$companyNavigationMenus->id);

        $this->assertResponseStatus(404);
    }
}
