<?php

class ConnectionApiPeru
{
    protected $url = "https://dniruc.apisperu.com/api/v1";
    protected $token = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJlbWFpbCI6IkpDUlVaQFNJU1RFTUFVU1FBWS5jb20ifQ.mrHZmcuSIMoWmy6qH7a9WX7ZBNmi-4OHPCze3bwzJ1M";

    public function fetchRuc(string $documento)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . '/ruc/' . $documento . '?token=' . $this->token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);

        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception("No se pudo obtener la respuesta " . $error);
        }

        curl_close($curl);

        $data = json_decode($response, true);

        if ($data === null) {
            throw new Exception("El ruc no existe o no se pudo obtener la información.");
        }

        return [
            'ruc' => $data['ruc'] ?? '',
            'razonSocial' => $data['razonSocial'] ?? '',
            'nombreComercial' => $data['nombreComercial'] ?? '',
            'telefonos' => $data['telefonos'] ?? [],
            'estado' => $data['estado'] ?? '',
            'condicion' => $data['condicion'] ?? '',
            'direccion' => $data['direccion'] ?? '',
            'departamento' => $data['departamento'] ?? '',
            'provincia' => $data['provincia'] ?? '',
            'distrito' => $data['distrito'] ?? '',
            'ubigeo' => $data['ubigeo'] ?? '',
            'capital' => $data['capital'] ?? ''
        ];
    }

    public function fetchDni(string $documento)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->url . '/dni/' . $documento . '?token=' . $this->token,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        $response = curl_exec($curl);

        if ($response === false) {
            $error = curl_error($curl);
            curl_close($curl);
            throw new \Exception("No se pudo obtener la respuesta " . $error);
        }

        curl_close($curl);

        $data = json_decode($response, true);

        if ($data === null) {
            throw new \Exception("El DNI no existe o no se pudo obtener la información.");
        }

        return [
            'dni' => $data['dni'] ?? '',
            'nombres' => $data['nombres'] ?? '',
            'apellidoPaterno' => $data['apellidoPaterno'] ?? '',
            'apellidoMaterno' => $data['apellidoMaterno'] ?? '',
            'codVerifica' => $data['codVerifica'] ?? ''
        ];
    }
}
