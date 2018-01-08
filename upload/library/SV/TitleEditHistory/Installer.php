<?php

class SV_TitleEditHistory_Installer
{
    const AddonNameSpace = 'SV_TitleEditHistory';

    public static function install($existingAddOn, $addOnData)
    {
        $version = isset($existingAddOn['version_id']) ? $existingAddOn['version_id'] : 0;
        $required = '5.4.0';
        $phpversion = phpversion();
        if (version_compare($phpversion, $required, '<'))
        {
            throw new XenForo_Exception(
                "PHP {$required} or newer is required. {$phpversion} does not meet this requirement. Please ask your host to upgrade PHP",
                true
            );
        }
        if (XenForo_Application::$versionId < 1030070)
        {
            throw new Exception('XenForo 1.3.0+ is Required!');
        }
        $db = XenForo_Application::getDb();

        $db->query(
            "
            INSERT IGNORE INTO xf_content_type
                (content_type, addon_id, fields)
            VALUES
                ('thread_title', 'SV_TitleEditHistory', '')
        "
        );

        $db->query(
            "
            INSERT IGNORE INTO xf_content_type_field
                (content_type, field_name, field_value)
            VALUES
                ('thread_title', 'edit_history_handler_class', 'SV_TitleEditHistory_EditHistoryHandler_Thread')
        "
        );

        if ($version != 0 && $version <= 10050)
        {
            // rename if possible
            SV_Utils_Install::renameColumn('xf_thread', 'edit_count', 'thread_title_edit_count', 'int not null default 0');
            SV_Utils_Install::renameColumn('xf_thread', 'last_edit_date', 'thread_title_last_edit_date', 'int not null default 0');
            SV_Utils_Install::renameColumn('xf_thread', 'last_edit_user_id', 'thread_title_last_edit_user_id', 'int not null default 0');
            // make sure we clean-up the old columns!
            SV_Utils_Install::dropColumn('xf_thread', 'edit_count');
            SV_Utils_Install::dropColumn('xf_thread', 'last_edit_date');
            SV_Utils_Install::dropColumn('xf_thread', 'last_edit_user_id');
        }

        SV_Utils_Install::addColumn('xf_thread', 'thread_title_edit_count', 'int not null default 0');
        SV_Utils_Install::addColumn('xf_thread', 'thread_title_last_edit_date', 'int not null default 0');
        SV_Utils_Install::addColumn('xf_thread', 'thread_title_last_edit_user_id', 'int not null default 0');
    }

    public static function uninstall()
    {
        $db = XenForo_Application::get('db');

        $db->query(
            "
            DELETE FROM xf_content_type
            WHERE xf_content_type.addon_id = 'SV_TitleEditHistory_EditHistoryHandler_Thread'
        "
        );

        $db->query(
            "
            DELETE FROM xf_content_type_field
            WHERE xf_content_type_field.field_value = 'SV_TitleEditHistory_EditHistoryHandler_Thread'
        "
        );

        $db->query(
            "
            DELETE FROM xf_edit_history
            WHERE content_type = 'thread_title'
        "
        );

        SV_Utils_Install::dropColumn('xf_thread', 'thread_title_edit_count');
        SV_Utils_Install::dropColumn('xf_thread', 'thread_title_last_edit_date');
        SV_Utils_Install::dropColumn('xf_thread', 'thread_title_last_edit_user_id');
    }
}
