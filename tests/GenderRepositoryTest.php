<?php namespace Tests\Repositories;

use App\Models\Gender;
use App\Repositories\GenderRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Traits\MakeGenderTrait;
use Tests\ApiTestTrait;

class GenderRepositoryTest extends TestCase
{
    use MakeGenderTrait, ApiTestTrait, DatabaseTransactions;

    /**
     * @var GenderRepository
     */
    protected $genderRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->genderRepo = \App::make(GenderRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_gender()
    {
        $gender = $this->fakeGenderData();
        $createdGender = $this->genderRepo->create($gender);
        $createdGender = $createdGender->toArray();
        $this->assertArrayHasKey('id', $createdGender);
        $this->assertNotNull($createdGender['id'], 'Created Gender must have id specified');
        $this->assertNotNull(Gender::find($createdGender['id']), 'Gender with given id must be in DB');
        $this->assertModelData($gender, $createdGender);
    }

    /**
     * @test read
     */
    public function test_read_gender()
    {
        $gender = $this->makeGender();
        $dbGender = $this->genderRepo->find($gender->id);
        $dbGender = $dbGender->toArray();
        $this->assertModelData($gender->toArray(), $dbGender);
    }

    /**
     * @test update
     */
    public function test_update_gender()
    {
        $gender = $this->makeGender();
        $fakeGender = $this->fakeGenderData();
        $updatedGender = $this->genderRepo->update($fakeGender, $gender->id);
        $this->assertModelData($fakeGender, $updatedGender->toArray());
        $dbGender = $this->genderRepo->find($gender->id);
        $this->assertModelData($fakeGender, $dbGender->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_gender()
    {
        $gender = $this->makeGender();
        $resp = $this->genderRepo->delete($gender->id);
        $this->assertTrue($resp);
        $this->assertNull(Gender::find($gender->id), 'Gender should not exist in DB');
    }
}
