<?php namespace Tests\Repositories;

use App\Models\ChequeTemplateBank;
use App\Repositories\ChequeTemplateBankRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ChequeTemplateBankRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ChequeTemplateBankRepository
     */
    protected $chequeTemplateBankRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->chequeTemplateBankRepo = \App::make(ChequeTemplateBankRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_cheque_template_bank()
    {
        $chequeTemplateBank = factory(ChequeTemplateBank::class)->make()->toArray();

        $createdChequeTemplateBank = $this->chequeTemplateBankRepo->create($chequeTemplateBank);

        $createdChequeTemplateBank = $createdChequeTemplateBank->toArray();
        $this->assertArrayHasKey('id', $createdChequeTemplateBank);
        $this->assertNotNull($createdChequeTemplateBank['id'], 'Created ChequeTemplateBank must have id specified');
        $this->assertNotNull(ChequeTemplateBank::find($createdChequeTemplateBank['id']), 'ChequeTemplateBank with given id must be in DB');
        $this->assertModelData($chequeTemplateBank, $createdChequeTemplateBank);
    }

    /**
     * @test read
     */
    public function test_read_cheque_template_bank()
    {
        $chequeTemplateBank = factory(ChequeTemplateBank::class)->create();

        $dbChequeTemplateBank = $this->chequeTemplateBankRepo->find($chequeTemplateBank->id);

        $dbChequeTemplateBank = $dbChequeTemplateBank->toArray();
        $this->assertModelData($chequeTemplateBank->toArray(), $dbChequeTemplateBank);
    }

    /**
     * @test update
     */
    public function test_update_cheque_template_bank()
    {
        $chequeTemplateBank = factory(ChequeTemplateBank::class)->create();
        $fakeChequeTemplateBank = factory(ChequeTemplateBank::class)->make()->toArray();

        $updatedChequeTemplateBank = $this->chequeTemplateBankRepo->update($fakeChequeTemplateBank, $chequeTemplateBank->id);

        $this->assertModelData($fakeChequeTemplateBank, $updatedChequeTemplateBank->toArray());
        $dbChequeTemplateBank = $this->chequeTemplateBankRepo->find($chequeTemplateBank->id);
        $this->assertModelData($fakeChequeTemplateBank, $dbChequeTemplateBank->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_cheque_template_bank()
    {
        $chequeTemplateBank = factory(ChequeTemplateBank::class)->create();

        $resp = $this->chequeTemplateBankRepo->delete($chequeTemplateBank->id);

        $this->assertTrue($resp);
        $this->assertNull(ChequeTemplateBank::find($chequeTemplateBank->id), 'ChequeTemplateBank should not exist in DB');
    }
}
