<?php

class Requests {
    public function __construct() {
        $this->curl = curl_init();
    }

    public function get($url, $headers = []) {

    }

    public function post($url, $data = [], $headers = []) {

    }

    public function put($url, $data = [], $headers = []) {

    }

    public function delete($url, $headers = []) {

    }

    public function options($url, $headers = []) {

    }

    public function head($url, $headers = []) {

    }

    

    public function close() {
        curl_close($this->curl);
    }
}