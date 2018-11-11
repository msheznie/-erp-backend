<?php

use App\Models\SalaryProcessMaster;
use App\Repositories\SalaryProcessMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class SalaryProcessMasterRepositoryTest extends TestCase
{
    use MakeSalaryProcessMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalaryProcessMasterRepository
     */
    protected $salaryProcessMasterRepo;

    public function setUp()
    {
        parent::setUp();
        $this->salaryProcessMasterRepo = App::make(SalaryProcessMasterRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateSalaryProcessMaster()
    {
        $salaryProcessMaster = $this->fakeSalaryProcessMasterData();
        $createdSalaryProcessMaster = $this->salaryProcessMasterRepo->create($salaryProcessMaster);
        $createdSalaryProcessMaster = $createdSalaryProcessMaster->toArray();
        $this->assertArrayHasKey('id', $createdSalaryProcessMaster);
        $this->assertNotNull($createdSalaryProcessMaster['id'], 'Created SalaryProcessMaster must have id specified');
        $this->assertNotNull(SalaryProcessMaster::find($createdSalaryProcessMaster['id']), 'SalaryProcessMaster with given id must be in DB');
        $this->assertModelData($salaryProcessMaster, $createdSalaryProcessMaster);
    }

    /**
     * @test read
     */
    public function testReadSalaryProcessMaster()
    {
        $salaryProcessMaster = $this->makeSalaryProcessMaster();
        $dbSalaryProcessMaster = $this->salaryProcessMasterRepo->find($salaryProcessMaster->id);
        $dbSalaryProcessMaster = $dbSalaryProcessMaster->toArray();
        $this->assertModelData($salaryProcessMaster->toArray(), $dbSalaryProcessMaster);
    }

    /**
     * @test update
     */
    public function testUpdateSalaryProcessMaster()
    {
        $salaryProcessMaster = $this->makeSalaryProcessMaster();
        $fakeSalaryProcessMaster = $this->fakeSalaryProcessMasterData();
        $updatedSalaryProcessMaster = $this->salaryProcessMasterRepo->update($fakeSalaryProcessMaster, $salaryProcessMaster->id);
        $this->assertModelData($fakeSalaryProcessMaster, $updatedSalaryProcessMaster->toArray());
        $dbSalaryProcessMaster = $this->salaryProcessMasterRepo->find($salaryProcessMaster->id);
        $this->assertModelData($fakeSalaryProcessMaster, $dbSalaryProcessMaster->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteSalaryProcessMaster()
    {
        $salaryProcessMaster = $this->makeSalaryProcessMaster();
        $resp = $this->salaryProcessMasterRepo->delete($salaryProcessMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(SalaryProcessMaster::find($salaryProcessMaster->id), 'SalaryProcessMaster should not exist in DB');
    }
}
