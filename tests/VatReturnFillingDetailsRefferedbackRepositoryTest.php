<?php namespace Tests\Repositories;

use App\Models\VatReturnFillingDetailsRefferedback;
use App\Repositories\VatReturnFillingDetailsRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatReturnFillingDetailsRefferedbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatReturnFillingDetailsRefferedbackRepository
     */
    protected $vatReturnFillingDetailsRefferedbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatReturnFillingDetailsRefferedbackRepo = \App::make(VatReturnFillingDetailsRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_return_filling_details_refferedback()
    {
        $vatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->make()->toArray();

        $createdVatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepo->create($vatReturnFillingDetailsRefferedback);

        $createdVatReturnFillingDetailsRefferedback = $createdVatReturnFillingDetailsRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdVatReturnFillingDetailsRefferedback);
        $this->assertNotNull($createdVatReturnFillingDetailsRefferedback['id'], 'Created VatReturnFillingDetailsRefferedback must have id specified');
        $this->assertNotNull(VatReturnFillingDetailsRefferedback::find($createdVatReturnFillingDetailsRefferedback['id']), 'VatReturnFillingDetailsRefferedback with given id must be in DB');
        $this->assertModelData($vatReturnFillingDetailsRefferedback, $createdVatReturnFillingDetailsRefferedback);
    }

    /**
     * @test read
     */
    public function test_read_vat_return_filling_details_refferedback()
    {
        $vatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->create();

        $dbVatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepo->find($vatReturnFillingDetailsRefferedback->id);

        $dbVatReturnFillingDetailsRefferedback = $dbVatReturnFillingDetailsRefferedback->toArray();
        $this->assertModelData($vatReturnFillingDetailsRefferedback->toArray(), $dbVatReturnFillingDetailsRefferedback);
    }

    /**
     * @test update
     */
    public function test_update_vat_return_filling_details_refferedback()
    {
        $vatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->create();
        $fakeVatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->make()->toArray();

        $updatedVatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepo->update($fakeVatReturnFillingDetailsRefferedback, $vatReturnFillingDetailsRefferedback->id);

        $this->assertModelData($fakeVatReturnFillingDetailsRefferedback, $updatedVatReturnFillingDetailsRefferedback->toArray());
        $dbVatReturnFillingDetailsRefferedback = $this->vatReturnFillingDetailsRefferedbackRepo->find($vatReturnFillingDetailsRefferedback->id);
        $this->assertModelData($fakeVatReturnFillingDetailsRefferedback, $dbVatReturnFillingDetailsRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_return_filling_details_refferedback()
    {
        $vatReturnFillingDetailsRefferedback = factory(VatReturnFillingDetailsRefferedback::class)->create();

        $resp = $this->vatReturnFillingDetailsRefferedbackRepo->delete($vatReturnFillingDetailsRefferedback->id);

        $this->assertTrue($resp);
        $this->assertNull(VatReturnFillingDetailsRefferedback::find($vatReturnFillingDetailsRefferedback->id), 'VatReturnFillingDetailsRefferedback should not exist in DB');
    }
}
