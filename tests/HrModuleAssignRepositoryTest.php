<?php namespace Tests\Repositories;

use App\Models\HrModuleAssign;
use App\Repositories\HrModuleAssignRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrModuleAssignRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrModuleAssignRepository
     */
    protected $hrModuleAssignRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrModuleAssignRepo = \App::make(HrModuleAssignRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_module_assign()
    {
        $hrModuleAssign = factory(HrModuleAssign::class)->make()->toArray();

        $createdHrModuleAssign = $this->hrModuleAssignRepo->create($hrModuleAssign);

        $createdHrModuleAssign = $createdHrModuleAssign->toArray();
        $this->assertArrayHasKey('id', $createdHrModuleAssign);
        $this->assertNotNull($createdHrModuleAssign['id'], 'Created HrModuleAssign must have id specified');
        $this->assertNotNull(HrModuleAssign::find($createdHrModuleAssign['id']), 'HrModuleAssign with given id must be in DB');
        $this->assertModelData($hrModuleAssign, $createdHrModuleAssign);
    }

    /**
     * @test read
     */
    public function test_read_hr_module_assign()
    {
        $hrModuleAssign = factory(HrModuleAssign::class)->create();

        $dbHrModuleAssign = $this->hrModuleAssignRepo->find($hrModuleAssign->id);

        $dbHrModuleAssign = $dbHrModuleAssign->toArray();
        $this->assertModelData($hrModuleAssign->toArray(), $dbHrModuleAssign);
    }

    /**
     * @test update
     */
    public function test_update_hr_module_assign()
    {
        $hrModuleAssign = factory(HrModuleAssign::class)->create();
        $fakeHrModuleAssign = factory(HrModuleAssign::class)->make()->toArray();

        $updatedHrModuleAssign = $this->hrModuleAssignRepo->update($fakeHrModuleAssign, $hrModuleAssign->id);

        $this->assertModelData($fakeHrModuleAssign, $updatedHrModuleAssign->toArray());
        $dbHrModuleAssign = $this->hrModuleAssignRepo->find($hrModuleAssign->id);
        $this->assertModelData($fakeHrModuleAssign, $dbHrModuleAssign->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_module_assign()
    {
        $hrModuleAssign = factory(HrModuleAssign::class)->create();

        $resp = $this->hrModuleAssignRepo->delete($hrModuleAssign->id);

        $this->assertTrue($resp);
        $this->assertNull(HrModuleAssign::find($hrModuleAssign->id), 'HrModuleAssign should not exist in DB');
    }
}
