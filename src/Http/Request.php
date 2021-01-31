<?php

namespace Http;

class Request implements RequestInterface
{
    protected $data;

    public function __construct(array $data = null)
    {
        self::boot();
        $this->data = $data;
    }

    private function boot()
    {
        foreach ($_SERVER as $key => $value) {
            $this->{$this->toCamelCase($key)} = $value;
        }
    }

    private function toCamelCase(string $string): array|string
    {
        $result = strtolower($string);
        preg_match_all('/_[a-z]/', $result, $matches);
        foreach ($matches[0] as $match) {
            $c = str_replace('_', '', strtoupper($match));
            $result = str_replace($match, $c, $result);
        }

        return $result;
    }

    /**
     * @return array|void
     */
    public function getBody()
    {
        $body = [];

        if (isset($this->requestMethod)) {
            if ($this->requestMethod == "POST") {
                foreach ($_POST as $key => $value) {
                    $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
                }
                return $body;
            }
        }
        if (!empty($this->requestMethod)) {
            if ($this->requestMethod == "GET") {
                if ($this->data !== null) {
                    return $this->data;
                }
                return;
            }
        }
    }
}
