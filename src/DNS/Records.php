<?php

namespace Studiow\DnsWatch\DNS;

use Countable;
use JsonSerializable;

class Records implements Countable, JsonSerializable
{
    private $records = [];

    public function __construct(array $records = [])
    {
        array_map([$this, 'add'], $records);
    }

    public function add(Record $record)
    {
        $this->records[] = $record;
    }

    public function contains(Record $needle): bool
    {
        return array_reduce($this->records, function (bool $found, Record $record) use ($needle) {
            if (! $found) {
                $found = $record->equals($needle);
            }

            return $found;
        }, false);
    }

    public function count(): int
    {
        return count($this->records);
    }

    public function withType(string $type): Records
    {
        return new static(
            array_filter($this->records, function (Record $record) use ($type) {
                return $record->getType() === $type;
            })
        );
    }

    public function jsonSerialize()
    {
        return $this->records;
    }
}
