<?php

use Phalcon\Mvc\Model;

class Carrusel extends Model
{
    public $id;
    public $nombre_archivo;
    public $fecha_subida;

    public function initialize()
    {
         $this->setSchema("public");
        $this->setSource("carrusel");
    }
}