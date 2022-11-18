# CloudyCRM API Wrapper para PHP
<h2>Instalación</h2>
Para poder utilizar el conector, primero debemos instalar el archivo crm_api.php y agregarlo a la carpeta que deseemos.<br>
Luego, lo incluimos con 
<code>include "api_crm.php";</code><br>
Una vez que lo incluimos (Puede ser include o require), inicializamos la clase.
Para ello debemos tener el subdominio, que sería el nombre anterior al “.cloudycrm.net.ar”. Por ejemplo, <b>kuaidev</b>.cloudycrm.net.ar<br>
Además, debemos tener una Api Key.<br>
<code>$cloudyCRM = new CloudyCRM($subDominio,$apiKey);</code>

<h2>Funciones</h2>
<h3>Contar la cantidad de datos en una carpeta</h3>
Esto nos devolverá cuántos datos hay en una carpeta dada una query.
$query = Una query hecha en lengua SQL.
$folder = la carpeta de la que deseamos obtener la información.<br>
<code>$cloudyCRM->contarDatos($query,$folder);</code>

<h3>Buscar el DOC_ID de un documento</h3>
Esta función nos permitirá buscar un documento dada una query, por ejemplo,
$query = “email LIKE ‘contact@kuaidev.net.ar’”
Además, debemos elegir la carpeta en la que buscamos el documento para agregar en $folder.<br>
<code>$cloudyCRM->searchDocId($query,$folder);</code>

<h3>Obtener toda la información de un documento</h3>
Esta función nos devuelve todos los datos de un documento, lo buscaremos utilizando su doc_id.<br>
<code>$cloudyCRM->getDocumento($docId);</code>

<h3>Modificar un documento</h3>
Con esta función podremos modificar cualquier campo editable de un documento. Utilizaremos el doc_id para identificar el documento, la variable $field para el campo que deseamos modificar y $value para el valor que deseamos darle al campo. Devolverá la información completa del documento ya modificado.
<br>
<code>
$cloudyCRM->modificarDocumento($docId,$field,$value);</code>
