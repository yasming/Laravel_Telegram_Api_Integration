<?php

namespace Tests;

use DB;
use Jenssegers\Mongodb\Schema\Blueprint;

trait RefreshDatabase
{
    /**
     * Set up function.
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->dropAllCollections();
    }

    /**
     * Drop all collections.
     */
    protected function dropAllCollections(): void
    {
        $mongo = DB::connection('mongodb');

        foreach ($mongo->listCollections() as $collection) {
            (new Blueprint($mongo, $name = (string) $collection->getName()))->drop();
        }
    }
}