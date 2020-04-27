<?php

namespace Studiow\DnsWatch\Test\DNS;

use PHPUnit\Framework\TestCase;
use Studiow\DnsWatch\DNS\Record;
use Studiow\DnsWatch\DNS\Records;

class RecordsTest extends TestCase
{
    public function testItCanBeSearched()
    {
        $records = new Records([
            new Record('A', 'value', 100),
        ]);

        $this->assertTrue($records->contains(new Record('A', 'value', 100)));

        //different type
        $this->assertFalse($records->contains(new Record('AAAA', 'value', 100)));

        //different value
        $this->assertFalse($records->contains(new Record('A', 'other', 100)));

        //different TTL
        $this->assertFalse($records->contains(new Record('A', 'value', 200)));

        //different priority
        $this->assertFalse($records->contains(new Record('A', 'value', 100, 1)));
    }

    public function testItCanBeCounted()
    {
        $records = new Records();
        $this->assertEquals(0, count($records));

        $records = new Records([
            new Record('A', 'value', 100),
            new Record('AAAA', 'value', 100),
        ]);
        $this->assertEquals(2, count($records));
    }

    public function testItCanBeSlicedByType()
    {
        $records = new Records([
            new Record('A', 'value', 100),
            new Record('AAAA', 'value', 100),
        ]);

        $onlyA = new Records([
            new Record('A', 'value', 100),
        ]);

        $this->assertEquals($onlyA, $records->withType('A'));
    }

    public function testItCanBeJsonSerialized()
    {
        $records = new Records([
            new Record('A', 'A-value', 100),
            new Record('AAAA', 'AAAA-value', 100),
        ]);

        $json = '[{"type":"A","value":"A-value","ttl":100,"prio":null},{"type":"AAAA","value":"AAAA-value","ttl":100,"prio":null}]';
        $this->assertEquals($json, json_encode($records));
    }

    public function testItCanDetectChanges()
    {
        $original = new Records([
            new Record('A', 'A-value', 100),
            new Record('AAAA', 'AAAA-value', 100),
        ]);

        $modified = new Records([
            new Record('A', 'A-value', 100),
            new Record('AAAA', 'AAAA-value-change', 100),
        ]);

        $this->assertEquals(0, $original->delta($modified)->countChanges('A'));
        $this->assertEquals(1, $original->delta($modified)->countChanges('AAAA'));

        $this->assertEquals(0, $original->delta($modified)->countChanges('MX'));
    }

    public function testItCanDetectAdditions()
    {
        $original = new Records([
            new Record('A', 'A-value', 100),
        ]);

        $modified = new Records([
            new Record('A', 'A-value', 100),
            new Record('AAAA', 'AAAA-value-added', 100),
        ]);

        $this->assertEquals(0, $original->delta($modified)->countChanges('A'));
        $this->assertEquals(1, $original->delta($modified)->countChanges('AAAA'));
    }

    public function testItCanDetectDeletions()
    {
        $original = new Records([
            new Record('A', 'A-value', 100),
            new Record('AAAA', 'AAAA-value-to-delete', 100),
        ]);

        $modified = new Records([
            new Record('A', 'A-value', 100),
        ]);

        $this->assertEquals(0, $original->delta($modified)->countChanges('A'));
        $this->assertEquals(1, $original->delta($modified)->countChanges('AAAA'));
    }
}
