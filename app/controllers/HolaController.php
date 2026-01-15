<?php

use App\Models\Alumnos;

/**
 * @property \Phalcon\Http\Request $request
 * @property \Phalcon\Flash\Direct $flash
 * @property \Phalcon\Http\Response $response
 * @property \Phalcon\Mvc\View $view
 * @property \Phalcon\Mvc\Url $url
 */
class HolaController extends ControllerBase
{
    public function indexAction()
    {
        // Esta acción solo muestra el formulario inicial
    }

    // Esta función procesa el formulario cuando le das clic a "Guardar en BD"
    public function guardarAction()
    {
        // 1. Verificamos que los datos vengan por método POST
        if ($this->request->isPost()) {

            // 2. RECAPTCHA: Validamos que no sea un robot
            $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
            $secretKey = "6Ld1KEosAAAAAH9u1ZO12TlbVCgf4TFefVXzWak8"; // Tu llave secreta actual

            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
            $responseData = json_decode($verify);
            
            if (!$responseData->success) {
                $this->flash->error("Por favor, confirma que no eres un robot.");
                // Redirección corregida para evitar 404
                return $this->response->redirect($this->url->get('hola/index'));
            }

            // 3. BASE DE DATOS: Guardamos el nombre
            $nombre = $this->request->getPost('nombre_completo');
            
            $alumno = new Alumnos();
            $alumno->nombre = $nombre;

            if ($alumno->save()) {
                // Éxito al guardar
                $this->flash->success("¡Genial! " . $nombre . " se guardó correctamente.");
                // Redirección corregida a la lista
                return $this->response->redirect($this->url->get('hola/lista'));
            } else {
                // SI FALLA, esto mandará el error real a los logs de Render
                foreach ($alumno->getMessages() as $message) {
                    error_log("ERROR PHALCON BD: " . $message);
                    $this->flash->error("Error al guardar: " . $message);
                }
                return $this->response->redirect($this->url->get('hola/index'));
            }
        }
    }

    public function listaAction()
    {
        // Consultamos todos los alumnos guardados
        // Importante: Alumnos debe tener la A mayúscula para Linux/Render
        $listaAlumnos = Alumnos::find([
            "order" => "fecha_registro DESC"
        ]);

        // Mandamos la lista a la vista
        $this->view->alumnos = $listaAlumnos;
    }
}



//use App\Models\Alumnos;

/**
 * @property \Phalcon\Http\Request $request
 * @property \Phalcon\Flash\Direct $flash
 * @property \Phalcon\Http\Response $response
 * @property \Phalcon\Mvc\View $view
 * @property \Phalcon\Mvc\Url $url
 */
/*class HolaController extends ControllerBase
{
    public function indexAction()
    {
        // Esta acción solo muestra el formulario inicial
    }

    // Esta función procesa el formulario cuando le das clic a "Guardar en BD"
    public function guardarAction()
    {
        // 1. Verificamos que los datos vengan por método POST
        if ($this->request->isPost()) {

            // 2. RECAPTCHA: Validamos que no sea un robot
            $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
            $secretKey = "6Ld1KEosAAAAAH9u1ZO12TlbVCgf4TFefVXzWak8"; // Tu llave secreta actual

            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
            $responseData = json_decode($verify);
            
            if (!$responseData->success) {
                // Si el captcha falla, mandamos un mensaje de error y regresamos
                $this->flash->error("Por favor, confirma que no eres un robot.");
                
                // USAMOS $this->url->get para que la ruta sea dinámica (Local o Nube)
                return $this->response->redirect($this->url->get('hola/index'));
            }

            // 3. BASE DE DATOS: Si el captcha es correcto, guardamos el nombre
            $nombre = $this->request->getPost('nombre_completo');
            
            $alumno = new Alumnos();
            $alumno->nombre = $nombre;

            if ($alumno->save()) {
                // Si se guarda con éxito
                $this->flash->success("¡Genial! " . $nombre . " se guardó en la base de datos.");
            } else {
                // Si hubo un error al guardar (ej. la tabla no existe)
                $this->flash->error("Houston, tenemos un problema al guardar en la BD.");
            }

            // 4. Regresamos a la lista para ver el resultado
            // USAMOS $this->url->get para que la ruta sea dinámica (Local o Nube)
            return $this->response->redirect($this->url->get('hola/lista'));
        }
    }

    public function listaAction()
    {
        // Consultamos todos los alumnos guardados
        $listaAlumnos = Alumnos::find([
            "order" => "fecha_registro DESC"
        ]);

        // Mandamos la lista a la vista
        $this->view->alumnos = $listaAlumnos;
    }
}
*/