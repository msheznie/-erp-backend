<?php namespace Tests\Repositories;

use App\Models\VatReturnFilledCategoryRefferedback;
use App\Repositories\VatReturnFilledCategoryRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class VatReturnFilledCategoryRefferedbackRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var VatReturnFilledCategoryRefferedbackRepository
     */
    protected $vatReturnFilledCategoryRefferedbackRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->vatReturnFilledCategoryRefferedbackRepo = \App::make(VatReturnFilledCategoryRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_vat_return_filled_category_refferedback()
    {
        $vatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->make()->toArray();

        $createdVatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepo->create($vatReturnFilledCategoryRefferedback);

        $createdVatReturnFilledCategoryRefferedback = $createdVatReturnFilledCategoryRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdVatReturnFilledCategoryRefferedback);
        $this->assertNotNull($createdVatReturnFilledCategoryRefferedback['id'], 'Created VatReturnFilledCategoryRefferedback must have id specified');
        $this->assertNotNull(VatReturnFilledCategoryRefferedback::find($createdVatReturnFilledCategoryRefferedback['id']), 'VatReturnFilledCategoryRefferedback with given id must be in DB');
        $this->assertModelData($vatReturnFilledCategoryRefferedback, $createdVatReturnFilledCategoryRefferedback);
    }

    /**
     * @test read
     */
    public function test_read_vat_return_filled_category_refferedback()
    {
        $vatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->create();

        $dbVatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepo->find($vatReturnFilledCategoryRefferedback->id);

        $dbVatReturnFilledCategoryRefferedback = $dbVatReturnFilledCategoryRefferedback->toArray();
        $this->assertModelData($vatReturnFilledCategoryRefferedback->toArray(), $dbVatReturnFilledCategoryRefferedback);
    }

    /**
     * @test update
     */
    public function test_update_vat_return_filled_category_refferedback()
    {
        $vatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->create();
        $fakeVatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->make()->toArray();

        $updatedVatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepo->update($fakeVatReturnFilledCategoryRefferedback, $vatReturnFilledCategoryRefferedback->id);

        $this->assertModelData($fakeVatReturnFilledCategoryRefferedback, $updatedVatReturnFilledCategoryRefferedback->toArray());
        $dbVatReturnFilledCategoryRefferedback = $this->vatReturnFilledCategoryRefferedbackRepo->find($vatReturnFilledCategoryRefferedback->id);
        $this->assertModelData($fakeVatReturnFilledCategoryRefferedback, $dbVatReturnFilledCategoryRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_vat_return_filled_category_refferedback()
    {
        $vatReturnFilledCategoryRefferedback = factory(VatReturnFilledCategoryRefferedback::class)->create();

        $resp = $this->vatReturnFilledCategoryRefferedbackRepo->delete($vatReturnFilledCategoryRefferedback->id);

        $this->assertTrue($resp);
        $this->assertNull(VatReturnFilledCategoryRefferedback::find($vatReturnFilledCategoryRefferedback->id), 'VatReturnFilledCategoryRefferedback should not exist in DB');
    }
}
