<?php

namespace App\Entities;

class Tile {

    public $orderId;
    public $width;
    public $height;
    public $href;
    public $text;
    public $color;
    public $imageBase64;
    public $imageScale;

    public function __construct($tileId, $orderId, $width, $height, $href, $text, $color, $imageBase64, $imageScale) {
        $this->tileId = $tileId;
        $this->orderId = $orderId;
        $this->width = $width;
        $this->height = $height;
        $this->href = $href;
        $this->text = $text;
        $this->color = $color;
        $this->imageBase64 = $imageBase64;
        $this->imageScale = $imageScale != null ? (int)$imageScale : null;
    }

    public function toJson() {
        $result = array(
            "tile" => $this->tileId,
            "order" => $this->orderId,
            "w" => $this->width,
            "h" => $this->height,
            "href" => $this->href
        );
        if (isset($this->text)) {
            $result["text"] = $this->text;
        }
        if (isset($this->color)) {
            $result["color"] = $this->color;
        }
        if (isset($this->imageBase64)) {
            $result["imageBase64"] = $this->imageBase64;
        }
        if (isset($this->imageScale)) {
            $result["imageScale"] = $this->imageScale;
        }
        return $result;
    }

}

?>