<?php

class Dadacallback{
    public function __construct()
    {
    }
    //
    public function index(){
    }
    public function baiduMapTogaodeMap($lng, $lat)
    {
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 *  sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $lngs = $z * cos($theta);
        $lats = $z * sin($theta);
        return [ 'lng'=>$lngs, 'lat'=>$lats ];
    }
    // 百度转高德
    function Convert_BD09_To_GCJ02($lat,$lng){
        $x_pi = 3.14159265358979324 * 3000.0 / 180.0;
        $x = $lng - 0.0065;
        $y = $lat - 0.006;
        $z = sqrt($x * $x + $y * $y) - 0.00002 * sin($y * $x_pi);
        $theta = atan2($y, $x) - 0.000003 * cos($x * $x_pi);
        $lng = $z * cos($theta);
        $lat = $z * sin($theta);
        return array('lat'=>$lat,'lng'=>$lng);
    }



}
$obj = new Dadacallback();

