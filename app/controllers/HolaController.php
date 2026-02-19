<?php

use App\Models\Alumnos;
use Phalcon\Http\Response;

class HolaController extends ControllerBase
{
    public function indexAction()
    {
        // $this->view->imagenes_db = \App\Models\Carrusel::find(); 
    }

    public function registroAction()
    {
        // Esta función vacía es necesaria para que cargue views/hola/registro.phtml
    }
    

    public function guardarAction()
    {
        if ($this->request->isPost()) {
            try {
                $nombre = $this->request->getPost('nombre_completo');
                if (empty($nombre)) {
                    $this->flash->error("El nombre es obligatorio.");
                    return $this->response->redirect('hola/index');
                }
                
                $alumno = new Alumnos();
                $alumno->nombre = $nombre;

                if ($alumno->save()) {
                    $this->flash->success("Alumno guardado correctamente.");
                    return $this->response->redirect('hola/lista');
                } else {
                    $this->flash->error("Error al guardar.");
                    return $this->response->redirect('hola/index');
                }
            } catch (\Exception $e) {
                die("Error: " . $e->getMessage());
            }
        }
    }

    public function listaAction()
    {
        // Carga solo la vista
    }

    // --- API JSON ---
    public function buscarAction()
    {
        $this->view->disable();
        $response = new Response();
        $response->setContentType('application/json', 'UTF-8');

        if ($this->request->isPost()) {
            $json = $this->request->getJsonRawBody();
            
            $texto = $json->texto ?? '';
            $fInicio = $json->fInicio ?? '';
            $fFin = $json->fFin ?? '';
            $orden = $json->orden ?? 'fecha_desc';

            $conditions = [];
            $bind = [];

            // 1. Buscador (ILIKE para Postgres = insensible a mayúsculas)
            if (!empty($texto)) {
                $conditions[] = "nombre ILIKE :texto:";
                $bind['texto'] = '%' . $texto . '%';
            }

            // 2. Rango de Fechas
            if (!empty($fInicio)) {
                $conditions[] = "DATE(fecha_registro) >= :inicio:";
                $bind['inicio'] = $fInicio;
            }
            if (!empty($fFin)) {
                $conditions[] = "DATE(fecha_registro) <= :fin:";
                $bind['fin'] = $fFin;
            }

            // 3. Ordenamiento
            switch ($orden) {
                case 'nombre_asc': $orderBy = "nombre ASC"; break;
                case 'nombre_desc': $orderBy = "nombre DESC"; break;
                case 'fecha_asc': $orderBy = "fecha_registro ASC"; break;
                case 'fecha_desc': 
                default: $orderBy = "fecha_registro DESC"; break;
            }

            $parametros = ['order' => $orderBy];
            if (count($conditions) > 0) {
                $parametros['conditions'] = implode(' AND ', $conditions);
                $parametros['bind'] = $bind;
            }

            $alumnos = Alumnos::find($parametros);
            
            $data = [];
            date_default_timezone_set('America/Mexico_City');

            foreach ($alumnos as $alumno) {
                // Formateo seguro de fecha - SOLO FECHA
                $fechaStr = "---";
                if (!empty($alumno->fecha_registro)) {
                    $timestamp = strtotime($alumno->fecha_registro);
                    if ($timestamp) {
                        $fechaStr = date('d/m/Y', $timestamp); // Solo fecha (dd/mm/aaaa)
                    }
                }

                $data[] = [
                    'id' => $alumno->id,
                    'nombre' => $alumno->nombre,
                    'fecha' => $fechaStr,
                    'fecha_original' => $alumno->fecha_registro // Para ordenamiento
                ];
            }

            return $response->setJsonContent(['status' => 'success', 'data' => $data]);
        }
    }

    public function editarAction()
    {
        $this->view->disable();
        $response = new Response();
        $response->setContentType('application/json', 'UTF-8');

        if ($this->request->isPost()) {
            $json = $this->request->getJsonRawBody();
            $alumno = Alumnos::findFirstById($json->id);
            if ($alumno) {
                $alumno->nombre = $json->nombre;
                if ($alumno->save()) {
                    return $response->setJsonContent(['status' => 'success']);
                }
            }
            return $response->setJsonContent(['status' => 'error']);
        }
    }

    public function eliminarAction($id)
    {
        $alumno = Alumnos::findFirstById($id);
        
        if ($this->request->isAjax() || $this->request->getHeader('Accept') === 'application/json') {
            $this->view->disable();
            $response = new Response();
            $response->setContentType('application/json', 'UTF-8');
            
            if ($alumno && $alumno->delete()) {
                return $response->setJsonContent(['status' => 'success']);
            }
            return $response->setJsonContent(['status' => 'error']);
        }

        if ($alumno) $alumno->delete();
        return $this->response->redirect('hola/lista');
    }
    



    public function guardarJsonAction()
{
    $this->view->disable();
    $response = new Response();
    $response->setContentType('application/json', 'UTF-8');

    if ($this->request->isPost()) {
        $json = $this->request->getJsonRawBody();
        $nombre = $json->nombre_completo ?? '';

        if (empty($nombre)) {
            return $response->setJsonContent([
                'status' => 'error',
                'mensaje' => 'El nombre es obligatorio'
            ]);
        }

        if (!preg_match('/^[a-zA-ZñÑáéíóúÁÉÍÓÚ\s]+$/', $nombre)) {
            return $response->setJsonContent([
                'status' => 'error',
                'mensaje' => 'Solo se permiten letras y espacios'
            ]);
        }

        if (strlen($nombre) > 35) {
            return $response->setJsonContent([
                'status' => 'error',
                'mensaje' => 'Máximo 35 caracteres'
            ]);
        }

        $alumno = new Alumnos();
        $alumno->nombre = $nombre;

        if ($alumno->save()) {
            return $response->setJsonContent([
                'status' => 'success',
                'mensaje' => 'Alumno guardado correctamente'
            ]);
        } else {
            return $response->setJsonContent([
                'status' => 'error',
                'mensaje' => 'Error al guardar en la base de datos'
            ]);
        }
    }

    return $response->setJsonContent([
        'status' => 'error',
        'mensaje' => 'Método no permitido'
    ]);
}




    public function formularioAction() {}
    public function error404Action() {}
    public function error500Action() {}
}

