<?php

class SV_TitleEditHistory_Listener
{
    const AddonNameSpace = 'SV_TitleEditHistory_';

    public static function load_class($class, array &$extend)
    {
        $extend[] = self::AddonNameSpace.$class;
    }
}