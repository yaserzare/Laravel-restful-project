<?php

namespace App\RestfulApi;

class ApiResponse
{
    private ?string $message = null;
    private mixed $data = null;
    private int $status = 200;
    private array $appends = [];

    public function setMessage(string $message)
    {
        $this->message = $message;
    }

    public function setData(mixed $data)
    {
        $this->data = $data;
    }

    public function setAppends(array $appends)
    {
        $this->appends = $appends;
    }



    public function response()
    {
        $body = [];
        !is_null($this->message) && $body['message'] = $this->message;
        !is_null($this->data) && $body['data'] = $this->data;
        $body = $body+$this->appends;
        return response()->json($body, $this->status);
    }





}
