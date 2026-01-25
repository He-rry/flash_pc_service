<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\ExcelServiceProvider;
use App\Services\ShopService;

class ShopServiceTest extends TestCase
{
    public function test_parse_excel_to_waypoints_skips_header_and_empty_rows()
    {
        $csv = "name,lat,lng,region\n";
        $csv .= "Header,Lat,Lng,Region\n";
        $csv .= "Shop A,16.5,96.1,North\n";
        $csv .= ",,,\n";
        $csv .= "Shop B,17.0,97.0,South\n";

        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);
        $path = $meta['uri'];
        file_put_contents($path, $csv);

        $uploaded = new UploadedFile($path, 'shops.csv', null, null, true);

        $service = $this->app->make(ShopService::class);

        $waypoints = $service->parseExcelToWaypoints($uploaded);

        $this->assertIsArray($waypoints);
        $this->assertCount(2, $waypoints);
        $this->assertEquals('Shop A', $waypoints[0]['name']);
        $this->assertEquals(16.5, $waypoints[0]['lat']);
        $this->assertEquals(96.1, $waypoints[0]['lng']);
        $this->assertEquals('North', $waypoints[0]['region']);

        $this->assertEquals('Shop B', $waypoints[1]['name']);
    }
}
