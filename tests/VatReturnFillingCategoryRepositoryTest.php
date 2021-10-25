<?php namespace Tests\Repositories;

use App\Models\VatReturnFillingCategory;
use App\Repositories\VatReturnFillingCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatReturnFillingCategoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatReturnFillingCategoryRepository
     */
    protected $vatReturnFillingCategoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatReturnFillingCategoryRepo = \App::make(VatReturnFillingCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_return_filling_category()
    {
        $vatReturnFillingCategory = factory(VatReturnFillingCategory::class)->make()->toArray();

        $createdVatReturnFillingCategory = $this->vatReturnFillingCategoryRepo->create($vatReturnFillingCategory);

        $createdVatReturnFillingCategory = $createdVatReturnFillingCategory->toArray();
        $this->assertArrayHasKey('id', $createdVatReturnFillingCategory);
        $this->assertNotNull($createdVatReturnFillingCategory['id'], 'Created VatReturnFillingCategory must have id specified');
        $this->assertNotNull(VatReturnFillingCategory::find($createdVatReturnFillingCategory['id']), 'VatReturnFillingCategory with given id must be in DB');
        $this->assertModelData($vatReturnFillingCategory, $createdVatReturnFillingCategory);
    }

    /**
     * @test read
     */
    public function test_read_vat_return_filling_category()
    {
        $vatReturnFillingCategory = factory(VatReturnFillingCategory::class)->create();

        $dbVatReturnFillingCategory = $this->vatReturnFillingCategoryRepo->find($vatReturnFillingCategory->id);

        $dbVatReturnFillingCategory = $dbVatReturnFillingCategory->toArray();
        $this->assertModelData($vatReturnFillingCategory->toArray(), $dbVatReturnFillingCategory);
    }

    /**
     * @test update
     */
    public function test_update_vat_return_filling_category()
    {
        $vatReturnFillingCategory = factory(VatReturnFillingCategory::class)->create();
        $fakeVatReturnFillingCategory = factory(VatReturnFillingCategory::class)->make()->toArray();

        $updatedVatReturnFillingCategory = $this->vatReturnFillingCategoryRepo->update($fakeVatReturnFillingCategory, $vatReturnFillingCategory->id);

        $this->assertModelData($fakeVatReturnFillingCategory, $updatedVatReturnFillingCategory->toArray());
        $dbVatReturnFillingCategory = $this->vatReturnFillingCategoryRepo->find($vatReturnFillingCategory->id);
        $this->assertModelData($fakeVatReturnFillingCategory, $dbVatReturnFillingCategory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_return_filling_category()
    {
        $vatReturnFillingCategory = factory(VatReturnFillingCategory::class)->create();

        $resp = $this->vatReturnFillingCategoryRepo->delete($vatReturnFillingCategory->id);

        $this->assertTrue($resp);
        $this->assertNull(VatReturnFillingCategory::find($vatReturnFillingCategory->id), 'VatReturnFillingCategory should not exist in DB');
    }
}
