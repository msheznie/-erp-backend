<?php namespace Tests\Repositories;

use App\Models\VatReturnFillingCategoryLanguage;
use App\Repositories\VatReturnFillingCategoryLanguageRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatReturnFillingCategoryLanguageRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatReturnFillingCategoryLanguageRepository
     */
    protected $vatReturnFillingCategoryLanguageRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatReturnFillingCategoryLanguageRepo = \App::make(VatReturnFillingCategoryLanguageRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_return_filling_category_language()
    {
        $vatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->make()->toArray();

        $createdVatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepo->create($vatReturnFillingCategoryLanguage);

        $createdVatReturnFillingCategoryLanguage = $createdVatReturnFillingCategoryLanguage->toArray();
        $this->assertArrayHasKey('id', $createdVatReturnFillingCategoryLanguage);
        $this->assertNotNull($createdVatReturnFillingCategoryLanguage['id'], 'Created VatReturnFillingCategoryLanguage must have id specified');
        $this->assertNotNull(VatReturnFillingCategoryLanguage::find($createdVatReturnFillingCategoryLanguage['id']), 'VatReturnFillingCategoryLanguage with given id must be in DB');
        $this->assertModelData($vatReturnFillingCategoryLanguage, $createdVatReturnFillingCategoryLanguage);
    }

    /**
     * @test read
     */
    public function test_read_vat_return_filling_category_language()
    {
        $vatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->create();

        $dbVatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepo->find($vatReturnFillingCategoryLanguage->id);

        $dbVatReturnFillingCategoryLanguage = $dbVatReturnFillingCategoryLanguage->toArray();
        $this->assertModelData($vatReturnFillingCategoryLanguage->toArray(), $dbVatReturnFillingCategoryLanguage);
    }

    /**
     * @test update
     */
    public function test_update_vat_return_filling_category_language()
    {
        $vatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->create();
        $fakeVatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->make()->toArray();

        $updatedVatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepo->update($fakeVatReturnFillingCategoryLanguage, $vatReturnFillingCategoryLanguage->id);

        $this->assertModelData($fakeVatReturnFillingCategoryLanguage, $updatedVatReturnFillingCategoryLanguage->toArray());
        $dbVatReturnFillingCategoryLanguage = $this->vatReturnFillingCategoryLanguageRepo->find($vatReturnFillingCategoryLanguage->id);
        $this->assertModelData($fakeVatReturnFillingCategoryLanguage, $dbVatReturnFillingCategoryLanguage->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_return_filling_category_language()
    {
        $vatReturnFillingCategoryLanguage = factory(VatReturnFillingCategoryLanguage::class)->create();

        $resp = $this->vatReturnFillingCategoryLanguageRepo->delete($vatReturnFillingCategoryLanguage->id);

        $this->assertTrue($resp);
        $this->assertNull(VatReturnFillingCategoryLanguage::find($vatReturnFillingCategoryLanguage->id), 'VatReturnFillingCategoryLanguage should not exist in DB');
    }
}
