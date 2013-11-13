<?php
/**
 * @author      Alexander Stepanenko <alex.stepanenko@gmail.com>
 * @license     http://mit-license.org/
 */

class OAuth2ServerResponse {

    protected static $messages = array(
        //Informational 1xx
        100 => '100 Continue',
        101 => '101 Switching Protocols',
        //Successful 2xx
        200 => '200 OK',
        201 => '201 Created',
        202 => '202 Accepted',
        203 => '203 Non-Authoritative Information',
        204 => '204 No Content',
        205 => '205 Reset Content',
        206 => '206 Partial Content',
        //Redirection 3xx
        300 => '300 Multiple Choices',
        301 => '301 Moved Permanently',
        302 => '302 Found',
        303 => '303 See Other',
        304 => '304 Not Modified',
        305 => '305 Use Proxy',
        306 => '306 (Unused)',
        307 => '307 Temporary Redirect',
        //Client Error 4xx
        400 => '400 Bad Request',
        401 => '401 Unauthorized',
        402 => '402 Payment Required',
        403 => '403 Forbidden',
        404 => '404 Not Found',
        405 => '405 Method Not Allowed',
        406 => '406 Not Acceptable',
        407 => '407 Proxy Authentication Required',
        408 => '408 Request Timeout',
        409 => '409 Conflict',
        410 => '410 Gone',
        411 => '411 Length Required',
        412 => '412 Precondition Failed',
        413 => '413 Request Entity Too Large',
        414 => '414 Request-URI Too Long',
        415 => '415 Unsupported Media Type',
        416 => '416 Requested Range Not Satisfiable',
        417 => '417 Expectation Failed',
        422 => '422 Unprocessable Entity',
        423 => '423 Locked',
        //Server Error 5xx
        500 => '500 Internal Server Error',
        501 => '501 Not Implemented',
        502 => '502 Bad Gateway',
        503 => '503 Service Unavailable',
        504 => '504 Gateway Timeout',
        505 => '505 HTTP Version Not Supported'
    );

    public $httpVersion = '1.1';

    protected $status, $data = array(), $headers = array();

    public function __construct($httpVersion = '1.1') {
        $this->httpVersion = $httpVersion;
    }

    public function set($data) {
        $this->data = $data;
        return $this;
    }

    public function add($data) {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    public function status($status) {
        $this->status = $status;
        return $this;
    }

    public function header($header, $value) {
        $this->headers[$header] = $value;
        return $this;
    }

    public static function getMessageForCode($status)
    {
        if (isset(self::$messages[$status])) {
            return self::$messages[$status];
        } else {
            return null;
        }
    }

    public function send($status = '200') {
        $data = json_encode($this->data);
        $this->status($status);
        $this->header('Content-Type', 'application/json');
        $this->header('Content-Length', strlen($data));
        if (strpos(PHP_SAPI, 'cgi') === 0) {
            header(sprintf('Status: %s', self::getMessageForCode($status)));
        } else {
            header(sprintf('HTTP/%s %s', $this->httpVersion, self::getMessageForCode($status)));
        }
        foreach ($this->headers as $name=>$value) {
            $hValues = explode("\n", $value);
            foreach ($hValues as $hVal) {
                header("$name: $hVal", false);
            }
        }
        echo $data;
        \Yii::app()->end();
    }

}