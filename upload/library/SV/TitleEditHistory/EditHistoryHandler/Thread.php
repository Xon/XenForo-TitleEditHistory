<?php

class SV_TitleEditHistory_EditHistoryHandler_Thread extends XenForo_EditHistoryHandler_Abstract
{
    protected $_prefix = 'threads';

    protected function _getContent($contentId, array $viewingUser)
    {
        $threadModel = $this->_getThreadModel();

        $thread = $threadModel->getThreadById(
            $contentId, [
            'join'                    => XenForo_Model_Thread::FETCH_FORUM |
                                         XenForo_Model_Thread::FETCH_FORUM_OPTIONS |
                                         XenForo_Model_Thread::FETCH_USER,
            'permissionCombinationId' => $viewingUser['permission_combination_id']
        ]
        );
        if ($thread)
        {
            $thread['permissions'] = XenForo_Permission::unserializePermissions($thread['node_permission_cache']);
        }

        return $thread;
    }

    protected function _canViewHistoryAndContent(array $content, array $viewingUser)
    {
        $threadModel = $this->_getThreadModel();

        return $threadModel->canViewThreadAndContainer($content, $content, $null, $content['permissions'], $viewingUser) &&
               $threadModel->canViewThreadTitleHistory($content, $content, $null, $content['permissions'], $viewingUser);
    }

    protected function _canRevertContent(array $content, array $viewingUser)
    {
        $threadModel = $this->_getThreadModel();

        return $threadModel->canEditThreadTitle($content, $content, $null, $content['permissions'], $viewingUser);
    }

    public function getText(array $content)
    {
        return htmlspecialchars($content['title']);
    }

    public function getTitle(array $content)
    {
        //return new XenForo_Phrase('post_in_thread_x', array('title' => $content['title']));
        return htmlspecialchars($content['title']); // TODO
    }

    public function getBreadcrumbs(array $content)
    {
        /* @var $nodeModel XenForo_Model_Node */
        $nodeModel = XenForo_Model::create('XenForo_Model_Node');

        $node = $nodeModel->getNodeById($content['node_id']);
        if ($node)
        {
            $crumb = $nodeModel->getNodeBreadCrumbs($node);
            $crumb[] = [
                'href'  => XenForo_Link::buildPublicLink('full:threads', $content),
                'value' => $content['title']
            ];

            return $crumb;
        }
        else
        {
            return [];
        }
    }

    public function getNavigationTab()
    {
        return 'forums';
    }

    public function formatHistory($string, XenForo_View $view)
    {
        return htmlspecialchars($string);
    }

    public function revertToVersion(array $content, $revertCount, array $history, array $previous = null)
    {
        $dw = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread', XenForo_DataWriter::ERROR_SILENT);
        $dw->setExistingData($content);
        $dw->set('title', $history['old_text']);
        $dw->set('thread_title_edit_count', $dw->get('thread_title_edit_count') + 1);
        if ($dw->get('thread_title_edit_count'))
        {
            if (!$previous || $previous['edit_user_id'] != $content['user_id'])
            {
                // if previous is a mod edit, don't show as it may have been hidden
                $dw->set('thread_title_last_edit_date', 0);
            }
            else if ($previous && $previous['edit_user_id'] == $content['user_id'])
            {
                $dw->set('thread_title_last_edit_date', $previous['edit_date']);
                $dw->set('thread_title_last_edit_user_id', $previous['edit_user_id']);
            }
        }

        return $dw->save();
    }

    protected $_threadModel = null;

    /**
     * @return SV_TitleEditHistory_XenForo_Model_Thread|XenForo_Model_Thread|XenForo_Model
     * @throws XenForo_Exception
     */
    protected function _getThreadModel()
    {
        if ($this->_threadModel === null)
        {
            $this->_threadModel = XenForo_Model::create('XenForo_Model_Thread');
        }

        return $this->_threadModel;
    }
}
