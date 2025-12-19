<?php namespace Tests\Repositories;

use App\Models\GrvDetailsPrn;
use App\Repositories\GrvDetailsPrnRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class GrvDetailsPrnRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var GrvDetailsPrnRepository
     */
    protected $grvDetailsPrnRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->grvDetailsPrnRepo = \App::make(GrvDetailsPrnRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_grv_details_prn()
    {
        $grvDetailsPrn = factory(GrvDetailsPrn::class)->make()->toArray();

        $createdGrvDetailsPrn = $this->grvDetailsPrnRepo->create($grvDetailsPrn);

        $createdGrvDetailsPrn = $createdGrvDetailsPrn->toArray();
        $this->assertArrayHasKey('id', $createdGrvDetailsPrn);
        $this->assertNotNull($createdGrvDetailsPrn['id'], 'Created GrvDetailsPrn must have id specified');
        $this->assertNotNull(GrvDetailsPrn::find($createdGrvDetailsPrn['id']), 'GrvDetailsPrn with given id must be in DB');
        $this->assertModelData($grvDetailsPrn, $createdGrvDetailsPrn);
    }

    /**
     * @test read
     */
    public function test_read_grv_details_prn()
    {
        $grvDetailsPrn = factory(GrvDetailsPrn::class)->create();

        $dbGrvDetailsPrn = $this->grvDetailsPrnRepo->find($grvDetailsPrn->id);

        $dbGrvDetailsPrn = $dbGrvDetailsPrn->toArray();
        $this->assertModelData($grvDetailsPrn->toArray(), $dbGrvDetailsPrn);
    }

    /**
     * @test update
     */
    public function test_update_grv_details_prn()
    {
        $grvDetailsPrn = factory(GrvDetailsPrn::class)->create();
        $fakeGrvDetailsPrn = factory(GrvDetailsPrn::class)->make()->toArray();

        $updatedGrvDetailsPrn = $this->grvDetailsPrnRepo->update($fakeGrvDetailsPrn, $grvDetailsPrn->id);

        $this->assertModelData($fakeGrvDetailsPrn, $updatedGrvDetailsPrn->toArray());
        $dbGrvDetailsPrn = $this->grvDetailsPrnRepo->find($grvDetailsPrn->id);
        $this->assertModelData($fakeGrvDetailsPrn, $dbGrvDetailsPrn->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_grv_details_prn()
    {
        $grvDetailsPrn = factory(GrvDetailsPrn::class)->create();

        $resp = $this->grvDetailsPrnRepo->delete($grvDetailsPrn->id);

        $this->assertTrue($resp);
        $this->assertNull(GrvDetailsPrn::find($grvDetailsPrn->id), 'GrvDetailsPrn should not exist in DB');
    }
}
