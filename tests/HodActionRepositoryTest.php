<?php namespace Tests\Repositories;

use App\Models\HodAction;
use App\Repositories\HodActionRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class HodActionRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var HodActionRepository
     */
    protected $hodActionRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->hodActionRepo = \App::make(HodActionRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_hod_action()
    {
        $hodAction = factory(HodAction::class)->make()->toArray();

        $createdHodAction = $this->hodActionRepo->create($hodAction);

        $createdHodAction = $createdHodAction->toArray();
        $this->assertArrayHasKey('id', $createdHodAction);
        $this->assertNotNull($createdHodAction['id'], 'Created HodAction must have id specified');
        $this->assertNotNull(HodAction::find($createdHodAction['id']), 'HodAction with given id must be in DB');
        $this->assertModelData($hodAction, $createdHodAction);
    }

    /**
     * @test read
     */
    public function test_read_hod_action()
    {
        $hodAction = factory(HodAction::class)->create();

        $dbHodAction = $this->hodActionRepo->find($hodAction->id);

        $dbHodAction = $dbHodAction->toArray();
        $this->assertModelData($hodAction->toArray(), $dbHodAction);
    }

    /**
     * @test update
     */
    public function test_update_hod_action()
    {
        $hodAction = factory(HodAction::class)->create();
        $fakeHodAction = factory(HodAction::class)->make()->toArray();

        $updatedHodAction = $this->hodActionRepo->update($fakeHodAction, $hodAction->id);

        $this->assertModelData($fakeHodAction, $updatedHodAction->toArray());
        $dbHodAction = $this->hodActionRepo->find($hodAction->id);
        $this->assertModelData($fakeHodAction, $dbHodAction->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_hod_action()
    {
        $hodAction = factory(HodAction::class)->create();

        $resp = $this->hodActionRepo->delete($hodAction->id);

        $this->assertTrue($resp);
        $this->assertNull(HodAction::find($hodAction->id), 'HodAction should not exist in DB');
    }
}
