<?php

class SV_TitleEditHistory_Listener
{
    public static function load_class($class, array &$extend)
    {
        $extend[] = 'SV_TitleEditHistory_' . $class;
    }
}
