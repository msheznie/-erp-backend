<?php namespace Tests\Repositories;

use App\Models\B2BSubmissionFileDetail;
use App\Repositories\B2BSubmissionFileDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class B2BSubmissionFileDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var B2BSubmissionFileDetailRepository
     */
    protected $b2BSubmissionFileDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->b2BSubmissionFileDetailRepo = \App::make(B2BSubmissionFileDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_b2_b_submission_file_detail()
    {
        $b2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->make()->toArray();

        $createdB2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepo->create($b2BSubmissionFileDetail);

        $createdB2BSubmissionFileDetail = $createdB2BSubmissionFileDetail->toArray();
        $this->assertArrayHasKey('id', $createdB2BSubmissionFileDetail);
        $this->assertNotNull($createdB2BSubmissionFileDetail['id'], 'Created B2BSubmissionFileDetail must have id specified');
        $this->assertNotNull(B2BSubmissionFileDetail::find($createdB2BSubmissionFileDetail['id']), 'B2BSubmissionFileDetail with given id must be in DB');
        $this->assertModelData($b2BSubmissionFileDetail, $createdB2BSubmissionFileDetail);
    }

    /**
     * @test read
     */
    public function test_read_b2_b_submission_file_detail()
    {
        $b2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->create();

        $dbB2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepo->find($b2BSubmissionFileDetail->id);

        $dbB2BSubmissionFileDetail = $dbB2BSubmissionFileDetail->toArray();
        $this->assertModelData($b2BSubmissionFileDetail->toArray(), $dbB2BSubmissionFileDetail);
    }

    /**
     * @test update
     */
    public function test_update_b2_b_submission_file_detail()
    {
        $b2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->create();
        $fakeB2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->make()->toArray();

        $updatedB2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepo->update($fakeB2BSubmissionFileDetail, $b2BSubmissionFileDetail->id);

        $this->assertModelData($fakeB2BSubmissionFileDetail, $updatedB2BSubmissionFileDetail->toArray());
        $dbB2BSubmissionFileDetail = $this->b2BSubmissionFileDetailRepo->find($b2BSubmissionFileDetail->id);
        $this->assertModelData($fakeB2BSubmissionFileDetail, $dbB2BSubmissionFileDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_b2_b_submission_file_detail()
    {
        $b2BSubmissionFileDetail = factory(B2BSubmissionFileDetail::class)->create();

        $resp = $this->b2BSubmissionFileDetailRepo->delete($b2BSubmissionFileDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(B2BSubmissionFileDetail::find($b2BSubmissionFileDetail->id), 'B2BSubmissionFileDetail should not exist in DB');
    }
}
