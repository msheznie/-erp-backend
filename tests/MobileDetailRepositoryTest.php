<?php namespace Tests\Repositories;

use App\Models\MobileDetail;
use App\Repositories\MobileDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class MobileDetailRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var MobileDetailRepository
     */
    protected $mobileDetailRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->mobileDetailRepo = \App::make(MobileDetailRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_mobile_detail()
    {
        $mobileDetail = factory(MobileDetail::class)->make()->toArray();

        $createdMobileDetail = $this->mobileDetailRepo->create($mobileDetail);

        $createdMobileDetail = $createdMobileDetail->toArray();
        $this->assertArrayHasKey('id', $createdMobileDetail);
        $this->assertNotNull($createdMobileDetail['id'], 'Created MobileDetail must have id specified');
        $this->assertNotNull(MobileDetail::find($createdMobileDetail['id']), 'MobileDetail with given id must be in DB');
        $this->assertModelData($mobileDetail, $createdMobileDetail);
    }

    /**
     * @test read
     */
    public function test_read_mobile_detail()
    {
        $mobileDetail = factory(MobileDetail::class)->create();

        $dbMobileDetail = $this->mobileDetailRepo->find($mobileDetail->id);

        $dbMobileDetail = $dbMobileDetail->toArray();
        $this->assertModelData($mobileDetail->toArray(), $dbMobileDetail);
    }

    /**
     * @test update
     */
    public function test_update_mobile_detail()
    {
        $mobileDetail = factory(MobileDetail::class)->create();
        $fakeMobileDetail = factory(MobileDetail::class)->make()->toArray();

        $updatedMobileDetail = $this->mobileDetailRepo->update($fakeMobileDetail, $mobileDetail->id);

        $this->assertModelData($fakeMobileDetail, $updatedMobileDetail->toArray());
        $dbMobileDetail = $this->mobileDetailRepo->find($mobileDetail->id);
        $this->assertModelData($fakeMobileDetail, $dbMobileDetail->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_mobile_detail()
    {
        $mobileDetail = factory(MobileDetail::class)->create();

        $resp = $this->mobileDetailRepo->delete($mobileDetail->id);

        $this->assertTrue($resp);
        $this->assertNull(MobileDetail::find($mobileDetail->id), 'MobileDetail should not exist in DB');
    }
}
