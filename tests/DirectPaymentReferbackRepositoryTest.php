<?php

use App\Models\DirectPaymentReferback;
use App\Repositories\DirectPaymentReferbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class DirectPaymentReferbackRepositoryTest extends TestCase
{
    use MakeDirectPaymentReferbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var DirectPaymentReferbackRepository
     */
    protected $directPaymentReferbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->directPaymentReferbackRepo = App::make(DirectPaymentReferbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateDirectPaymentReferback()
    {
        $directPaymentReferback = $this->fakeDirectPaymentReferbackData();
        $createdDirectPaymentReferback = $this->directPaymentReferbackRepo->create($directPaymentReferback);
        $createdDirectPaymentReferback = $createdDirectPaymentReferback->toArray();
        $this->assertArrayHasKey('id', $createdDirectPaymentReferback);
        $this->assertNotNull($createdDirectPaymentReferback['id'], 'Created DirectPaymentReferback must have id specified');
        $this->assertNotNull(DirectPaymentReferback::find($createdDirectPaymentReferback['id']), 'DirectPaymentReferback with given id must be in DB');
        $this->assertModelData($directPaymentReferback, $createdDirectPaymentReferback);
    }

    /**
     * @test read
     */
    public function testReadDirectPaymentReferback()
    {
        $directPaymentReferback = $this->makeDirectPaymentReferback();
        $dbDirectPaymentReferback = $this->directPaymentReferbackRepo->find($directPaymentReferback->id);
        $dbDirectPaymentReferback = $dbDirectPaymentReferback->toArray();
        $this->assertModelData($directPaymentReferback->toArray(), $dbDirectPaymentReferback);
    }

    /**
     * @test update
     */
    public function testUpdateDirectPaymentReferback()
    {
        $directPaymentReferback = $this->makeDirectPaymentReferback();
        $fakeDirectPaymentReferback = $this->fakeDirectPaymentReferbackData();
        $updatedDirectPaymentReferback = $this->directPaymentReferbackRepo->update($fakeDirectPaymentReferback, $directPaymentReferback->id);
        $this->assertModelData($fakeDirectPaymentReferback, $updatedDirectPaymentReferback->toArray());
        $dbDirectPaymentReferback = $this->directPaymentReferbackRepo->find($directPaymentReferback->id);
        $this->assertModelData($fakeDirectPaymentReferback, $dbDirectPaymentReferback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteDirectPaymentReferback()
    {
        $directPaymentReferback = $this->makeDirectPaymentReferback();
        $resp = $this->directPaymentReferbackRepo->delete($directPaymentReferback->id);
        $this->assertTrue($resp);
        $this->assertNull(DirectPaymentReferback::find($directPaymentReferback->id), 'DirectPaymentReferback should not exist in DB');
    }
}
