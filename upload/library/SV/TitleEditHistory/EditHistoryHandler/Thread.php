<?php

class SV_TitleEditHistory_EditHistoryHandler_Thread extends XenForo_EditHistoryHandler_Abstract
{
    protected $_prefix = 'threads';
    
	protected function _getContent($contentId, array $viewingUser)
    {
		/* @var $postModel XenForo_Model_Post */
		$threadModel = XenForo_Model::create('XenForo_Model_Thread');
            
		$thread = $threadModel->getThreadById($contentId, array(
			'join' => XenForo_Model_Thread::FETCH_FORUM | XenForo_Model_Thread::FETCH_USER,
			'permissionCombinationId' => $viewingUser['permission_combination_id']
		));
		if ($thread)
		{
			$thread['permissions'] = XenForo_Permission::unserializePermissions($thread['node_permission_cache']);
		}

		return $thread;
    }

	protected function _canViewHistoryAndContent(array $content, array $viewingUser)
    {        
        $threadModel = XenForo_Model::create('XenForo_Model_Thread');
        
		return !$threadModel->canEditThreadTitle($content, $content, $null);
    }

	protected function _canRevertContent(array $content, array $viewingUser)
    {
        $threadModel = XenForo_Model::create('XenForo_Model_Thread');
        
		return !$threadModel->canEditThreadTitle($content, $content, $null);
    }

	public function getText(array $content)
    {
		return $content['title'];
    }

	public function getTitle(array $content)
    {
        //return new XenForo_Phrase('post_in_thread_x', array('title' => $content['title']));
        return $content['title']; // TODO
    }
    
	public function getBreadcrumbs(array $content)
    {
		/* @var $nodeModel XenForo_Model_Node */
		$nodeModel = XenForo_Model::create('XenForo_Model_Node');

		$node = $nodeModel->getNodeById($content['node_id']);
		if ($node)
		{
			$crumb = $nodeModel->getNodeBreadCrumbs($node);
			$crumb[] = array(
				'href' => XenForo_Link::buildPublicLink('full:posts', $content),
				'value' => $content['title']
			);
			return $crumb;
		}
		else
		{
			return array();
		}
    }

	public function getNavigationTab()
    {
		return 'forums';
    }

	public function formatHistory($string, XenForo_View $view)
    {
		//$parser = XenForo_BbCode_Parser::create(XenForo_BbCode_Formatter_Base::create('Base', array('view' => $view)));
		//return new XenForo_BbCode_TextWrapper($string, $parser);
        return $string; // TODO
    }

	public function revertToVersion(array $content, $revertCount, array $history, array $previous = null)
	{
		$dw = XenForo_DataWriter::create('XenForo_DataWriter_Discussion_Thread', XenForo_DataWriter::ERROR_SILENT);
		$dw->setExistingData($content);
		$dw->set('title', $history['old_text']);

		return $dw->save();
	}

}