<?php namespace Tests\Repositories;

use App\Models\RegisteredSupplierAttachment;
use App\Repositories\RegisteredSupplierAttachmentRepository;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\ApiTestTrait;

class RegisteredSupplierAttachmentRepositoryTest extends TestCase
{
    use ApiTestTrait, DatabaseTransactions;

    /**
     * @var RegisteredSupplierAttachmentRepository
     */
    protected $registeredSupplierAttachmentRepo;

    public function setUp() : void
    {
        parent::setUp();
        $this->registeredSupplierAttachmentRepo = \App::make(RegisteredSupplierAttachmentRepository::class);
    }

    /**
     * @test create
     */
    public function test_create_registered_supplier_attachment()
    {
        $registeredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->make()->toArray();

        $createdRegisteredSupplierAttachment = $this->registeredSupplierAttachmentRepo->create($registeredSupplierAttachment);

        $createdRegisteredSupplierAttachment = $createdRegisteredSupplierAttachment->toArray();
        $this->assertArrayHasKey('id', $createdRegisteredSupplierAttachment);
        $this->assertNotNull($createdRegisteredSupplierAttachment['id'], 'Created RegisteredSupplierAttachment must have id specified');
        $this->assertNotNull(RegisteredSupplierAttachment::find($createdRegisteredSupplierAttachment['id']), 'RegisteredSupplierAttachment with given id must be in DB');
        $this->assertModelData($registeredSupplierAttachment, $createdRegisteredSupplierAttachment);
    }

    /**
     * @test read
     */
    public function test_read_registered_supplier_attachment()
    {
        $registeredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->create();

        $dbRegisteredSupplierAttachment = $this->registeredSupplierAttachmentRepo->find($registeredSupplierAttachment->id);

        $dbRegisteredSupplierAttachment = $dbRegisteredSupplierAttachment->toArray();
        $this->assertModelData($registeredSupplierAttachment->toArray(), $dbRegisteredSupplierAttachment);
    }

    /**
     * @test update
     */
    public function test_update_registered_supplier_attachment()
    {
        $registeredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->create();
        $fakeRegisteredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->make()->toArray();

        $updatedRegisteredSupplierAttachment = $this->registeredSupplierAttachmentRepo->update($fakeRegisteredSupplierAttachment, $registeredSupplierAttachment->id);

        $this->assertModelData($fakeRegisteredSupplierAttachment, $updatedRegisteredSupplierAttachment->toArray());
        $dbRegisteredSupplierAttachment = $this->registeredSupplierAttachmentRepo->find($registeredSupplierAttachment->id);
        $this->assertModelData($fakeRegisteredSupplierAttachment, $dbRegisteredSupplierAttachment->toArray());
    }

    /**
     * @test delete
     */
    public function test_delete_registered_supplier_attachment()
    {
        $registeredSupplierAttachment = factory(RegisteredSupplierAttachment::class)->create();

        $resp = $this->registeredSupplierAttachmentRepo->delete($registeredSupplierAttachment->id);

        $this->assertTrue($resp);
        $this->assertNull(RegisteredSupplierAttachment::find($registeredSupplierAttachment->id), 'RegisteredSupplierAttachment should not exist in DB');
    }
}
