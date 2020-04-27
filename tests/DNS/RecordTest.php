<?php

namespace Studiow\DnsWatch\Test\DNS;

use PHPUnit\Framework\TestCase;
use Studiow\DnsWatch\DNS\Record;

class RecordTest extends TestCase
{
    public function testItCanCalculateDifference()
    {
        $a = new Record('A', 'original-value', 100);
        $b = new Record('CNAME', 'changed-value', 200, 1);

        $this->assertFalse($a->equals($b));
        $this->assertEquals(['type', 'value', 'ttl', 'prio'], $a->delta($b));

        $this->assertTrue($a->equals($a));
        $this->assertEmpty($a->delta($a));
    }

    public function testARecordCanBeCreatedFromArray()
    {
        $input = [
            'host' => 'example.com',
            'class' => 'IN',
            'ttl' => '4502',
            'type' => 'A',
            'ip' => '93.184.216.34',
        ];

        $expected = new Record('A', '93.184.216.34', 4502, null);
        $this->assertEquals($expected, Record::fromArray($input));
    }

    public function testAAAARecordCanBeCreatedFromArray()
    {
        $input = [
            'host' => 'example.com',
            'class' => 'IN',
            'ttl' => '4502',
            'type' => 'AAAA',
            'ipv6' => '2606:2800:220:1:248:1893:25c8:1946',
        ];

        $expected = new Record('AAAA', '2606:2800:220:1:248:1893:25c8:1946', 4502, null);
        $this->assertEquals($expected, Record::fromArray($input));
    }

    public function testMXRecordCanBeCreatedFromArray()
    {
        $input = [
            'host' => 'example.com',
            'class' => 'IN',
            'ttl' => '4502',
            'type' => 'MX',
            'pri' => 0,
            'target' => '',
        ];

        $expected = new Record('MX', '', 4502, 0);
        $this->assertEquals($expected, Record::fromArray($input));
    }

    public function testNSRecordCanBeCreatedFromArray()
    {
        $input = [
            'host' => 'example.com',
            'class' => 'IN',
            'ttl' => '4502',
            'type' => 'NS',
            'target' => 'a.iana-servers.net',
        ];

        $expected = new Record('NS', 'a.iana-servers.net', 4502, null);
        $this->assertEquals($expected, Record::fromArray($input));
    }

    public function testTXTRecordCanBeCreatedFromArray()
    {
        $input = [
            'host' => 'example.com',
            'class' => 'IN',
            'ttl' => '4502',
            'type' => 'TXT',
            'txt' => 'v=spf1 -all',
        ];

        $expected = new Record('TXT', 'v=spf1 -all', 4502, null);
        $this->assertEquals($expected, Record::fromArray($input));
    }

    public function testItCanBeJsonSerialized()
    {
        $record = new Record('A', 'original-value', 100);
        $json = '{"type":"A","value":"original-value","ttl":100,"prio":null}';

        $this->assertEquals($json, json_encode($record));
    }
}
