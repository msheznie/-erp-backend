<?php

use App\Models\SalaryProcessEmploymentTypes;
use App\Repositories\SalaryProcessEmploymentTypesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalaryProcessEmploymentTypesRepositoryTest extends TestCase
{
    use MakeSalaryProcessEmploymentTypesTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalaryProcessEmploymentTypesRepository
     */
    protected $salaryProcessEmploymentTypesRepo;

    public function setUp()
    {
        parent::setUp();
        $this->salaryProcessEmploymentTypesRepo = App::make(SalaryProcessEmploymentTypesRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSalaryProcessEmploymentTypes()
    {
        $salaryProcessEmploymentTypes = $this->fakeSalaryProcessEmploymentTypesData();
        $createdSalaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepo->create($salaryProcessEmploymentTypes);
        $createdSalaryProcessEmploymentTypes = $createdSalaryProcessEmploymentTypes->toArray();
        $this->assertArrayHasKey('id', $createdSalaryProcessEmploymentTypes);
        $this->assertNotNull($createdSalaryProcessEmploymentTypes['id'], 'Created SalaryProcessEmploymentTypes must have id specified');
        $this->assertNotNull(SalaryProcessEmploymentTypes::find($createdSalaryProcessEmploymentTypes['id']), 'SalaryProcessEmploymentTypes with given id must be in DB');
        $this->assertModelData($salaryProcessEmploymentTypes, $createdSalaryProcessEmploymentTypes);
    }

    /**
     * @test read
     */
    public function testReadSalaryProcessEmploymentTypes()
    {
        $salaryProcessEmploymentTypes = $this->makeSalaryProcessEmploymentTypes();
        $dbSalaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepo->find($salaryProcessEmploymentTypes->id);
        $dbSalaryProcessEmploymentTypes = $dbSalaryProcessEmploymentTypes->toArray();
        $this->assertModelData($salaryProcessEmploymentTypes->toArray(), $dbSalaryProcessEmploymentTypes);
    }

    /**
     * @test update
     */
    public function testUpdateSalaryProcessEmploymentTypes()
    {
        $salaryProcessEmploymentTypes = $this->makeSalaryProcessEmploymentTypes();
        $fakeSalaryProcessEmploymentTypes = $this->fakeSalaryProcessEmploymentTypesData();
        $updatedSalaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepo->update($fakeSalaryProcessEmploymentTypes, $salaryProcessEmploymentTypes->id);
        $this->assertModelData($fakeSalaryProcessEmploymentTypes, $updatedSalaryProcessEmploymentTypes->toArray());
        $dbSalaryProcessEmploymentTypes = $this->salaryProcessEmploymentTypesRepo->find($salaryProcessEmploymentTypes->id);
        $this->assertModelData($fakeSalaryProcessEmploymentTypes, $dbSalaryProcessEmploymentTypes->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSalaryProcessEmploymentTypes()
    {
        $salaryProcessEmploymentTypes = $this->makeSalaryProcessEmploymentTypes();
        $resp = $this->salaryProcessEmploymentTypesRepo->delete($salaryProcessEmploymentTypes->id);
        $this->assertTrue($resp);
        $this->assertNull(SalaryProcessEmploymentTypes::find($salaryProcessEmploymentTypes->id), 'SalaryProcessEmploymentTypes should not exist in DB');
    }
}
