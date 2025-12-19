<?php namespace Tests\Repositories;

use App\Models\SalaryProcessDetail;
use App\Repositories\SalaryProcessDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeSalaryProcessDetailTrait;
use Tests\ApiTestTrait;

class SalaryProcessDetailRepositoryTest extends TestCase
{
    use MakeSalaryProcessDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var SalaryProcessDetailRepository
     */
    protected $salaryProcessDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->salaryProcessDetailRepo = \App::make(SalaryProcessDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_salary_process_detail()
    {
        $salaryProcessDetail = $this->fakeSalaryProcessDetailData();
        $createdSalaryProcessDetail = $this->salaryProcessDetailRepo->create($salaryProcessDetail);
        $createdSalaryProcessDetail = $createdSalaryProcessDetail->toArray();
        $this->assertArrayHasKey('id', $createdSalaryProcessDetail);
        $this->assertNotNull($createdSalaryProcessDetail['id'], 'Created SalaryProcessDetail must have id specified');
        $this->assertNotNull(SalaryProcessDetail::find($createdSalaryProcessDetail['id']), 'SalaryProcessDetail with given id must be in DB');
        $this->assertModelData($salaryProcessDetail, $createdSalaryProcessDetail);
    }

    /**
     * @test read
     */
    public function test_read_salary_process_detail()
    {
        $salaryProcessDetail = $this->makeSalaryProcessDetail();
        $dbSalaryProcessDetail = $this->salaryProcessDetailRepo->find($salaryProcessDetail->id);
        $dbSalaryProcessDetail = $dbSalaryProcessDetail->toArray();
        $this->assertModelData($salaryProcessDetail->toArray(), $dbSalaryProcessDetail);
    }

    /**
     * @test update
     */
    public function test_update_salary_process_detail()
    {
        $salaryProcessDetail = $this->makeSalaryProcessDetail();
        $fakeSalaryProcessDetail = $this->fakeSalaryProcessDetailData();
        $updatedSalaryProcessDetail = $this->salaryProcessDetailRepo->update($fakeSalaryProcessDetail, $salaryProcessDetail->id);
        $this->assertModelData($fakeSalaryProcessDetail, $updatedSalaryProcessDetail->toArray());
        $dbSalaryProcessDetail = $this->salaryProcessDetailRepo->find($salaryProcessDetail->id);
        $this->assertModelData($fakeSalaryProcessDetail, $dbSalaryProcessDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_salary_process_detail()
    {
        $salaryProcessDetail = $this->makeSalaryProcessDetail();
        $resp = $this->salaryProcessDetailRepo->delete($salaryProcessDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(SalaryProcessDetail::find($salaryProcessDetail->id), 'SalaryProcessDetail should not exist in DB');
    }
}
