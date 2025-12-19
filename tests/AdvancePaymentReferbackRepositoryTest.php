<?php

use App\Models\AdvancePaymentReferback;
use App\Repositories\AdvancePaymentReferbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class AdvancePaymentReferbackRepositoryTest extends TestCase
{
    use MakeAdvancePaymentReferbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var AdvancePaymentReferbackRepository
     */
    protected $advancePaymentReferbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->advancePaymentReferbackRepo = App::make(AdvancePaymentReferbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateAdvancePaymentReferback()
    {
        $advancePaymentReferback = $this->fakeAdvancePaymentReferbackData();
        $createdAdvancePaymentReferback = $this->advancePaymentReferbackRepo->create($advancePaymentReferback);
        $createdAdvancePaymentReferback = $createdAdvancePaymentReferback->toArray();
        $this->assertArrayHasKey('id', $createdAdvancePaymentReferback);
        $this->assertNotNull($createdAdvancePaymentReferback['id'], 'Created AdvancePaymentReferback must have id specified');
        $this->assertNotNull(AdvancePaymentReferback::find($createdAdvancePaymentReferback['id']), 'AdvancePaymentReferback with given id must be in DB');
        $this->assertModelData($advancePaymentReferback, $createdAdvancePaymentReferback);
    }

    /**
     * @test read
     */
    public function testReadAdvancePaymentReferback()
    {
        $advancePaymentReferback = $this->makeAdvancePaymentReferback();
        $dbAdvancePaymentReferback = $this->advancePaymentReferbackRepo->find($advancePaymentReferback->id);
        $dbAdvancePaymentReferback = $dbAdvancePaymentReferback->toArray();
        $this->assertModelData($advancePaymentReferback->toArray(), $dbAdvancePaymentReferback);
    }

    /**
     * @test update
     */
    public function testUpdateAdvancePaymentReferback()
    {
        $advancePaymentReferback = $this->makeAdvancePaymentReferback();
        $fakeAdvancePaymentReferback = $this->fakeAdvancePaymentReferbackData();
        $updatedAdvancePaymentReferback = $this->advancePaymentReferbackRepo->update($fakeAdvancePaymentReferback, $advancePaymentReferback->id);
        $this->assertModelData($fakeAdvancePaymentReferback, $updatedAdvancePaymentReferback->toArray());
        $dbAdvancePaymentReferback = $this->advancePaymentReferbackRepo->find($advancePaymentReferback->id);
        $this->assertModelData($fakeAdvancePaymentReferback, $dbAdvancePaymentReferback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteAdvancePaymentReferback()
    {
        $advancePaymentReferback = $this->makeAdvancePaymentReferback();
        $resp = $this->advancePaymentReferbackRepo->delete($advancePaymentReferback->id);
        $this->assertTrue($resp);
        $this->assertNull(AdvancePaymentReferback::find($advancePaymentReferback->id), 'AdvancePaymentReferback should not exist in DB');
    }
}
