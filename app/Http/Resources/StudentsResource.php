<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class StudentsResource extends JsonResource
{
    public $status;
    public $msg;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */

    public function __construct($status, $msg, $resource)
    {
        parent::__construct($resource);
        $this->status = $status;
        $this->msg = $msg;
    }
    public function toArray($request)
    {
        return [
            'success' => $this->status,
            'messages' => $this->msg,
            'data' => $this->resource
        ];
    }
}