<?php

use App\Models\Alumnos;


/**
 * @property \Phalcon\Http\Request $request
 * @property \Phalcon\Flash\Direct $flash
 * @property \Phalcon\Http\Response $response
 * @property \Phalcon\Mvc\Url $url
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

            // 1. RECAPTCHA
            $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
            $secretKey = "6Ld1KEosAAAAAH9u1ZO12TlbVCgf4TFefVXzWak8";

            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
            $responseData = json_decode($verify);
            
            if (!$responseData->success) {
                $this->flash->error("Por favor, confirma que no eres un robot.");
                return $this->response->redirect($this->url->get('hola/index'));
            }

            // 2. BASE DE DATOS
            $nombre = $this->request->getPost('nombre_completo');

            if (empty($nombre)) {
                $this->flash->error("El nombre no puede estar vacío.");
                return $this->response->redirect($this->url->get('hola/index'));
            }
            
            $alumno = new Alumnos();
            $alumno->nombre = $nombre;

            // En Postgres, save() devuelve false si hay problemas de permisos o tipos
            if ($alumno->save()) {
                $this->flash->success("¡Genial! " . $nombre . " se guardó correctamente.");
                return $this->response->redirect($this->url->get('hola/lista'));
            } else {
                foreach ($alumno->getMessages() as $message) {
                    // Esto saldrá en tus Logs de Render
                    error_log("ERROR EN BD: " . $message);
                    $this->flash->error("Error: " . $message);
                }
                return $this->response->redirect($this->url->get('hola/index'));
            }
        }
    }

    public function listaAction()
    {
        // Traemos todos los alumnos
        $this->view->alumnos = Alumnos::find([
            "order" => "fecha_registro DESC"
        ]);
    }
}