<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSecondaryCompanyTrait;
use Tests\ApiTestTrait;

class SecondaryCompanyApiTest extends TestCase
{
    use MakeSecondaryCompanyTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_secondary_company()
    {
        $secondaryCompany = $this->fakeSecondaryCompanyData();
        $this->response = $this->json('POST', '/api/secondaryCompanies', $secondaryCompany);

        $this->assertApiResponse($secondaryCompany);
    }

    /**
     * @test
     */
    public function test_read_secondary_company()
    {
        $secondaryCompany = $this->makeSecondaryCompany();
        $this->response = $this->json('GET', '/api/secondaryCompanies/'.$secondaryCompany->id);

        $this->assertApiResponse($secondaryCompany->toArray());
    }

    /**
     * @test
     */
    public function test_update_secondary_company()
    {
        $secondaryCompany = $this->makeSecondaryCompany();
        $editedSecondaryCompany = $this->fakeSecondaryCompanyData();

        $this->response = $this->json('PUT', '/api/secondaryCompanies/'.$secondaryCompany->id, $editedSecondaryCompany);

        $this->assertApiResponse($editedSecondaryCompany);
    }

    /**
     * @test
     */
    public function test_delete_secondary_company()
    {
        $secondaryCompany = $this->makeSecondaryCompany();
        $this->response = $this->json('DELETE', '/api/secondaryCompanies/'.$secondaryCompany->id);

        $this->assertApiSuccess();
        $this->response = $this->json('GET', '/api/secondaryCompanies/'.$secondaryCompany->id);

        $this->response->assertStatus(404);
    }
}
