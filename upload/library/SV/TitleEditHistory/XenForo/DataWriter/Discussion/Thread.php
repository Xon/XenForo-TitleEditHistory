<?php

class SV_TitleEditHistory_XenForo_DataWriter_Discussion_Thread extends XFCP_SV_TitleEditHistory_XenForo_DataWriter_Discussion_Thread
{
	protected function _discussionPostSave()
	{
		if ($this->isUpdate() && $this->isChanged('title'))
		{
			$this->_insertEditHistory();
		}
        
        parent::_discussionPostSave();
    }
    
	protected function _insertEditHistory()
	{
		$historyDw = XenForo_DataWriter::create('XenForo_DataWriter_EditHistory', XenForo_DataWriter::ERROR_SILENT);
		$historyDw->bulkSet(array(
			'content_type' => $this->getContentType(),
			'content_id' => $this->getDiscussionId(),
			'edit_user_id' => XenForo_Visitor::getUserId(),
			'old_text' => $this->getExisting('title')
		));
		$historyDw->save();
	}
}