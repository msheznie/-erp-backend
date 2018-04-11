<?php

use App\Models\PoAdvancePayment;
use App\Repositories\PoAdvancePaymentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoAdvancePaymentRepositoryTest extends TestCase
{
    use MakePoAdvancePaymentTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoAdvancePaymentRepository
     */
    protected $poAdvancePaymentRepo;

    public function setUp()
    {
        parent::setUp();
        $this->poAdvancePaymentRepo = App::make(PoAdvancePaymentRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePoAdvancePayment()
    {
        $poAdvancePayment = $this->fakePoAdvancePaymentData();
        $createdPoAdvancePayment = $this->poAdvancePaymentRepo->create($poAdvancePayment);
        $createdPoAdvancePayment = $createdPoAdvancePayment->toArray();
        $this->assertArrayHasKey('id', $createdPoAdvancePayment);
        $this->assertNotNull($createdPoAdvancePayment['id'], 'Created PoAdvancePayment must have id specified');
        $this->assertNotNull(PoAdvancePayment::find($createdPoAdvancePayment['id']), 'PoAdvancePayment with given id must be in DB');
        $this->assertModelData($poAdvancePayment, $createdPoAdvancePayment);
    }

    /**
     * @test read
     */
    public function testReadPoAdvancePayment()
    {
        $poAdvancePayment = $this->makePoAdvancePayment();
        $dbPoAdvancePayment = $this->poAdvancePaymentRepo->find($poAdvancePayment->id);
        $dbPoAdvancePayment = $dbPoAdvancePayment->toArray();
        $this->assertModelData($poAdvancePayment->toArray(), $dbPoAdvancePayment);
    }

    /**
     * @test update
     */
    public function testUpdatePoAdvancePayment()
    {
        $poAdvancePayment = $this->makePoAdvancePayment();
        $fakePoAdvancePayment = $this->fakePoAdvancePaymentData();
        $updatedPoAdvancePayment = $this->poAdvancePaymentRepo->update($fakePoAdvancePayment, $poAdvancePayment->id);
        $this->assertModelData($fakePoAdvancePayment, $updatedPoAdvancePayment->toArray());
        $dbPoAdvancePayment = $this->poAdvancePaymentRepo->find($poAdvancePayment->id);
        $this->assertModelData($fakePoAdvancePayment, $dbPoAdvancePayment->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePoAdvancePayment()
    {
        $poAdvancePayment = $this->makePoAdvancePayment();
        $resp = $this->poAdvancePaymentRepo->delete($poAdvancePayment->id);
        $this->assertTrue($resp);
        $this->assertNull(PoAdvancePayment::find($poAdvancePayment->id), 'PoAdvancePayment should not exist in DB');
    }
}
