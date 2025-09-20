<?php

namespace App\Http\Resources\Concerns;

trait CompletableResource
{
    protected bool $complete = false;

    public function complete(bool $on = true): static
    {
        $this->complete = $on;

        return $this;
    }

    public static function collectionComplete($resource, bool $on = true)
    {
        $res = parent::collection($resource);

        $res->collection = $res->collection->map(fn ($r) => $r->complete($on));

        return $res;
    }
}
