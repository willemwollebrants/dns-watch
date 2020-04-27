<?php

namespace Studiow\DnsWatch\Test\DNS;

use PHPUnit\Framework\TestCase;
use Studiow\DnsWatch\DNS\Changes;

class ChangesTest extends TestCase
{
    public function testItCanBeCounted()
    {
        $empty = new Changes(['A' => 0, 'AAAA' => 0]);
        $this->assertEquals(0, count($empty));

        $this->assertFalse($empty->hasChanges());

        $changes = new Changes(['A' => 1, 'AAAA' => 2]);

        $this->assertEquals(3, count($changes));
        $this->assertEquals(1, $changes->countChanges('A'));
        $this->assertEquals(2, $changes->countChanges('AAAA'));

        $this->assertTrue($changes->hasChanges());
        $this->assertTrue($changes->hasChanges('A'));
        $this->assertTrue($changes->hasChanges('AAAA'));
        $this->assertFalse($changes->hasChanges('MX'));
    }
}
