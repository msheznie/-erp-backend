<?php namespace Tests\Repositories;

use App\Models\RegisteredBankMemoSupplier;
use App\Repositories\RegisteredBankMemoSupplierRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RegisteredBankMemoSupplierRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RegisteredBankMemoSupplierRepository
     */
    protected $registeredBankMemoSupplierRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->registeredBankMemoSupplierRepo = \App::make(RegisteredBankMemoSupplierRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_registered_bank_memo_supplier()
    {
        $registeredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->make()->toArray();

        $createdRegisteredBankMemoSupplier = $this->registeredBankMemoSupplierRepo->create($registeredBankMemoSupplier);

        $createdRegisteredBankMemoSupplier = $createdRegisteredBankMemoSupplier->toArray();
        $this->assertArrayHasKey('id', $createdRegisteredBankMemoSupplier);
        $this->assertNotNull($createdRegisteredBankMemoSupplier['id'], 'Created RegisteredBankMemoSupplier must have id specified');
        $this->assertNotNull(RegisteredBankMemoSupplier::find($createdRegisteredBankMemoSupplier['id']), 'RegisteredBankMemoSupplier with given id must be in DB');
        $this->assertModelData($registeredBankMemoSupplier, $createdRegisteredBankMemoSupplier);
    }

    /**
     * @test read
     */
    public function test_read_registered_bank_memo_supplier()
    {
        $registeredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->create();

        $dbRegisteredBankMemoSupplier = $this->registeredBankMemoSupplierRepo->find($registeredBankMemoSupplier->id);

        $dbRegisteredBankMemoSupplier = $dbRegisteredBankMemoSupplier->toArray();
        $this->assertModelData($registeredBankMemoSupplier->toArray(), $dbRegisteredBankMemoSupplier);
    }

    /**
     * @test update
     */
    public function test_update_registered_bank_memo_supplier()
    {
        $registeredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->create();
        $fakeRegisteredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->make()->toArray();

        $updatedRegisteredBankMemoSupplier = $this->registeredBankMemoSupplierRepo->update($fakeRegisteredBankMemoSupplier, $registeredBankMemoSupplier->id);

        $this->assertModelData($fakeRegisteredBankMemoSupplier, $updatedRegisteredBankMemoSupplier->toArray());
        $dbRegisteredBankMemoSupplier = $this->registeredBankMemoSupplierRepo->find($registeredBankMemoSupplier->id);
        $this->assertModelData($fakeRegisteredBankMemoSupplier, $dbRegisteredBankMemoSupplier->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_registered_bank_memo_supplier()
    {
        $registeredBankMemoSupplier = factory(RegisteredBankMemoSupplier::class)->create();

        $resp = $this->registeredBankMemoSupplierRepo->delete($registeredBankMemoSupplier->id);

        $this->assertTrue($resp);
        $this->assertNull(RegisteredBankMemoSupplier::find($registeredBankMemoSupplier->id), 'RegisteredBankMemoSupplier should not exist in DB');
    }
}
