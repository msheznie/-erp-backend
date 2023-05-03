<?php namespace Tests\Repositories;

use App\Models\HRDocumentApproved;
use App\Repositories\HRDocumentApprovedRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HRDocumentApprovedRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HRDocumentApprovedRepository
     */
    protected $hRDocumentApprovedRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hRDocumentApprovedRepo = \App::make(HRDocumentApprovedRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_h_r_document_approved()
    {
        $hRDocumentApproved = factory(HRDocumentApproved::class)->make()->toArray();

        $createdHRDocumentApproved = $this->hRDocumentApprovedRepo->create($hRDocumentApproved);

        $createdHRDocumentApproved = $createdHRDocumentApproved->toArray();
        $this->assertArrayHasKey('id', $createdHRDocumentApproved);
        $this->assertNotNull($createdHRDocumentApproved['id'], 'Created HRDocumentApproved must have id specified');
        $this->assertNotNull(HRDocumentApproved::find($createdHRDocumentApproved['id']), 'HRDocumentApproved with given id must be in DB');
        $this->assertModelData($hRDocumentApproved, $createdHRDocumentApproved);
    }

    /**
     * @test read
     */
    public function test_read_h_r_document_approved()
    {
        $hRDocumentApproved = factory(HRDocumentApproved::class)->create();

        $dbHRDocumentApproved = $this->hRDocumentApprovedRepo->find($hRDocumentApproved->id);

        $dbHRDocumentApproved = $dbHRDocumentApproved->toArray();
        $this->assertModelData($hRDocumentApproved->toArray(), $dbHRDocumentApproved);
    }

    /**
     * @test update
     */
    public function test_update_h_r_document_approved()
    {
        $hRDocumentApproved = factory(HRDocumentApproved::class)->create();
        $fakeHRDocumentApproved = factory(HRDocumentApproved::class)->make()->toArray();

        $updatedHRDocumentApproved = $this->hRDocumentApprovedRepo->update($fakeHRDocumentApproved, $hRDocumentApproved->id);

        $this->assertModelData($fakeHRDocumentApproved, $updatedHRDocumentApproved->toArray());
        $dbHRDocumentApproved = $this->hRDocumentApprovedRepo->find($hRDocumentApproved->id);
        $this->assertModelData($fakeHRDocumentApproved, $dbHRDocumentApproved->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_h_r_document_approved()
    {
        $hRDocumentApproved = factory(HRDocumentApproved::class)->create();

        $resp = $this->hRDocumentApprovedRepo->delete($hRDocumentApproved->id);

        $this->assertTrue($resp);
        $this->assertNull(HRDocumentApproved::find($hRDocumentApproved->id), 'HRDocumentApproved should not exist in DB');
    }
}
