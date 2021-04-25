<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\HrmsDesignation;

class HrmsDesignationApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_hrms_designation()
    {
        $hrmsDesignation = factory(HrmsDesignation::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/hrms_designations', $hrmsDesignation
        );

        $this->assertApiResponse($hrmsDesignation);
    }

    /**
     * @test
     */
    public function test_read_hrms_designation()
    {
        $hrmsDesignation = factory(HrmsDesignation::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/hrms_designations/'.$hrmsDesignation->id
        );

        $this->assertApiResponse($hrmsDesignation->toArray());
    }

    /**
     * @test
     */
    public function test_update_hrms_designation()
    {
        $hrmsDesignation = factory(HrmsDesignation::class)->create();
        $editedHrmsDesignation = factory(HrmsDesignation::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/hrms_designations/'.$hrmsDesignation->id,
            $editedHrmsDesignation
        );

        $this->assertApiResponse($editedHrmsDesignation);
    }

    /**
     * @test
     */
    public function test_delete_hrms_designation()
    {
        $hrmsDesignation = factory(HrmsDesignation::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/hrms_designations/'.$hrmsDesignation->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/hrms_designations/'.$hrmsDesignation->id
        );

        $this->response->assertStatus(404);
    }
}
