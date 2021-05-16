<?php

if (!function_exists('getAttributesValueFromBot')) {
    function getAttributesValueFromBot($attributes, $request)
    {
        $arrayOfAttributes = explode(',', $attributes);
        $value             = $request;
        foreach($arrayOfAttributes as $attribute) {
            if(!isset($value[$attribute])) return null;
            $value = $value[$attribute];
        }

        return $value;
    }
}
