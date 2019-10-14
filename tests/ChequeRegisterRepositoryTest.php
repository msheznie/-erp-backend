<?php namespace Tests\Repositories;

use App\Models\ChequeRegister;
use App\Repositories\ChequeRegisterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChequeRegisterTrait;
use Tests\ApiTestTrait;

class ChequeRegisterRepositoryTest extends TestCase
{
    use MakeChequeRegisterTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChequeRegisterRepository
     */
    protected $chequeRegisterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->chequeRegisterRepo = \App::make(ChequeRegisterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cheque_register()
    {
        $chequeRegister = $this->fakeChequeRegisterData();
        $createdChequeRegister = $this->chequeRegisterRepo->create($chequeRegister);
        $createdChequeRegister = $createdChequeRegister->toArray();
        $this->assertArrayHasKey('id', $createdChequeRegister);
        $this->assertNotNull($createdChequeRegister['id'], 'Created ChequeRegister must have id specified');
        $this->assertNotNull(ChequeRegister::find($createdChequeRegister['id']), 'ChequeRegister with given id must be in DB');
        $this->assertModelData($chequeRegister, $createdChequeRegister);
    }

    /**
     * @test read
     */
    public function test_read_cheque_register()
    {
        $chequeRegister = $this->makeChequeRegister();
        $dbChequeRegister = $this->chequeRegisterRepo->find($chequeRegister->id);
        $dbChequeRegister = $dbChequeRegister->toArray();
        $this->assertModelData($chequeRegister->toArray(), $dbChequeRegister);
    }

    /**
     * @test update
     */
    public function test_update_cheque_register()
    {
        $chequeRegister = $this->makeChequeRegister();
        $fakeChequeRegister = $this->fakeChequeRegisterData();
        $updatedChequeRegister = $this->chequeRegisterRepo->update($fakeChequeRegister, $chequeRegister->id);
        $this->assertModelData($fakeChequeRegister, $updatedChequeRegister->toArray());
        $dbChequeRegister = $this->chequeRegisterRepo->find($chequeRegister->id);
        $this->assertModelData($fakeChequeRegister, $dbChequeRegister->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cheque_register()
    {
        $chequeRegister = $this->makeChequeRegister();
        $resp = $this->chequeRegisterRepo->delete($chequeRegister->id);
        $this->assertTrue($resp);
        $this->assertNull(ChequeRegister::find($chequeRegister->id), 'ChequeRegister should not exist in DB');
    }
}
