<?php

class SV_TitleEditHistory_XenForo_ControllerPublic_Thread  extends XFCP_SV_TitleEditHistory_XenForo_ControllerPublic_Thread
{
    public function actionTitleHistory()
    {
        $this->_request->setParam('content_type', 'thread_title');
        $this->_request->setParam('content_id', $this->_input->filterSingle('thread_id', XenForo_Input::UINT));
        return $this->responseReroute('XenForo_ControllerPublic_EditHistory', 'index');
    }
}