<?php
namespace App\Models; // Esta línea es vital
use Phalcon\Mvc\Model;

class Alumnos extends Model
{
    public $id;
    public $nombre;
    public $fecha_registro;

    public function initialize()
    {
        // Esto le dice a Phalcon que use la tabla 'alumnos'
        $this->setSource("alumnos");
    }
}