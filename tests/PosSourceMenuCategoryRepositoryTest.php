<?php namespace Tests\Repositories;

use App\Models\PosSourceMenuCategory;
use App\Repositories\PosSourceMenuCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PosSourceMenuCategoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PosSourceMenuCategoryRepository
     */
    protected $posSourceMenuCategoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->posSourceMenuCategoryRepo = \App::make(PosSourceMenuCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_pos_source_menu_category()
    {
        $posSourceMenuCategory = factory(PosSourceMenuCategory::class)->make()->toArray();

        $createdPosSourceMenuCategory = $this->posSourceMenuCategoryRepo->create($posSourceMenuCategory);

        $createdPosSourceMenuCategory = $createdPosSourceMenuCategory->toArray();
        $this->assertArrayHasKey('id', $createdPosSourceMenuCategory);
        $this->assertNotNull($createdPosSourceMenuCategory['id'], 'Created PosSourceMenuCategory must have id specified');
        $this->assertNotNull(PosSourceMenuCategory::find($createdPosSourceMenuCategory['id']), 'PosSourceMenuCategory with given id must be in DB');
        $this->assertModelData($posSourceMenuCategory, $createdPosSourceMenuCategory);
    }

    /**
     * @test read
     */
    public function test_read_pos_source_menu_category()
    {
        $posSourceMenuCategory = factory(PosSourceMenuCategory::class)->create();

        $dbPosSourceMenuCategory = $this->posSourceMenuCategoryRepo->find($posSourceMenuCategory->id);

        $dbPosSourceMenuCategory = $dbPosSourceMenuCategory->toArray();
        $this->assertModelData($posSourceMenuCategory->toArray(), $dbPosSourceMenuCategory);
    }

    /**
     * @test update
     */
    public function test_update_pos_source_menu_category()
    {
        $posSourceMenuCategory = factory(PosSourceMenuCategory::class)->create();
        $fakePosSourceMenuCategory = factory(PosSourceMenuCategory::class)->make()->toArray();

        $updatedPosSourceMenuCategory = $this->posSourceMenuCategoryRepo->update($fakePosSourceMenuCategory, $posSourceMenuCategory->id);

        $this->assertModelData($fakePosSourceMenuCategory, $updatedPosSourceMenuCategory->toArray());
        $dbPosSourceMenuCategory = $this->posSourceMenuCategoryRepo->find($posSourceMenuCategory->id);
        $this->assertModelData($fakePosSourceMenuCategory, $dbPosSourceMenuCategory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_pos_source_menu_category()
    {
        $posSourceMenuCategory = factory(PosSourceMenuCategory::class)->create();

        $resp = $this->posSourceMenuCategoryRepo->delete($posSourceMenuCategory->id);

        $this->assertTrue($resp);
        $this->assertNull(PosSourceMenuCategory::find($posSourceMenuCategory->id), 'PosSourceMenuCategory should not exist in DB');
    }
}
