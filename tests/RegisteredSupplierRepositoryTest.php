<?php namespace Tests\Repositories;

use App\Models\RegisteredSupplier;
use App\Repositories\RegisteredSupplierRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RegisteredSupplierRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RegisteredSupplierRepository
     */
    protected $registeredSupplierRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->registeredSupplierRepo = \App::make(RegisteredSupplierRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_registered_supplier()
    {
        $registeredSupplier = factory(RegisteredSupplier::class)->make()->toArray();

        $createdRegisteredSupplier = $this->registeredSupplierRepo->create($registeredSupplier);

        $createdRegisteredSupplier = $createdRegisteredSupplier->toArray();
        $this->assertArrayHasKey('id', $createdRegisteredSupplier);
        $this->assertNotNull($createdRegisteredSupplier['id'], 'Created RegisteredSupplier must have id specified');
        $this->assertNotNull(RegisteredSupplier::find($createdRegisteredSupplier['id']), 'RegisteredSupplier with given id must be in DB');
        $this->assertModelData($registeredSupplier, $createdRegisteredSupplier);
    }

    /**
     * @test read
     */
    public function test_read_registered_supplier()
    {
        $registeredSupplier = factory(RegisteredSupplier::class)->create();

        $dbRegisteredSupplier = $this->registeredSupplierRepo->find($registeredSupplier->id);

        $dbRegisteredSupplier = $dbRegisteredSupplier->toArray();
        $this->assertModelData($registeredSupplier->toArray(), $dbRegisteredSupplier);
    }

    /**
     * @test update
     */
    public function test_update_registered_supplier()
    {
        $registeredSupplier = factory(RegisteredSupplier::class)->create();
        $fakeRegisteredSupplier = factory(RegisteredSupplier::class)->make()->toArray();

        $updatedRegisteredSupplier = $this->registeredSupplierRepo->update($fakeRegisteredSupplier, $registeredSupplier->id);

        $this->assertModelData($fakeRegisteredSupplier, $updatedRegisteredSupplier->toArray());
        $dbRegisteredSupplier = $this->registeredSupplierRepo->find($registeredSupplier->id);
        $this->assertModelData($fakeRegisteredSupplier, $dbRegisteredSupplier->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_registered_supplier()
    {
        $registeredSupplier = factory(RegisteredSupplier::class)->create();

        $resp = $this->registeredSupplierRepo->delete($registeredSupplier->id);

        $this->assertTrue($resp);
        $this->assertNull(RegisteredSupplier::find($registeredSupplier->id), 'RegisteredSupplier should not exist in DB');
    }
}
