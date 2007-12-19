<?php

class CCurl {
    var $m_handle;
    var $m_header;
    var $m_body;

    public function CCurl($sUrl) {
        $this->m_handle = curl_init();
        curl_setopt($this->m_handle, CURLOPT_URL, $sUrl);
        curl_setopt($this->m_handle, CURLOPT_HEADER, 1);
        curl_setopt($this->m_handle, CURLOPT_RETURNTRANSFER, 1);
        return;
    }

    public function get_header() {
        return $this->m_header;
    }

    public function get_status_code()
    {
		// Get HTTP Status code from the response
		$status_code = array();
		preg_match('/\d\d\d/', $this->m_header, $status_code);
		return $status_code[0];
    }

    public function execute() {
        $sResponse = curl_exec($this->m_handle);
        $this->m_body = substr($sResponse, strpos($sResponse, "\r\n\r\n") + 4);
        $this->m_header = substr($sResponse, 0, -strlen($this->m_body));
        return $this->m_body;
    }

    public function close() {
        curl_close($this->m_handle);
        return;
    }
}