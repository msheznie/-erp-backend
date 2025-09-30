<?php namespace Tests\Repositories;

use App\Models\ExchangeSetupDocumentTypeTranslations;
use App\Repositories\ExchangeSetupDocumentTypeTranslationsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class ExchangeSetupDocumentTypeTranslationsRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var ExchangeSetupDocumentTypeTranslationsRepository
     */
    protected $exchangeSetupDocumentTypeTranslationsRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->exchangeSetupDocumentTypeTranslationsRepo = \App::make(ExchangeSetupDocumentTypeTranslationsRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_exchange_setup_document_type_translations()
    {
        $exchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->make()->toArray();

        $createdExchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepo->create($exchangeSetupDocumentTypeTranslations);

        $createdExchangeSetupDocumentTypeTranslations = $createdExchangeSetupDocumentTypeTranslations->toArray();
        $this->assertArrayHasKey('id', $createdExchangeSetupDocumentTypeTranslations);
        $this->assertNotNull($createdExchangeSetupDocumentTypeTranslations['id'], 'Created ExchangeSetupDocumentTypeTranslations must have id specified');
        $this->assertNotNull(ExchangeSetupDocumentTypeTranslations::find($createdExchangeSetupDocumentTypeTranslations['id']), 'ExchangeSetupDocumentTypeTranslations with given id must be in DB');
        $this->assertModelData($exchangeSetupDocumentTypeTranslations, $createdExchangeSetupDocumentTypeTranslations);
    }

    /**
     * @test read
     */
    public function test_read_exchange_setup_document_type_translations()
    {
        $exchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->create();

        $dbExchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepo->find($exchangeSetupDocumentTypeTranslations->id);

        $dbExchangeSetupDocumentTypeTranslations = $dbExchangeSetupDocumentTypeTranslations->toArray();
        $this->assertModelData($exchangeSetupDocumentTypeTranslations->toArray(), $dbExchangeSetupDocumentTypeTranslations);
    }

    /**
     * @test update
     */
    public function test_update_exchange_setup_document_type_translations()
    {
        $exchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->create();
        $fakeExchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->make()->toArray();

        $updatedExchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepo->update($fakeExchangeSetupDocumentTypeTranslations, $exchangeSetupDocumentTypeTranslations->id);

        $this->assertModelData($fakeExchangeSetupDocumentTypeTranslations, $updatedExchangeSetupDocumentTypeTranslations->toArray());
        $dbExchangeSetupDocumentTypeTranslations = $this->exchangeSetupDocumentTypeTranslationsRepo->find($exchangeSetupDocumentTypeTranslations->id);
        $this->assertModelData($fakeExchangeSetupDocumentTypeTranslations, $dbExchangeSetupDocumentTypeTranslations->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_exchange_setup_document_type_translations()
    {
        $exchangeSetupDocumentTypeTranslations = factory(ExchangeSetupDocumentTypeTranslations::class)->create();

        $resp = $this->exchangeSetupDocumentTypeTranslationsRepo->delete($exchangeSetupDocumentTypeTranslations->id);

        $this->assertTrue($resp);
        $this->assertNull(ExchangeSetupDocumentTypeTranslations::find($exchangeSetupDocumentTypeTranslations->id), 'ExchangeSetupDocumentTypeTranslations should not exist in DB');
    }
}
