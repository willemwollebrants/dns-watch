<?php

namespace Studiow\DnsWatch\DNS;

use JsonSerializable;

class Record implements JsonSerializable
{
    private $type;
    private $value;
    private $ttl;
    private $prio;

    public function __construct(string $type, string $value, int $ttl, ?int $prio = null)
    {
        $this->type = $type;
        $this->value = $value;
        $this->ttl = $ttl;
        $this->prio = $prio;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public static function fromArray(array $input): Record
    {
        $type = $input['type'];

        $valueKey = ([
                'A' => 'ip',
                'AAAA' => 'ipv6',
                'MX' => 'target',
                'NS' => 'target',
                'TXT' => 'txt',
            ])[$type] ?? null;

        return new static($type, $input[$valueKey], $input['ttl'], $input['prio'] ?? null);
    }

    public function delta(Record $compare): array
    {
        $different = [
            'type' => $this->type !== $compare->type,
            'value' => $this->value !== $compare->value,
            'ttl' => $this->ttl !== $compare->ttl,
            'prio' => $this->prio !== $compare->prio,
        ];

        return array_keys(array_filter($different));
    }

    public function equals(Record $compare): bool
    {
        return empty($this->delta($compare));
    }

    public function jsonSerialize()
    {
        return [
            'type' => $this->type,
            'value' => $this->value,
            'ttl' => $this->ttl,
            'prio' => $this->prio,
        ];
    }
}
