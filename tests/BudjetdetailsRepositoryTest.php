<?php

use App\Models\Budjetdetails;
use App\Repositories\BudjetdetailsRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class BudjetdetailsRepositoryTest extends TestCase
{
    use MakeBudjetdetailsTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var BudjetdetailsRepository
     */
    protected $budjetdetailsRepo;

    public function setUp()
    {
        parent::setUp();
        $this->budjetdetailsRepo = App::make(BudjetdetailsRepository::class);
    }

    /**
     * @test create
     */
    public function testCreateBudjetdetails()
    {
        $budjetdetails = $this->fakeBudjetdetailsData();
        $createdBudjetdetails = $this->budjetdetailsRepo->create($budjetdetails);
        $createdBudjetdetails = $createdBudjetdetails->toArray();
        $this->assertArrayHasKey('id', $createdBudjetdetails);
        $this->assertNotNull($createdBudjetdetails['id'], 'Created Budjetdetails must have id specified');
        $this->assertNotNull(Budjetdetails::find($createdBudjetdetails['id']), 'Budjetdetails with given id must be in DB');
        $this->assertModelData($budjetdetails, $createdBudjetdetails);
    }

    /**
     * @test read
     */
    public function testReadBudjetdetails()
    {
        $budjetdetails = $this->makeBudjetdetails();
        $dbBudjetdetails = $this->budjetdetailsRepo->find($budjetdetails->id);
        $dbBudjetdetails = $dbBudjetdetails->toArray();
        $this->assertModelData($budjetdetails->toArray(), $dbBudjetdetails);
    }

    /**
     * @test update
     */
    public function testUpdateBudjetdetails()
    {
        $budjetdetails = $this->makeBudjetdetails();
        $fakeBudjetdetails = $this->fakeBudjetdetailsData();
        $updatedBudjetdetails = $this->budjetdetailsRepo->update($fakeBudjetdetails, $budjetdetails->id);
        $this->assertModelData($fakeBudjetdetails, $updatedBudjetdetails->toArray());
        $dbBudjetdetails = $this->budjetdetailsRepo->find($budjetdetails->id);
        $this->assertModelData($fakeBudjetdetails, $dbBudjetdetails->toArray());
    }

    /**
     * @test delete
     */
    public function testDeleteBudjetdetails()
    {
        $budjetdetails = $this->makeBudjetdetails();
        $resp = $this->budjetdetailsRepo->delete($budjetdetails->id);
        $this->assertTrue($resp);
        $this->assertNull(Budjetdetails::find($budjetdetails->id), 'Budjetdetails should not exist in DB');
    }
}
