<?php

use Phalcon\Mvc\Controller;

class CarruselController extends Controller
{
    public function indexAction()
    {
        // Obtener todas las imágenes subidas
        $this->view->imagenes = Carrusel::find(['order' => 'id DESC']);
    }

    public function subirAction()
    {
        // Verificar si se envió un archivo
        if ($this->request->hasFiles()) {
            $archivos = $this->request->getUploadedFiles();

            foreach ($archivos as $archivo) {
                // Mover el archivo a la carpeta pública
                $nombreReal = $archivo->getName();
                // Limpiamos el nombre para evitar problemas
                $nombreLimpio = preg_replace('/[^a-zA-Z0-9_.-]/', '', $nombreReal);
                
                // Ruta donde se guardará
                $rutaDestino = 'img/banners/' . $nombreLimpio;

                if ($archivo->moveTo($rutaDestino)) {
                    // Guardar en Base de Datos
                    $imagen = new Carrusel();
                    $imagen->nombre_archivo = $nombreLimpio;
                    $imagen->save();
                }
            }
        }
        // Redirigir al administrador del carrusel
        return $this->response->redirect('carrusel/index');
    }

    public function eliminarAction($id)
    {
        $imagen = Carrusel::findFirst($id);
        if ($imagen) {
            // Borrar el archivo físico
            $rutaArchivo = 'img/banners/' . $imagen->nombre_archivo;
            if (file_exists($rutaArchivo)) {
                unlink($rutaArchivo);
            }
            // Borrar de la BD
            $imagen->delete();
        }
        return $this->response->redirect('carrusel/index');
    }
}