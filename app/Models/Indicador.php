<?php

namespace App\Models;

use CodeIgniter\Model;
use mysqli;

class Indicador extends Model
{

    public function getDatos()
    {
        $query = $this->db->query("SELECT * FROM historicos_uf");
        return $query;
    }

    public function getLastMonthUF($arrayDatos)
    {
        foreach ($arrayDatos as $key => $value) {

            $fecha = $value["fecha"];

            $valor = $value["valor"];

            $query  = $this->db->query("INSERT INTO historicos_uf(fecha, valor) VALUES ('$fecha', '$valor')");
        }

        $result = 0;

        if ($query) {
            $result = 1;
        } else {
            $result = 0;
        }

        return $result;
    }

    public function eliminarFila($id){

        $eliminar = $this->db->query("DELETE FROM historicos_uf WHERE id = $id");

        $result = 0;

        if ($eliminar) {
            $result = 1;
        } else {
            $result = 0;
        }

        return $result;
        

    }

    public function getFila($id){

        $getOne = $this->db->query("SELECT * FROM historicos_uf WHERE id = '$id'");

        return $getOne->getResult();

    }

    public function updateFila($fecha, $valor, $id){

        $update = $this->db->query("UPDATE historicos_uf SET fecha = '$fecha', valor = '$valor' WHERE id = '$id'");

        $result = 0;

        if ($update) {
            $result = 1;
        } else {
            $result = 0;
        }
        
        return $result;

    }
}
