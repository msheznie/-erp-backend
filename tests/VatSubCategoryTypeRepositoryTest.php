<?php namespace Tests\Repositories;

use App\Models\VatSubCategoryType;
use App\Repositories\VatSubCategoryTypeRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatSubCategoryTypeRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatSubCategoryTypeRepository
     */
    protected $vatSubCategoryTypeRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatSubCategoryTypeRepo = \App::make(VatSubCategoryTypeRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_sub_category_type()
    {
        $vatSubCategoryType = factory(VatSubCategoryType::class)->make()->toArray();

        $createdVatSubCategoryType = $this->vatSubCategoryTypeRepo->create($vatSubCategoryType);

        $createdVatSubCategoryType = $createdVatSubCategoryType->toArray();
        $this->assertArrayHasKey('id', $createdVatSubCategoryType);
        $this->assertNotNull($createdVatSubCategoryType['id'], 'Created VatSubCategoryType must have id specified');
        $this->assertNotNull(VatSubCategoryType::find($createdVatSubCategoryType['id']), 'VatSubCategoryType with given id must be in DB');
        $this->assertModelData($vatSubCategoryType, $createdVatSubCategoryType);
    }

    /**
     * @test read
     */
    public function test_read_vat_sub_category_type()
    {
        $vatSubCategoryType = factory(VatSubCategoryType::class)->create();

        $dbVatSubCategoryType = $this->vatSubCategoryTypeRepo->find($vatSubCategoryType->id);

        $dbVatSubCategoryType = $dbVatSubCategoryType->toArray();
        $this->assertModelData($vatSubCategoryType->toArray(), $dbVatSubCategoryType);
    }

    /**
     * @test update
     */
    public function test_update_vat_sub_category_type()
    {
        $vatSubCategoryType = factory(VatSubCategoryType::class)->create();
        $fakeVatSubCategoryType = factory(VatSubCategoryType::class)->make()->toArray();

        $updatedVatSubCategoryType = $this->vatSubCategoryTypeRepo->update($fakeVatSubCategoryType, $vatSubCategoryType->id);

        $this->assertModelData($fakeVatSubCategoryType, $updatedVatSubCategoryType->toArray());
        $dbVatSubCategoryType = $this->vatSubCategoryTypeRepo->find($vatSubCategoryType->id);
        $this->assertModelData($fakeVatSubCategoryType, $dbVatSubCategoryType->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_sub_category_type()
    {
        $vatSubCategoryType = factory(VatSubCategoryType::class)->create();

        $resp = $this->vatSubCategoryTypeRepo->delete($vatSubCategoryType->id);

        $this->assertTrue($resp);
        $this->assertNull(VatSubCategoryType::find($vatSubCategoryType->id), 'VatSubCategoryType should not exist in DB');
    }
}
