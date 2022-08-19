<?php namespace Tests\APIs;

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;
use App\Models\PoCutoffJob;

class PoCutoffJobApiTest extends TestCase
{
    use ApiTestTrait, WithoutMiddleware, DatabaseTransactions;

    /**
     * @test
     */
    public function test_create_po_cutoff_job()
    {
        $poCutoffJob = factory(PoCutoffJob::class)->make()->toArray();

        $this->response = $this->json(
            'POST',
            '/api/po_cutoff_jobs', $poCutoffJob
        );

        $this->assertApiResponse($poCutoffJob);
    }

    /**
     * @test
     */
    public function test_read_po_cutoff_job()
    {
        $poCutoffJob = factory(PoCutoffJob::class)->create();

        $this->response = $this->json(
            'GET',
            '/api/po_cutoff_jobs/'.$poCutoffJob->id
        );

        $this->assertApiResponse($poCutoffJob->toArray());
    }

    /**
     * @test
     */
    public function test_update_po_cutoff_job()
    {
        $poCutoffJob = factory(PoCutoffJob::class)->create();
        $editedPoCutoffJob = factory(PoCutoffJob::class)->make()->toArray();

        $this->response = $this->json(
            'PUT',
            '/api/po_cutoff_jobs/'.$poCutoffJob->id,
            $editedPoCutoffJob
        );

        $this->assertApiResponse($editedPoCutoffJob);
    }

    /**
     * @test
     */
    public function test_delete_po_cutoff_job()
    {
        $poCutoffJob = factory(PoCutoffJob::class)->create();

        $this->response = $this->json(
            'DELETE',
             '/api/po_cutoff_jobs/'.$poCutoffJob->id
         );

        $this->assertApiSuccess();
        $this->response = $this->json(
            'GET',
            '/api/po_cutoff_jobs/'.$poCutoffJob->id
        );

        $this->response->assertStatus(404);
    }
}
