<?php

use Faker\Factory as Faker;
use App\Models\Priority;
use App\Repositories\PriorityRepository;

trait MakePriorityTrait
{
    /**
     * Create fake instance of Priority and save it in database
     *
     * @param array $priorityFields
     * @return Priority
     */
    public function makePriority($priorityFields = [])
    {
        /** @var PriorityRepository $priorityRepo */
        $priorityRepo = App::make(PriorityRepository::class);
        $theme = $this->fakePriorityData($priorityFields);
        return $priorityRepo->create($theme);
    }

    /**
     * Get fake instance of Priority
     *
     * @param array $priorityFields
     * @return Priority
     */
    public function fakePriority($priorityFields = [])
    {
        return new Priority($this->fakePriorityData($priorityFields));
    }

    /**
     * Get fake data of Priority
     *
     * @param array $postFields
     * @return array
     */
    public function fakePriorityData($priorityFields = [])
    {
        $fake = Faker::create();

        return array_merge([
            'priorityDescription' => $fake->word
        ], $priorityFields);
    }
}
