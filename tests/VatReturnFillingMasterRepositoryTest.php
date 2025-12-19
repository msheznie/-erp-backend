<?php namespace Tests\Repositories;

use App\Models\VatReturnFillingMaster;
use App\Repositories\VatReturnFillingMasterRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatReturnFillingMasterRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatReturnFillingMasterRepository
     */
    protected $vatReturnFillingMasterRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatReturnFillingMasterRepo = \App::make(VatReturnFillingMasterRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_return_filling_master()
    {
        $vatReturnFillingMaster = factory(VatReturnFillingMaster::class)->make()->toArray();

        $createdVatReturnFillingMaster = $this->vatReturnFillingMasterRepo->create($vatReturnFillingMaster);

        $createdVatReturnFillingMaster = $createdVatReturnFillingMaster->toArray();
        $this->assertArrayHasKey('id', $createdVatReturnFillingMaster);
        $this->assertNotNull($createdVatReturnFillingMaster['id'], 'Created VatReturnFillingMaster must have id specified');
        $this->assertNotNull(VatReturnFillingMaster::find($createdVatReturnFillingMaster['id']), 'VatReturnFillingMaster with given id must be in DB');
        $this->assertModelData($vatReturnFillingMaster, $createdVatReturnFillingMaster);
    }

    /**
     * @test read
     */
    public function test_read_vat_return_filling_master()
    {
        $vatReturnFillingMaster = factory(VatReturnFillingMaster::class)->create();

        $dbVatReturnFillingMaster = $this->vatReturnFillingMasterRepo->find($vatReturnFillingMaster->id);

        $dbVatReturnFillingMaster = $dbVatReturnFillingMaster->toArray();
        $this->assertModelData($vatReturnFillingMaster->toArray(), $dbVatReturnFillingMaster);
    }

    /**
     * @test update
     */
    public function test_update_vat_return_filling_master()
    {
        $vatReturnFillingMaster = factory(VatReturnFillingMaster::class)->create();
        $fakeVatReturnFillingMaster = factory(VatReturnFillingMaster::class)->make()->toArray();

        $updatedVatReturnFillingMaster = $this->vatReturnFillingMasterRepo->update($fakeVatReturnFillingMaster, $vatReturnFillingMaster->id);

        $this->assertModelData($fakeVatReturnFillingMaster, $updatedVatReturnFillingMaster->toArray());
        $dbVatReturnFillingMaster = $this->vatReturnFillingMasterRepo->find($vatReturnFillingMaster->id);
        $this->assertModelData($fakeVatReturnFillingMaster, $dbVatReturnFillingMaster->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_return_filling_master()
    {
        $vatReturnFillingMaster = factory(VatReturnFillingMaster::class)->create();

        $resp = $this->vatReturnFillingMasterRepo->delete($vatReturnFillingMaster->id);

        $this->assertTrue($resp);
        $this->assertNull(VatReturnFillingMaster::find($vatReturnFillingMaster->id), 'VatReturnFillingMaster should not exist in DB');
    }
}
