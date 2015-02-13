<?php

class SV_TitleEditHistory_Listener
{
    const AddonNameSpace = 'SV_TitleEditHistory';

    public static function install($existingAddOn, $addOnData)
    {
        $db = XenForo_Application::getDb();

        $db->query("
            INSERT IGNORE INTO xf_content_type_field
                (content_type, field_name, field_value)
            VALUES
                ('thread', 'edit_history_handler_class', '".self::AddonNameSpace."_EditHistoryHandler_Thread'),
        ");
        return true;
    }

    public static function uninstall()
    {
        $db = XenForo_Application::get('db');

        $db->query("
            DELETE FROM xf_content_type_field
            WHERE xf_content_type_field.field_value = '".self::AddonNameSpace."_EditHistoryHandler_Thread'
        ");

        return true;
    }
  
    public static function load_class($class, array &$extend)
    {
        switch($class)
        {
            case 'XenForo_DataWriter_Discussion_Thread':
                $extend[] = self::AddonNameSpace.'_'.$class;
                break;
        }
    }
}