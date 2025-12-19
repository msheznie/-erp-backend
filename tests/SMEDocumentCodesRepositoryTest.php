<?php namespace Tests\Repositories;

use App\Models\SMEDocumentCodes;
use App\Repositories\SMEDocumentCodesRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SMEDocumentCodesRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SMEDocumentCodesRepository
     */
    protected $sMEDocumentCodesRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->sMEDocumentCodesRepo = \App::make(SMEDocumentCodesRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_s_m_e_document_codes()
    {
        $sMEDocumentCodes = factory(SMEDocumentCodes::class)->make()->toArray();

        $createdSMEDocumentCodes = $this->sMEDocumentCodesRepo->create($sMEDocumentCodes);

        $createdSMEDocumentCodes = $createdSMEDocumentCodes->toArray();
        $this->assertArrayHasKey('id', $createdSMEDocumentCodes);
        $this->assertNotNull($createdSMEDocumentCodes['id'], 'Created SMEDocumentCodes must have id specified');
        $this->assertNotNull(SMEDocumentCodes::find($createdSMEDocumentCodes['id']), 'SMEDocumentCodes with given id must be in DB');
        $this->assertModelData($sMEDocumentCodes, $createdSMEDocumentCodes);
    }

    /**
     * @test read
     */
    public function test_read_s_m_e_document_codes()
    {
        $sMEDocumentCodes = factory(SMEDocumentCodes::class)->create();

        $dbSMEDocumentCodes = $this->sMEDocumentCodesRepo->find($sMEDocumentCodes->id);

        $dbSMEDocumentCodes = $dbSMEDocumentCodes->toArray();
        $this->assertModelData($sMEDocumentCodes->toArray(), $dbSMEDocumentCodes);
    }

    /**
     * @test update
     */
    public function test_update_s_m_e_document_codes()
    {
        $sMEDocumentCodes = factory(SMEDocumentCodes::class)->create();
        $fakeSMEDocumentCodes = factory(SMEDocumentCodes::class)->make()->toArray();

        $updatedSMEDocumentCodes = $this->sMEDocumentCodesRepo->update($fakeSMEDocumentCodes, $sMEDocumentCodes->id);

        $this->assertModelData($fakeSMEDocumentCodes, $updatedSMEDocumentCodes->toArray());
        $dbSMEDocumentCodes = $this->sMEDocumentCodesRepo->find($sMEDocumentCodes->id);
        $this->assertModelData($fakeSMEDocumentCodes, $dbSMEDocumentCodes->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_s_m_e_document_codes()
    {
        $sMEDocumentCodes = factory(SMEDocumentCodes::class)->create();

        $resp = $this->sMEDocumentCodesRepo->delete($sMEDocumentCodes->id);

        $this->assertTrue($resp);
        $this->assertNull(SMEDocumentCodes::find($sMEDocumentCodes->id), 'SMEDocumentCodes should not exist in DB');
    }
}
