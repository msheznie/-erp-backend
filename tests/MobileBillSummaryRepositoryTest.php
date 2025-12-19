<?php namespace Tests\Repositories;

use App\Models\MobileBillSummary;
use App\Repositories\MobileBillSummaryRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MobileBillSummaryRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MobileBillSummaryRepository
     */
    protected $mobileBillSummaryRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->mobileBillSummaryRepo = \App::make(MobileBillSummaryRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mobile_bill_summary()
    {
        $mobileBillSummary = factory(MobileBillSummary::class)->make()->toArray();

        $createdMobileBillSummary = $this->mobileBillSummaryRepo->create($mobileBillSummary);

        $createdMobileBillSummary = $createdMobileBillSummary->toArray();
        $this->assertArrayHasKey('id', $createdMobileBillSummary);
        $this->assertNotNull($createdMobileBillSummary['id'], 'Created MobileBillSummary must have id specified');
        $this->assertNotNull(MobileBillSummary::find($createdMobileBillSummary['id']), 'MobileBillSummary with given id must be in DB');
        $this->assertModelData($mobileBillSummary, $createdMobileBillSummary);
    }

    /**
     * @test read
     */
    public function test_read_mobile_bill_summary()
    {
        $mobileBillSummary = factory(MobileBillSummary::class)->create();

        $dbMobileBillSummary = $this->mobileBillSummaryRepo->find($mobileBillSummary->id);

        $dbMobileBillSummary = $dbMobileBillSummary->toArray();
        $this->assertModelData($mobileBillSummary->toArray(), $dbMobileBillSummary);
    }

    /**
     * @test update
     */
    public function test_update_mobile_bill_summary()
    {
        $mobileBillSummary = factory(MobileBillSummary::class)->create();
        $fakeMobileBillSummary = factory(MobileBillSummary::class)->make()->toArray();

        $updatedMobileBillSummary = $this->mobileBillSummaryRepo->update($fakeMobileBillSummary, $mobileBillSummary->id);

        $this->assertModelData($fakeMobileBillSummary, $updatedMobileBillSummary->toArray());
        $dbMobileBillSummary = $this->mobileBillSummaryRepo->find($mobileBillSummary->id);
        $this->assertModelData($fakeMobileBillSummary, $dbMobileBillSummary->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mobile_bill_summary()
    {
        $mobileBillSummary = factory(MobileBillSummary::class)->create();

        $resp = $this->mobileBillSummaryRepo->delete($mobileBillSummary->id);

        $this->assertTrue($resp);
        $this->assertNull(MobileBillSummary::find($mobileBillSummary->id), 'MobileBillSummary should not exist in DB');
    }
}
