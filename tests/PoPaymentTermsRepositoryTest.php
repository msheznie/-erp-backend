<?php

use App\Models\PoPaymentTerms;
use App\Repositories\PoPaymentTermsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoPaymentTermsRepositoryTest extends TestCase
{
    use MakePoPaymentTermsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoPaymentTermsRepository
     */
    protected $poPaymentTermsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->poPaymentTermsRepo = App::make(PoPaymentTermsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePoPaymentTerms()
    {
        $poPaymentTerms = $this->fakePoPaymentTermsData();
        $createdPoPaymentTerms = $this->poPaymentTermsRepo->create($poPaymentTerms);
        $createdPoPaymentTerms = $createdPoPaymentTerms->toArray();
        $this->assertArrayHasKey('id', $createdPoPaymentTerms);
        $this->assertNotNull($createdPoPaymentTerms['id'], 'Created PoPaymentTerms must have id specified');
        $this->assertNotNull(PoPaymentTerms::find($createdPoPaymentTerms['id']), 'PoPaymentTerms with given id must be in DB');
        $this->assertModelData($poPaymentTerms, $createdPoPaymentTerms);
    }

    /**
     * @test read
     */
    public function testReadPoPaymentTerms()
    {
        $poPaymentTerms = $this->makePoPaymentTerms();
        $dbPoPaymentTerms = $this->poPaymentTermsRepo->find($poPaymentTerms->id);
        $dbPoPaymentTerms = $dbPoPaymentTerms->toArray();
        $this->assertModelData($poPaymentTerms->toArray(), $dbPoPaymentTerms);
    }

    /**
     * @test update
     */
    public function testUpdatePoPaymentTerms()
    {
        $poPaymentTerms = $this->makePoPaymentTerms();
        $fakePoPaymentTerms = $this->fakePoPaymentTermsData();
        $updatedPoPaymentTerms = $this->poPaymentTermsRepo->update($fakePoPaymentTerms, $poPaymentTerms->id);
        $this->assertModelData($fakePoPaymentTerms, $updatedPoPaymentTerms->toArray());
        $dbPoPaymentTerms = $this->poPaymentTermsRepo->find($poPaymentTerms->id);
        $this->assertModelData($fakePoPaymentTerms, $dbPoPaymentTerms->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePoPaymentTerms()
    {
        $poPaymentTerms = $this->makePoPaymentTerms();
        $resp = $this->poPaymentTermsRepo->delete($poPaymentTerms->id);
        $this->assertTrue($resp);
        $this->assertNull(PoPaymentTerms::find($poPaymentTerms->id), 'PoPaymentTerms should not exist in DB');
    }
}
