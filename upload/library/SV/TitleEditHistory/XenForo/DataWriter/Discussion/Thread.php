<?php

class SV_TitleEditHistory_XenForo_DataWriter_Discussion_Thread extends XFCP_SV_TitleEditHistory_XenForo_DataWriter_Discussion_Thread
{
    const OPTION_LOG_EDIT = 'logEdit';

    protected function _getDefaultOptions()
    {
        $defaults = parent::_getDefaultOptions();

        $options = XenForo_Application::get('options');
        $defaults[self::OPTION_LOG_EDIT] = $options->editHistory['enabled'];

        return $defaults;
    }

    protected function _getCommonFields()
    {
        $fields = parent::_getCommonFields();
        $fields["xf_thread"]['thread_title_last_edit_date'] = ['type' => self::TYPE_UINT, 'default' => 0];
        $fields["xf_thread"]['thread_title_last_edit_user_id'] = ['type' => self::TYPE_UINT, 'default' => 0];
        $fields["xf_thread"]['thread_title_edit_count'] = ['type' => self::TYPE_UINT_FORCED, 'default' => 0];

        return $fields;
    }

    protected function _discussionPreSave()
    {
        if ($this->isUpdate() && $this->isChanged('title'))
        {
            if (!$this->isChanged('thread_title_last_edit_date'))
            {
                $this->set('thread_title_last_edit_date', XenForo_Application::$time);
                if (!$this->isChanged('thread_title_last_edit_user_id'))
                {
                    $this->set('thread_title_last_edit_user_id', XenForo_Visitor::getUserId());
                }
            }

            if (!$this->isChanged('thread_title_edit_count'))
            {
                $this->set('thread_title_edit_count', $this->get('thread_title_edit_count') + 1);
            }
        }
        if ($this->isChanged('thread_title_edit_count') && $this->get('thread_title_edit_count') == 0)
        {
            $this->set('thread_title_last_edit_date', 0);
        }
        if (!$this->get('thread_title_last_edit_date'))
        {
            $this->set('thread_title_last_edit_user_id', 0);
        }

        return parent::_discussionPreSave();
    }

    protected function _discussionPostSave()
    {
        if ($this->isUpdate() && $this->isChanged('title'))
        {
            $this->_insertTitleEditHistory();
        }

        return parent::_discussionPostSave();
    }

    protected function _insertTitleEditHistory()
    {
        $historyDw = XenForo_DataWriter::create('XenForo_DataWriter_EditHistory', XenForo_DataWriter::ERROR_SILENT);
        $historyDw->bulkSet(
            [
                'content_type' => 'thread_title',
                'content_id'   => $this->getDiscussionId(),
                'edit_user_id' => XenForo_Visitor::getUserId(),
                'old_text'     => $this->getExisting('title')
            ]
        );
        $historyDw->save();
    }
}
