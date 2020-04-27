<?php

namespace Studiow\DnsWatch\DNS;

use Countable;

class Changes implements Countable
{
    private $changes = [];

    public function __construct(array $changes = [])
    {
        $this->changes = $changes;
    }

    public function hasChanges(?string $type = null): bool
    {
        if (is_null($type)) {
            return $this->count() > 0;
        }

        return array_key_exists($type, $this->changes) && $this->changes[$type] > 0;
    }

    public function countChanges(?string $type = null): int
    {
        if (is_null($type)) {
            return $this->count();
        }

        return $this->hasChanges($type) ? $this->changes[$type] : 0;
    }

    public function count()
    {
        return array_sum($this->changes);
    }
}
