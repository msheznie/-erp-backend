<?php

use App\Models\FreeBilling;
use App\Repositories\FreeBillingRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FreeBillingRepositoryTest extends TestCase
{
    use MakeFreeBillingTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FreeBillingRepository
     */
    protected $freeBillingRepo;

    public function setUp()
    {
        parent::setUp();
        $this->freeBillingRepo = App::make(FreeBillingRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFreeBilling()
    {
        $freeBilling = $this->fakeFreeBillingData();
        $createdFreeBilling = $this->freeBillingRepo->create($freeBilling);
        $createdFreeBilling = $createdFreeBilling->toArray();
        $this->assertArrayHasKey('id', $createdFreeBilling);
        $this->assertNotNull($createdFreeBilling['id'], 'Created FreeBilling must have id specified');
        $this->assertNotNull(FreeBilling::find($createdFreeBilling['id']), 'FreeBilling with given id must be in DB');
        $this->assertModelData($freeBilling, $createdFreeBilling);
    }

    /**
     * @test read
     */
    public function testReadFreeBilling()
    {
        $freeBilling = $this->makeFreeBilling();
        $dbFreeBilling = $this->freeBillingRepo->find($freeBilling->id);
        $dbFreeBilling = $dbFreeBilling->toArray();
        $this->assertModelData($freeBilling->toArray(), $dbFreeBilling);
    }

    /**
     * @test update
     */
    public function testUpdateFreeBilling()
    {
        $freeBilling = $this->makeFreeBilling();
        $fakeFreeBilling = $this->fakeFreeBillingData();
        $updatedFreeBilling = $this->freeBillingRepo->update($fakeFreeBilling, $freeBilling->id);
        $this->assertModelData($fakeFreeBilling, $updatedFreeBilling->toArray());
        $dbFreeBilling = $this->freeBillingRepo->find($freeBilling->id);
        $this->assertModelData($fakeFreeBilling, $dbFreeBilling->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFreeBilling()
    {
        $freeBilling = $this->makeFreeBilling();
        $resp = $this->freeBillingRepo->delete($freeBilling->id);
        $this->assertTrue($resp);
        $this->assertNull(FreeBilling::find($freeBilling->id), 'FreeBilling should not exist in DB');
    }
}
