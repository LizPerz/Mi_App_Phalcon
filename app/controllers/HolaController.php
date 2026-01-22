<?php

use App\Models\Alumnos;

/**
 * @property \Phalcon\Http\Request $request
 * @property \Phalcon\Flash\Direct $flash
 * @property \Phalcon\Http\Response $response
 * @property \Phalcon\Mvc\Url $url
 * @property \Phalcon\Mvc\View $view
 */
class HolaController extends ControllerBase
{
    public function indexAction()
    {
        // Solo muestra el formulario
    }

    public function guardarAction()
    {
        if ($this->request->isPost()) {
            try {
                // 1. RECAPTCHA
                $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
                $secretKey = "6Ld1KEosAAAAAH9u1ZO12TlbVCgf4TFefVXzWak8";

                $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
                $responseData = json_decode($verify);
                
                if (!$responseData->success) {
                    $this->flash->error("Por favor, confirma que no eres un robot.");
                    return $this->response->redirect('hola/index');
                }

                // 2. BASE DE DATOS
                $nombre = $this->request->getPost('nombre_completo');

                if (empty($nombre)) {
                    $this->flash->error("El nombre no puede estar vacío.");
                    return $this->response->redirect('hola/index');
                }
                
                $alumno = new Alumnos();
                $alumno->nombre = $nombre;

                if ($alumno->save()) {
                    $this->flash->success("¡Genial! " . $nombre . " se guardó correctamente.");
                    return $this->response->redirect('hola/lista');
                } else {
                    $errores = "";
                    foreach ($alumno->getMessages() as $message) {
                        $errores .= $message . " ";
                    }
                    $this->flash->error("Error: " . $errores);
                    return $this->response->redirect('hola/index');
                }

            } catch (\Exception $e) {
                die("DETALLE DEL FALLO CRÍTICO: " . $e->getMessage());
            }
        }
    }

    public function listaAction()
    {
        // Ajustamos la zona horaria para la consulta
        date_default_timezone_set('America/Mexico_City');
        
        $this->view->alumnos = Alumnos::find([
            "order" => "fecha_registro DESC"
        ]);
    }

    public function eliminarAction($id)
    {
        // Buscamos el registro por su ID
        $alumno = Alumnos::findFirstById($id);

        if (!$alumno) {
            $this->flash->error("El alumno no existe en la base de datos.");
            return $this->response->redirect('hola/lista');
        }

        // Ejecutamos la eliminación
        if ($alumno->delete()) {
            $this->flash->success("Tripulante eliminado de la órbita correctamente.");
        } else {
            $this->flash->error("No se pudo eliminar el registro.");
        }

        return $this->response->redirect('hola/lista');
    }
}