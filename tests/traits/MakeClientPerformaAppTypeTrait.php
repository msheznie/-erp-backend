<?php namespace Tests\Traits;

use Faker\Factory as Faker;
use App\Models\ClientPerformaAppType;
use App\Repositories\ClientPerformaAppTypeRepository;

trait MakeClientPerformaAppTypeTrait
{
    /**
     * Create fake instance of ClientPerformaAppType and save it in database
     *
     * @param array $clientPerformaAppTypeFields
     * @return ClientPerformaAppType
     */
    public function makeClientPerformaAppType($clientPerformaAppTypeFields = [])
    {
        /** @var ClientPerformaAppTypeRepository $clientPerformaAppTypeRepo */
        $clientPerformaAppTypeRepo = \App::make(ClientPerformaAppTypeRepository::class);
        $theme = $this->fakeClientPerformaAppTypeData($clientPerformaAppTypeFields);
        return $clientPerformaAppTypeRepo->create($theme);
    }

    /**
     * Get fake instance of ClientPerformaAppType
     *
     * @param array $clientPerformaAppTypeFields
     * @return ClientPerformaAppType
     */
    public function fakeClientPerformaAppType($clientPerformaAppTypeFields = [])
    {
        return new ClientPerformaAppType($this->fakeClientPerformaAppTypeData($clientPerformaAppTypeFields));
    }

    /**
     * Get fake data of ClientPerformaAppType
     *
     * @param array $clientPerformaAppTypeFields
     * @return array
     */
    public function fakeClientPerformaAppTypeData($clientPerformaAppTypeFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'description' => $fake->word,
            'timestamp' => $fake->date('Y-m-d H:i:s')
        ], $clientPerformaAppTypeFields);
    }
}
