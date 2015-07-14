<?php

class SV_TitleEditHistory_Listener
{
    const AddonNameSpace = 'SV_TitleEditHistory';

    public static function install($existingAddOn, $addOnData)
    {
        $version = isset($existingAddOn['version_id']) ? $existingAddOn['version_id'] : 0;
        $db = XenForo_Application::getDb();

        $db->query("
            INSERT IGNORE INTO xf_content_type
                (content_type, addon_id, fields)
            VALUES
                ('thread_title', '".self::AddonNameSpace."', '')
        ");

        $db->query("
            INSERT IGNORE INTO xf_content_type_field
                (content_type, field_name, field_value)
            VALUES
                ('thread_title', 'edit_history_handler_class', '".self::AddonNameSpace."_EditHistoryHandler_Thread')
        ");

        if ($version == 0)
        {
            SV_TitleEditHistory_Install::addColumn('xf_thread','thread_title_edit_count', 'int not null default 0');
            SV_TitleEditHistory_Install::addColumn('xf_thread','thread_title_last_edit_date', 'int not null default 0');
            SV_TitleEditHistory_Install::addColumn('xf_thread','thread_title_last_edit_user_id', 'int not null default 0');
        }
        
        if ($version < 10010)
        {
            SV_TitleEditHistory_Install::renameColumn('xf_thread','edit_count', 'thread_title_edit_count', 'int not null default 0');
            SV_TitleEditHistory_Install::renameColumn('xf_thread','last_edit_date', 'thread_title_last_edit_date', 'int not null default 0');
            SV_TitleEditHistory_Install::renameColumn('xf_thread','last_edit_user_id', 'thread_title_last_edit_user_id', 'int not null default 0');
        }

        XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();
        return true;
    }

    public static function uninstall()
    {
        $db = XenForo_Application::get('db');

        $db->query("
            DELETE FROM xf_content_type
            WHERE xf_content_type.addon_id = '".self::AddonNameSpace."_EditHistoryHandler_Thread'
        ");

        $db->query("
            DELETE FROM xf_content_type_field
            WHERE xf_content_type_field.field_value = '".self::AddonNameSpace."_EditHistoryHandler_Thread'
        ");

        XenForo_Model::create('XenForo_Model_ContentType')->rebuildContentTypeCache();
        return true;
    }

    public static function load_class($class, array &$extend)
    {
        $extend[] = self::AddonNameSpace.'_'.$class;
    }
}