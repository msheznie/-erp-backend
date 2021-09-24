<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\ProjectGlDetail;

class ProjectGlDetailApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_project_gl_detail()
    {
        $projectGlDetail = factory(ProjectGlDetail::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/project_gl_details', $projectGlDetail
        );

        $this->assertApiResponse($projectGlDetail);
    }

    /**
     * @test
     */
    public function test_read_project_gl_detail()
    {
        $projectGlDetail = factory(ProjectGlDetail::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/project_gl_details/'.$projectGlDetail->id
        );

        $this->assertApiResponse($projectGlDetail->toArray());
    }

    /**
     * @test
     */
    public function test_update_project_gl_detail()
    {
        $projectGlDetail = factory(ProjectGlDetail::class)->create();
        $editedProjectGlDetail = factory(ProjectGlDetail::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/project_gl_details/'.$projectGlDetail->id,
            $editedProjectGlDetail
        );

        $this->assertApiResponse($editedProjectGlDetail);
    }

    /**
     * @test
     */
    public function test_delete_project_gl_detail()
    {
        $projectGlDetail = factory(ProjectGlDetail::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/project_gl_details/'.$projectGlDetail->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/project_gl_details/'.$projectGlDetail->id
        );

        $this->response->assertStatus(404);
    }
}
