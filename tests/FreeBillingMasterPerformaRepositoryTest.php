<?php

use App\Models\FreeBillingMasterPerforma;
use App\Repositories\FreeBillingMasterPerformaRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FreeBillingMasterPerformaRepositoryTest extends TestCase
{
    use MakeFreeBillingMasterPerformaTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var FreeBillingMasterPerformaRepository
     */
    protected $freeBillingMasterPerformaRepo;

    public function setUp()
    {
        parent::setUp();
        $this->freeBillingMasterPerformaRepo = App::make(FreeBillingMasterPerformaRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateFreeBillingMasterPerforma()
    {
        $freeBillingMasterPerforma = $this->fakeFreeBillingMasterPerformaData();
        $createdFreeBillingMasterPerforma = $this->freeBillingMasterPerformaRepo->create($freeBillingMasterPerforma);
        $createdFreeBillingMasterPerforma = $createdFreeBillingMasterPerforma->toArray();
        $this->assertArrayHasKey('id', $createdFreeBillingMasterPerforma);
        $this->assertNotNull($createdFreeBillingMasterPerforma['id'], 'Created FreeBillingMasterPerforma must have id specified');
        $this->assertNotNull(FreeBillingMasterPerforma::find($createdFreeBillingMasterPerforma['id']), 'FreeBillingMasterPerforma with given id must be in DB');
        $this->assertModelData($freeBillingMasterPerforma, $createdFreeBillingMasterPerforma);
    }

    /**
     * @test read
     */
    public function testReadFreeBillingMasterPerforma()
    {
        $freeBillingMasterPerforma = $this->makeFreeBillingMasterPerforma();
        $dbFreeBillingMasterPerforma = $this->freeBillingMasterPerformaRepo->find($freeBillingMasterPerforma->id);
        $dbFreeBillingMasterPerforma = $dbFreeBillingMasterPerforma->toArray();
        $this->assertModelData($freeBillingMasterPerforma->toArray(), $dbFreeBillingMasterPerforma);
    }

    /**
     * @test update
     */
    public function testUpdateFreeBillingMasterPerforma()
    {
        $freeBillingMasterPerforma = $this->makeFreeBillingMasterPerforma();
        $fakeFreeBillingMasterPerforma = $this->fakeFreeBillingMasterPerformaData();
        $updatedFreeBillingMasterPerforma = $this->freeBillingMasterPerformaRepo->update($fakeFreeBillingMasterPerforma, $freeBillingMasterPerforma->id);
        $this->assertModelData($fakeFreeBillingMasterPerforma, $updatedFreeBillingMasterPerforma->toArray());
        $dbFreeBillingMasterPerforma = $this->freeBillingMasterPerformaRepo->find($freeBillingMasterPerforma->id);
        $this->assertModelData($fakeFreeBillingMasterPerforma, $dbFreeBillingMasterPerforma->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteFreeBillingMasterPerforma()
    {
        $freeBillingMasterPerforma = $this->makeFreeBillingMasterPerforma();
        $resp = $this->freeBillingMasterPerformaRepo->delete($freeBillingMasterPerforma->id);
        $this->assertTrue($resp);
        $this->assertNull(FreeBillingMasterPerforma::find($freeBillingMasterPerforma->id), 'FreeBillingMasterPerforma should not exist in DB');
    }
}
