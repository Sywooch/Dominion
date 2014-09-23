<?php
/* ##########################
	E-Trade Http Tunnel v1.7.
	HTTP tunnel script.    
	
	Copyright (c) 2011-2012 ElbuzGroup
	http://www.elbuz.com
	
	This script allows you to manage database server even if the corresponding port is blocked or remote access to database server is not allowed.    
   ##########################
*/

$ConvertSpecialCharactersToHTMLEntities = 0; // Convert special characters to HTML entities

header("Content-type: text/html; charset=utf-8");

if (version_compare(phpversion(), "4") <= 0) {
    echo 'Версия PHP ' . phpversion() . ' не совместима для работы E-Trade Http Tunnel, обновите версию PHP до 5 и выше.';
    exit;
}

//error_reporting(E_ERROR);

// Debug level
$debug = 1;
if ($debug == 1) {
    error_reporting(E_ALL ^ (E_NOTICE | E_DEPRECATED)); // E_ERROR | E_WARNING | E_PARSE | E_NOTICE
    ini_set('display_errors', 1);
    ini_set('html_errors', 0);

    set_error_handler('myErrorHandler', E_ALL ^ (E_NOTICE | E_DEPRECATED));
    register_shutdown_function('fatalErrorShutdownHandler');
}

require_once('./etrade_http_tunnel_login.php'); // Auth setup

$authenticated = 0;
if (isset($_GET['authorization'])) {
    if (preg_match('/^Basic\s+(.*)$/i', $_GET['authorization'], $user_pass)) {
        list($user, $pass) = explode(':', base64_decode($user_pass[1]));

        if ($user == $login && $pass == $password) {
            $authenticated = 1;
        }
    }
} else {
    if (isset($_SERVER['PHP_AUTH_USER'])) {
        if ($_SERVER['PHP_AUTH_USER'] == $login && $_SERVER['PHP_AUTH_PW'] == $password) {
            $authenticated = 1;
        }
    }
}

if ($authenticated == 0) {
    header('WWW-Authenticate: Basic realm="Restricted Area"');
    header('HTTP/1.1 401 Unauthorized');
    SendAnswer("Error: Authenticate login or password not valid! (Ошибка: Не правильный логин или пароль для доступа к модулю!)");
    exit;
}

if (stristr(PHP_OS, 'WIN')) { // Detect operation system
    $dir_separator = '\\\\';
} else {
    $dir_separator = '/';
}
$base_path = dirname(__FILE__) . $dir_separator;
$base_path = str_ireplace('\\', '\\\\', $base_path); // for Windows OS only

require_once('./etrade_http_tunnel_ifunc.php');
require_once('./json.php');
require_once('./pclzip.lib.php');
require_once('./fgetcsv.php');

if (!function_exists('json_encode')) {
    function json_encode($data)
    {
        $json = new Services_JSON();

        return ($json->encode($data));
    }
}

// Future-friendly json_decode
if (!function_exists('json_decode')) {
    function json_decode($data, $bool)
    {
        if ($bool) {
            $json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        } else {
            $json = new Services_JSON();
        }

        return ($json->decode($data));
    }
}

// Create temp dir
if (is_dir('./temp/') == false) {
    mkdir('./temp/', 0777, true);
}

date_default_timezone_set('Europe/Kiev');
ini_set("memory_limit", "512M");
ini_set("post_max_size", "128M");
ini_set("upload_max_filesize", "128M");
ini_set("max_execution_time", "30000");
ini_set("max_input_time", "6000");
ini_set('auto_detect_line_endings', '1');

$ini_get_locale = setlocale(LC_ALL, array('ru_RU.CP1251', 'ru_RU.cp1251', 'ru_RU.UTF8', 'ru_RU.utf8', 'ru_ru.CP1251', 'ru_ru.cp1251', 'ru_ru.UTF8', 'ru_ru.utf8', 'ru_RU', 'ru_ru', 'Russian'));

// Get options
$load_data_infile = '0';


if (isset($_POST['dbhost']) == true) $dbhost = $_POST['dbhost'];
if (isset($_POST['dbuser']) == true) $dbuser = $_POST['dbuser'];
if (isset($_POST['dbpass']) == true) $dbpass = $_POST['dbpass'];
if (isset($_POST['dbname']) == true) $dbname = $_POST['dbname'];
if (isset($_POST['dbcharset']) == true) $dbcharset = $_POST['dbcharset'];
if (isset($_POST['sql_query']) == true) $sql_query = $_POST['sql_query'];
if (isset($_POST['sql_query2']) == true) $sql_query2 = $_POST['sql_query2'];
if (isset($_POST['rules_scheme']) == true) $rules_scheme = $_POST['rules_scheme'];
if (isset($_POST['zlib']) == true) $zlib = $_POST['zlib'];
if (isset($_POST['zlib_upload']) == true) $zlib_upload = $_POST['zlib_upload'];
if (isset($_POST['file_name']) == true) $file_name = $_POST['file_name'];
if (isset($_POST['type_op']) == true) $type_op = $_POST['type_op'];
if (isset($_POST['load_data_infile']) == true) $load_data_infile = $_POST['load_data_infile'];

// Check parametrs
if (strlen($type_op) == 0) {
    header("Content-type: text/html; charset=utf-8");

    echo '<p>Вы двигаетесь в верном направлении и Вам удалось успешно установить модуль интеграции E-Trade HTTP Tunnel.<br />
</p>
<p>На данный момент Вам необходимо вставить ссылку, по которой Вы только что перешли на эту страницу, в настройку <br />
  E-Trade HTTP Tunnel в программе серии E-Trade.</p><p>Это можно сделать нажав кнопку &quot;Файл&quot; в верхнем левом углу программы серии E-Trade и выбрать раздел &quot;Импорт данных&quot;.</p>
<img src="http://www.elbuz.com/images/my/file_export_setup_etrade_tunnel.png" alt="" width="154" height="134" border="0">
<br />
<p><img src="http://www.elbuz.com/ETradeDocs/PLI/setup_etrade_tunnel4.png" alt="" width="524" height="563" border="0" align="left">Адрес к туннелю (URL) - это ссылка по которой вы перешли сейчас, <br /> которая отображается в строке адреса сайта.<br />
Имя пользователя и пароль это те данные, которые Вы вводили <br />
переходя по ссылке, до того как увидеть это сообщение.<br />
  <br />
  <a href="http://www.elbuz.com/ETradeDocs/PLI/configure_and_import_data_with_e_trade_http_tunnel.htm">Подробнее в документации, где найти это окно</a> <br />
</p>
<p>После ввода этих данных, необходимо перейти к настройкам <br />
  подключения к базе данных Вашего сайта.<br />
  Как найти эти данные для Вашего движка (CMS интернет-магазина) <br />
  Вы сможете прочитать в документации в разделе <br />
  <a href="http://www.elbuz.com/ETradeDocs/PLI/setting_up_access_to_the_database_site_and_other_settings.htm">Настройка доступа к БД сайта и другие настройки для Вашего движка</a>
</p>
<p>Если вы не смогли настроить модуль интеграции E-Trade HTTP Tunnel - <br />
обратись в службу технической поддержки с указанием проблемы, <br />
постараемся Вам помочь.</p>';

    //SendAnswer("Query 'type_op' is empty!");
    exit;
}
if (strlen($dbhost) == 0) {
    if ($debug == 1) SendAnswer("Error: Query 'dbhost' is empty!");
    exit;
}
if (strlen($dbuser) == 0) {
    if ($debug == 1) SendAnswer("Error: Query 'dbuser' is empty!");
    exit;
}
if (strlen($dbname) == 0) {
    if ($debug == 1) SendAnswer("Error: Query 'dbname' is empty!");
    exit;
}
if (strlen($dbcharset) == 0) {
    if ($debug == 1) SendAnswer("Error: Query 'dbcharset' is empty!");
    exit;
}
if (strlen($sql_query) == 0 && $type_op != "TEST") {
    if ($debug == 1) SendAnswer("Error: Query 'sql_query' is empty!");
    exit;
}
if (strlen($zlib) == 0) {
    if ($debug == 1) SendAnswer("Error: Query 'zlib' is empty!");
    exit;
}
if (strlen($zlib_upload) == 0) {
    if ($debug == 1) SendAnswer("Error: Query 'zlib_upload' is empty!");
    exit;
}

if ($type_op == "CATALOG_CSV") {
    if (strlen($rules_scheme) == 0) {
        if ($debug == 1) SendAnswer("Error: Query 'rules_scheme' is empty!");
        exit;
    }

    if (strlen($sql_query2) == 0) {
        if ($debug == 1) SendAnswer("Error: Query 'sql_query2' is empty!");
        exit;
    }
}

// Connect to DB
$link = connect_db($dbhost, $dbuser, $dbpass, $dbname, $dbcharset);
if (!$link OR is_resource($link) == FALSE) {
    SendAnswer('Error: no connect to database!');
    exit;
}


if ($type_op == "TEST") {
    SendAnswer("Connected!\n" . "Memory Limit: " . ini_get("memory_limit") . "\nPost max size: " . ini_get("post_max_size") . "\nLocale: " . $ini_get_locale);
    exit;
}

// Run internal command
if ($type_op == "RUN_COMMAND") {
    // base64 decode
    $sql_query = str_replace(' ', '+', $sql_query);
    $sql_query = base64_decode($sql_query);
    eval($sql_query);

    SendAnswer("Complete!");
    exit;
}


if ($zlib_upload == 1) { // Unpack sql query
    // base64 decode
    $sql_query = str_replace(' ', '+', $sql_query);
    $sql_query = base64_decode($sql_query);

    // save file
    $temp_file_name = md5(microtime());
    file_put_contents('./temp/' . $temp_file_name . '.zip', $sql_query);

    // unpack file contents
    if (class_exists('ZipArchive')) {
        $archive = new ZipArchive;
        if ($archive->open('./temp/' . $temp_file_name . '.zip') === true) {
            $archive->extractTo('./temp/');
            $archive->close();
        } else {
            echo 'Не могу найти файл архива!';
        }
    } else {
        $archive = new PclZip('./temp/' . $temp_file_name . '.zip');
        $list = $archive->extract(PCLZIP_OPT_PATH, './temp/',
            PCLZIP_OPT_REMOVE_ALL_PATH);
    }

    // delete temp file
    if (file_exists('./temp/' . $temp_file_name . '.zip')) {
        unlink('./temp/' . $temp_file_name . '.zip');
    }

    // get contents from extracted file
    if ($type_op != "CATALOG_CSV") {
        if (file_exists('./temp/' . $file_name)) {
            $sql_query = file_get_contents('./temp/' . $file_name);
            unlink('./temp/' . $file_name);
        }
    }
}


// Import CSV catalog
if ($type_op == "CATALOG_CSV") {
    $import_file_name = $base_path . 'temp' . $dir_separator . $file_name;
    if (file_exists($import_file_name) == false) {
        SendAnswer("Error: File " . $import_file_name . " not exists!");
        exit;
    }

    // Create temporary tables for import CSV catalog
    run_sql_commands($sql_query2, $type_op, $link, 1, "", 0);

    $rules_scheme = str_replace(' ', '+', $rules_scheme);
    $rules_scheme = base64_decode($rules_scheme);
    $import_rules_obj = json_decode($rules_scheme, true);


    if ($load_data_infile == '1') { // fast mode: LOAD DATA INFILE
        foreach ($import_rules_obj as $rules_block) {
            $my_str = '';
            $csv_file_name = $base_path . 'temp' . $dir_separator . 'table_type_' . $rules_block['row_type'] . '.csv';

            if (!$handle = fopen($import_file_name, "r")) {
                SendAnswer("Error: Could not open file " . $import_file_name . " for read.");
                exit;
            }

            while (($data = fgetcsv($handle, filesize($import_file_name), "\t")) !== FALSE) {
                if ($data[0] == $rules_block['row_type']) {
                    foreach ($data as $id => $data_column) {
                        if ($id > 0) {
                            $my_str .= $data_column;
                            if ($id <> count($data)) $my_str .= "\t";
                        }
                    }

                    $my_str .= chr(13) . chr(10);
                }
            }
            fclose($handle);

            file_put_contents($csv_file_name, $my_str);

            mysql_query("LOAD DATA INFILE '" . $csv_file_name . "' INTO TABLE " . $rules_block['table_name'] . " (" . $rules_block['fields_list'] . ") ", $link) or die(SendAnswer("Error: " . mysql_error()));

            unlink($csv_file_name);
        }
    } else {

        if (!$handle = fopen($import_file_name, "r")) {
            SendAnswer("Error: Could not open file " . $import_file_name . " for read.");
            exit;
        }

        // Import CSV catalog
        // variant #1
        //while (($data = fgetcsv($handle, filesize($import_file_name), "\t")) !== FALSE) {
        // variant #2
        //while (($data = File_FGetCSV::fgetcsv($handle, filesize($import_file_name), "\t")) !== FALSE) {
        // variant #3
        // for ($i=0; $row=fgets($handle,filesize($import_file_name)); $i++)
        // {
        // $data=explode('\t', $row);

        while (($data = fgetcsv($handle, filesize($import_file_name), "\t")) !== FALSE) {
            foreach ($import_rules_obj as $rules_block) {
                $my_str = "";

                if ($data[0] == $rules_block['row_type']) {
                    $fields_values = explode(",", $rules_block['fields_values']);
                    foreach ($fields_values as $field_column_num) {
                        $my_str .= ((strlen($my_str) > 0) ? "," : "") . "'" . wash_string($data[trim($field_column_num)]) . "'";
                    }

                    mysql_query("INSERT INTO " . $rules_block['table_name'] . "(" . $rules_block['fields_list'] . ") VALUES(" . $my_str . ")", $link) or die(SendAnswer("Invalid query: " . $rules_block['table_name'] . ". Error: " . mysql_error()));
                }
            }
        }

        fclose($handle);
    }


    // Delete temp file
    unlink($import_file_name);

    SendAnswer("Complete!");
    exit;
}


$server_answer = run_sql_commands($sql_query, $type_op, $link, 1, $file_name, $zlib);
SendAnswer($server_answer);
unset($server_answer);


// Connecting to the database
function connect_db($dbhost, $dbuser, $dbpass, $dbname, $dbcharset)
{

    if (!$link = mysql_connect($dbhost, $dbuser, $dbpass)) {
        SendAnswer("Connect error: " . mysql_error());

        return 0;
    }

    if (is_resource($link) == FALSE) {
        SendAnswer("Check link error: " . mysqli_error($link));

        return 0;
    }

    if (!mysql_select_db($dbname, $link)) {
        SendAnswer("Select DB error: " . mysql_error());

        return 0;
    }

    mysql_query('SET names ' . $dbcharset, $link);
    mysql_query('SET SESSION character_set_database = ' . $dbcharset, $link);
    mysql_query("SET SESSION sql_mode='';", $link);
    mysql_query("SET SQL_BIG_SELECTS=1;", $link);

    return $link;
}

// Run SQL Commands
function run_sql_commands($sql_query, $type_op, $link, $base64_decode, $file_name, $pack_answer)
{

    $csv_result_all = "";

    if ($base64_decode == 1) {
        $sql_query = str_replace(' ', '+', $sql_query);
        $sql_query = base64_decode($sql_query);
    }

    $sql_query_all = explode(";;;", $sql_query);
    foreach ($sql_query_all as $my_sql_query) {
        $my_sql_query = str_replace(array(chr(13) . chr(10), chr(13), chr(10)), ' ', $my_sql_query);
        if (strlen(trim($my_sql_query)) > 0) {
            //$result = mysql_query($my_sql_query, $link) or die(SendAnswer("Error: ". mysql_error(). "\r\n SQL: ".$my_sql_query));
            $result = mysql_unbuffered_query($my_sql_query, $link) or die(SendAnswer("Error: " . mysql_error() . "\r\n SQL: " . $my_sql_query));

            if ($type_op == "SELECT" && is_resource($result) == TRUE) {
                $field_separator = "\t";
                $row_separator = "\r\n";

                while ($row = mysql_fetch_row($result)) {
                    $row = str_ireplace(array(chr(13), chr(10), chr(9)), ' ', $row);
                    $csv_result_all .= implode($field_separator, $row) . $row_separator;
                }

                mysql_free_result($result);
            }

            //if ($type_op=="INSERT") $server_answer.=" ID: ".mysql_insert_id($link);
        }
    }

    if ($type_op == "SELECT") {
        if ($pack_answer == 1 && strlen($file_name) > 0) { // Pack answer
            file_put_contents('./temp/' . $file_name . '.csv', $csv_result_all);

            if (class_exists('ZipArchive')) {
                $zip = new ZipArchive;
                $res = $zip->open('./temp/' . $file_name . '.zip', ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE);
                if ($res === TRUE) {
                    $zip->addFromString('./temp/' . $file_name . '.csv', $csv_result_all);
                }
                $zip->close();
            } else {
                $archive = new PclZip('./temp/' . $file_name . '.zip');
                $list = $archive->add('./temp/' . $file_name . '.csv', PCLZIP_OPT_REMOVE_ALL_PATH);
            }

            if (file_exists('./temp/' . $file_name . '.zip')) {
                $csv_result_all = file_get_contents('./temp/' . $file_name . '.zip');
                unlink('./temp/' . $file_name . '.zip');
            }
            if (file_exists('./temp/' . $file_name . '.csv')) {
                unlink('./temp/' . $file_name . '.csv');
            }
        }
    }

    if (strlen($csv_result_all) == 0) {
        $csv_result_all = "Complete!";
    }

    return $csv_result_all;
}

function wash_string($string)
{
    global $ConvertSpecialCharactersToHTMLEntities;

    //Conversion from utf8 to win1251
    // if (DB_CHARSET=="utf8") {
    // $string=iconv("windows-1251", 'utf-8', $string);
    // }

    // Remove line breaks
    $string = str_replace(array(chr(13) . chr(10), chr(13), chr(10), chr(9)), ' ', $string);

    // Convert special characters to HTML entities
    if ($ConvertSpecialCharactersToHTMLEntities == 1) $string = htmlspecialchars($string, ENT_QUOTES);

    // Mnemoni special characters in a string for use in a SQL statement, taking into account the current charset / charset connection
    if (get_magic_quotes_gpc()) {
        $string = stripslashes($string);
    }

    $string = mysql_real_escape_string($string);

    return $string;
}


// Send answer
function SendAnswer($text)
{
    global $link;

    if (is_resource($link) == TRUE) {
        mysql_close($link);
    }

    echo base64_encode($text);
    unset($text);
}

// error handler function
function myErrorHandler($errno, $errstr, $errfile, $errline)
{
    if (!(error_reporting() & $errno)) {
        // This error code is not included in error_reporting
        //return;
    }

    switch ($errno) {
        case E_USER_ERROR:
            $type_error = "Fatal error ";
            break;
        case E_USER_WARNING:
            $type_error = "WARNING ";
            break;
        case E_USER_NOTICE:
            $type_error = "NOTICE ";
            break;
        default:
            $type_error = "Unknown error type ";
            break;
    }

    SendAnswer(error_reporting() . "Error: " . $type_error . "\nFile: " . $errfile . "\nMessage: " . $errstr . "\nLine: " . $errline);

    /* Don't execute PHP internal error handler */

    return true;
}

function fatalErrorShutdownHandler()
{
    if (version_compare(phpversion(), "5.2") >= 0) {
        $last_error = error_get_last();
        if ($last_error['type'] === E_ERROR) {
            // fatal error
            myErrorHandler(E_ERROR, $last_error['message'], $last_error['file'], $last_error['line']);
        }
    }
}

?>