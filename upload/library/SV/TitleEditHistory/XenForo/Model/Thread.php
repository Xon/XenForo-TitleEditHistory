<?php

class SV_TitleEditHistory_XenForo_Model_Thread extends XFCP_SV_TitleEditHistory_XenForo_Model_Thread
{
    public function canViewThreadTitleHistory(array $thread, array $forum, &$errorPhraseKey = '', array $nodePermissions = null, array $viewingUser = null)
    {
        $this->standardizeViewingUserReferenceForNode($thread['node_id'], $viewingUser, $nodePermissions);

        if (!$viewingUser['user_id'])
        {
            return false;
        }

        if (!XenForo_Application::getOptions()->editHistory['enabled'])
        {
            return false;
        }

        if (XenForo_Permission::hasContentPermission($nodePermissions, 'editAnyPost'))
        {
            return true;
        }

        return false;
    }
}