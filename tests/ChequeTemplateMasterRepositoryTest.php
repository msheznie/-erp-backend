<?php namespace Tests\Repositories;

use App\Models\ChequeTemplateMaster;
use App\Repositories\ChequeTemplateMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ChequeTemplateMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChequeTemplateMasterRepository
     */
    protected $chequeTemplateMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->chequeTemplateMasterRepo = \App::make(ChequeTemplateMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cheque_template_master()
    {
        $chequeTemplateMaster = factory(ChequeTemplateMaster::class)->make()->toArray();

        $createdChequeTemplateMaster = $this->chequeTemplateMasterRepo->create($chequeTemplateMaster);

        $createdChequeTemplateMaster = $createdChequeTemplateMaster->toArray();
        $this->assertArrayHasKey('id', $createdChequeTemplateMaster);
        $this->assertNotNull($createdChequeTemplateMaster['id'], 'Created ChequeTemplateMaster must have id specified');
        $this->assertNotNull(ChequeTemplateMaster::find($createdChequeTemplateMaster['id']), 'ChequeTemplateMaster with given id must be in DB');
        $this->assertModelData($chequeTemplateMaster, $createdChequeTemplateMaster);
    }

    /**
     * @test read
     */
    public function test_read_cheque_template_master()
    {
        $chequeTemplateMaster = factory(ChequeTemplateMaster::class)->create();

        $dbChequeTemplateMaster = $this->chequeTemplateMasterRepo->find($chequeTemplateMaster->id);

        $dbChequeTemplateMaster = $dbChequeTemplateMaster->toArray();
        $this->assertModelData($chequeTemplateMaster->toArray(), $dbChequeTemplateMaster);
    }

    /**
     * @test update
     */
    public function test_update_cheque_template_master()
    {
        $chequeTemplateMaster = factory(ChequeTemplateMaster::class)->create();
        $fakeChequeTemplateMaster = factory(ChequeTemplateMaster::class)->make()->toArray();

        $updatedChequeTemplateMaster = $this->chequeTemplateMasterRepo->update($fakeChequeTemplateMaster, $chequeTemplateMaster->id);

        $this->assertModelData($fakeChequeTemplateMaster, $updatedChequeTemplateMaster->toArray());
        $dbChequeTemplateMaster = $this->chequeTemplateMasterRepo->find($chequeTemplateMaster->id);
        $this->assertModelData($fakeChequeTemplateMaster, $dbChequeTemplateMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cheque_template_master()
    {
        $chequeTemplateMaster = factory(ChequeTemplateMaster::class)->create();

        $resp = $this->chequeTemplateMasterRepo->delete($chequeTemplateMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(ChequeTemplateMaster::find($chequeTemplateMaster->id), 'ChequeTemplateMaster should not exist in DB');
    }
}
