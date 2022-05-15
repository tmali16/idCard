<?php

namespace Database\Seeders;

use App\Models\Geo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

class GeoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ini_set('memory_limit', -1);
        $f =  ("H://bsfordb_11052022.csv");
        $ar = $this->csvs($f);
        $id=429333;
        foreach ($ar as $key=> $r) {
            $old = Geo::where('lac', $r['lac'])->where('ci', $r['ci'])->where('mnc', $r['mnc'])->where('azimuth', $r['azimuth'])->count();
            if($old == 0) {
                $da = [];
                try {
//                    $da = [];
//                    foreach ($r as $k => $v) {
//                        switch ($k) {
//                            case '﻿mcc':
//                                $da['mcc']=(int)$v;
//                                break;
//                            case 'site_lon' || 'site_lat' || 'cvr_cntr_lon' || 'cvr_cntr_lat':
//                                $da[$k] = str_replace(',', '.', $v);
//                                break;
//                            case "height":
//                                $da['height'] = null;
//                                break;
//                            default:
//                                $da[$k] = strlen(trim($v)) === 0 ? NULL : trim($v);
//                                break;
//                        }
//                    }
                    print_r($id . "\n");
                    $da = [
                        'id'=>$id,
                        'mcc'=>437,
                        'mnc'=>(int)$r['mnc'],
                        'region'=>(int)$r['region'],
                        'lac'=>(int)$r['lac'],
                        'ci'=>(int)$r['ci'],
                        'adress'=>$r['adress'],
                        'bsid'=>$r['bsid']??0,
                        'sectorname'=>$r['sectorname'],
                        'diapason'=>$r['diapason'],
                        'type'=>$r['type'],
                        'azimuth'=>$r['azimuth'],
                        'inclination'=>strlen($r['inclination']) == 0 ? null: $r['inclination'],
                        'height'=>strlen($r['height']) == 0 ? null: $r['height'],
                        'power'=>strlen($r['power']) == 0 ? null: $r['power'],
                        'amplification'=>strlen($r['amplification']) == 0 ? 0: $r['amplification'],
                        'bandwidth'=>strlen($r['bandwidth']) == 0 ? null: $r['bandwidth'],
                        'polarization'=>strlen($r['polarization']) == 0 ? null: $r['polarization'],
                        'controller'=>$r['controller'] ?? 0,
                        'g'=>strlen($r['g']) == 0 ? 0: $r['g'],
                        'site_lon'=>str_replace(',', '.', $r['site_lon']),
                        'site_lat'=>str_replace(',', '.', $r['site_lat']),
                    ];
//                    var_dump($da);
                    Geo::insert($da);
                    $id =  $id+1;
                } catch (\Exception $e) {
//                    print_r($da['ci'] . "\n");
//                    print_r($e->getMessage());
//                    Log::error($da['ci']  . "; " .$e->getMessage());
//                    break;
                }
            }
        }

    }
    function csvs($csvfile) {
        $csv = Array();
        $rowcount = 0;
        if (($handle = fopen($csvfile, "r")) !== FALSE) {
            $max_line_length = defined('MAX_LINE_LENGTH') ? MAX_LINE_LENGTH : 10000;
            $header = fgetcsv($handle, $max_line_length, ";");
            $header_colcount = count($header);
            while (($row = fgetcsv($handle, $max_line_length, ";")) !== FALSE) {
                $row_colcount = count($row);
                if ($row_colcount == $header_colcount) {
                    $entry = array_combine($header, $row);
                    $csv[] = $entry;
                }
                else {
                    print_r($row);
                    print_r($header);
                    print_r("csvreader: Invalid number of columns at line " . ($rowcount + 2) . " (row " . ($rowcount + 1) . "). Expected=$header_colcount Got=$row_colcount\n");
                    return null;
                }
                $rowcount++;
            }
            echo "Totally $rowcount rows found\n";
            fclose($handle);
        }
        else {
            print_r("csvreader: Could not read CSV \"$csvfile\"");
            return null;
        }
        return $csv;
    }
}