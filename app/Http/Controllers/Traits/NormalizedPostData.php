<?php

namespace App\Http\Controllers\Traits;

trait NormalizedPostData
{
    public function getNormalizedPostData()
    {
        $data = $_POST;
        $data = json_encode($data);
        $data = str_replace("\u0000", '', $data);
        $data = json_decode($data, true);

        return $data;
    }
}
