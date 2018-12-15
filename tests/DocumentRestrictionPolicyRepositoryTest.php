<?php

use App\Models\DocumentRestrictionPolicy;
use App\Repositories\DocumentRestrictionPolicyRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DocumentRestrictionPolicyRepositoryTest extends TestCase
{
    use MakeDocumentRestrictionPolicyTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DocumentRestrictionPolicyRepository
     */
    protected $documentRestrictionPolicyRepo;

    public function setUp()
    {
        parent::setUp();
        $this->documentRestrictionPolicyRepo = App::make(DocumentRestrictionPolicyRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDocumentRestrictionPolicy()
    {
        $documentRestrictionPolicy = $this->fakeDocumentRestrictionPolicyData();
        $createdDocumentRestrictionPolicy = $this->documentRestrictionPolicyRepo->create($documentRestrictionPolicy);
        $createdDocumentRestrictionPolicy = $createdDocumentRestrictionPolicy->toArray();
        $this->assertArrayHasKey('id', $createdDocumentRestrictionPolicy);
        $this->assertNotNull($createdDocumentRestrictionPolicy['id'], 'Created DocumentRestrictionPolicy must have id specified');
        $this->assertNotNull(DocumentRestrictionPolicy::find($createdDocumentRestrictionPolicy['id']), 'DocumentRestrictionPolicy with given id must be in DB');
        $this->assertModelData($documentRestrictionPolicy, $createdDocumentRestrictionPolicy);
    }

    /**
     * @test read
     */
    public function testReadDocumentRestrictionPolicy()
    {
        $documentRestrictionPolicy = $this->makeDocumentRestrictionPolicy();
        $dbDocumentRestrictionPolicy = $this->documentRestrictionPolicyRepo->find($documentRestrictionPolicy->id);
        $dbDocumentRestrictionPolicy = $dbDocumentRestrictionPolicy->toArray();
        $this->assertModelData($documentRestrictionPolicy->toArray(), $dbDocumentRestrictionPolicy);
    }

    /**
     * @test update
     */
    public function testUpdateDocumentRestrictionPolicy()
    {
        $documentRestrictionPolicy = $this->makeDocumentRestrictionPolicy();
        $fakeDocumentRestrictionPolicy = $this->fakeDocumentRestrictionPolicyData();
        $updatedDocumentRestrictionPolicy = $this->documentRestrictionPolicyRepo->update($fakeDocumentRestrictionPolicy, $documentRestrictionPolicy->id);
        $this->assertModelData($fakeDocumentRestrictionPolicy, $updatedDocumentRestrictionPolicy->toArray());
        $dbDocumentRestrictionPolicy = $this->documentRestrictionPolicyRepo->find($documentRestrictionPolicy->id);
        $this->assertModelData($fakeDocumentRestrictionPolicy, $dbDocumentRestrictionPolicy->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDocumentRestrictionPolicy()
    {
        $documentRestrictionPolicy = $this->makeDocumentRestrictionPolicy();
        $resp = $this->documentRestrictionPolicyRepo->delete($documentRestrictionPolicy->id);
        $this->assertTrue($resp);
        $this->assertNull(DocumentRestrictionPolicy::find($documentRestrictionPolicy->id), 'DocumentRestrictionPolicy should not exist in DB');
    }
}
