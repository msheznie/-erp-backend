<?php namespace Tests\Repositories;

use App\Models\VatReturnFillingMasterRefferedback;
use App\Repositories\VatReturnFillingMasterRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatReturnFillingMasterRefferedbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatReturnFillingMasterRefferedbackRepository
     */
    protected $vatReturnFillingMasterRefferedbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatReturnFillingMasterRefferedbackRepo = \App::make(VatReturnFillingMasterRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_return_filling_master_refferedback()
    {
        $vatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->make()->toArray();

        $createdVatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepo->create($vatReturnFillingMasterRefferedback);

        $createdVatReturnFillingMasterRefferedback = $createdVatReturnFillingMasterRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdVatReturnFillingMasterRefferedback);
        $this->assertNotNull($createdVatReturnFillingMasterRefferedback['id'], 'Created VatReturnFillingMasterRefferedback must have id specified');
        $this->assertNotNull(VatReturnFillingMasterRefferedback::find($createdVatReturnFillingMasterRefferedback['id']), 'VatReturnFillingMasterRefferedback with given id must be in DB');
        $this->assertModelData($vatReturnFillingMasterRefferedback, $createdVatReturnFillingMasterRefferedback);
    }

    /**
     * @test read
     */
    public function test_read_vat_return_filling_master_refferedback()
    {
        $vatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->create();

        $dbVatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepo->find($vatReturnFillingMasterRefferedback->id);

        $dbVatReturnFillingMasterRefferedback = $dbVatReturnFillingMasterRefferedback->toArray();
        $this->assertModelData($vatReturnFillingMasterRefferedback->toArray(), $dbVatReturnFillingMasterRefferedback);
    }

    /**
     * @test update
     */
    public function test_update_vat_return_filling_master_refferedback()
    {
        $vatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->create();
        $fakeVatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->make()->toArray();

        $updatedVatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepo->update($fakeVatReturnFillingMasterRefferedback, $vatReturnFillingMasterRefferedback->id);

        $this->assertModelData($fakeVatReturnFillingMasterRefferedback, $updatedVatReturnFillingMasterRefferedback->toArray());
        $dbVatReturnFillingMasterRefferedback = $this->vatReturnFillingMasterRefferedbackRepo->find($vatReturnFillingMasterRefferedback->id);
        $this->assertModelData($fakeVatReturnFillingMasterRefferedback, $dbVatReturnFillingMasterRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_return_filling_master_refferedback()
    {
        $vatReturnFillingMasterRefferedback = factory(VatReturnFillingMasterRefferedback::class)->create();

        $resp = $this->vatReturnFillingMasterRefferedbackRepo->delete($vatReturnFillingMasterRefferedback->id);

        $this->assertTrue($resp);
        $this->assertNull(VatReturnFillingMasterRefferedback::find($vatReturnFillingMasterRefferedback->id), 'VatReturnFillingMasterRefferedback should not exist in DB');
    }
}
