<?php
class CloudyCRM{
    private $apiKey;
    private $subDominio;

    public function __construct($subDominio,$apiKey)
    {
        $this->apiKey = $apiKey;
        $this->subDominio = $subDominio;
    }
    /**
     * Cuenta la cantidad de datos según el query. 
     */
    public function contarDatos($query,$folder)
    {
        $curl = curl_init();
        $formula = urlencode($query);
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://$this->subDominio.cloudycrm.net/restful/folders/$folder/documents?fields=doc_id&formula=$formula&order=apellido,nombre&maxDocs=1000&recursive=false&maxDescrLength=1000",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
              "apikey: $this->apiKey"
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true );

        $contar = sizeof($response["InternalObject"]);

        return $contar;
    }

    /**
     * Busca el DocId de un documento según la query.
     * Devuelve un solo documento, en caso de que se encuentren varios, devuelve el que tenga el doc_id más bajo.
     */
    public function searchDocId($query,$folder)
    {
        $curl = curl_init();
        $formula = urlencode($query);
        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://$this->subDominio.cloudycrm.net/restful/folders/$folder/documents?fields=doc_id&formula=$formula&order=doc_id&maxDocs=1&recursive=false&maxDescrLength=1000",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
              "apikey: $this->apiKey"
        ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        $response = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true );
        
        return $response["InternalObject"][0]["DOC_ID"];
    }

    /**
     * Devuelve toda la información de un documento según su DocId
     */
    public function getDocumento($docId){
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://$this->subDominio.cloudycrm.net/restful/documents/$docId",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            "apikey: $this->apiKey"
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        $response = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true );
        return $response;
    }

    /**
     * Modifica los campos de un documento.
     * $docId = La ID del documento a modificar.
     * $field = El campo a modificar.
     * $value = El valor nuevo del campo modificado.
     */
    public function modificarDocumento($docId,$field,$value)
    {
        $documento = $this->getDocumento($docId);
        $count = 0;
        $i = 0;
            foreach($documento["InternalObject"]["CustomFields"] as $dato){
            if($dato["Name"] == strtoupper($field)){
                if($dato["Updatable"] == true){
                $count = 1;
                $documento["InternalObject"]["CustomFields"][$i]["Value"] = $value;
                $value = $dato["Value"];
                }else{
                    return "El campo '$field' no puede ser editado.";
                }
            }
            $i++;
            //print_r($dato["Name"]);
        }
        if($count == 0){
            return "El campo '$field' no existe en el documento. Por favor, verifique los datos ingresados";
        }

        $documentoJson = json_encode($documento["InternalObject"]);
        $documentoJson = str_replace("[]","{}",$documentoJson);
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://identidadarg.cloudycrm.net/restful/documents/$docId",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => '{
            "document": 
            '.$documentoJson.'
        
        }',
        CURLOPT_HTTPHEADER => array(
            "apikey: $this->apiKey"
          ),
        ));
        
        $response = curl_exec($curl);

        curl_close($curl);
        $response = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $response), true );
        return $response;
    }
}
