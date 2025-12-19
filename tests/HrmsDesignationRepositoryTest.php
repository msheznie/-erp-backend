<?php namespace Tests\Repositories;

use App\Models\HrmsDesignation;
use App\Repositories\HrmsDesignationRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrmsDesignationRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrmsDesignationRepository
     */
    protected $hrmsDesignationRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrmsDesignationRepo = \App::make(HrmsDesignationRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hrms_designation()
    {
        $hrmsDesignation = factory(HrmsDesignation::class)->make()->toArray();

        $createdHrmsDesignation = $this->hrmsDesignationRepo->create($hrmsDesignation);

        $createdHrmsDesignation = $createdHrmsDesignation->toArray();
        $this->assertArrayHasKey('id', $createdHrmsDesignation);
        $this->assertNotNull($createdHrmsDesignation['id'], 'Created HrmsDesignation must have id specified');
        $this->assertNotNull(HrmsDesignation::find($createdHrmsDesignation['id']), 'HrmsDesignation with given id must be in DB');
        $this->assertModelData($hrmsDesignation, $createdHrmsDesignation);
    }

    /**
     * @test read
     */
    public function test_read_hrms_designation()
    {
        $hrmsDesignation = factory(HrmsDesignation::class)->create();

        $dbHrmsDesignation = $this->hrmsDesignationRepo->find($hrmsDesignation->id);

        $dbHrmsDesignation = $dbHrmsDesignation->toArray();
        $this->assertModelData($hrmsDesignation->toArray(), $dbHrmsDesignation);
    }

    /**
     * @test update
     */
    public function test_update_hrms_designation()
    {
        $hrmsDesignation = factory(HrmsDesignation::class)->create();
        $fakeHrmsDesignation = factory(HrmsDesignation::class)->make()->toArray();

        $updatedHrmsDesignation = $this->hrmsDesignationRepo->update($fakeHrmsDesignation, $hrmsDesignation->id);

        $this->assertModelData($fakeHrmsDesignation, $updatedHrmsDesignation->toArray());
        $dbHrmsDesignation = $this->hrmsDesignationRepo->find($hrmsDesignation->id);
        $this->assertModelData($fakeHrmsDesignation, $dbHrmsDesignation->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hrms_designation()
    {
        $hrmsDesignation = factory(HrmsDesignation::class)->create();

        $resp = $this->hrmsDesignationRepo->delete($hrmsDesignation->id);

        $this->assertTrue($resp);
        $this->assertNull(HrmsDesignation::find($hrmsDesignation->id), 'HrmsDesignation should not exist in DB');
    }
}
