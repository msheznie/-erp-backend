<?php namespace Tests\Repositories;

use App\Models\CustomFiltersColumn;
use App\Repositories\CustomFiltersColumnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class CustomFiltersColumnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var CustomFiltersColumnRepository
     */
    protected $customFiltersColumnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->customFiltersColumnRepo = \App::make(CustomFiltersColumnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_custom_filters_column()
    {
        $customFiltersColumn = factory(CustomFiltersColumn::class)->make()->toArray();

        $createdCustomFiltersColumn = $this->customFiltersColumnRepo->create($customFiltersColumn);

        $createdCustomFiltersColumn = $createdCustomFiltersColumn->toArray();
        $this->assertArrayHasKey('id', $createdCustomFiltersColumn);
        $this->assertNotNull($createdCustomFiltersColumn['id'], 'Created CustomFiltersColumn must have id specified');
        $this->assertNotNull(CustomFiltersColumn::find($createdCustomFiltersColumn['id']), 'CustomFiltersColumn with given id must be in DB');
        $this->assertModelData($customFiltersColumn, $createdCustomFiltersColumn);
    }

    /**
     * @test read
     */
    public function test_read_custom_filters_column()
    {
        $customFiltersColumn = factory(CustomFiltersColumn::class)->create();

        $dbCustomFiltersColumn = $this->customFiltersColumnRepo->find($customFiltersColumn->id);

        $dbCustomFiltersColumn = $dbCustomFiltersColumn->toArray();
        $this->assertModelData($customFiltersColumn->toArray(), $dbCustomFiltersColumn);
    }

    /**
     * @test update
     */
    public function test_update_custom_filters_column()
    {
        $customFiltersColumn = factory(CustomFiltersColumn::class)->create();
        $fakeCustomFiltersColumn = factory(CustomFiltersColumn::class)->make()->toArray();

        $updatedCustomFiltersColumn = $this->customFiltersColumnRepo->update($fakeCustomFiltersColumn, $customFiltersColumn->id);

        $this->assertModelData($fakeCustomFiltersColumn, $updatedCustomFiltersColumn->toArray());
        $dbCustomFiltersColumn = $this->customFiltersColumnRepo->find($customFiltersColumn->id);
        $this->assertModelData($fakeCustomFiltersColumn, $dbCustomFiltersColumn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_custom_filters_column()
    {
        $customFiltersColumn = factory(CustomFiltersColumn::class)->create();

        $resp = $this->customFiltersColumnRepo->delete($customFiltersColumn->id);

        $this->assertTrue($resp);
        $this->assertNull(CustomFiltersColumn::find($customFiltersColumn->id), 'CustomFiltersColumn should not exist in DB');
    }
}
