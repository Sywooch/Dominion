<?php

require_once(dirname(__FILE__) . '/core.php');

class SCMF_extension extends SCMF
{

    public $debugMode = 0;

    public static function singleton()
    {
        static $db = null;

        if ($db == null) {
            return new SCMF_extension();
        }
    }


    /**
     * @return string
     *
     * @param array  $array
     * @param string $tag
     *
     * @desc Возвращает xml-строку представления $array-массива (array(0=>array('NAME'=>...)...)). Осторожно, strtolower()!
     */
    function array2xml($array = array(), $tag = "nodef")
    {
        $Output = '';

        if (is_array($array)) {
            foreach ($array as $rNum => $arr) {
                $Output .= "<" . $tag . " _record_='" . $rNum . "'>";
                foreach ($arr as $key => $value) {
                    $key = strtolower($key);
                    if (is_array($value)) {
                        $Output .= $this->array2xml($value, $key);
                    }
                    else {
                        $Output .= "<" . $key . ">" . $value . "</" . $key . ">";
                    }
                }
                $Output .= "</" . $tag . ">";
            }
        }

        return ($Output);
    }

    /**
     * @return string
     *
     * @param array  $array
     * @param string $tag
     *
     * @desc Возвращает xml-строку представления $array-массива.
     */
    function arrayPlain2xml($array = array(), $tag = "nodef")
    {
        $Output = "";
        if (is_array($array)) {
            $Output .= "<{$tag}";
            foreach ($array as $key => $value) {
                if (strspn(strtolower($key), 'abcdefghijklmnopqrstuvwxyz0123456789_') == strlen($key) && !is_numeric($key[0])) // пропускаем пока без цифр и символов подчеркивания
                {
                    if (is_numeric($key)) {
                        $key = "_numeric_" . $key;
                    }
                    if (is_array($value)) {
                        $Output .= ">" . $this->arrayPlain2xml($value, $key);
                        $tagClosed = 1;
                        break;
                    }
                    else {
                        $Output .= " {$key}='" . htmlspecialchars($value, ENT_QUOTES) . "'";
                    }
                }
            }
            $Output .= $tagClosed ? "</{$tag}>" : " />";
        }

        return ($Output);
    }


    /**
     * @return 2D-array
     *
     * @param string $stringImage
     * @param string $delimiter
     *
     * @desc   Возвращает двумерный массив расспличенной строки
     */
    function splitImageProperties($stringImage, $delimiter = "#")
    {
        $tmp = split($delimiter, $stringImage);
        $Out[0]['src'] = $tmp[0];
        $Out[0]['w'] = $tmp[1];
        $Out[0]['h'] = $tmp[2];

        return $Out;
    }

    /**
     * @return 2D-array
     *
     * @param string $stringFile
     * @param string $delimiter
     *
     * @desc   Возвращает двумерный массив расспличенной строки
     */
    function splitFileProperties($stringFile, $delimiter = "#")
    {
        $tmp = split($delimiter, $stringFile);
        $Out[0]['src'] = $tmp[0];
        $Out[0]['size'] = $tmp[1];
        $Out[0]['ext'] = $tmp[2];

        return $Out;
    }

    /**
     * @return integer
     * @desc Определяет языковую версию по HTTP_HOST
     */
    function detectLanguage()
    {
        $sth = $this->execute('select CMF_LANG_ID,SYSTEM_NAME from CMF_LANG where STATUS=1 order by ORDERING desc');
        $langArray = array();
        while (list($id, $hostName) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $defaultLangId = $id;
            if ($hostName == $_SERVER['HTTP_HOST']) {
                $langId = $id;
            }
        }
        $this->CMF_LANG_ID = isset($langId) ? $langId : $defaultLangId;

        return $this->CMF_LANG_ID;
    }

    function getMenu($parentId, $pathIDs = array())
    {
        $menu = $this->select('select * from ANOTHER_PAGES where PARENT_ID=? and REALSTATUS=1 order by ORDER_ asc', $parentId);
        for ($i = 0; $i < count($menu); $i++) {
            $menu[$i]['IMAGE'] = $this->splitImageProperties($menu[$i]['IMAGE']);
            $menu[$i]['IMAGE1'] = $this->splitImageProperties($menu[$i]['IMAGE1']);
            $menu[$i]['selected'] = ($menu[$i]['ANOTHER_PAGES_ID'] == $pathIDs[count($pathIDs) - 1] ? 1 : 0);
            $menu[$i]['on_path'] = (in_array($menu[$i]['ANOTHER_PAGES_ID'], $pathIDs) ? 1 : 0);
            //if($menu[$i]['on_path'])
            //{
            $children = $this->getMenu($menu[$i]['ANOTHER_PAGES_ID'], $pathIDs);
            if ($children) {
                $menu[$i]['menu'] = $children;
            }
            //}
        }

        return ($menu);
    }

    function setDomainCookie($name, $value, $expire = 0, $path = '/')
    {
        return setcookie($name, $value, $expire, $path);
    }
// --------------------------------------------------------------------------
    /**
     * Выбрать строку
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return string
     */
    public function selectString()
    {
        $args = func_get_args();
        $rowSet = call_user_func_array(array(&$this, 'execute'), $args);
        $a = mysql_fetch_row($rowSet);

        return $a ? (string) $a[0] : null;
    }

    // --------------------------------------------------------------------------
    /**
     * Выбрать число с плавающей точкой
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return float
     */
    public function selectFloat()
    {
        $args = func_get_args();
        $rowSet = call_user_func_array(array(&$this, 'execute'), $args);
        $a = mysql_fetch_row($rowSet);

        return $a ? (float) $a[0] : null;
    }

    // --------------------------------------------------------------------------
    /**
     * Выбрать целое число
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return int
     */
    public function selectInt()
    {
        $args = func_get_args();
        $rowSet = call_user_func_array(array(&$this, 'execute'), $args);
        $a = mysql_fetch_row($rowSet);

        return $a ? (int) $a[0] : null;
    }

    // --------------------------------------------------------------------------
    /**
     * Выбрать одну строку таблицы в виде ассоциативного массива
     * array ('ITEM_ID' => 1, 'NAME' => 'утюг');
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return array
     */
    public function selectRow()
    {
        $args = func_get_args();
        $rowSet = call_user_func_array(array(&$this, 'execute'), $args);

        return mysql_fetch_assoc($rowSet);
    }

    // --------------------------------------------------------------------------
    /**
     * Выбрать масив строк таблицы в виде массива ассоциативных массивов
     * array (
     *   array ('ITEM_ID' => 1, 'NAME' => 'утюг'),
     *   array ('ITEM_ID' => 2, 'NAME' => 'самовар'),
     *   ...
     * );
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return ADataSet
     */
    public function selectRowArray()
    {
        $args = func_get_args();
        $rowSet = call_user_func_array(array(&$this, 'execute'), $args);
        $a = array();
        while ($row = mysql_fetch_assoc($rowSet)) {
            $a[] = $row;
        }

        return $a;
    }

    /**
     * выбрать массив целых чисел
     *
     * @example array (1234, 234, 231, 12);
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return array
     */
    public function selectIntArray()
    {
        $args = func_get_args();
        $rowSet = call_user_func_array(array(&$this, 'execute'), $args);
        $a = array();
        while ($row = mysql_fetch_row($rowSet)) {
            $a[] = (int) $row[0];
        }

        return $a;
    }

    // --------------------------------------------------------------------------
    /**
     * Выполнение форматированного запроса SQL.
     * В качестве palceholder'ов используются знаки вопроса
     * Результат аналогичен вызову mysql_query
     *
     * $sql = 'select * from TABLE where FIELD=?';
     * $db->execute($sql, $param1, $param2[, ...]);
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return resource
     */
    public function execute()
    {
        $args = func_get_args();
        $sql = call_user_func_array(array(&$this, 'format'), $args);

        return $this->query($sql);
    }

    // --------------------------------------------------------------------------
    /**
     * Форматирование запроса с парметрами
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return string
     */
    public function format()
    {
        $argc = func_num_args();
        if ($argc < 1) {
            echo('Не задан запрос SQL');
        }
        $argv = func_get_args();
        $sql = $argv[0];

        $offset = 0;
        for ($idx = 1; $idx < $argc; $idx++) {
            $position = strpos($sql, '?', $offset);
            if ($position === false) {
                break;
            }

            $curArg = $argv[$idx];
            if (is_null($curArg)) {
                $value = 'NULL';
            }
            else {
                if (is_int($curArg)) {
                    $value = (string) $curArg;
                }
                elseif (is_string($curArg)) {
                    $value = '"' . $this->escapeString($curArg) . '"';
                }
                elseif (is_bool($curArg)) {
                    $value = (string) ($curArg ? 1 : 0);
                }
                elseif (is_float($curArg)) {
                    $value = str_replace(',', '.', (string) $curArg);
                } // для независимости от локали
                else {
                    echo('Недопустимы тип параметра');
                }
                /**
                 * @todo тут можно сделать обработку аргумента типа "массив"
                 */
            }
            $offset = $position + strlen($value);
            $sql = substr($sql, 0, $position) . $value . substr($sql, $position + 1);
        }

        return $sql;
    }
    // --------------------------------------------------------------------------
    /**
     * выполнение неформатированного запроса sql.
     * Результат аналогичен вызову mysql_query
     *
     * @example $db->query($sql);
     *
     * @param string $sql
     * @param mixed  $param
     *
     * @return resource
     */
    public function query($sql)
    {
        // если нет подключения к БД
        if (!$this->dbh) // подключаемся
        {
            $this->connect();
        }

        // выполняем запрос
        if ($this->debugMode) {
            $this->queriesCount++;
            $timeStart = microtime(true);

            $result = mysql_query($sql, $this->dbh);

            $time = microtime(true) - $timeStart;
            $this->queries[] = array(
              'sql' => $sql,
              'time' => $time,
              'affectedRows' => $this->getAffectedRows()
            );
        }
        else {
            $result = mysql_query($sql, $this->dbh);
        }

        // проверяем на наличие ошибок
        $errNo = (int) mysql_errno($this->dbh);
        // если были ошибки
        if ($errNo > 0) // пораждаем исключение
        {
            echo('Ошибка в ходе выполнения запроса (' . mysql_error($this->dbh) . ') ' . $sql);
        }

        // возвращаем результат
        return $result;
    }

    // --------------------------------------------------------------------------
    /**
     * Количество измененные строк таблицы
     *
     * @return int
     */
    public function getAffectedRows()
    {
        return mysql_affected_rows($this->dbh);
    }

// --------------------------------------------------------------------------
    /**
     * экранирование зарезирвированных символов с учетом кодировки соединения
     *
     * @param string $string
     *
     * @return string
     */
    public function escapeString($string)
    {
        // если нет подключения к БД
        if (!$this->dbh) // подключаемся
        {
            $this->connect();
        }

        return mysql_real_escape_string($string, $this->dbh);
    }
}

/**
 * @return void
 *
 * @param string $string
 *
 * @desc Отображает xml-строку с квоченными симовлами '<','>' и с <br />-ками
 */
function showXml($string)
{
    $newString = preg_replace('~(<(.*?)>)~', '&lt;\2&gt;<br />', $string);
    print $newString;
}

?>
