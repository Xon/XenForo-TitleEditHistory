<?php

class SV_TitleEditHistory_XenForo_DataWriter_Discussion_Thread extends XFCP_SV_TitleEditHistory_XenForo_DataWriter_Discussion_Thread
{

    protected function _getCommonFields()
    {
        $fields = parent::_getCommonFields();
        $fields["xf_thread"]['last_edit_date'] = array('type' => self::TYPE_UINT, 'default' => 0);
        $fields["xf_thread"]['last_edit_user_id'] = array('type' => self::TYPE_UINT, 'default' => 0);
        $fields["xf_thread"]['edit_count'] = array('type' => self::TYPE_UINT_FORCED, 'default' => 0);
        return $fields;
    }

    protected function _discussionPreSave()
    {
        if ($this->isUpdate() && $this->isChanged('title'))
        {
            $this->set('last_edit_date', XenForo_Application::$time);
            $this->set('last_edit_user_id', XenForo_Visitor::getUserId());
            $this->set('edit_count', $this->get('edit_count') + 1);
        }
    }

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
            'content_type' => 'thread_title',
            'content_id' => $this->getDiscussionId(),
            'edit_user_id' => XenForo_Visitor::getUserId(),
            'old_text' => $this->getExisting('title')
        ));
        $historyDw->save();
    }
}