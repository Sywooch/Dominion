<?php
require_once(SITE_PATH . '/admin/core_extension.php');

/**
 * Класс Транслит предназначен для перевода строки символов кириллицы в строку
 * символов латиницы в соответствии с правилами транслитерации,
 * указанными в админке.
 *
 * @author Администратор
 */
class Translit
{
    /**
     * Преобразовывает строку символов кириллицы в строку символов латиницы.
     *
     * @param string $cyrStr
     *
     * @return string
     */
    public function getLatin($cyrStr)
    {
        $rules = $this->getRules();
        $cyrStr = mb_convert_case($cyrStr, MB_CASE_LOWER, "utf-8");
        $latinStr = strtr($cyrStr, $rules);
        $latinStr = preg_replace("/\s+/s", '-', $latinStr);

        return $latinStr;
    }

    private function getRules()
    {
        $db = SCMF_extension::singleton();
        $sth = $db->execute("select * from TRANSLIT_RULE");
        $rules = array();
        while (list($V_TRANSLIT_RULE_ID, $V_SRC, $V_TRANSLIT) = mysql_fetch_array($sth, MYSQL_NUM)) {
            $rules[$V_SRC] = $V_TRANSLIT;
        }

        return $rules;
    }
}