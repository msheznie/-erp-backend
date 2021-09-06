<?php namespace Tests\Repositories;

use App\Models\SrpErpFormCategory;
use App\Repositories\SrpErpFormCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SrpErpFormCategoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SrpErpFormCategoryRepository
     */
    protected $srpErpFormCategoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->srpErpFormCategoryRepo = \App::make(SrpErpFormCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_srp_erp_form_category()
    {
        $srpErpFormCategory = factory(SrpErpFormCategory::class)->make()->toArray();

        $createdSrpErpFormCategory = $this->srpErpFormCategoryRepo->create($srpErpFormCategory);

        $createdSrpErpFormCategory = $createdSrpErpFormCategory->toArray();
        $this->assertArrayHasKey('id', $createdSrpErpFormCategory);
        $this->assertNotNull($createdSrpErpFormCategory['id'], 'Created SrpErpFormCategory must have id specified');
        $this->assertNotNull(SrpErpFormCategory::find($createdSrpErpFormCategory['id']), 'SrpErpFormCategory with given id must be in DB');
        $this->assertModelData($srpErpFormCategory, $createdSrpErpFormCategory);
    }

    /**
     * @test read
     */
    public function test_read_srp_erp_form_category()
    {
        $srpErpFormCategory = factory(SrpErpFormCategory::class)->create();

        $dbSrpErpFormCategory = $this->srpErpFormCategoryRepo->find($srpErpFormCategory->id);

        $dbSrpErpFormCategory = $dbSrpErpFormCategory->toArray();
        $this->assertModelData($srpErpFormCategory->toArray(), $dbSrpErpFormCategory);
    }

    /**
     * @test update
     */
    public function test_update_srp_erp_form_category()
    {
        $srpErpFormCategory = factory(SrpErpFormCategory::class)->create();
        $fakeSrpErpFormCategory = factory(SrpErpFormCategory::class)->make()->toArray();

        $updatedSrpErpFormCategory = $this->srpErpFormCategoryRepo->update($fakeSrpErpFormCategory, $srpErpFormCategory->id);

        $this->assertModelData($fakeSrpErpFormCategory, $updatedSrpErpFormCategory->toArray());
        $dbSrpErpFormCategory = $this->srpErpFormCategoryRepo->find($srpErpFormCategory->id);
        $this->assertModelData($fakeSrpErpFormCategory, $dbSrpErpFormCategory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_srp_erp_form_category()
    {
        $srpErpFormCategory = factory(SrpErpFormCategory::class)->create();

        $resp = $this->srpErpFormCategoryRepo->delete($srpErpFormCategory->id);

        $this->assertTrue($resp);
        $this->assertNull(SrpErpFormCategory::find($srpErpFormCategory->id), 'SrpErpFormCategory should not exist in DB');
    }
}
