<?php namespace Tests\Repositories;

use App\Models\HrmsEmployeeManager;
use App\Repositories\HrmsEmployeeManagerRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrmsEmployeeManagerRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrmsEmployeeManagerRepository
     */
    protected $hrmsEmployeeManagerRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrmsEmployeeManagerRepo = \App::make(HrmsEmployeeManagerRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hrms_employee_manager()
    {
        $hrmsEmployeeManager = factory(HrmsEmployeeManager::class)->make()->toArray();

        $createdHrmsEmployeeManager = $this->hrmsEmployeeManagerRepo->create($hrmsEmployeeManager);

        $createdHrmsEmployeeManager = $createdHrmsEmployeeManager->toArray();
        $this->assertArrayHasKey('id', $createdHrmsEmployeeManager);
        $this->assertNotNull($createdHrmsEmployeeManager['id'], 'Created HrmsEmployeeManager must have id specified');
        $this->assertNotNull(HrmsEmployeeManager::find($createdHrmsEmployeeManager['id']), 'HrmsEmployeeManager with given id must be in DB');
        $this->assertModelData($hrmsEmployeeManager, $createdHrmsEmployeeManager);
    }

    /**
     * @test read
     */
    public function test_read_hrms_employee_manager()
    {
        $hrmsEmployeeManager = factory(HrmsEmployeeManager::class)->create();

        $dbHrmsEmployeeManager = $this->hrmsEmployeeManagerRepo->find($hrmsEmployeeManager->id);

        $dbHrmsEmployeeManager = $dbHrmsEmployeeManager->toArray();
        $this->assertModelData($hrmsEmployeeManager->toArray(), $dbHrmsEmployeeManager);
    }

    /**
     * @test update
     */
    public function test_update_hrms_employee_manager()
    {
        $hrmsEmployeeManager = factory(HrmsEmployeeManager::class)->create();
        $fakeHrmsEmployeeManager = factory(HrmsEmployeeManager::class)->make()->toArray();

        $updatedHrmsEmployeeManager = $this->hrmsEmployeeManagerRepo->update($fakeHrmsEmployeeManager, $hrmsEmployeeManager->id);

        $this->assertModelData($fakeHrmsEmployeeManager, $updatedHrmsEmployeeManager->toArray());
        $dbHrmsEmployeeManager = $this->hrmsEmployeeManagerRepo->find($hrmsEmployeeManager->id);
        $this->assertModelData($fakeHrmsEmployeeManager, $dbHrmsEmployeeManager->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hrms_employee_manager()
    {
        $hrmsEmployeeManager = factory(HrmsEmployeeManager::class)->create();

        $resp = $this->hrmsEmployeeManagerRepo->delete($hrmsEmployeeManager->id);

        $this->assertTrue($resp);
        $this->assertNull(HrmsEmployeeManager::find($hrmsEmployeeManager->id), 'HrmsEmployeeManager should not exist in DB');
    }
}
