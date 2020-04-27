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

    public function delta(Records $compare)
    {
        $out = [];
        foreach (Record::TYPES as $type) {
            $out[$type] = $this->deltaPerType($type, $compare);
        }

        return new Changes($out);
    }

    private function deltaPerType(string $type, Records $compare): int
    {
        $mine = $this->withType($type);
        $theirs = $compare->withType($type);

        //was empty, is empty
        if (count($mine) + count($theirs) === 0) {
            return 0;
        }

        if (count($mine) > count($theirs)) {
            return array_reduce($mine->records, function (int $diffCount, Record $record) use ($theirs) {
                if (! $theirs->contains($record)) {
                    ++$diffCount;
                }

                return $diffCount;
            }, 0);
        }

        return array_reduce($theirs->records, function (int $diffCount, Record $record) use ($mine) {
            if (! $mine->contains($record)) {
                ++$diffCount;
            }

            return $diffCount;
        }, 0);
    }
}
