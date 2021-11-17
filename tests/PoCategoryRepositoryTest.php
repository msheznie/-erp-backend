<?php namespace Tests\Repositories;

use App\Models\PoCategory;
use App\Repositories\PoCategoryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class PoCategoryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoCategoryRepository
     */
    protected $poCategoryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->poCategoryRepo = \App::make(PoCategoryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_po_category()
    {
        $poCategory = factory(PoCategory::class)->make()->toArray();

        $createdPoCategory = $this->poCategoryRepo->create($poCategory);

        $createdPoCategory = $createdPoCategory->toArray();
        $this->assertArrayHasKey('id', $createdPoCategory);
        $this->assertNotNull($createdPoCategory['id'], 'Created PoCategory must have id specified');
        $this->assertNotNull(PoCategory::find($createdPoCategory['id']), 'PoCategory with given id must be in DB');
        $this->assertModelData($poCategory, $createdPoCategory);
    }

    /**
     * @test read
     */
    public function test_read_po_category()
    {
        $poCategory = factory(PoCategory::class)->create();

        $dbPoCategory = $this->poCategoryRepo->find($poCategory->id);

        $dbPoCategory = $dbPoCategory->toArray();
        $this->assertModelData($poCategory->toArray(), $dbPoCategory);
    }

    /**
     * @test update
     */
    public function test_update_po_category()
    {
        $poCategory = factory(PoCategory::class)->create();
        $fakePoCategory = factory(PoCategory::class)->make()->toArray();

        $updatedPoCategory = $this->poCategoryRepo->update($fakePoCategory, $poCategory->id);

        $this->assertModelData($fakePoCategory, $updatedPoCategory->toArray());
        $dbPoCategory = $this->poCategoryRepo->find($poCategory->id);
        $this->assertModelData($fakePoCategory, $dbPoCategory->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_po_category()
    {
        $poCategory = factory(PoCategory::class)->create();

        $resp = $this->poCategoryRepo->delete($poCategory->id);

        $this->assertTrue($resp);
        $this->assertNull(PoCategory::find($poCategory->id), 'PoCategory should not exist in DB');
    }
}
