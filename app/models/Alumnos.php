<?php
namespace App\Models;

use Phalcon\Mvc\Model;

class Alumnos extends Model
{
    public $id;
    public $nombre;
    public $fecha_registro;

    public function initialize()
    {
        $this->setSchema("public");
        $this->setSource("alumnos");
    }

    public function beforeCreate()
    {
        date_default_timezone_set('America/Mexico_City');
        $this->fecha_registro = date('Y-m-d H:i:s');
    }
}
