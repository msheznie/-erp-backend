<?php namespace Tests\Repositories;

use App\Models\POSSatgSalesReturn;
use App\Repositories\POSSatgSalesReturnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class POSSatgSalesReturnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var POSSatgSalesReturnRepository
     */
    protected $pOSSatgSalesReturnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->pOSSatgSalesReturnRepo = \App::make(POSSatgSalesReturnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_p_o_s_satg_sales_return()
    {
        $pOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->make()->toArray();

        $createdPOSSatgSalesReturn = $this->pOSSatgSalesReturnRepo->create($pOSSatgSalesReturn);

        $createdPOSSatgSalesReturn = $createdPOSSatgSalesReturn->toArray();
        $this->assertArrayHasKey('id', $createdPOSSatgSalesReturn);
        $this->assertNotNull($createdPOSSatgSalesReturn['id'], 'Created POSSatgSalesReturn must have id specified');
        $this->assertNotNull(POSSatgSalesReturn::find($createdPOSSatgSalesReturn['id']), 'POSSatgSalesReturn with given id must be in DB');
        $this->assertModelData($pOSSatgSalesReturn, $createdPOSSatgSalesReturn);
    }

    /**
     * @test read
     */
    public function test_read_p_o_s_satg_sales_return()
    {
        $pOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->create();

        $dbPOSSatgSalesReturn = $this->pOSSatgSalesReturnRepo->find($pOSSatgSalesReturn->id);

        $dbPOSSatgSalesReturn = $dbPOSSatgSalesReturn->toArray();
        $this->assertModelData($pOSSatgSalesReturn->toArray(), $dbPOSSatgSalesReturn);
    }

    /**
     * @test update
     */
    public function test_update_p_o_s_satg_sales_return()
    {
        $pOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->create();
        $fakePOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->make()->toArray();

        $updatedPOSSatgSalesReturn = $this->pOSSatgSalesReturnRepo->update($fakePOSSatgSalesReturn, $pOSSatgSalesReturn->id);

        $this->assertModelData($fakePOSSatgSalesReturn, $updatedPOSSatgSalesReturn->toArray());
        $dbPOSSatgSalesReturn = $this->pOSSatgSalesReturnRepo->find($pOSSatgSalesReturn->id);
        $this->assertModelData($fakePOSSatgSalesReturn, $dbPOSSatgSalesReturn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_p_o_s_satg_sales_return()
    {
        $pOSSatgSalesReturn = factory(POSSatgSalesReturn::class)->create();

        $resp = $this->pOSSatgSalesReturnRepo->delete($pOSSatgSalesReturn->id);

        $this->assertTrue($resp);
        $this->assertNull(POSSatgSalesReturn::find($pOSSatgSalesReturn->id), 'POSSatgSalesReturn should not exist in DB');
    }
}
