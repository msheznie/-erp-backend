<?php namespace Tests\Repositories;

use App\Models\FinanceCategorySerial;
use App\Repositories\FinanceCategorySerialRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class FinanceCategorySerialRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var FinanceCategorySerialRepository
     */
    protected $financeCategorySerialRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->financeCategorySerialRepo = \App::make(FinanceCategorySerialRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_finance_category_serial()
    {
        $financeCategorySerial = factory(FinanceCategorySerial::class)->make()->toArray();

        $createdFinanceCategorySerial = $this->financeCategorySerialRepo->create($financeCategorySerial);

        $createdFinanceCategorySerial = $createdFinanceCategorySerial->toArray();
        $this->assertArrayHasKey('id', $createdFinanceCategorySerial);
        $this->assertNotNull($createdFinanceCategorySerial['id'], 'Created FinanceCategorySerial must have id specified');
        $this->assertNotNull(FinanceCategorySerial::find($createdFinanceCategorySerial['id']), 'FinanceCategorySerial with given id must be in DB');
        $this->assertModelData($financeCategorySerial, $createdFinanceCategorySerial);
    }

    /**
     * @test read
     */
    public function test_read_finance_category_serial()
    {
        $financeCategorySerial = factory(FinanceCategorySerial::class)->create();

        $dbFinanceCategorySerial = $this->financeCategorySerialRepo->find($financeCategorySerial->id);

        $dbFinanceCategorySerial = $dbFinanceCategorySerial->toArray();
        $this->assertModelData($financeCategorySerial->toArray(), $dbFinanceCategorySerial);
    }

    /**
     * @test update
     */
    public function test_update_finance_category_serial()
    {
        $financeCategorySerial = factory(FinanceCategorySerial::class)->create();
        $fakeFinanceCategorySerial = factory(FinanceCategorySerial::class)->make()->toArray();

        $updatedFinanceCategorySerial = $this->financeCategorySerialRepo->update($fakeFinanceCategorySerial, $financeCategorySerial->id);

        $this->assertModelData($fakeFinanceCategorySerial, $updatedFinanceCategorySerial->toArray());
        $dbFinanceCategorySerial = $this->financeCategorySerialRepo->find($financeCategorySerial->id);
        $this->assertModelData($fakeFinanceCategorySerial, $dbFinanceCategorySerial->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_finance_category_serial()
    {
        $financeCategorySerial = factory(FinanceCategorySerial::class)->create();

        $resp = $this->financeCategorySerialRepo->delete($financeCategorySerial->id);

        $this->assertTrue($resp);
        $this->assertNull(FinanceCategorySerial::find($financeCategorySerial->id), 'FinanceCategorySerial should not exist in DB');
    }
}
