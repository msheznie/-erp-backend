<?php

use App\Models\QuotationMasterVersion;
use App\Repositories\QuotationMasterVersionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class QuotationMasterVersionRepositoryTest extends TestCase
{
    use MakeQuotationMasterVersionTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var QuotationMasterVersionRepository
     */
    protected $quotationMasterVersionRepo;

    public function setUp()
    {
        parent::setUp();
        $this->quotationMasterVersionRepo = App::make(QuotationMasterVersionRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateQuotationMasterVersion()
    {
        $quotationMasterVersion = $this->fakeQuotationMasterVersionData();
        $createdQuotationMasterVersion = $this->quotationMasterVersionRepo->create($quotationMasterVersion);
        $createdQuotationMasterVersion = $createdQuotationMasterVersion->toArray();
        $this->assertArrayHasKey('id', $createdQuotationMasterVersion);
        $this->assertNotNull($createdQuotationMasterVersion['id'], 'Created QuotationMasterVersion must have id specified');
        $this->assertNotNull(QuotationMasterVersion::find($createdQuotationMasterVersion['id']), 'QuotationMasterVersion with given id must be in DB');
        $this->assertModelData($quotationMasterVersion, $createdQuotationMasterVersion);
    }

    /**
     * @test read
     */
    public function testReadQuotationMasterVersion()
    {
        $quotationMasterVersion = $this->makeQuotationMasterVersion();
        $dbQuotationMasterVersion = $this->quotationMasterVersionRepo->find($quotationMasterVersion->id);
        $dbQuotationMasterVersion = $dbQuotationMasterVersion->toArray();
        $this->assertModelData($quotationMasterVersion->toArray(), $dbQuotationMasterVersion);
    }

    /**
     * @test update
     */
    public function testUpdateQuotationMasterVersion()
    {
        $quotationMasterVersion = $this->makeQuotationMasterVersion();
        $fakeQuotationMasterVersion = $this->fakeQuotationMasterVersionData();
        $updatedQuotationMasterVersion = $this->quotationMasterVersionRepo->update($fakeQuotationMasterVersion, $quotationMasterVersion->id);
        $this->assertModelData($fakeQuotationMasterVersion, $updatedQuotationMasterVersion->toArray());
        $dbQuotationMasterVersion = $this->quotationMasterVersionRepo->find($quotationMasterVersion->id);
        $this->assertModelData($fakeQuotationMasterVersion, $dbQuotationMasterVersion->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteQuotationMasterVersion()
    {
        $quotationMasterVersion = $this->makeQuotationMasterVersion();
        $resp = $this->quotationMasterVersionRepo->delete($quotationMasterVersion->id);
        $this->assertTrue($resp);
        $this->assertNull(QuotationMasterVersion::find($quotationMasterVersion->id), 'QuotationMasterVersion should not exist in DB');
    }
}
