<?php namespace Tests\Repositories;

use App\Models\HrEmpDepartments;
use App\Repositories\HrEmpDepartmentsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HrEmpDepartmentsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HrEmpDepartmentsRepository
     */
    protected $hrEmpDepartmentsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hrEmpDepartmentsRepo = \App::make(HrEmpDepartmentsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hr_emp_departments()
    {
        $hrEmpDepartments = factory(HrEmpDepartments::class)->make()->toArray();

        $createdHrEmpDepartments = $this->hrEmpDepartmentsRepo->create($hrEmpDepartments);

        $createdHrEmpDepartments = $createdHrEmpDepartments->toArray();
        $this->assertArrayHasKey('id', $createdHrEmpDepartments);
        $this->assertNotNull($createdHrEmpDepartments['id'], 'Created HrEmpDepartments must have id specified');
        $this->assertNotNull(HrEmpDepartments::find($createdHrEmpDepartments['id']), 'HrEmpDepartments with given id must be in DB');
        $this->assertModelData($hrEmpDepartments, $createdHrEmpDepartments);
    }

    /**
     * @test read
     */
    public function test_read_hr_emp_departments()
    {
        $hrEmpDepartments = factory(HrEmpDepartments::class)->create();

        $dbHrEmpDepartments = $this->hrEmpDepartmentsRepo->find($hrEmpDepartments->id);

        $dbHrEmpDepartments = $dbHrEmpDepartments->toArray();
        $this->assertModelData($hrEmpDepartments->toArray(), $dbHrEmpDepartments);
    }

    /**
     * @test update
     */
    public function test_update_hr_emp_departments()
    {
        $hrEmpDepartments = factory(HrEmpDepartments::class)->create();
        $fakeHrEmpDepartments = factory(HrEmpDepartments::class)->make()->toArray();

        $updatedHrEmpDepartments = $this->hrEmpDepartmentsRepo->update($fakeHrEmpDepartments, $hrEmpDepartments->id);

        $this->assertModelData($fakeHrEmpDepartments, $updatedHrEmpDepartments->toArray());
        $dbHrEmpDepartments = $this->hrEmpDepartmentsRepo->find($hrEmpDepartments->id);
        $this->assertModelData($fakeHrEmpDepartments, $dbHrEmpDepartments->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hr_emp_departments()
    {
        $hrEmpDepartments = factory(HrEmpDepartments::class)->create();

        $resp = $this->hrEmpDepartmentsRepo->delete($hrEmpDepartments->id);

        $this->assertTrue($resp);
        $this->assertNull(HrEmpDepartments::find($hrEmpDepartments->id), 'HrEmpDepartments should not exist in DB');
    }
}
