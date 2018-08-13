<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AddonCostCategoriesApiTest extends TestCase
{
    use MakeAddonCostCategoriesTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateAddonCostCategories()
    {
        $addonCostCategories = $this->fakeAddonCostCategoriesData();
        $this->json('POST', '/api/v1/addonCostCategories', $addonCostCategories);

        $this->assertApiResponse($addonCostCategories);
    }

    /**
     * @test
     */
    public function testReadAddonCostCategories()
    {
        $addonCostCategories = $this->makeAddonCostCategories();
        $this->json('GET', '/api/v1/addonCostCategories/'.$addonCostCategories->id);

        $this->assertApiResponse($addonCostCategories->toArray());
    }

    /**
     * @test
     */
    public function testUpdateAddonCostCategories()
    {
        $addonCostCategories = $this->makeAddonCostCategories();
        $editedAddonCostCategories = $this->fakeAddonCostCategoriesData();

        $this->json('PUT', '/api/v1/addonCostCategories/'.$addonCostCategories->id, $editedAddonCostCategories);

        $this->assertApiResponse($editedAddonCostCategories);
    }

    /**
     * @test
     */
    public function testDeleteAddonCostCategories()
    {
        $addonCostCategories = $this->makeAddonCostCategories();
        $this->json('DELETE', '/api/v1/addonCostCategories/'.$addonCostCategories->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/addonCostCategories/'.$addonCostCategories->id);

        $this->assertResponseStatus(404);
    }
}
