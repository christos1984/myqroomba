<?php

namespace MyQRoomba\Libs;

class ArrayTransposer
{
    public static function transposeArray(array $arr)
    {
        $retData = array();

        foreach ($arr as $row => $columns) {
            foreach ($columns as $row2 => $column2) {
                $retData[$row2][$row] = $column2;
            }
        }

        return $retData;
    }
}
