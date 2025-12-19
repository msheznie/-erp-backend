<?php namespace Tests\Repositories;

use App\Models\VatReturnFilledCategory;
use App\Repositories\VatReturnFilledCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatReturnFilledCategoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatReturnFilledCategoryRepository
     */
    protected $vatReturnFilledCategoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatReturnFilledCategoryRepo = \App::make(VatReturnFilledCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_return_filled_category()
    {
        $vatReturnFilledCategory = factory(VatReturnFilledCategory::class)->make()->toArray();

        $createdVatReturnFilledCategory = $this->vatReturnFilledCategoryRepo->create($vatReturnFilledCategory);

        $createdVatReturnFilledCategory = $createdVatReturnFilledCategory->toArray();
        $this->assertArrayHasKey('id', $createdVatReturnFilledCategory);
        $this->assertNotNull($createdVatReturnFilledCategory['id'], 'Created VatReturnFilledCategory must have id specified');
        $this->assertNotNull(VatReturnFilledCategory::find($createdVatReturnFilledCategory['id']), 'VatReturnFilledCategory with given id must be in DB');
        $this->assertModelData($vatReturnFilledCategory, $createdVatReturnFilledCategory);
    }

    /**
     * @test read
     */
    public function test_read_vat_return_filled_category()
    {
        $vatReturnFilledCategory = factory(VatReturnFilledCategory::class)->create();

        $dbVatReturnFilledCategory = $this->vatReturnFilledCategoryRepo->find($vatReturnFilledCategory->id);

        $dbVatReturnFilledCategory = $dbVatReturnFilledCategory->toArray();
        $this->assertModelData($vatReturnFilledCategory->toArray(), $dbVatReturnFilledCategory);
    }

    /**
     * @test update
     */
    public function test_update_vat_return_filled_category()
    {
        $vatReturnFilledCategory = factory(VatReturnFilledCategory::class)->create();
        $fakeVatReturnFilledCategory = factory(VatReturnFilledCategory::class)->make()->toArray();

        $updatedVatReturnFilledCategory = $this->vatReturnFilledCategoryRepo->update($fakeVatReturnFilledCategory, $vatReturnFilledCategory->id);

        $this->assertModelData($fakeVatReturnFilledCategory, $updatedVatReturnFilledCategory->toArray());
        $dbVatReturnFilledCategory = $this->vatReturnFilledCategoryRepo->find($vatReturnFilledCategory->id);
        $this->assertModelData($fakeVatReturnFilledCategory, $dbVatReturnFilledCategory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_return_filled_category()
    {
        $vatReturnFilledCategory = factory(VatReturnFilledCategory::class)->create();

        $resp = $this->vatReturnFilledCategoryRepo->delete($vatReturnFilledCategory->id);

        $this->assertTrue($resp);
        $this->assertNull(VatReturnFilledCategory::find($vatReturnFilledCategory->id), 'VatReturnFilledCategory should not exist in DB');
    }
}
