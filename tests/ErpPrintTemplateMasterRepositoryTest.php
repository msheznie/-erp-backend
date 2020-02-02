<?php namespace Tests\Repositories;

use App\Models\ErpPrintTemplateMaster;
use App\Repositories\ErpPrintTemplateMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeErpPrintTemplateMasterTrait;
use Tests\ApiTestTrait;

class ErpPrintTemplateMasterRepositoryTest extends TestCase
{
    use MakeErpPrintTemplateMasterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ErpPrintTemplateMasterRepository
     */
    protected $erpPrintTemplateMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->erpPrintTemplateMasterRepo = \App::make(ErpPrintTemplateMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_erp_print_template_master()
    {
        $erpPrintTemplateMaster = $this->fakeErpPrintTemplateMasterData();
        $createdErpPrintTemplateMaster = $this->erpPrintTemplateMasterRepo->create($erpPrintTemplateMaster);
        $createdErpPrintTemplateMaster = $createdErpPrintTemplateMaster->toArray();
        $this->assertArrayHasKey('id', $createdErpPrintTemplateMaster);
        $this->assertNotNull($createdErpPrintTemplateMaster['id'], 'Created ErpPrintTemplateMaster must have id specified');
        $this->assertNotNull(ErpPrintTemplateMaster::find($createdErpPrintTemplateMaster['id']), 'ErpPrintTemplateMaster with given id must be in DB');
        $this->assertModelData($erpPrintTemplateMaster, $createdErpPrintTemplateMaster);
    }

    /**
     * @test read
     */
    public function test_read_erp_print_template_master()
    {
        $erpPrintTemplateMaster = $this->makeErpPrintTemplateMaster();
        $dbErpPrintTemplateMaster = $this->erpPrintTemplateMasterRepo->find($erpPrintTemplateMaster->id);
        $dbErpPrintTemplateMaster = $dbErpPrintTemplateMaster->toArray();
        $this->assertModelData($erpPrintTemplateMaster->toArray(), $dbErpPrintTemplateMaster);
    }

    /**
     * @test update
     */
    public function test_update_erp_print_template_master()
    {
        $erpPrintTemplateMaster = $this->makeErpPrintTemplateMaster();
        $fakeErpPrintTemplateMaster = $this->fakeErpPrintTemplateMasterData();
        $updatedErpPrintTemplateMaster = $this->erpPrintTemplateMasterRepo->update($fakeErpPrintTemplateMaster, $erpPrintTemplateMaster->id);
        $this->assertModelData($fakeErpPrintTemplateMaster, $updatedErpPrintTemplateMaster->toArray());
        $dbErpPrintTemplateMaster = $this->erpPrintTemplateMasterRepo->find($erpPrintTemplateMaster->id);
        $this->assertModelData($fakeErpPrintTemplateMaster, $dbErpPrintTemplateMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_erp_print_template_master()
    {
        $erpPrintTemplateMaster = $this->makeErpPrintTemplateMaster();
        $resp = $this->erpPrintTemplateMasterRepo->delete($erpPrintTemplateMaster->id);
        $this->assertTrue($resp);
        $this->assertNull(ErpPrintTemplateMaster::find($erpPrintTemplateMaster->id), 'ErpPrintTemplateMaster should not exist in DB');
    }
}
