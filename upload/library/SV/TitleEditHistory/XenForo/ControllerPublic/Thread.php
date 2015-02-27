<?php

class SV_TitleEditHistory_XenForo_ControllerPublic_Thread  extends XFCP_SV_TitleEditHistory_XenForo_ControllerPublic_Thread
{
    public function actionTitleHistory()
    {
        $this->_request->setParam('content_type', 'thread_title');
        $this->_request->setParam('content_id', $this->_input->filterSingle('thread_id', XenForo_Input::UINT));
        return $this->responseReroute('XenForo_ControllerPublic_EditHistory', 'index');
    }

	protected function _updateModeratorLogThreadEdit(array $thread, XenForo_DataWriter_Discussion_Thread $dw, array $skip = array())
	{
        $skip += array('thread_title_last_edit_date', 'thread_title_last_edit_user_id', 'thread_title_edit_count');
        parent::_updateModeratorLogThreadEdit($thread, $dw, $skip);
    }
}