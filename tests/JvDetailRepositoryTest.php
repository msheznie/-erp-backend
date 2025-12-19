<?php

use App\Models\JvDetail;
use App\Repositories\JvDetailRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class JvDetailRepositoryTest extends TestCase
{
    use MakeJvDetailTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var JvDetailRepository
     */
    protected $jvDetailRepo;

    public function setUp()
    {
        parent::setUp();
        $this->jvDetailRepo = App::make(JvDetailRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateJvDetail()
    {
        $jvDetail = $this->fakeJvDetailData();
        $createdJvDetail = $this->jvDetailRepo->create($jvDetail);
        $createdJvDetail = $createdJvDetail->toArray();
        $this->assertArrayHasKey('id', $createdJvDetail);
        $this->assertNotNull($createdJvDetail['id'], 'Created JvDetail must have id specified');
        $this->assertNotNull(JvDetail::find($createdJvDetail['id']), 'JvDetail with given id must be in DB');
        $this->assertModelData($jvDetail, $createdJvDetail);
    }

    /**
     * @test read
     */
    public function testReadJvDetail()
    {
        $jvDetail = $this->makeJvDetail();
        $dbJvDetail = $this->jvDetailRepo->find($jvDetail->id);
        $dbJvDetail = $dbJvDetail->toArray();
        $this->assertModelData($jvDetail->toArray(), $dbJvDetail);
    }

    /**
     * @test update
     */
    public function testUpdateJvDetail()
    {
        $jvDetail = $this->makeJvDetail();
        $fakeJvDetail = $this->fakeJvDetailData();
        $updatedJvDetail = $this->jvDetailRepo->update($fakeJvDetail, $jvDetail->id);
        $this->assertModelData($fakeJvDetail, $updatedJvDetail->toArray());
        $dbJvDetail = $this->jvDetailRepo->find($jvDetail->id);
        $this->assertModelData($fakeJvDetail, $dbJvDetail->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteJvDetail()
    {
        $jvDetail = $this->makeJvDetail();
        $resp = $this->jvDetailRepo->delete($jvDetail->id);
        $this->assertTrue($resp);
        $this->assertNull(JvDetail::find($jvDetail->id), 'JvDetail should not exist in DB');
    }
}
