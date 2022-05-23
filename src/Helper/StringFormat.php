<?php

namespace App\Helper;

use Doctrine\Common\Collections\ArrayCollection;

class StringFormat
{

    public static function CSV(array|ArrayCollection $values, ?string $method = null): string
    {
        if(!$values instanceof ArrayCollection) {
            $values = new ArrayCollection($values);
        }
        $string = "";
        $last = $values->count() - 1;
        foreach ($values as $key => $value){
            if(is_null($method)) {
                if ($key === $last) {
                    $string .= $value;
                } elseif ($key === $last - 1) {
                    $string .= $value . " et ";
                } else {
                    $string .= $value . ", ";
                }
            } else {
                if ($key === $last) {
                    $string .= $value->$method();
                } elseif ($key === $last - 1) {
                    $string .= $value->$method() . " et ";
                } else {
                    $string .= $value->$method() . ", ";
                }
            }
        }
        return $string;
    }

}