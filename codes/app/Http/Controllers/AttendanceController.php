<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Rats\Zkteco\Lib\ZKTeco;


class AttendanceController extends Controller
{
    public function getData($ip) {
        $zk = new ZKTeco($ip);

        return response()->json([
            "status" => "Success",
            "connect" => $zk->connect(),
            "getAttendance" => $zk->getAttendance(),

        ]);
    }
}
