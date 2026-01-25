<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Helpers\Waypoints;

class WaypointsTest extends TestCase
{
    public function testNormalizeWithIndexedArray()
    {
        $input = [
            ['Shop A', '16.1', '96.1', 'Dagon'],
            ['Shop B', 17.2, 97.2, 'Bahan']
        ];

        $out = Waypoints::normalize($input);

        $this->assertCount(2, $out);
        $this->assertEquals('Shop A', $out[0]['name']);
        $this->assertIsFloat($out[0]['lat']);
        $this->assertEquals('Dagon', $out[0]['region']);
    }

    public function testNormalizeWithAssocArray()
    {
        $input = [
            ['name' => 'X', 'lat' => '15.5', 'lng' => '95.5', 'region' => 'Insein']
        ];

        $out = Waypoints::normalize($input);
        $this->assertCount(1, $out);
        $this->assertEquals('X', $out[0]['name']);
        $this->assertEquals(15.5, $out[0]['lat']);
    }

    public function testNormalizeSkipsInvalid()
    {
        $input = [
            ['name' => 'Bad', 'lat' => null, 'lng' => null],
            ['Shop C', '18.0', '98.0']
        ];

        $out = Waypoints::normalize($input);
        $this->assertCount(1, $out);
        $this->assertEquals('Shop C', $out[0]['name']);
    }

    public function testNormalizeFromJsonString()
    {
        $json = json_encode([
            ['Shop D', '19.0', '99.0', 'Tamwe']
        ]);

        $out = Waypoints::normalize($json);
        $this->assertCount(1, $out);
        $this->assertEquals('Tamwe', $out[0]['region']);
    }
}
