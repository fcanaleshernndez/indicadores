<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\indicador;

class IndicadoresController extends Controller
{

    public function index()
    {

        $indicador = new Indicador();

        $datos['indicadores'] = $indicador->getDatos();
        $datos['header'] = view('templates/cabecera');
        $datos['footer'] = view('templates/footer');

        return view('indicators/listar', $datos);
    }

    public function mindicadorUF()
    {

        if (isset($_POST['indicador'])) {

            $indicador = $_POST['indicador'];

            $apiUrl = 'https://mindicador.cl/api/' . $indicador;
            //Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
            if (ini_get('allow_url_fopen')) {
                $json = file_get_contents($apiUrl);
            } else {
                //De otra forma utilizamos cURL
                $curl = curl_init($apiUrl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $json = curl_exec($curl);
                curl_close($curl);
            }

            $dailyIndicators = json_decode($json, true);

            $arrayUF = [];

            foreach ($dailyIndicators["serie"] as $key => $value) {

                $fecha = strtotime($value['fecha']);

                $fechaFormat = date('Y-m-d', substr($fecha, 0, 10));

                $arrayUF[] = array('fecha' => $fechaFormat, 'valor' => $value['valor']);
            }

            return json_encode($arrayUF);
        }
    }


    public function agregarUltimoMesUF()
    {

        if (isset($_POST['accion'])) {

            $apiUrl = 'https://mindicador.cl/api/uf';
            //Es necesario tener habilitada la directiva allow_url_fopen para usar file_get_contents
            if (ini_get('allow_url_fopen')) {
                $json = file_get_contents($apiUrl);
            } else {
                //De otra forma utilizamos cURL
                $curl = curl_init($apiUrl);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                $json = curl_exec($curl);
                curl_close($curl);
            }

            $dailyIndicators = json_decode($json, true);

            $arrayUFLastMonth = [];

            foreach ($dailyIndicators["serie"] as $key => $value) {

                $arrayUFLastMonth[] = array('fecha' => substr($value['fecha'], 0, 10), 'valor' => $value['valor']);
            }

            $indicador = new Indicador();

            //var_dump($arrayUFLastMonth[0]['fecha']);

            $multipleInsert = $indicador->getLastMonthUF($arrayUFLastMonth);

            if ($multipleInsert == 1) {
                echo "exito";
            } else {
                echo "fracaso";
            }
        }
    }



    public function eliminarFila()
    {

        $id = $_POST['id'];

        $indicador = new Indicador();

        $idEliminar = $indicador->eliminarFila($id);

        if ($idEliminar == 1) {
            echo "exito";
        } else {
            echo "error";
        }
    }

    public function getFila()
    {

        if (isset($_POST)) {

            $id = $_POST['id'];

            $indicador = new Indicador();

            $fila = $indicador->getFila($id);

            return json_encode($fila);
        }
    }

    public function updateFila()
    {

        if (isset($_POST)) {

            $id = $_POST['id'];
            $fecha = $_POST['fecha'];
            $valor = $_POST['valor'];

            $indicador = new Indicador();

            $upt = $indicador->updateFila($fecha, $valor, $id);

            if ($upt == 1) {
                return "exito";
            }else{
                return "error";
            }

        }
    }
}
