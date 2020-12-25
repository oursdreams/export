<?php


namespace Oursdreams\Export;

use Psr\Http\Message\StreamInterface;

class Response
{

    protected $status;
    protected $header;
    /** @var StreamInterface */
    protected $body;

    public function __construct($status, $header, $body)
    {
        $this->status = $status;
        $this->header = $header;
        $this->body   = $body;
    }

    public function getGuzzleHttpResponse()
    {
        return new \GuzzleHttp\Psr7\Response($this->status,$this->header,$this->body->getContents());
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getHeader()
    {
        return $this->header;
    }

    public function getBody()
    {
        return $this->body->getContents();
    }

    public function getErrorMessage()
    {
        return $this->header["Msg"];
    }

    public function getMetadata()
    {
        return $this->body->getMetadata();
    }

    public function getSize()
    {
        return $this->body->getSize();
    }
}