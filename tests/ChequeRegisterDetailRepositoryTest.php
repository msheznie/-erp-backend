<?php namespace Tests\Repositories;

use App\Models\ChequeRegisterDetail;
use App\Repositories\ChequeRegisterDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeChequeRegisterDetailTrait;
use Tests\ApiTestTrait;

class ChequeRegisterDetailRepositoryTest extends TestCase
{
    use MakeChequeRegisterDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChequeRegisterDetailRepository
     */
    protected $chequeRegisterDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->chequeRegisterDetailRepo = \App::make(ChequeRegisterDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cheque_register_detail()
    {
        $chequeRegisterDetail = $this->fakeChequeRegisterDetailData();
        $createdChequeRegisterDetail = $this->chequeRegisterDetailRepo->create($chequeRegisterDetail);
        $createdChequeRegisterDetail = $createdChequeRegisterDetail->toArray();
        $this->assertArrayHasKey('id', $createdChequeRegisterDetail);
        $this->assertNotNull($createdChequeRegisterDetail['id'], 'Created ChequeRegisterDetail must have id specified');
        $this->assertNotNull(ChequeRegisterDetail::find($createdChequeRegisterDetail['id']), 'ChequeRegisterDetail with given id must be in DB');
        $this->assertModelData($chequeRegisterDetail, $createdChequeRegisterDetail);
    }

    /**
     * @test read
     */
    public function test_read_cheque_register_detail()
    {
        $chequeRegisterDetail = $this->makeChequeRegisterDetail();
        $dbChequeRegisterDetail = $this->chequeRegisterDetailRepo->find($chequeRegisterDetail->id);
        $dbChequeRegisterDetail = $dbChequeRegisterDetail->toArray();
        $this->assertModelData($chequeRegisterDetail->toArray(), $dbChequeRegisterDetail);
    }

    /**
     * @test update
     */
    public function test_update_cheque_register_detail()
    {
        $chequeRegisterDetail = $this->makeChequeRegisterDetail();
        $fakeChequeRegisterDetail = $this->fakeChequeRegisterDetailData();
        $updatedChequeRegisterDetail = $this->chequeRegisterDetailRepo->update($fakeChequeRegisterDetail, $chequeRegisterDetail->id);
        $this->assertModelData($fakeChequeRegisterDetail, $updatedChequeRegisterDetail->toArray());
        $dbChequeRegisterDetail = $this->chequeRegisterDetailRepo->find($chequeRegisterDetail->id);
        $this->assertModelData($fakeChequeRegisterDetail, $dbChequeRegisterDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cheque_register_detail()
    {
        $chequeRegisterDetail = $this->makeChequeRegisterDetail();
        $resp = $this->chequeRegisterDetailRepo->delete($chequeRegisterDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(ChequeRegisterDetail::find($chequeRegisterDetail->id), 'ChequeRegisterDetail should not exist in DB');
    }
}
