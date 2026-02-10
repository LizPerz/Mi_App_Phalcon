<?php
use App\Models\Alumnos;
use Phalcon\Http\Response;

class HolaController extends ControllerBase
{
    public function indexAction()
    {
        // Solo muestra el formulario
        $this->view->imagenes_db = Carrusel::find();
    }

    public function guardarAction()
    {
        // ... (Tu código de guardar original se queda igual) ...
        if ($this->request->isPost()) {
            try {
                $recaptchaResponse = $this->request->getPost('g-recaptcha-response');
                $secretKey = "6Ld1KEosAAAAAH9u1ZO12TlbVCgf4TFefVXzWak8";
                $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$secretKey}&response={$recaptchaResponse}");
                $responseData = json_decode($verify);
                
                if (!$responseData->success) {
                    $this->flash->error("Por favor, confirma que no eres un robot.");
                    return $this->response->redirect('hola/index');
                }

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
                    foreach ($alumno->getMessages() as $message) { $errores .= $message . " "; }
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
        date_default_timezone_set('America/Mexico_City');
        $this->view->alumnos = Alumnos::find(["order" => "fecha_registro DESC"]);
    }

    // --- NUEVO: Acción para Editar vía Fetch API (JSON) ---
    public function editarAction()
    {
        $this->view->disable(); // No necesitamos vista, solo JSON
        $response = new Response();
        $response->setContentType('application/json');

        if ($this->request->isPost()) { // Fetch usa POST o PUT
            // Leemos el JSON que envía Javascript
            $json = $this->request->getJsonRawBody();
            
            $id = $json->id;
            $nuevoNombre = $json->nombre;

            if (!$id || empty($nuevoNombre)) {
                return $response->setJsonContent(['status' => 'error', 'msg' => 'Datos incompletos']);
            }

            $alumno = Alumnos::findFirstById($id);
            if ($alumno) {
                $alumno->nombre = $nuevoNombre;
                if ($alumno->save()) {
                    return $response->setJsonContent(['status' => 'success', 'msg' => 'Actualizado correctamente']);
                }
            }
            return $response->setJsonContent(['status' => 'error', 'msg' => 'No se pudo actualizar']);
        }
    }

    // --- MODIFICADO: Acción Eliminar compatible con Fetch API ---
    public function eliminarAction($id)
    {
        $alumno = Alumnos::findFirstById($id);
        
        // Verificamos si la petición pide JSON (Fetch)
        if ($this->request->isAjax() || $this->request->getHeader('Accept') === 'application/json') {
            $this->view->disable();
            $response = new Response();
            $response->setContentType('application/json');

            if ($alumno && $alumno->delete()) {
                return $response->setJsonContent(['status' => 'success']);
            } else {
                return $response->setJsonContent(['status' => 'error']);
            }
        }

        // Fallback tradicional (por si acaso)
        if ($alumno) {
            $alumno->delete();
            $this->flash->success("Eliminado correctamente.");
        }
        return $this->response->redirect('hola/lista');
    }

    public function formularioAction() { }
    
    // Tus rutas de error se quedan igual
    public function error404Action() { return $this->dispatcher->forward(['controller'=>'errors','action'=>'show404']); }
    public function error500Action() { return $this->dispatcher->forward(['controller'=>'errors','action'=>'show500']); }
}





/* use App\Models\Alumnos;

/**
 * @property \Phalcon\Http\Request $request
 * @property \Phalcon\Flash\Direct $flash
 * @property \Phalcon\Http\Response $response
 * @property \Phalcon\Mvc\Url $url
 * @property \Phalcon\Mvc\View $view
 */
/*class HolaController extends ControllerBase
{
    public function indexAction()
    {
        // Solo muestra el formulario
        $this->view->imagenes_db = Carrusel::find();
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

    
    public function formularioAction() { } // Solo muestra la vista  

    // Rutas de prueba para errores

   /* public function error404Action() 
    {
        // Forzamos el despacho a una ruta inexistente
        return $this->dispatcher->forward([
            'controller' => 'errors',
            'action'     => 'show404'
        ]);
    }

    public function error500Action()
    {
        // Forzamos el despacho a la vista del error 500
        return $this->dispatcher->forward([
            'controller' => 'errors',
            'action'     => 'show500'
        ]);
    }
} */