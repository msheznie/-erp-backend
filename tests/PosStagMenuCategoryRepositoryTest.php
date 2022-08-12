<?php namespace Tests\Repositories;

use App\Models\PosStagMenuCategory;
use App\Repositories\PosStagMenuCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PosStagMenuCategoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PosStagMenuCategoryRepository
     */
    protected $posStagMenuCategoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->posStagMenuCategoryRepo = \App::make(PosStagMenuCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pos_stag_menu_category()
    {
        $posStagMenuCategory = factory(PosStagMenuCategory::class)->make()->toArray();

        $createdPosStagMenuCategory = $this->posStagMenuCategoryRepo->create($posStagMenuCategory);

        $createdPosStagMenuCategory = $createdPosStagMenuCategory->toArray();
        $this->assertArrayHasKey('id', $createdPosStagMenuCategory);
        $this->assertNotNull($createdPosStagMenuCategory['id'], 'Created PosStagMenuCategory must have id specified');
        $this->assertNotNull(PosStagMenuCategory::find($createdPosStagMenuCategory['id']), 'PosStagMenuCategory with given id must be in DB');
        $this->assertModelData($posStagMenuCategory, $createdPosStagMenuCategory);
    }

    /**
     * @test read
     */
    public function test_read_pos_stag_menu_category()
    {
        $posStagMenuCategory = factory(PosStagMenuCategory::class)->create();

        $dbPosStagMenuCategory = $this->posStagMenuCategoryRepo->find($posStagMenuCategory->id);

        $dbPosStagMenuCategory = $dbPosStagMenuCategory->toArray();
        $this->assertModelData($posStagMenuCategory->toArray(), $dbPosStagMenuCategory);
    }

    /**
     * @test update
     */
    public function test_update_pos_stag_menu_category()
    {
        $posStagMenuCategory = factory(PosStagMenuCategory::class)->create();
        $fakePosStagMenuCategory = factory(PosStagMenuCategory::class)->make()->toArray();

        $updatedPosStagMenuCategory = $this->posStagMenuCategoryRepo->update($fakePosStagMenuCategory, $posStagMenuCategory->id);

        $this->assertModelData($fakePosStagMenuCategory, $updatedPosStagMenuCategory->toArray());
        $dbPosStagMenuCategory = $this->posStagMenuCategoryRepo->find($posStagMenuCategory->id);
        $this->assertModelData($fakePosStagMenuCategory, $dbPosStagMenuCategory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pos_stag_menu_category()
    {
        $posStagMenuCategory = factory(PosStagMenuCategory::class)->create();

        $resp = $this->posStagMenuCategoryRepo->delete($posStagMenuCategory->id);

        $this->assertTrue($resp);
        $this->assertNull(PosStagMenuCategory::find($posStagMenuCategory->id), 'PosStagMenuCategory should not exist in DB');
    }
}
