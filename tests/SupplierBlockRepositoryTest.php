<?php namespace Tests\Repositories;

use App\Models\SupplierBlock;
use App\Repositories\SupplierBlockRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class SupplierBlockRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var SupplierBlockRepository
     */
    protected $supplierBlockRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->supplierBlockRepo = \App::make(SupplierBlockRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_supplier_block()
    {
        $supplierBlock = factory(SupplierBlock::class)->make()->toArray();

        $createdSupplierBlock = $this->supplierBlockRepo->create($supplierBlock);

        $createdSupplierBlock = $createdSupplierBlock->toArray();
        $this->assertArrayHasKey('id', $createdSupplierBlock);
        $this->assertNotNull($createdSupplierBlock['id'], 'Created SupplierBlock must have id specified');
        $this->assertNotNull(SupplierBlock::find($createdSupplierBlock['id']), 'SupplierBlock with given id must be in DB');
        $this->assertModelData($supplierBlock, $createdSupplierBlock);
    }

    /**
     * @test read
     */
    public function test_read_supplier_block()
    {
        $supplierBlock = factory(SupplierBlock::class)->create();

        $dbSupplierBlock = $this->supplierBlockRepo->find($supplierBlock->id);

        $dbSupplierBlock = $dbSupplierBlock->toArray();
        $this->assertModelData($supplierBlock->toArray(), $dbSupplierBlock);
    }

    /**
     * @test update
     */
    public function test_update_supplier_block()
    {
        $supplierBlock = factory(SupplierBlock::class)->create();
        $fakeSupplierBlock = factory(SupplierBlock::class)->make()->toArray();

        $updatedSupplierBlock = $this->supplierBlockRepo->update($fakeSupplierBlock, $supplierBlock->id);

        $this->assertModelData($fakeSupplierBlock, $updatedSupplierBlock->toArray());
        $dbSupplierBlock = $this->supplierBlockRepo->find($supplierBlock->id);
        $this->assertModelData($fakeSupplierBlock, $dbSupplierBlock->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_supplier_block()
    {
        $supplierBlock = factory(SupplierBlock::class)->create();

        $resp = $this->supplierBlockRepo->delete($supplierBlock->id);

        $this->assertTrue($resp);
        $this->assertNull(SupplierBlock::find($supplierBlock->id), 'SupplierBlock should not exist in DB');
    }
}
