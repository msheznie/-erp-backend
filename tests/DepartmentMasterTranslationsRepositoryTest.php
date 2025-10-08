<?php namespace Tests\Repositories;

use App\Models\DepartmentMasterTranslations;
use App\Repositories\DepartmentMasterTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class DepartmentMasterTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var DepartmentMasterTranslationsRepository
     */
    protected $departmentMasterTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->departmentMasterTranslationsRepo = \App::make(DepartmentMasterTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_department_master_translations()
    {
        $departmentMasterTranslations = factory(DepartmentMasterTranslations::class)->make()->toArray();

        $createdDepartmentMasterTranslations = $this->departmentMasterTranslationsRepo->create($departmentMasterTranslations);

        $createdDepartmentMasterTranslations = $createdDepartmentMasterTranslations->toArray();
        $this->assertArrayHasKey('id', $createdDepartmentMasterTranslations);
        $this->assertNotNull($createdDepartmentMasterTranslations['id'], 'Created DepartmentMasterTranslations must have id specified');
        $this->assertNotNull(DepartmentMasterTranslations::find($createdDepartmentMasterTranslations['id']), 'DepartmentMasterTranslations with given id must be in DB');
        $this->assertModelData($departmentMasterTranslations, $createdDepartmentMasterTranslations);
    }

    /**
     * @test read
     */
    public function test_read_department_master_translations()
    {
        $departmentMasterTranslations = factory(DepartmentMasterTranslations::class)->create();

        $dbDepartmentMasterTranslations = $this->departmentMasterTranslationsRepo->find($departmentMasterTranslations->id);

        $dbDepartmentMasterTranslations = $dbDepartmentMasterTranslations->toArray();
        $this->assertModelData($departmentMasterTranslations->toArray(), $dbDepartmentMasterTranslations);
    }

    /**
     * @test update
     */
    public function test_update_department_master_translations()
    {
        $departmentMasterTranslations = factory(DepartmentMasterTranslations::class)->create();
        $fakeDepartmentMasterTranslations = factory(DepartmentMasterTranslations::class)->make()->toArray();

        $updatedDepartmentMasterTranslations = $this->departmentMasterTranslationsRepo->update($fakeDepartmentMasterTranslations, $departmentMasterTranslations->id);

        $this->assertModelData($fakeDepartmentMasterTranslations, $updatedDepartmentMasterTranslations->toArray());
        $dbDepartmentMasterTranslations = $this->departmentMasterTranslationsRepo->find($departmentMasterTranslations->id);
        $this->assertModelData($fakeDepartmentMasterTranslations, $dbDepartmentMasterTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_department_master_translations()
    {
        $departmentMasterTranslations = factory(DepartmentMasterTranslations::class)->create();

        $resp = $this->departmentMasterTranslationsRepo->delete($departmentMasterTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(DepartmentMasterTranslations::find($departmentMasterTranslations->id), 'DepartmentMasterTranslations should not exist in DB');
    }
}
