<?php

use App\Models\PoPaymentTermsRefferedback;
use App\Repositories\PoPaymentTermsRefferedbackRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PoPaymentTermsRefferedbackRepositoryTest extends TestCase
{
    use MakePoPaymentTermsRefferedbackTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var PoPaymentTermsRefferedbackRepository
     */
    protected $poPaymentTermsRefferedbackRepo;

    public function setUp()
    {
        parent::setUp();
        $this->poPaymentTermsRefferedbackRepo = App::make(PoPaymentTermsRefferedbackRepository::class);
    }

    /**
     * @test create
     */
    public function testCreatePoPaymentTermsRefferedback()
    {
        $poPaymentTermsRefferedback = $this->fakePoPaymentTermsRefferedbackData();
        $createdPoPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepo->create($poPaymentTermsRefferedback);
        $createdPoPaymentTermsRefferedback = $createdPoPaymentTermsRefferedback->toArray();
        $this->assertArrayHasKey('id', $createdPoPaymentTermsRefferedback);
        $this->assertNotNull($createdPoPaymentTermsRefferedback['id'], 'Created PoPaymentTermsRefferedback must have id specified');
        $this->assertNotNull(PoPaymentTermsRefferedback::find($createdPoPaymentTermsRefferedback['id']), 'PoPaymentTermsRefferedback with given id must be in DB');
        $this->assertModelData($poPaymentTermsRefferedback, $createdPoPaymentTermsRefferedback);
    }

    /**
     * @test read
     */
    public function testReadPoPaymentTermsRefferedback()
    {
        $poPaymentTermsRefferedback = $this->makePoPaymentTermsRefferedback();
        $dbPoPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepo->find($poPaymentTermsRefferedback->id);
        $dbPoPaymentTermsRefferedback = $dbPoPaymentTermsRefferedback->toArray();
        $this->assertModelData($poPaymentTermsRefferedback->toArray(), $dbPoPaymentTermsRefferedback);
    }

    /**
     * @test update
     */
    public function testUpdatePoPaymentTermsRefferedback()
    {
        $poPaymentTermsRefferedback = $this->makePoPaymentTermsRefferedback();
        $fakePoPaymentTermsRefferedback = $this->fakePoPaymentTermsRefferedbackData();
        $updatedPoPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepo->update($fakePoPaymentTermsRefferedback, $poPaymentTermsRefferedback->id);
        $this->assertModelData($fakePoPaymentTermsRefferedback, $updatedPoPaymentTermsRefferedback->toArray());
        $dbPoPaymentTermsRefferedback = $this->poPaymentTermsRefferedbackRepo->find($poPaymentTermsRefferedback->id);
        $this->assertModelData($fakePoPaymentTermsRefferedback, $dbPoPaymentTermsRefferedback->toArray());
    }

    /**
     * @test delete
     */
    public function testDeletePoPaymentTermsRefferedback()
    {
        $poPaymentTermsRefferedback = $this->makePoPaymentTermsRefferedback();
        $resp = $this->poPaymentTermsRefferedbackRepo->delete($poPaymentTermsRefferedback->id);
        $this->assertTrue($resp);
        $this->assertNull(PoPaymentTermsRefferedback::find($poPaymentTermsRefferedback->id), 'PoPaymentTermsRefferedback should not exist in DB');
    }
}
