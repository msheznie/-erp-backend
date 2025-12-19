<?php namespace Tests\Repositories;

use App\Models\SoPaymentTerms;
use App\Repositories\SoPaymentTermsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SoPaymentTermsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SoPaymentTermsRepository
     */
    protected $soPaymentTermsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->soPaymentTermsRepo = \App::make(SoPaymentTermsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_so_payment_terms()
    {
        $soPaymentTerms = factory(SoPaymentTerms::class)->make()->toArray();

        $createdSoPaymentTerms = $this->soPaymentTermsRepo->create($soPaymentTerms);

        $createdSoPaymentTerms = $createdSoPaymentTerms->toArray();
        $this->assertArrayHasKey('id', $createdSoPaymentTerms);
        $this->assertNotNull($createdSoPaymentTerms['id'], 'Created SoPaymentTerms must have id specified');
        $this->assertNotNull(SoPaymentTerms::find($createdSoPaymentTerms['id']), 'SoPaymentTerms with given id must be in DB');
        $this->assertModelData($soPaymentTerms, $createdSoPaymentTerms);
    }

    /**
     * @test read
     */
    public function test_read_so_payment_terms()
    {
        $soPaymentTerms = factory(SoPaymentTerms::class)->create();

        $dbSoPaymentTerms = $this->soPaymentTermsRepo->find($soPaymentTerms->id);

        $dbSoPaymentTerms = $dbSoPaymentTerms->toArray();
        $this->assertModelData($soPaymentTerms->toArray(), $dbSoPaymentTerms);
    }

    /**
     * @test update
     */
    public function test_update_so_payment_terms()
    {
        $soPaymentTerms = factory(SoPaymentTerms::class)->create();
        $fakeSoPaymentTerms = factory(SoPaymentTerms::class)->make()->toArray();

        $updatedSoPaymentTerms = $this->soPaymentTermsRepo->update($fakeSoPaymentTerms, $soPaymentTerms->id);

        $this->assertModelData($fakeSoPaymentTerms, $updatedSoPaymentTerms->toArray());
        $dbSoPaymentTerms = $this->soPaymentTermsRepo->find($soPaymentTerms->id);
        $this->assertModelData($fakeSoPaymentTerms, $dbSoPaymentTerms->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_so_payment_terms()
    {
        $soPaymentTerms = factory(SoPaymentTerms::class)->create();

        $resp = $this->soPaymentTermsRepo->delete($soPaymentTerms->id);

        $this->assertTrue($resp);
        $this->assertNull(SoPaymentTerms::find($soPaymentTerms->id), 'SoPaymentTerms should not exist in DB');
    }
}
