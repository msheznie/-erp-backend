<?php namespace Tests\Repositories;

use App\Models\ProjectGlDetail;
use App\Repositories\ProjectGlDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ProjectGlDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ProjectGlDetailRepository
     */
    protected $projectGlDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->projectGlDetailRepo = \App::make(ProjectGlDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_project_gl_detail()
    {
        $projectGlDetail = factory(ProjectGlDetail::class)->make()->toArray();

        $createdProjectGlDetail = $this->projectGlDetailRepo->create($projectGlDetail);

        $createdProjectGlDetail = $createdProjectGlDetail->toArray();
        $this->assertArrayHasKey('id', $createdProjectGlDetail);
        $this->assertNotNull($createdProjectGlDetail['id'], 'Created ProjectGlDetail must have id specified');
        $this->assertNotNull(ProjectGlDetail::find($createdProjectGlDetail['id']), 'ProjectGlDetail with given id must be in DB');
        $this->assertModelData($projectGlDetail, $createdProjectGlDetail);
    }

    /**
     * @test read
     */
    public function test_read_project_gl_detail()
    {
        $projectGlDetail = factory(ProjectGlDetail::class)->create();

        $dbProjectGlDetail = $this->projectGlDetailRepo->find($projectGlDetail->id);

        $dbProjectGlDetail = $dbProjectGlDetail->toArray();
        $this->assertModelData($projectGlDetail->toArray(), $dbProjectGlDetail);
    }

    /**
     * @test update
     */
    public function test_update_project_gl_detail()
    {
        $projectGlDetail = factory(ProjectGlDetail::class)->create();
        $fakeProjectGlDetail = factory(ProjectGlDetail::class)->make()->toArray();

        $updatedProjectGlDetail = $this->projectGlDetailRepo->update($fakeProjectGlDetail, $projectGlDetail->id);

        $this->assertModelData($fakeProjectGlDetail, $updatedProjectGlDetail->toArray());
        $dbProjectGlDetail = $this->projectGlDetailRepo->find($projectGlDetail->id);
        $this->assertModelData($fakeProjectGlDetail, $dbProjectGlDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_project_gl_detail()
    {
        $projectGlDetail = factory(ProjectGlDetail::class)->create();

        $resp = $this->projectGlDetailRepo->delete($projectGlDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(ProjectGlDetail::find($projectGlDetail->id), 'ProjectGlDetail should not exist in DB');
    }
}
