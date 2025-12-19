<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class EmploymentTypeApiTest extends TestCase
{
    use MakeEmploymentTypeTrait, ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function testCreateEmploymentType()
    {
        $employmentType = $this->fakeEmploymentTypeData();
        $this->json('POST', '/api/v1/employmentTypes', $employmentType);

        $this->assertApiResponse($employmentType);
    }

    /**
     * @test
     */
    public function testReadEmploymentType()
    {
        $employmentType = $this->makeEmploymentType();
        $this->json('GET', '/api/v1/employmentTypes/'.$employmentType->id);

        $this->assertApiResponse($employmentType->toArray());
    }

    /**
     * @test
     */
    public function testUpdateEmploymentType()
    {
        $employmentType = $this->makeEmploymentType();
        $editedEmploymentType = $this->fakeEmploymentTypeData();

        $this->json('PUT', '/api/v1/employmentTypes/'.$employmentType->id, $editedEmploymentType);

        $this->assertApiResponse($editedEmploymentType);
    }

    /**
     * @test
     */
    public function testDeleteEmploymentType()
    {
        $employmentType = $this->makeEmploymentType();
        $this->json('DELETE', '/api/v1/employmentTypes/'.$employmentType->id);

        $this->assertApiSuccess();
        $this->json('GET', '/api/v1/employmentTypes/'.$employmentType->id);

        $this->assertResponseStatus(404);
    }
}
