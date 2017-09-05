<?php

class Request
{
    /**
     * @return bool
     */
    public function isPost(): bool
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'post';
    }
    
    /**
     * @return bool
     */
    public function isGet(): bool
    {
        return isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) === 'get';
    }
    
    /**
     * @return bool
     */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get a POST param from request
     * @param string $param
     * @return mixed
     */
    public function post(string $param)
    {
        return $_POST[$param] ?? null;
    }
    
    /**
     * Get a GET param from request
     * @param string $param
     * @return type
     */
    public function get(string $param)
    {
        return $_GET[$param] ?? null;
    }
}
