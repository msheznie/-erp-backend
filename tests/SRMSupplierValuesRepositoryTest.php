<?php namespace Tests\Repositories;

use App\Models\SRMSupplierValues;
use App\Repositories\SRMSupplierValuesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SRMSupplierValuesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SRMSupplierValuesRepository
     */
    protected $sRMSupplierValuesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sRMSupplierValuesRepo = \App::make(SRMSupplierValuesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_r_m_supplier_values()
    {
        $sRMSupplierValues = factory(SRMSupplierValues::class)->make()->toArray();

        $createdSRMSupplierValues = $this->sRMSupplierValuesRepo->create($sRMSupplierValues);

        $createdSRMSupplierValues = $createdSRMSupplierValues->toArray();
        $this->assertArrayHasKey('id', $createdSRMSupplierValues);
        $this->assertNotNull($createdSRMSupplierValues['id'], 'Created SRMSupplierValues must have id specified');
        $this->assertNotNull(SRMSupplierValues::find($createdSRMSupplierValues['id']), 'SRMSupplierValues with given id must be in DB');
        $this->assertModelData($sRMSupplierValues, $createdSRMSupplierValues);
    }

    /**
     * @test read
     */
    public function test_read_s_r_m_supplier_values()
    {
        $sRMSupplierValues = factory(SRMSupplierValues::class)->create();

        $dbSRMSupplierValues = $this->sRMSupplierValuesRepo->find($sRMSupplierValues->id);

        $dbSRMSupplierValues = $dbSRMSupplierValues->toArray();
        $this->assertModelData($sRMSupplierValues->toArray(), $dbSRMSupplierValues);
    }

    /**
     * @test update
     */
    public function test_update_s_r_m_supplier_values()
    {
        $sRMSupplierValues = factory(SRMSupplierValues::class)->create();
        $fakeSRMSupplierValues = factory(SRMSupplierValues::class)->make()->toArray();

        $updatedSRMSupplierValues = $this->sRMSupplierValuesRepo->update($fakeSRMSupplierValues, $sRMSupplierValues->id);

        $this->assertModelData($fakeSRMSupplierValues, $updatedSRMSupplierValues->toArray());
        $dbSRMSupplierValues = $this->sRMSupplierValuesRepo->find($sRMSupplierValues->id);
        $this->assertModelData($fakeSRMSupplierValues, $dbSRMSupplierValues->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_r_m_supplier_values()
    {
        $sRMSupplierValues = factory(SRMSupplierValues::class)->create();

        $resp = $this->sRMSupplierValuesRepo->delete($sRMSupplierValues->id);

        $this->assertTrue($resp);
        $this->assertNull(SRMSupplierValues::find($sRMSupplierValues->id), 'SRMSupplierValues should not exist in DB');
    }
}
