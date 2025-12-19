<?php namespace Tests\Repositories;

use App\Models\VatReturnFillingDetail;
use App\Repositories\VatReturnFillingDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatReturnFillingDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatReturnFillingDetailRepository
     */
    protected $vatReturnFillingDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatReturnFillingDetailRepo = \App::make(VatReturnFillingDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_return_filling_detail()
    {
        $vatReturnFillingDetail = factory(VatReturnFillingDetail::class)->make()->toArray();

        $createdVatReturnFillingDetail = $this->vatReturnFillingDetailRepo->create($vatReturnFillingDetail);

        $createdVatReturnFillingDetail = $createdVatReturnFillingDetail->toArray();
        $this->assertArrayHasKey('id', $createdVatReturnFillingDetail);
        $this->assertNotNull($createdVatReturnFillingDetail['id'], 'Created VatReturnFillingDetail must have id specified');
        $this->assertNotNull(VatReturnFillingDetail::find($createdVatReturnFillingDetail['id']), 'VatReturnFillingDetail with given id must be in DB');
        $this->assertModelData($vatReturnFillingDetail, $createdVatReturnFillingDetail);
    }

    /**
     * @test read
     */
    public function test_read_vat_return_filling_detail()
    {
        $vatReturnFillingDetail = factory(VatReturnFillingDetail::class)->create();

        $dbVatReturnFillingDetail = $this->vatReturnFillingDetailRepo->find($vatReturnFillingDetail->id);

        $dbVatReturnFillingDetail = $dbVatReturnFillingDetail->toArray();
        $this->assertModelData($vatReturnFillingDetail->toArray(), $dbVatReturnFillingDetail);
    }

    /**
     * @test update
     */
    public function test_update_vat_return_filling_detail()
    {
        $vatReturnFillingDetail = factory(VatReturnFillingDetail::class)->create();
        $fakeVatReturnFillingDetail = factory(VatReturnFillingDetail::class)->make()->toArray();

        $updatedVatReturnFillingDetail = $this->vatReturnFillingDetailRepo->update($fakeVatReturnFillingDetail, $vatReturnFillingDetail->id);

        $this->assertModelData($fakeVatReturnFillingDetail, $updatedVatReturnFillingDetail->toArray());
        $dbVatReturnFillingDetail = $this->vatReturnFillingDetailRepo->find($vatReturnFillingDetail->id);
        $this->assertModelData($fakeVatReturnFillingDetail, $dbVatReturnFillingDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_return_filling_detail()
    {
        $vatReturnFillingDetail = factory(VatReturnFillingDetail::class)->create();

        $resp = $this->vatReturnFillingDetailRepo->delete($vatReturnFillingDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(VatReturnFillingDetail::find($vatReturnFillingDetail->id), 'VatReturnFillingDetail should not exist in DB');
    }
}
