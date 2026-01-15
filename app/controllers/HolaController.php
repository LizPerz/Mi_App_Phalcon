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

/* 
<?php
use App\Models\Alumnos;
class HolaController extends ControllerBase
{
    public function indexAction()
    {

    }
    // Esta función procesa el formulario cuando le das clic a "Guardar en BD"
    public function guardarAction()
    {
        // 1. Verificamos que los datos vengan por método POST
        if ($this->request->isPost()) {

            // 2. RECAPTCHA: Validamos que no sea un robot
            $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
            $secretKey = "6Ld1KEosAAAAAH9u1ZO12TlbVCgf4TFefVXzWak8"; // Pega aquí la "Secret Key" de Google

            $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
            $responseData = json_decode($verify);

            if (!$responseData->success) {
                // Si el captcha falla, mandamos un mensaje de error y regresamos
                $this->flash->error("Por favor, confirma que no eres un robot.");
                return $this->response->redirect('/Mi_App/hola/index');
            }

            // 3. BASE DE DATOS: Si el captcha es correcto, guardamos el nombre
            $nombre = $this->request->getPost('nombre_completo');
            
            $alumno = new Alumnos();
            $alumno->nombre = $nombre;

            if ($alumno->save()) {
                // Si se guarda con éxito
                $this->flash->success("¡Genial! " . $nombre . " se guardó en la base de datos de la universidad.");
            } else {
                // Si hubo un error al guardar (ej. la tabla no existe)
                $this->flash->error("Houston, tenemos un problema al guardar en la BD.");
            }

            // 4. Regresamos a la pantalla principal para ver el mensaje
            return $this->response->redirect('/Mi_App/hola/lista');
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