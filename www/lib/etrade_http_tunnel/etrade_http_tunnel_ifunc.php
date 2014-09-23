<?php

function doubleExplode($del1, $del2, $array)
{
    $array1 = explode($del1, $array);

    foreach ($array1 as $key => $value) {
        $array2 = explode($del2, $value);

        foreach ($array2 as $key2 => $value2) {
            $array3[] = $value2;
        }
    }

    $afinal = array();
    for ($i = 0; $i <= count($array3); $i += 2) {
        if ($array3[$i] != "") {
            $afinal[trim($array3[$i])] = trim($array3[$i + 1]);
        }
    }

    return $afinal;
}

function get_file_extension($file_name)
{
    return end(explode('.', $file_name));
}

/**
 * Recursively remove directory (or just a file)
 *
 * @param string $source
 * @param bool   $delete_root
 * @param string $pattern
 *
 * @return bool
 */
function remove_files($source, $delete_root = true, $pattern = '')
{
    if (stristr(PHP_OS, 'WIN')) {// Detect operation system
        $source = str_replace('/', '\\', $source);
    }

    // Simple copy for a file
    if (is_file($source)) {
        $res = true;
        if (empty($pattern) || (!empty($pattern) && preg_match('/' . $pattern . '/', basename($source)))) {
            $res = @unlink($source);
        }
        clearstatcache();
        if (@is_file($source)) {
            $filesys = preg_replace("/", "\\", $source);
            $delete = @system("del $filesys");
            clearstatcache();
            if (@is_file($source)) {
                $delete = @chmod($source, 0775);
                $delete = @unlink($source);
                $delete = @system("del $filesys");
            }
        }
        clearstatcache();
        if (@is_file($source)) {
            return false;
        } else {
            return true;
        }
    }

    // Loop through the folder
    if (is_dir($source)) {
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..' || $entry == 'index.html' || $entry == 'index.htm' || $entry == '.htaccess') {
                continue;
            }
            if (remove_files($source . '/' . $entry, true, $pattern) == false) {
                return false;
            }
        }
        // Clean up
        $dir->close();

        return ($delete_root == true && empty($pattern)) ? @rmdir($source) : true;
    } else {
        return false;
    }
}

// translit
function cyr_to_translit($content)
{
    $transA = array('А' => 'a', 'Б' => 'b', 'В' => 'v', 'Г' => 'h', 'Ґ' => 'g', 'Д' => 'd', 'Е' => 'e', 'Ё' => 'jo', 'Є' => 'e', 'Ж' => 'zh', 'З' => 'z', 'И' => 'i', 'І' => 'i', 'Й' => 'i', 'Ї' => 'i', 'К' => 'k', 'Л' => 'l', 'М' => 'm', 'Н' => 'n', 'О' => 'o', 'П' => 'p', 'Р' => 'r', 'С' => 's', 'Т' => 't', 'У' => 'u', 'Ў' => 'u', 'Ф' => 'f', 'Х' => 'h', 'Ц' => 'c', 'Ч' => 'ch', 'Ш' => 'sh', 'Щ' => 'sz', 'Ъ' => '', 'Ы' => 'y', 'Ь' => '', 'Э' => 'e', 'Ю' => 'yu', 'Я' => 'ya');
    $transB = array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'ґ' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'jo', 'є' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'і' => 'i', 'й' => 'i', 'ї' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ў' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sz', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu', 'я' => 'ya', '&quot;' => '', '&amp;' => '', 'µ' => 'u', '№' => '');
    $content = trim(strip_tags($content));
    $content = strtr($content, $transA);
    $content = strtr($content, $transB);
    $content = preg_replace("/\s+/ms", "-", $content);
    $content = preg_replace("/[ ]+/", "-", $content);
    $content = preg_replace("/[^a-z0-9_]+/mi", "", $content);
    $content = stripslashes($content);

    return $content;
}

function imageResize($sourceFile, $destFile, $destWidth = NULL, $destHeight = NULL, $fileType = 'jpg')
{
    list($sourceWidth, $sourceHeight, $type, $attr) = getimagesize($sourceFile);
    if (!$sourceWidth)
        return false;
    if ($destWidth == NULL) $destWidth = $sourceWidth;
    if ($destHeight == NULL) $destHeight = $sourceHeight;

    $sourceImage = createSrcImage($type, $sourceFile);

    $widthDiff = $destWidth / $sourceWidth;
    $heightDiff = $destHeight / $sourceHeight;

    if ($widthDiff > 1 AND $heightDiff > 1) {
        $nextWidth = $sourceWidth;
        $nextHeight = $sourceHeight;
    } else {
        if ($widthDiff > $heightDiff) {
            $nextHeight = $destHeight;
            $nextWidth = round(($sourceWidth * $nextHeight) / $sourceHeight);
            $destWidth = (int)$destWidth; // $nextWidth
        } else {
            $nextWidth = $destWidth;
            $nextHeight = round($sourceHeight * $destWidth / $sourceWidth);
            $destHeight = (int)$destHeight; // $nextHeight
        }
    }

    $destImage = imagecreatetruecolor($destWidth, $destHeight);

    $white = imagecolorallocate($destImage, 255, 255, 255);
    imagefill($destImage, 0, 0, $white);

    imagecopyresampled($destImage, $sourceImage, (int)(($destWidth - $nextWidth) / 2), (int)(($destHeight - $nextHeight) / 2), 0, 0, $nextWidth, $nextHeight, $sourceWidth, $sourceHeight);
    imagecolortransparent($destImage, $white);

    return (returnDestImage($fileType, $destImage, $destFile));
}

function returnDestImage($type, $ressource, $filename)
{
    $flag = false;
    switch ($type) {
        case 'gif':
            $flag = imagegif($ressource, $filename);
            break;
        case 'png':
            $flag = imagepng($ressource, $filename, 7);
            break;
        case 'jpeg':
        default:
            $flag = imagejpeg($ressource, $filename, 90);
            break;
    }
    imagedestroy($ressource);

    return $flag;
}


function createSrcImage($type, $filename)
{
    switch ($type) {
        case 1:
            return imagecreatefromgif($filename);
            break;
        case 3:
            return imagecreatefrompng($filename);
            break;
        case 2:
        default:
            return imagecreatefromjpeg($filename);
            break;
    }
}

function phpshop_import_pf_type()
{
    global $link;
    $sql_result = mysql_query("SELECT row_type, field_value1, field_value2 FROM etrade_cc_filters WHERE row_type='pf' OR row_type='cs'", $link) or die(SendAnswer("Error: " . mysql_error()));

    while ($sql_row = mysql_fetch_array($sql_result)) {
        if ($sql_row['row_type'] == 'pf') {
            $vendor_new = doubleExplode(',', ':', $sql_row['field_value2']);
            $vendor = "";

            if (is_array($vendor_new)) {
                foreach ($vendor_new as $k => $v) {
                    if (is_array($v)) {
                        foreach ($v as $o => $p)
                            @$vendor .= "i" . $k . "-" . $p . "i";
                    } else {
                        @$vendor .= "i" . $k . "-" . $v . "i";
                    }
                }
            }

            mysql_query("UPDATE phpshop_products SET vendor='" . $vendor . "', vendor_array='" . addslashes(serialize($vendor_new)) . "' WHERE id='" . $sql_row['field_value1'] . "'", $link) or die(SendAnswer("Error: " . mysql_error()));
        }

        if ($sql_row['row_type'] == 'cs') {
            $serialized_sort = addslashes(serialize(explode(",", $sql_row['field_value2'])));
            mysql_query("UPDATE phpshop_categories SET sort='" . $serialized_sort . "' WHERE id=" . $sql_row['field_value1'], $link) or die(SendAnswer("Error: " . mysql_error()));
        }
    }
}

function virtuemart_import_ptv_type()
{
    global $link;
    $DB_TablePrefix = "jos_";

    // ссылка на тип товара для товаров
    mysql_query("INSERT INTO " . $DB_TablePrefix . "vm_product_product_type_xref (product_id, product_type_id) SELECT field_value1, field_value4 FROM etrade_cc_filters WHERE row_type='ptv' AND etrade_cc_filters.field_value1 NOT IN (SELECT product_id FROM " . $DB_TablePrefix . "vm_product_product_type_xref) GROUP BY field_value1, field_value4") or die(SendAnswer("Error: " . mysql_error()));


    // создаём таблицы
    $sql_result1 = mysql_query("SELECT row_type, field_value1, field_value2, field_value3, field_value4 FROM etrade_cc_filters WHERE row_type='pt'", $link) or die(SendAnswer("Error: " . mysql_error()));

    while ($sql_row = mysql_fetch_array($sql_result1)) {

        if ($sql_row['row_type'] == 'pt') {
            mysql_query("INSERT INTO " . $DB_TablePrefix . "vm_product_type (product_type_id, product_type_name, product_type_publish) VALUES('" . $sql_row['field_value1'] . "', '" . mysql_real_escape_string($sql_row['field_value2']) . "', 'Y')") or die(SendAnswer("Error: " . mysql_error()));

            // Удаление таблицы
            mysql_query("DROP TABLE IF EXISTS " . $DB_TablePrefix . "vm_product_type_" . $sql_row['field_value1']) or die(SendAnswer("Error: " . mysql_error()));

            $fields_list = '';
            $sql_result2 = mysql_query("SELECT field_value4, field_value2 FROM `etrade_cc_filters` WHERE `row_type`='ptv' AND field_value4='" . $sql_row['field_value1'] . "' GROUP BY field_value4, field_value2", $link) or die(SendAnswer("Error: " . mysql_error()));

            while ($sql_row2 = mysql_fetch_array($sql_result2)) {
                $fields_list .= "`" . $sql_row2['field_value2'] . "` TEXT NULL, ";
            }

            // создание таблиц которые будут хранить значения характеристик
            $result = mysql_query("CREATE TABLE " . $DB_TablePrefix . "vm_product_type_" . $sql_row['field_value1'] . " (
									`product_id` INT NOT NULL , " . $fields_list . "
									PRIMARY KEY ( `product_id` ) 
									) ENGINE = MYISAM DEFAULT CHARSET = utf8;") or die(SendAnswer("Error: " . mysql_error()));
        }
    }


    // добавляем данные в таблицах
    $sql_result3 = mysql_query("SELECT row_type, field_value1, field_value2, field_value3, field_value4 FROM etrade_cc_filters WHERE row_type='ptv'", $link) or die(SendAnswer("Error: " . mysql_error()));

    while ($sql_row = mysql_fetch_array($sql_result3)) {
        if ($sql_row['row_type'] == 'ptv') {
            mysql_query("INSERT INTO " . $DB_TablePrefix . "vm_product_type_" . $sql_row['field_value4'] . " (" . $sql_row['field_value2'] . ", product_id) VALUES('" . mysql_real_escape_string($sql_row['field_value3']) . "', '" . $sql_row['field_value1'] . "') ON DUPLICATE KEY UPDATE " . $sql_row['field_value2'] . "='" . mysql_real_escape_string($sql_row['field_value3']) . "'") or die(SendAnswer("Error: " . mysql_error()));

            $count_features_values_add++;
        }
    }
}


function hostcms_import_pics($DB_TablePrefix)
{
    $delete_temp_file = 0; // удалять временные файлы 0-нет или 1-да

    $UploadDirTemp = "../upload/my_products_img/";
    if (is_dir($UploadDirTemp) == false) die(SendAnswer('Error: Для копирования фотограий, создайте временную папку - ' . $UploadDirTemp . ', перепишите файлы выгруженные из прогаммы E-Trade Content Creator в эту папку.'));

    if (is_file('../main_classes.php') == false) die(SendAnswer('Error: Не найден файл ../main_classes.php'));
    if (is_file('../modules/shop/shop.class.php') == false) die(SendAnswer('Error: Не найден файл ../modules/shop/shop.class.php'));

    // nesting_level for HostCMS v5
    // $sql_result = mysql_query("SELECT site_nesting_level FROM site_table WHERE site_id=1", $link) or die(SendAnswer("Error: ". mysql_error()));

    // d:\WebServers\home\hostcms6.ru\www\modules\shop\item\model.php
    // /modules/hostcms5/shop/shop.class.php

    require_once('../main_classes.php');
    require_once('../modules/shop/shop.class.php');
    $shop = new shop();

    global $link;

    $sql_result = mysql_query("SELECT tov_id, pic_small, pic_medium, pic_big, pic_order, picID, tov_name, tov_guid FROM etrade_cc_pics_flat", $link) or die(SendAnswer("Error: " . mysql_error()));

    while ($sql_row = mysql_fetch_array($sql_result)) {
        // создание каталога для хранения фотографий товаров
        $UploadDir = '../' . $shop->GetItemDir($sql_row['tov_id']);
        if (is_dir($UploadDir) == false) mkdir($UploadDir, 0777, true);
        if (is_dir($UploadDir) == false) die(SendAnswer('Error: ошибка создания директории для хранения фотографий товаров - ' . $UploadDir));

        // Копирование файлов из временной папки в основную
        if (is_file($UploadDirTemp . strtolower($sql_row['pic_small']))) {
            copy($UploadDirTemp . strtolower($sql_row['pic_small']), $UploadDir . strtolower($sql_row['pic_small']));
            if ($delete_temp_file == 1) unlink($UploadDirTemp . strtolower($sql_row['pic_small']));
        }

        if (is_file($UploadDirTemp . strtolower($sql_row['pic_medium']))) {
            copy($UploadDirTemp . strtolower($sql_row['pic_medium']), $UploadDir . strtolower($sql_row['pic_medium']));
            if ($delete_temp_file == 1) unlink($UploadDirTemp . strtolower($sql_row['pic_medium']));
        }

        if (is_file($UploadDirTemp . strtolower($sql_row['pic_big']))) {
            copy($UploadDirTemp . strtolower($sql_row['pic_big']), $UploadDir . strtolower($sql_row['pic_big']));
            if ($delete_temp_file == 1) unlink($UploadDirTemp . strtolower($sql_row['pic_big']));
        }
    }
}


function hostcms6_import_pics($DB_TablePrefix, $shop_id)
{
    global $link;

    $delete_temp_file = 0; // удалять временные файлы 0-нет или 1-да

    $UploadDirTemp = "../upload/my_products_img/";
    if (is_dir($UploadDirTemp) == false) die(SendAnswer('Error: Для копирования фотограий, создайте временную папку - ' . $UploadDirTemp . ', перепишите файлы выгруженные из прогаммы E-Trade Content Creator в эту папку.'));

    if ($shop_id == 0) die(SendAnswer('Error: Не указан ИД сайта!'));

    // nesting_level for HostCMS v6
    // $sql_result = mysql_query("SELECT nesting_level FROM sites WHERE id=1", $link) or die(SendAnswer("Error: ". mysql_error()));

    $sql_result = mysql_query("SELECT tov_id, pic_small, pic_medium, pic_big, pic_order, picID, tov_name, tov_guid FROM etrade_cc_pics_flat", $link) or die(SendAnswer("Error: " . mysql_error()));

    while ($sql_row = mysql_fetch_array($sql_result)) {
        // создание каталога для хранения фотографий товаров
        $UploadDir = '../upload/shop_' . $shop_id . '/' . hostcms_getNestingDirPath($sql_row['tov_id']) . '/item_' . $sql_row['tov_id'] . '/';

        if (is_dir($UploadDir) == false) mkdir($UploadDir, 0777, true);
        if (is_dir($UploadDir) == false) die(SendAnswer('Error: ошибка создания директории для хранения фотографий товаров - ' . $UploadDir));

        // Копирование файлов из временной папки в основную
        if (is_file($UploadDirTemp . strtolower($sql_row['pic_small']))) {
            copy($UploadDirTemp . strtolower($sql_row['pic_small']), $UploadDir . strtolower($sql_row['pic_small']));
            if ($delete_temp_file == 1) unlink($UploadDirTemp . strtolower($sql_row['pic_small']));
        }

        if (is_file($UploadDirTemp . strtolower($sql_row['pic_medium']))) {
            copy($UploadDirTemp . strtolower($sql_row['pic_medium']), $UploadDir . strtolower($sql_row['pic_medium']));
            if ($delete_temp_file == 1) unlink($UploadDirTemp . strtolower($sql_row['pic_medium']));
        }

        if (is_file($UploadDirTemp . strtolower($sql_row['pic_big']))) {
            copy($UploadDirTemp . strtolower($sql_row['pic_big']), $UploadDir . strtolower($sql_row['pic_big']));
            if ($delete_temp_file == 1) unlink($UploadDirTemp . strtolower($sql_row['pic_big']));
        }
    }
}

/**
 * Получение пути к директории определенного уровня вложенности по идентификатору сущности.
 * Например, для сущности с кодом 17 и уровнем вложенности 3 вернется строка 0/1/7 или массив из 3-х элементов - 0,1,7
 * Для сущности с кодом 23987 и уровнем вложенности 3 возвращается строка 2/3/9 или массив из 3-х элементов - 2,3,9.
 *
 * @param $id    идентификатор сущности
 * @param $level уровень вложенности, по умолчанию 3
 * @param $type  тип возвращаемого результата, 0 (по умолчанию) - строка, 1 - массив
 *
 * @return mixed строка или массив названий групп
 */
function hostcms_getNestingDirPath($id, $level = 3, $type = 0)
{
    $id = intval($id);
    $level = intval($level);
    $sId = sprintf("%0{$level}d", $id);
    $aPath = array();

    for ($i = 0; $i < $level; $i++) {
        $aPath[$i] = $sId{$i};
    }

    if ($type == 0) return implode('/', $aPath);

    return $aPath;
}


// CS-Cart
function cs_cart_import_pics($DB_TablePrefix, $TableSource, $pic1_pic2_identical_pics)
{
    global $link;

    //  проверка индексов в таблицах
    $index_query = mysql_query("SHOW INDEX FROM " . $DB_TablePrefix . "images WHERE key_name = 'image_path'") or die("Invalid query: " . mysql_error());
    if (mysql_num_rows($index_query) == 0) {
        mysql_query("ALTER TABLE " . $DB_TablePrefix . "images ADD INDEX (image_path)") or die("Invalid query: " . mysql_error());
    }

    $index_query = mysql_query("SHOW INDEX FROM " . $DB_TablePrefix . "images_links WHERE key_name = 'detailed_id'") or die("Invalid query: " . mysql_error());
    if (mysql_num_rows($index_query) == 0) {
        mysql_query("ALTER TABLE " . $DB_TablePrefix . "images_links ADD INDEX (detailed_id)") or die("Invalid query: " . mysql_error());
    }

    // список фотографий (общий)
    mysql_query("DROP TEMPORARY TABLE IF EXISTS pics_list_temp", $link) or die(SendAnswer("Error: " . mysql_error()));
    mysql_query("CREATE TEMPORARY TABLE pics_list_temp (product_id int(11) NOT NULL, pic_file_name varchar(120) NOT NULL, pic_type varchar(24) NOT NULL, new_image_id int(11) NOT NULL, image_exist tinyint(1) NOT NULL, image_id_exist tinyint(1) NOT NULL, KEY product_id (product_id),  KEY pic_file_name (pic_file_name), KEY new_image_id (new_image_id), KEY image_exist (image_exist), KEY image_id_exist (image_id_exist)) ENGINE=MyISAM DEFAULT CHARSET=utf8", $link) or die(SendAnswer("Error: " . mysql_error()));

    // список фотографий из CC
    if ($TableSource == 'etrade_cc_desc' or $TableSource == '') {
        $sql_result = mysql_query("SELECT product_id, pic_file1, pic_file2, product_addon_pics FROM etrade_cc_desc", $link) or die(SendAnswer("Error: " . mysql_error()));
    } else {
        $sql_result = mysql_query("SELECT field_value1 as product_id, field_value2 as pic_file1, field_value3 as pic_file2, field_value4 as product_addon_pics FROM etrade_cc_filters WHERE row_type='pics'", $link) or die(SendAnswer("Error: " . mysql_error()));
    }

    // формируем список фотографий
    while ($sql_row = mysql_fetch_array($sql_result)) {
        // основные фотографии
        if ($pic1_pic2_identical_pics == 1) { // Фото №1 и Фото №2 в программе СС одинаковые, загружаем только Фото №2 (большое)
            mysql_query("INSERT INTO pics_list_temp (product_id, pic_file_name, pic_type) VALUES (" . $sql_row['product_id'] . ", '" . strtolower($sql_row['pic_file2']) . "', 'M')", $link) or die(SendAnswer("Error: " . mysql_error()));
        } else {
            mysql_query("INSERT INTO pics_list_temp (product_id, pic_file_name, pic_type) VALUES (" . $sql_row['product_id'] . ", '" . strtolower($sql_row['pic_file1']) . "', 'M')", $link) or die(SendAnswer("Error: " . mysql_error()));
            mysql_query("INSERT INTO pics_list_temp (product_id, pic_file_name, pic_type) VALUES (" . $sql_row['product_id'] . ", '" . strtolower($sql_row['pic_file2']) . "', 'A')", $link) or die(SendAnswer("Error: " . mysql_error()));
        }

        // дополнительные фотографии
        $product_addon_pics = explode(',', $sql_row['product_addon_pics']);

        foreach ($product_addon_pics as $product_addon_pic) {
            mysql_query("INSERT INTO pics_list_temp (product_id, pic_file_name, pic_type) VALUES (" . $sql_row['product_id'] . ", '" . strtolower(trim($product_addon_pic)) . "', 'A')", $link) or die(SendAnswer("Error: " . mysql_error()));
        }
    }

    // удаляем старые фотографии
    //mysql_query("DELETE FROM ".$DB_TablePrefix."images WHERE image_id IN (SELECT image_id FROM ".$DB_TablePrefix."images_links WHERE object_type='product' AND object_id IN (SELECT product_id FROM pics_list_temp GROUP BY product_id))", $link) or die(SendAnswer("Error: ". mysql_error()));
    //mysql_query("DELETE FROM ".$DB_TablePrefix."images_links WHERE object_type='product' AND object_id IN (SELECT product_id FROM pics_list_temp GROUP BY product_id)", $link) or die(SendAnswer("Error: ". mysql_error()));

    mysql_query("UPDATE pics_list_temp, " . $DB_TablePrefix . "images SET image_exist=1 WHERE pics_list_temp.pic_file_name<>'' AND pics_list_temp.pic_file_name=" . $DB_TablePrefix . "images.image_path", $link) or die(SendAnswer("Error: " . mysql_error()));

    mysql_query("INSERT INTO " . $DB_TablePrefix . "images (image_path) SELECT pic_file_name FROM pics_list_temp WHERE pics_list_temp.pic_file_name<>'' AND pics_list_temp.image_exist=0", $link) or die(SendAnswer("Error: " . mysql_error()));

    mysql_query("UPDATE pics_list_temp, " . $DB_TablePrefix . "images SET pics_list_temp.new_image_id=" . $DB_TablePrefix . "images.image_id WHERE pics_list_temp.pic_file_name<>'' AND pics_list_temp.pic_file_name=" . $DB_TablePrefix . "images.image_path", $link) or die(SendAnswer("Error: " . mysql_error()));

    mysql_query("UPDATE pics_list_temp, " . $DB_TablePrefix . "images_links SET pics_list_temp.image_id_exist=1 WHERE pics_list_temp.pic_file_name<>'' AND pics_list_temp.new_image_id=" . $DB_TablePrefix . "images_links.detailed_id", $link) or die(SendAnswer("Error: " . mysql_error()));

    mysql_query("INSERT INTO " . $DB_TablePrefix . "images_links (object_id, object_type, type, detailed_id) SELECT product_id, 'product' as object_type, pic_type, new_image_id FROM pics_list_temp WHERE pics_list_temp.pic_file_name<>'' AND pics_list_temp.image_id_exist=0", $link) or die(SendAnswer("Error: " . mysql_error()));

    // копируем фотографии в нужные папки
    $UploadDirTemp = "../images/detailed/";
    $config_local_file = "../config.local.php";
    $delete_temp_file = 0;
    if (is_dir($UploadDirTemp) == true && is_file($config_local_file) == true) {
        // MAX_FILES_IN_DIR
        $config_local_file_contents = file_get_contents('../config.local.php');
        preg_match("@define\('MAX_FILES_IN_DIR', (.*?)\);@smi", $config_local_file_contents, $nMAX_FILES_IN_DIR);
        $nMAX_FILES_IN_DIR = $nMAX_FILES_IN_DIR[1];
        if (empty($nMAX_FILES_IN_DIR)) $nMAX_FILES_IN_DIR = 1000;

        $sql_result = mysql_query("SELECT image_id, image_path FROM " . $DB_TablePrefix . "images WHERE image_path IN (SELECT pic_file_name FROM pics_list_temp GROUP BY pic_file_name) GROUP BY image_id", $link) or die(SendAnswer("Error: " . mysql_error()));
        while ($sql_row = mysql_fetch_array($sql_result)) {
            if (is_file($UploadDirTemp . strtolower($sql_row['image_path']))) {
                $UploadDir_prefix = floor((int)$sql_row['image_id'] / (int)$nMAX_FILES_IN_DIR) . '/';
                $UploadDir = $UploadDirTemp . $UploadDir_prefix;
                if (is_dir($UploadDir) == false) mkdir($UploadDir, 0755, true);

                if (is_file($UploadDir . strtolower($sql_row['image_path'])) == false) { // копируем только если файла нет
                    copy($UploadDirTemp . strtolower($sql_row['image_path']), $UploadDir . strtolower($sql_row['image_path']));
                } else { // копируем только если изменился размер файла
                    $UploadTempFileSize = filesize($UploadDirTemp . strtolower($sql_row['image_path']));
                    $UploadFileSize = filesize($UploadDir . strtolower($sql_row['image_path']));
                    if ($UploadFileSize <> $UploadTempFileSize) {
                        copy($UploadDirTemp . strtolower($sql_row['image_path']), $UploadDir . strtolower($sql_row['image_path']));
                    }
                }

                if ($delete_temp_file == 1) unlink($UploadDirTemp . strtolower($sql_row['image_path']));
            }
        }
    }

    mysql_query("DROP TEMPORARY TABLE IF EXISTS pics_list_temp", $link) or die(SendAnswer("Error: " . mysql_error()));
}


function prestashop_import_pics($DB_TablePrefix, $lang_id)
{
    global $link;

    $delete_temp_pics = 0;
    $UploadDir_my = "../img/my_products_img/";

    if (is_dir($UploadDir_my) == false) die(SendAnswer('Error: Для копирования фотограий, создайте временную папку - ' . $UploadDir_my . ', перепишите файлы выгруженные из прогаммы E-Trade Content Creator в эту папку.'));

    // фото
    $sql_result = mysql_query("SELECT product_id, pic_file1, pic_file2, product_addon_pics, product_name FROM etrade_cc_desc", $link) or die(SendAnswer("Error: " . mysql_error()));


    while ($sql_row = mysql_fetch_array($sql_result)) {

        // ФОТО1
        if (!empty($sql_row['pic_file1']) && is_file($UploadDir_my . strtolower($sql_row['pic_file1']))) {

            $parameters_query = mysql_query("SELECT id_image, id_product FROM " . $DB_TablePrefix . "image WHERE id_product='" . $sql_row['product_id'] . "' AND cover=1 LIMIT 1", $link);
            if ($my_row = mysql_fetch_assoc($parameters_query)) {
                $insert_id = $my_row['id_image'];
            } else {
                mysql_query("INSERT INTO " . $DB_TablePrefix . "image (id_product, position, cover) VALUES(" . $sql_row['product_id'] . ", 1, 1)", $link) or die(SendAnswer("Error: " . mysql_error()));

                $insert_id = mysql_insert_id($link) or die(SendAnswer("Error: " . mysql_error()));

                $sql_lang_result = mysql_query("SELECT id_lang FROM " . $DB_TablePrefix . "lang WHERE active=1", $link) or die(SendAnswer("Error: " . mysql_error()));
                while ($sql_lang_row = mysql_fetch_array($sql_lang_result)) {
                    mysql_query("INSERT INTO " . $DB_TablePrefix . "image_lang (id_image, id_lang, legend) VALUES(" . $insert_id . ", '" . $sql_lang_row['id_lang'] . "', '" . mysql_real_escape_string($sql_row['product_name']) . "')", $link) or die(SendAnswer("Error: " . mysql_error()));
                }

                mysql_query("INSERT INTO " . $DB_TablePrefix . "image_shop (id_image, id_shop, cover) VALUES(" . $insert_id . ", '1', '1')", $link) or die(SendAnswer("Error: " . mysql_error()));
            }

            // создание каталога для хранения фотографий товаров
            $UploadDir = '../img/p/' . PrestaShop_getImgFolderStatic($insert_id);
            if (is_dir($UploadDir) == false) mkdir($UploadDir, 0777, true);
            if (is_dir($UploadDir) == false) die(SendAnswer('Error: ошибка создания директории для хранения фотографий товаров - ' . $UploadDir));

            // Список типов картинок и настройки ресайза
            $image_types = mysql_query("SELECT name, width, height FROM " . $DB_TablePrefix . "image_type WHERE products=1", $link) or die(SendAnswer("Error: " . mysql_error()));

            while ($row1 = mysql_fetch_assoc($image_types)) {
                imageResize($UploadDir_my . strtolower($sql_row['pic_file1']), $UploadDir . (int)$insert_id . '-' . stripslashes($row1['name']) . '.jpg', $row1['width'], $row1['height']);
            }

            // orig file
            copy($UploadDir_my . strtolower($sql_row['pic_file1']), $UploadDir . (int)$insert_id . '.jpg');
        }

        // ФОТО2 - дополнительное
        if (!empty($sql_row['pic_file2']) && is_file($UploadDir_my . strtolower($sql_row['pic_file2']))) {

            $parameters_query = mysql_query("SELECT id_image, id_product FROM " . $DB_TablePrefix . "image WHERE id_product='" . $sql_row['product_id'] . "' AND cover=0 AND position=2 LIMIT 1", $link);
            if ($my_row = mysql_fetch_assoc($parameters_query)) {
                $insert_id = $my_row['id_image'];
            } else {
                mysql_query("INSERT INTO " . $DB_TablePrefix . "image (id_product, position, cover) VALUES(" . $sql_row['product_id'] . ", 2, 0)", $link) or die(SendAnswer("Error: " . mysql_error()));

                $insert_id = mysql_insert_id($link) or die(SendAnswer("Error: " . mysql_error()));

                $sql_lang_result = mysql_query("SELECT id_lang FROM " . $DB_TablePrefix . "lang WHERE active=1", $link) or die(SendAnswer("Error: " . mysql_error()));
                while ($sql_lang_row = mysql_fetch_array($sql_lang_result)) {
                    mysql_query("INSERT INTO " . $DB_TablePrefix . "image_lang (id_image, id_lang, legend) VALUES(" . $insert_id . ", '" . $sql_lang_row['id_lang'] . "', '" . mysql_real_escape_string($sql_row['product_name']) . "')", $link) or die(SendAnswer("Error: " . mysql_error()));
                }

                mysql_query("INSERT INTO " . $DB_TablePrefix . "image_shop (id_image, id_shop, cover) VALUES(" . $insert_id . ", '1', '0')", $link) or die(SendAnswer("Error: " . mysql_error()));
            }

            // создание каталога для хранения фотографий товаров
            $UploadDir = '../img/p/' . PrestaShop_getImgFolderStatic($insert_id);
            if (is_dir($UploadDir) == false) mkdir($UploadDir, 0777, true);
            if (is_dir($UploadDir) == false) die(SendAnswer('Error: ошибка создания директории для хранения фотографий товаров - ' . $UploadDir));

            // Список типов картинок и настройки ресайза
            $image_types = mysql_query("SELECT name, width, height FROM " . $DB_TablePrefix . "image_type WHERE products=1", $link) or die(SendAnswer("Error: " . mysql_error()));

            while ($row1 = mysql_fetch_assoc($image_types)) {
                imageResize($UploadDir_my . strtolower($sql_row['pic_file2']), $UploadDir . (int)$insert_id . '-' . stripslashes($row1['name']) . '.jpg', $row1['width'], $row1['height']);
            }

            // orig file
            copy($UploadDir_my . strtolower($sql_row['pic_file2']), $UploadDir . (int)$insert_id . '.jpg');
        }


        // delete temp pics
        if ($delete_temp_pics == 1) {
            if (is_file($UploadDir_my . strtolower($sql_row['pic_file2']))) {
                unlink($UploadDir_my . strtolower($sql_row['pic_file2']));
            }

            if (is_file($UploadDir_my . strtolower($sql_row['pic_file1']))) {
                unlink($UploadDir_my . strtolower($sql_row['pic_file1']));
            }
        }

        // дополнительные фото для товара
        $product_addon_pics = explode(',', $sql_row['product_addon_pics']);
        $pic_position = 2;

        foreach ($product_addon_pics as $product_addon_pic) {
            $product_addon_pic = strtolower(trim($product_addon_pic));

            if (!empty($product_addon_pic) && is_file($UploadDir_my . $product_addon_pic)) {
                $pic_position++;

                $parameters_query = mysql_query("SELECT id_image, id_product FROM " . $DB_TablePrefix . "image WHERE id_product='" . $sql_row['product_id'] . "' AND cover=0 AND position=" . $pic_position . " LIMIT 1", $link) or die(SendAnswer("Error: " . mysql_error()));

                if ($my_row = mysql_fetch_assoc($parameters_query)) {
                    $insert_id = $my_row['id_image'];
                } else {
                    mysql_query("INSERT INTO " . $DB_TablePrefix . "image (id_product, position, cover) VALUES(" . $sql_row['product_id'] . "," . $pic_position . ", 0)", $link) or die(SendAnswer("Error: " . mysql_error()));

                    $insert_id = mysql_insert_id($link) or die("Invalid query: mysql_insert_id() - " . mysql_error());

                    $sql_lang_result = mysql_query("SELECT id_lang FROM " . $DB_TablePrefix . "lang WHERE active=1", $link) or die(SendAnswer("Error: " . mysql_error()));
                    while ($sql_lang_row = mysql_fetch_array($sql_lang_result)) {
                        mysql_query("INSERT INTO " . $DB_TablePrefix . "image_lang (id_image, id_lang, legend) VALUES(" . $insert_id . ", '" . $sql_lang_row['id_lang'] . "', '" . mysql_real_escape_string($sql_row['product_name']) . "')", $link) or die(SendAnswer("Error: " . mysql_error()));
                    }

                    mysql_query("INSERT INTO " . $DB_TablePrefix . "image_shop (id_image, id_shop, cover) VALUES(" . $insert_id . ", '1', '0')", $link) or die(SendAnswer("Error: " . mysql_error()));
                }

                // создание каталога для хранения фотографий товаров
                $UploadDir = '../img/p/' . PrestaShop_getImgFolderStatic($insert_id);
                if (is_dir($UploadDir) == false) mkdir($UploadDir, 0777, true);
                if (is_dir($UploadDir) == false) die(SendAnswer('Error: ошибка создания директории для хранения фотографий товаров - ' . $UploadDir));

                // Список типов картинок и настройки ресайза
                $image_types = mysql_query("SELECT name, width, height FROM " . $DB_TablePrefix . "image_type WHERE products=1", $link) or die(SendAnswer("Error: " . mysql_error()));

                while ($row1 = mysql_fetch_assoc($image_types)) {
                    imageResize($UploadDir_my . $product_addon_pic, $UploadDir . (int)$insert_id . '-' . stripslashes($row1['name']) . '.jpg', $row1['width'], $row1['height']);
                }

                // orig file
                copy($UploadDir_my . $product_addon_pic, $UploadDir . (int)$insert_id . '.jpg');

                // delete temp pics
                if ($delete_temp_pics == 1) {
                    if (is_file($UploadDir_my . strtolower(trim($product_addon_pic)))) {
                        unlink($UploadDir_my . strtolower(trim($product_addon_pic)));
                    }
                }
            }
        }

    }
}


/** PrestaShop
 * Returns the path to the folder containing the image in the new filesystem
 *
 * @param mixed $id_image
 *
 * @return string path to folder
 */
function PrestaShop_getImgFolderStatic($id_image)
{
    if (!is_numeric($id_image)) return false;

    $folders = str_split((string)$id_image);

    return implode('/', $folders) . '/';
}


// Recommended indexs for Bitrix
// ALTER TABLE `b_iblock_element` ADD INDEX ( `IBLOCK_ID` ); 
// ALTER TABLE `b_iblock_element` ADD INDEX ( `XML_ID` ); 
// ALTER TABLE `b_iblock_element` ADD INDEX ( `ACTIVE` ); 
// ALTER TABLE `b_iblock` ADD INDEX ( `IBLOCK_TYPE_ID` ); 
// ALTER TABLE `b_iblock` ADD INDEX ( `ACTIVE` ); 
// ALTER TABLE `b_iblock` ADD INDEX ( `XML_ID` ); 
// ALTER TABLE `b_file` ADD INDEX ( `FILE_NAME` ); 
// ALTER TABLE `b_iblock_element_property` ADD INDEX ( `IBLOCK_ELEMENT_ID` );
// ALTER TABLE `b_iblock_property` ADD INDEX ( `ACTIVE` );
// ALTER TABLE `b_iblock_property` ADD INDEX ( `NAME` ) 
// ALTER TABLE `b_iblock_section_element` ADD INDEX ( `IBLOCK_SECTION_ID` );
// ALTER TABLE `b_catalog_price` ADD INDEX ( `PRODUCT_ID` );

// Bitrix - Create search content for products
function Bitrix_SearchContent()
{
    // get data from db
    global $link;

    mysql_query("CREATE TABLE IF NOT EXISTS `b_search_content_text` (
	  `SEARCH_CONTENT_ID` int(11) NOT NULL,
	  `SEARCH_CONTENT_MD5` char(32) collate utf8_unicode_ci NOT NULL,
	  `SEARCHABLE_CONTENT` longtext collate utf8_unicode_ci,
	  PRIMARY KEY  (`SEARCH_CONTENT_ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci", $link) or die(SendAnswer("Error: " . mysql_error()));

    //$res_block_list = mysql_query("SELECT b_iblock_element.ID, b_iblock_element.XML_ID as EXTERNAL_ID, b_iblock_element.IBLOCK_SECTION_ID, b_iblock_element.IBLOCK_ID, b_iblock.CODE as IBLOCK_CODE, b_iblock.XML_ID as IBLOCK_EXTERNAL_ID, b_iblock_element.CODE, b_iblock_element.NAME as TITLE FROM b_iblock_element LEFT JOIN b_iblock ON b_iblock_element.IBLOCK_ID=b_iblock.ID WHERE b_iblock.IBLOCK_TYPE_ID='catalog' AND b_iblock.active='Y' AND b_iblock_element.ID NOT IN (SELECT ITEM_ID FROM b_search_content WHERE ITEM_ID>0 AND MODULE_ID='iblock')", $link) or die(SendAnswer("Error: ". mysql_error()));

    mysql_query("DROP TABLE IF EXISTS b_search_content_tmp", $link) or die(SendAnswer("Error: " . mysql_error()));
    mysql_query("CREATE TEMPORARY TABLE b_search_content_tmp (`item_id` int(11) NOT NULL, KEY `item_id` (`item_id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8", $link) or die(SendAnswer("Error: " . mysql_error()));
    mysql_query("INSERT INTO b_search_content_tmp (item_id) SELECT ITEM_ID FROM b_search_content WHERE MODULE_ID='iblock' AND ITEM_ID>0 GROUP BY ITEM_ID", $link) or die(SendAnswer("Error: " . mysql_error()));

    $res_block_list = mysql_query("SELECT b_iblock_element.ID, b_iblock_element.XML_ID as EXTERNAL_ID, b_iblock_element.IBLOCK_SECTION_ID, b_iblock_element.IBLOCK_ID, b_iblock.CODE as IBLOCK_CODE,
		b_iblock.XML_ID as IBLOCK_EXTERNAL_ID, b_iblock_element.CODE, b_iblock_element.NAME as TITLE 
		FROM b_iblock_element 
		LEFT JOIN b_iblock ON b_iblock_element.IBLOCK_ID=b_iblock.ID 
		LEFT JOIN b_search_content_tmp ON b_iblock_element.ID=b_search_content_tmp.item_id 
		WHERE b_iblock.IBLOCK_TYPE_ID='catalog' AND b_iblock.active='Y' AND b_search_content_tmp.item_id IS NULL", $link) or die(SendAnswer("Error: " . mysql_error()));

    while ($arr_block_list = mysql_fetch_assoc($res_block_list)) {
        mysql_query("INSERT INTO b_search_content (DATE_CHANGE, MODULE_ID, ITEM_ID, URL, TITLE, BODY, TAGS, PARAM1, PARAM2) VALUES (now(), 'iblock', " . $arr_block_list["ID"] . ", '=ID=" . $arr_block_list["ID"] . "&EXTERNAL_ID=" . $arr_block_list["EXTERNAL_ID"] . "&IBLOCK_SECTION_ID=" . $arr_block_list["IBLOCK_SECTION_ID"] . "&IBLOCK_TYPE_ID=catalog&IBLOCK_ID=" . $arr_block_list["IBLOCK_ID"] . "&IBLOCK_CODE=" . $arr_block_list["IBLOCK_CODE"] . "&IBLOCK_EXTERNAL_ID=" . $arr_block_list["IBLOCK_EXTERNAL_ID"] . "&CODE=" . $arr_block_list["CODE"] . "', '" . mysql_real_escape_string($arr_block_list["TITLE"]) . "', '', '', 'catalog', " . $arr_block_list["IBLOCK_ID"] . ")", $link) or die(SendAnswer("Error: " . mysql_error()));

        $search_content_id = mysql_insert_id();

        mysql_query("INSERT INTO b_search_content_right (SEARCH_CONTENT_ID, GROUP_CODE) VALUES(" . $search_content_id . ", 'G1') ON DUPLICATE KEY UPDATE GROUP_CODE='G1'", $link) or die(SendAnswer("Error: " . mysql_error()));
        mysql_query("INSERT INTO b_search_content_right (SEARCH_CONTENT_ID, GROUP_CODE) VALUES(" . $search_content_id . ", 'G2') ON DUPLICATE KEY UPDATE GROUP_CODE='G2'", $link) or die(SendAnswer("Error: " . mysql_error()));
        mysql_query("INSERT INTO b_search_content_site (SEARCH_CONTENT_ID, SITE_ID, URL) VALUES (" . $search_content_id . ", 's1', '') ON DUPLICATE KEY UPDATE SITE_ID='s1'", $link) or die(SendAnswer("Error: " . mysql_error()));
        mysql_query("INSERT INTO b_search_content_title (SEARCH_CONTENT_ID, SITE_ID, POS, WORD) VALUES (" . $search_content_id . ", 's1', 0, '" . mysql_real_escape_string($arr_block_list["TITLE"]) . "') ON DUPLICATE KEY UPDATE SITE_ID='s1'", $link) or die(SendAnswer("Error: " . mysql_error()));
        mysql_query("INSERT INTO b_search_content_stem (SEARCH_CONTENT_ID, LANGUAGE_ID, STEM, TF) VALUES (" . $search_content_id . ", 'ru', 235, 0.2314) ON DUPLICATE KEY UPDATE LANGUAGE_ID='ru'", $link) or die(SendAnswer("Error: " . mysql_error()));
        mysql_query("INSERT INTO b_search_content_text (SEARCH_CONTENT_ID, SEARCH_CONTENT_MD5, SEARCHABLE_CONTENT) VALUES (" . $search_content_id . ", md5('" . mysql_real_escape_string($arr_block_list["TITLE"]) . "'), '" . mysql_real_escape_string($arr_block_list["TITLE"]) . "\r\n\r\n') ON DUPLICATE KEY UPDATE SEARCHABLE_CONTENT='" . mysql_real_escape_string($arr_block_list["TITLE"]) . "\r\n\r\n'", $link) or die(SendAnswer("Error: " . mysql_error()));
    }

    mysql_query("DROP TABLE IF EXISTS b_search_content_tmp", $link) or die(SendAnswer("Error: " . mysql_error()));
}

// Bitrix - Create new block on HDD
function Bitrix_CreateNewBlock()
{
    if (!is_file('./iBlockTemplate.dat')) exit;


    // get data from db
    global $link;

    mysql_query("CREATE TABLE IF NOT EXISTS `b_search_content_text` (
	  `SEARCH_CONTENT_ID` int(11) NOT NULL,
	  `SEARCH_CONTENT_MD5` char(32) collate utf8_unicode_ci NOT NULL,
	  `SEARCHABLE_CONTENT` longtext collate utf8_unicode_ci,
	  PRIMARY KEY  (`SEARCH_CONTENT_ID`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci", $link) or die(SendAnswer("Error: " . mysql_error()));

    $res_block_list = mysql_query("SELECT id, code, name FROM b_iblock WHERE iblock_type_id='catalog' AND active='Y'", $link) or die(SendAnswer("Error: " . mysql_error()));

    while ($arr_block_list = mysql_fetch_assoc($res_block_list)) {
        $iBlockID = $arr_block_list["id"];
        $iBlockCode = $arr_block_list["code"];
        $iBlockName = $arr_block_list["name"];

        $dest_dir = '../catalog/' . $iBlockCode . '/';

        if (is_dir($dest_dir) == false) { // Create temp dir
            mkdir($dest_dir, 0755, true);

            // block template
            if (is_dir($dest_dir) == true) {
                $iBlockTemplate = file_get_contents('./iBlockTemplate.dat');
                $iBlockTemplate = str_replace('{BlockTitle}', $iBlockName, $iBlockTemplate);
                $iBlockTemplate = str_replace('{BlockID}', $iBlockID, $iBlockTemplate);
                $iBlockTemplate = str_replace('{BlockFolderName}', $iBlockCode, $iBlockTemplate);

                file_put_contents($dest_dir . 'index.php', $iBlockTemplate);

                mysql_query("INSERT INTO b_search_content (DATE_CHANGE, MODULE_ID, ITEM_ID, URL, TITLE, BODY, TAGS, PARAM1, PARAM2) VALUES (now(), 'main', 's1|/catalog/" . $iBlockCode . "/index.php', '/catalog/" . $iBlockCode . "/index.php', '" . mysql_real_escape_string($iBlockName) . "', '', '', '', '') ON DUPLICATE KEY UPDATE DATE_CHANGE=now()", $link) or die(SendAnswer("Error: " . mysql_error()));
                $search_content_id = mysql_insert_id();

                mysql_query("INSERT INTO b_search_content_right (SEARCH_CONTENT_ID, GROUP_CODE) VALUES(" . $search_content_id . ", 'G1') ON DUPLICATE KEY UPDATE GROUP_CODE='G1'", $link) or die(SendAnswer("Error: " . mysql_error()));
                mysql_query("INSERT INTO b_search_content_right (SEARCH_CONTENT_ID, GROUP_CODE) VALUES(" . $search_content_id . ", 'G2') ON DUPLICATE KEY UPDATE GROUP_CODE='G2'", $link) or die(SendAnswer("Error: " . mysql_error()));
                mysql_query("INSERT INTO b_search_content_site (SEARCH_CONTENT_ID, SITE_ID, URL) VALUES (" . $search_content_id . ", 's1', '') ON DUPLICATE KEY UPDATE SITE_ID='s1'", $link) or die(SendAnswer("Error: " . mysql_error()));
                mysql_query("INSERT INTO b_search_content_title (SEARCH_CONTENT_ID, SITE_ID, POS, WORD) VALUES (" . $search_content_id . ", 's1', 0, '" . mysql_real_escape_string($iBlockName) . "') ON DUPLICATE KEY UPDATE SITE_ID='s1'", $link) or die(SendAnswer("Error: " . mysql_error()));
                mysql_query("INSERT INTO b_search_content_stem (SEARCH_CONTENT_ID, LANGUAGE_ID, STEM, TF) VALUES (" . $search_content_id . ", 'ru', 235, 0.2314) ON DUPLICATE KEY UPDATE LANGUAGE_ID='ru'", $link) or die(SendAnswer("Error: " . mysql_error()));
                mysql_query("INSERT INTO b_search_content_text (SEARCH_CONTENT_ID, SEARCH_CONTENT_MD5, SEARCHABLE_CONTENT) VALUES (" . $search_content_id . ", md5('" . mysql_real_escape_string($iBlockName) . "'), '" . mysql_real_escape_string($iBlockName) . "\r\n\r\n') ON DUPLICATE KEY UPDATE SEARCHABLE_CONTENT='" . mysql_real_escape_string($iBlockName) . "\r\n\r\n'", $link) or die(SendAnswer("Error: " . mysql_error()));

            }
        }
    }

    // delete cache
    remove_files('../bitrix/managed_cache/MYSQL/b_iblock', $delete_root = false, $pattern = '');
    remove_files('../bitrix/managed_cache/MYSQL/b_iblock_type', $delete_root = false, $pattern = '');
}

// Bitrix - Block ReSort sections
function Bitrix_Block_ReSort($iblockIDs)
{
    if (!empty($iblockIDs)) {
        $pos1 = stripos($iblockIDs, ",");

        if ($pos1 === false) { // one block ID only
            Bitrix_ReSort($iblockIDs, 0, 0, 0, "Y");
        } else { // many blocks ID exist
            $my_iblockIDs = explode(",", $iblockIDs);
            foreach ($my_iblockIDs as $iblockID) {
                Bitrix_ReSort($iblockID, 0, 0, 0, "Y");
            }
        }
    }

    // processing NULL data for new blocks (for new cats)
    global $link;
    $res_empty_blocks = mysql_query("SELECT iblock_id FROM b_iblock_section WHERE iblock_id>0 AND (left_margin IS NULL OR right_margin IS NULL OR depth_level IS NULL)  GROUP BY iblock_id", $link) or die(SendAnswer("Error: " . mysql_error()));
    while ($arr_blocks_info = mysql_fetch_assoc($res_empty_blocks)) {
        Bitrix_ReSort($arr_blocks_info["iblock_id"], 0, 0, 0, "Y");
    }

    // Create new block on HDD
    Bitrix_CreateNewBlock();
}

// Bitrix - ReSort sections
function Bitrix_ReSort($iblockID, $id = 0, $cnt = 0, $depth = 0, $active = "Y")
{

    global $link;
    $iblockID = IntVal($iblockID);

    if ($id > 0)
        mysql_query(
            "UPDATE b_iblock_section SET " .
            "	TIMESTAMP_X = TIMESTAMP_X, " .
            "	RIGHT_MARGIN = " . IntVal($cnt) . ", " .
            "	LEFT_MARGIN = " . IntVal($cnt) . " " .
            "WHERE ID=" . IntVal($id), $link) or die(SendAnswer("Error: " . mysql_error()));

    $strSql =
        "SELECT BS.ID, BS.ACTIVE " .
        "FROM b_iblock_section BS " .
        "WHERE BS.IBLOCK_ID = " . $iblockID . " " .
        "	AND " . (($id > 0) ? "BS.IBLOCK_SECTION_ID = " . IntVal($id) : "BS.IBLOCK_SECTION_ID IS NULL") . " " .
        "ORDER BY BS.SORT, BS.NAME ";

    $cnt++;
    $res = mysql_query($strSql, $link) or die(SendAnswer("Error: " . mysql_error()));
    while ($arr = mysql_fetch_assoc($res))
        $cnt = Bitrix_ReSort($iblockID, $arr["ID"], $cnt, $depth + 1, (($active == "Y" && $arr["ACTIVE"] == "Y") ? "Y" : "N"));

    if ($id == 0)
        return true;

    mysql_query(
        "UPDATE b_iblock_section SET " .
        "	TIMESTAMP_X = TIMESTAMP_X, " .
        "	RIGHT_MARGIN = " . IntVal($cnt) . ", " .
        "	DEPTH_LEVEL = " . IntVal($depth) . ", " .
        "	GLOBAL_ACTIVE = '" . $active . "' " .
        "WHERE ID=" . IntVal($id), $link) or die(SendAnswer("Error: " . mysql_error()));

    return $cnt + 1;
}


// AmiroCMS - Meta tags (обновление мета тегов)
function AmiroCMS_meta_tags($DB_TablePrefix)
{
    $activate_update_meta_tags = 0; // 0 - выключено, 1 - включено

    if ($activate_update_meta_tags == 0) exit;

    global $link;
    $sql_result = mysql_query("SELECT tov_id, head_title, head_desc, head_keywords FROM etrade_products WHERE head_title<>'' or head_desc<>'' or head_keywords<>''", $link) or die(SendAnswer("Error: " . mysql_error()));

    while ($sql_row = mysql_fetch_array($sql_result)) {
        unset($meta);
        unset($meta_data);

        $meta['title'] = $sql_row['head_title'];
        $meta['keywords'] = $sql_row['head_keywords'];
        $meta['description'] = $sql_row['head_desc'];

        $meta_data = serialize($meta);

        mysql_query("UPDATE " . $DB_TablePrefix . "es_items SET sm_data='" . mysql_real_escape_string($meta_data) . "' WHERE id=" . $sql_row['tov_id'], $link) or die(SendAnswer("Error: " . mysql_error()));

    }
}


function spichka_import_pics($DB_TablePrefix, $TableSource)
{
// for del
}

/* function spichka_import_pics($DB_TablePrefix, $TableSource) {

	global $link;
	
	//  проверка индексов в таблицах
	$index_query=mysql_query("SHOW INDEX FROM ".$DB_TablePrefix."product_image WHERE key_name = 'product_id'") or die("Invalid query: " . mysql_error());
	if (mysql_num_rows($index_query)==0) {
		mysql_query("ALTER TABLE ".$DB_TablePrefix."product_image ADD INDEX (product_id)") or die("Invalid query: " . mysql_error());
	}
	
	$index_query=mysql_query("SHOW INDEX FROM ".$DB_TablePrefix."product_image WHERE key_name = 'image'") or die("Invalid query: " . mysql_error());
	if (mysql_num_rows($index_query)==0) {
		mysql_query("ALTER TABLE ".$DB_TablePrefix."product_image ADD INDEX (image)") or die("Invalid query: " . mysql_error());
	}
	
	mysql_query("CREATE TEMPORARY TABLE addon_pics_temp ( `product_id` int(11) NOT NULL, `pic_file_name` varchar(120) NOT NULL, KEY `product_id` (`product_id`),  KEY `pic_file_name` (`pic_file_name`)) ENGINE=MyISAM DEFAULT CHARSET=utf8", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	if ($TableSource=='etrade_cc_desc' or $TableSource=='') {
		$sql_result = mysql_query("SELECT product_id, pic_file1, pic_file2, product_addon_pics FROM etrade_cc_desc", $link) or die(SendAnswer("Error: ". mysql_error()));
	} else {
		$sql_result = mysql_query("SELECT field_value1 as product_id, field_value2 as pic_file1, field_value3 as pic_file2, field_value4 as product_addon_pics FROM etrade_cc_filters WHERE row_type='pics'", $link) or die(SendAnswer("Error: ". mysql_error()));
	}

	// формируем список фотографий
	while ($sql_row = mysql_fetch_array($sql_result)) {
		mysql_query("INSERT INTO addon_pics_temp (product_id, pic_file_name) VALUES (".$sql_row['product_id'].", '".strtolower(trim($sql_row['pic_file2']))."')", $link) or die(SendAnswer("Error: ". mysql_error()));
		
		if (empty($sql_row['product_addon_pics'])) continue;
		
		$product_addon_pics=explode(',', $sql_row['product_addon_pics']);
		
		foreach ($product_addon_pics as $product_addon_pic) {
			mysql_query("INSERT INTO addon_pics_temp (product_id, pic_file_name) VALUES (".$sql_row['product_id'].", '".strtolower(trim($product_addon_pic))."')", $link) or die(SendAnswer("Error: ". mysql_error()));
		}
	}

	// удаляем старые фотографии
	mysql_query("DELETE ".$DB_TablePrefix."product_image FROM addon_pics_temp JOIN ".$DB_TablePrefix."product_image ON ".$DB_TablePrefix."product_image.product_id = addon_pics_temp.product_id", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	// добавляем новые 
	mysql_query("INSERT INTO ".$DB_TablePrefix."product_image (product_id, image) SELECT product_id, CONCAT('data/products/', addon_pics_temp.pic_file_name) as addon_pic FROM addon_pics_temp WHERE CONCAT('data/products/', addon_pics_temp.pic_file_name) NOT IN (SELECT image FROM ".$DB_TablePrefix."product_image)", $link) or die(SendAnswer("Error: ". mysql_error()));
				
	mysql_query("DROP TEMPORARY TABLE IF EXISTS addon_pics_temp", $link) or die(SendAnswer("Error: ". mysql_error()));
} */

// delete cache
function spichka_delete_cache()
{
    remove_files('../system/cache/', $delete_root = false, $pattern = '');
}


function ShopScriptWA_import_pics($DB_TablePrefix, $TableSource)
{

    global $link;

    //  проверка индексов в таблицах
    $index_query = mysql_query("SHOW INDEX FROM " . $DB_TablePrefix . "SC_product_pictures WHERE key_name = 'priority'") or die("Invalid query: " . mysql_error());
    if (mysql_num_rows($index_query) == 0) {
        mysql_query("ALTER TABLE " . $DB_TablePrefix . "SC_product_pictures ADD INDEX (priority)") or die("Invalid query: " . mysql_error());
    }

    $sql_result = mysql_query("SELECT tov_id, pic_small, pic_medium, pic_big, pic_order, picID, tov_name, tov_guid FROM etrade_cc_pics_flat", $link) or die(SendAnswer("Error: " . mysql_error()));

    // удаляем старые фотографии
    mysql_query("DELETE " . $DB_TablePrefix . "SC_product_pictures FROM etrade_cc_pics_flat JOIN " . $DB_TablePrefix . "SC_product_pictures ON " . $DB_TablePrefix . "SC_product_pictures.productID = etrade_cc_pics_flat.tov_id", $link) or die(SendAnswer("Error: " . mysql_error()));

    // добавляем новые
    mysql_query("INSERT INTO " . $DB_TablePrefix . "SC_product_pictures (productID, filename, thumbnail, enlarged, priority) SELECT tov_id, pic_medium, pic_small, pic_big, (pic_order-1) as priority FROM etrade_cc_pics_flat", $link) or die(SendAnswer("Error: " . mysql_error()));

    mysql_query("UPDATE etrade_cc_pics_flat, SC_product_pictures SET etrade_cc_pics_flat.picID=SC_product_pictures.photoID WHERE etrade_cc_pics_flat.tov_id=SC_product_pictures.productID AND SC_product_pictures.priority=0", $link) or die(SendAnswer("Error: " . mysql_error()));

    mysql_query("UPDATE SC_products, etrade_cc_pics_flat SET SC_products.default_picture=etrade_cc_pics_flat.picID WHERE SC_products.productID=etrade_cc_pics_flat.tov_id AND etrade_cc_pics_flat.pic_order=1", $link) or die(SendAnswer("Error: " . mysql_error()));
}

/* function ShopScriptWA_import_pics($DB_TablePrefix, $TableSource) {

	global $link;

	//  проверка индексов в таблицах
	$index_query=mysql_query("SHOW INDEX FROM ".$DB_TablePrefix."SC_product_pictures WHERE key_name = 'priority'") or die("Invalid query: " . mysql_error());
	if (mysql_num_rows($index_query)==0) {
		mysql_query("ALTER TABLE ".$DB_TablePrefix."SC_product_pictures ADD INDEX (priority)") or die("Invalid query: " . mysql_error());
	}
	
	mysql_query("CREATE TEMPORARY TABLE addon_pics_temp (product_id int(11) NOT NULL, priority int(11) NOT NULL, photoID_new int(11) NOT NULL, filename varchar(240) NOT NULL, thumbnail varchar(240) NOT NULL, enlarged varchar(240) NOT NULL, KEY `product_id` (`product_id`),  KEY `filename` (`filename`), KEY `photoID_new` (`photoID_new`)) ENGINE=MyISAM DEFAULT CHARSET=utf8", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	if ($TableSource=='etrade_cc_desc' or $TableSource=='') {
		$sql_result = mysql_query("SELECT product_id, pic_file1, pic_file2, product_addon_pics FROM etrade_cc_desc", $link) or die(SendAnswer("Error: ". mysql_error()));
	} else {
		$sql_result = mysql_query("SELECT field_value1 as product_id, field_value2 as pic_file1, field_value3 as pic_file2, field_value4 as product_addon_pics FROM etrade_cc_filters WHERE row_type='pics'", $link) or die(SendAnswer("Error: ". mysql_error()));
	}

	// формируем список фотографий
	while ($sql_row = mysql_fetch_array($sql_result)) {
 		// фото №1
		$priority_num=0;
		$file_extension=get_file_extension($sql_row['pic_file1']);
		$pic_thumbnail=strtolower(trim(str_ireplace('.'.$file_extension, '_1.'.$file_extension, $sql_row['pic_file1'])));
		$pic_medium=strtolower(trim(str_ireplace('.'.$file_extension, '_2.'.$file_extension, $sql_row['pic_file1'])));
		$pic_enlarged=strtolower(trim(str_ireplace('.'.$file_extension, '_3.'.$file_extension, $sql_row['pic_file1'])));
		
		mysql_query("INSERT INTO addon_pics_temp (product_id, priority, filename, thumbnail, enlarged) VALUES (".$sql_row['product_id'].", ".$priority_num.", '".$pic_medium."', '".$pic_thumbnail."', '".$pic_enlarged."')", $link) or die(SendAnswer("Error: ". mysql_error()));
		
		// фото №2
		$priority_num=1;
		$file_extension=get_file_extension($sql_row['pic_file2']);
		$pic_thumbnail=strtolower(trim(str_ireplace('.'.$file_extension, '_1.'.$file_extension, $sql_row['pic_file2'])));
		$pic_medium=strtolower(trim(str_ireplace('.'.$file_extension, '_2.'.$file_extension, $sql_row['pic_file2'])));
		$pic_enlarged=strtolower(trim(str_ireplace('.'.$file_extension, '_3.'.$file_extension, $sql_row['pic_file2'])));
		
		if (!empty($pic_thumbnail)) {
			mysql_query("INSERT INTO addon_pics_temp (product_id, priority, filename, thumbnail, enlarged) VALUES (".$sql_row['product_id'].", ".$priority_num.", '".$pic_medium."', '".$pic_thumbnail."', '".$pic_enlarged."')", $link) or die(SendAnswer("Error: ". mysql_error())); 
		}
		
		// фото №х
		if (empty($sql_row['product_addon_pics'])) continue; // нет доп. фото
		
		$priority_num=0;
		$product_addon_pics=explode(',', $sql_row['product_addon_pics']);
		
		foreach ($product_addon_pics as $product_addon_pic) {
			if (empty($product_addon_pic)) continue; // нет доп. фото

			$file_extension=get_file_extension($product_addon_pic);
			$pic_thumbnail=strtolower(trim(str_ireplace('.'.$file_extension, '_1.'.$file_extension, $product_addon_pic)));
			$pic_medium=strtolower(trim(str_ireplace('.'.$file_extension, '_2.'.$file_extension, $product_addon_pic)));
			$pic_enlarged=strtolower(trim(str_ireplace('.'.$file_extension, '_3.'.$file_extension, $product_addon_pic)));
			
			mysql_query("INSERT INTO addon_pics_temp (product_id, priority, filename, thumbnail, enlarged) VALUES (".$sql_row['product_id'].", ".$priority_num.", '".$pic_medium."', '".$pic_thumbnail."', '".$pic_enlarged."')", $link) or die(SendAnswer("Error: ". mysql_error()));
			
			$priority_num++;
		}
	}

	// удаляем старые фотографии
	mysql_query("DELETE ".$DB_TablePrefix."SC_product_pictures FROM addon_pics_temp JOIN ".$DB_TablePrefix."SC_product_pictures ON ".$DB_TablePrefix."SC_product_pictures.productID = addon_pics_temp.product_id", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	// добавляем новые 
	mysql_query("INSERT INTO ".$DB_TablePrefix."SC_product_pictures (productID, filename, thumbnail, enlarged, priority) SELECT product_id, addon_pics_temp.filename, addon_pics_temp.thumbnail, addon_pics_temp.enlarged, addon_pics_temp.priority FROM addon_pics_temp", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	mysql_query("UPDATE addon_pics_temp, SC_product_pictures SET addon_pics_temp.photoID_new=SC_product_pictures.photoID WHERE addon_pics_temp.product_id=SC_product_pictures.productID AND SC_product_pictures.priority=0", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	mysql_query("UPDATE SC_products, addon_pics_temp SET SC_products.default_picture=addon_pics_temp.photoID_new WHERE SC_products.productID=addon_pics_temp.product_id AND addon_pics_temp.priority=0", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	mysql_query("DROP TEMPORARY TABLE IF EXISTS addon_pics_temp", $link) or die(SendAnswer("Error: ". mysql_error()));
} */


// UMI.CMS - hierarchy_relations
function umicms_hierarchy_relations_upd()
{
    global $link;

    // cats
    mysql_query("DELETE cms3_hierarchy_relations FROM etrade_cats JOIN cms3_hierarchy_relations ON etrade_cats.uc_hier_parent_id=cms3_hierarchy_relations.rel_id", $link) or die(SendAnswer("Error: " . mysql_error()));
    mysql_query("DELETE cms3_hierarchy_relations FROM etrade_cats JOIN cms3_hierarchy_relations ON etrade_cats.uc_hier_id=cms3_hierarchy_relations.child_id", $link) or die(SendAnswer("Error: " . mysql_error()));

    $sql_result = mysql_query("SELECT uc_obj_id, uc_hier_id, uc_obj_type_id FROM etrade_cats WHERE uc_hier_id>0", $link) or die(SendAnswer("Error: " . mysql_error()));
    while ($sql_row = mysql_fetch_array($sql_result)) {

        umicms_makeHierarchyRelationsTable($sql_row['uc_hier_id']);

        if ((int)$sql_row['uc_obj_id'] > 0) { // create new default fields for new cats
            mysql_query("INSERT INTO cms3_object_content (obj_id, field_id)
				SELECT " . $sql_row['uc_obj_id'] . " as uc_obj_id, cms3_fields_controller.field_id
				FROM cms3_fields_controller 
				INNER JOIN cms3_object_field_groups ON cms3_fields_controller.group_id=cms3_object_field_groups.id 
				WHERE cms3_fields_controller.group_id IN (SELECT id FROM cms3_object_field_groups WHERE type_id =(SELECT id FROM cms3_object_types WHERE guid='catalog-category' GROUP BY guid)) AND 
				" . $sql_row['uc_obj_id'] . " NOT IN (SELECT cms3_object_content.obj_id FROM cms3_object_content WHERE cms3_object_content.field_id=cms3_fields_controller.field_id)
				GROUP BY cms3_fields_controller.field_id", $link) or die(SendAnswer("Error: " . mysql_error()));
        }

        if ((int)$sql_row['uc_obj_type_id'] > 0) { // create new field groups
            mysql_query("INSERT INTO cms3_object_field_groups (type_id, name, title, is_active, is_visible, ord, is_locked)
				SELECT " . $sql_row['uc_obj_type_id'] . " as type_id, name, title, is_active, is_visible, ord, is_locked
				FROM (SELECT name, title, is_active, is_visible, ord, is_locked FROM cms3_object_field_groups WHERE type_id=(SELECT id FROM cms3_object_types WHERE guid='catalog-object' GROUP BY guid)) as t2 
				WHERE " . $sql_row['uc_obj_type_id'] . " NOT IN (SELECT type_id FROM cms3_object_field_groups GROUP BY type_id)", $link) or die(SendAnswer("Error: " . mysql_error()));

            mysql_query("DROP TABLE IF EXISTS cms3_fields_controller_tmp", $link) or die(SendAnswer("Error: " . mysql_error()));
            mysql_query("CREATE TEMPORARY TABLE cms3_fields_controller_tmp ( `field_id` int(11) NOT NULL, `ord` int(11) NOT NULL, `group_id` int(11) NOT NULL, `new_group_id` int(11) NOT NULL, `name` VARCHAR(120) NOT NULL,  KEY `field_id` (`field_id`),  KEY `group_id` (`group_id`),  KEY `name` (`name`)) ENGINE=MyISAM DEFAULT CHARSET=utf8", $link) or die(SendAnswer("Error: " . mysql_error()));
            mysql_query("INSERT INTO cms3_fields_controller_tmp (field_id, ord, group_id, new_group_id, name)
			SELECT cms3_fields_controller.field_id, cms3_fields_controller.ord, cms3_fields_controller.group_id, 000000 as new_group_id, cms3_object_field_groups.name 
			FROM cms3_fields_controller 
			INNER JOIN cms3_object_field_groups ON cms3_fields_controller.group_id=cms3_object_field_groups.id 
			WHERE group_id IN (SELECT id FROM cms3_object_field_groups WHERE type_id =(SELECT id FROM cms3_object_types WHERE guid='catalog-object' GROUP BY guid)) 
			ORDER BY cms3_object_field_groups.name", $link) or die(SendAnswer("Error: " . mysql_error()));
            mysql_query("UPDATE cms3_fields_controller_tmp, cms3_object_field_groups SET new_group_id=cms3_object_field_groups.id WHERE cms3_fields_controller_tmp.name=cms3_object_field_groups.name AND cms3_object_field_groups.type_id=" . $sql_row['uc_obj_type_id'], $link) or die(SendAnswer("Error: " . mysql_error()));
            mysql_query("INSERT INTO cms3_fields_controller (ord, field_id, group_id)
			SELECT ord, field_id, new_group_id 
			FROM cms3_fields_controller_tmp 
			WHERE new_group_id NOT IN (SELECT cms3_fields_controller.group_id FROM cms3_fields_controller WHERE cms3_fields_controller.field_id=cms3_fields_controller_tmp.field_id)", $link) or die(SendAnswer("Error: " . mysql_error()));


            // CREATE TEMPORARY TABLE cms3_fields_controller_tmp ( `field_id` int(11) NOT NULL, `ord` int(11) NOT NULL, `group_id` int(11) NOT NULL, `new_group_id` int(11) NOT NULL, `name` VARCHAR(120) NOT NULL,  KEY `field_id` (`field_id`),  KEY `group_id` (`group_id`),  KEY `name` (`name`)) ENGINE=MyISAM DEFAULT CHARSET=utf8;
            // INSERT INTO cms3_fields_controller_tmp (field_id, ord, group_id, new_group_id, name)
            // SELECT cms3_fields_controller.field_id, cms3_fields_controller.ord, cms3_fields_controller.group_id, 000000 as new_group_id, cms3_object_field_groups.name
            // FROM cms3_fields_controller
            // INNER JOIN cms3_object_field_groups ON cms3_fields_controller.group_id=cms3_object_field_groups.id
            // WHERE group_id IN (SELECT id FROM cms3_object_field_groups WHERE type_id =(SELECT id FROM cms3_object_types WHERE guid='catalog-object' GROUP BY guid))
            // ORDER BY cms3_object_field_groups.name;
            // UPDATE cms3_fields_controller_tmp, cms3_object_field_groups SET new_group_id=cms3_object_field_groups.id WHERE cms3_fields_controller_tmp.name=cms3_object_field_groups.name AND cms3_object_field_groups.type_id=261;
            // INSERT INTO cms3_fields_controller (ord, field_id, group_id)
            // SELECT ord, field_id, new_group_id
            // FROM cms3_fields_controller_tmp
            // WHERE new_group_id NOT IN (SELECT cms3_fields_controller.group_id FROM cms3_fields_controller WHERE cms3_fields_controller.field_id=cms3_fields_controller_tmp.field_id);

            //SELECT * FROM cms3_fields_controller_tmp;

        }
    }

    // products
    mysql_query("DELETE cms3_hierarchy_relations FROM etrade_products JOIN cms3_hierarchy_relations ON etrade_products.uc_hier_id=cms3_hierarchy_relations.child_id", $link) or die(SendAnswer("Error: " . mysql_error()));

    $sql_result = mysql_query("SELECT uc_hier_id, uc_obj_id FROM etrade_products WHERE uc_hier_id>0", $link) or die(SendAnswer("Error: " . mysql_error()));
    while ($sql_row = mysql_fetch_array($sql_result)) {
        umicms_makeHierarchyRelationsTable($sql_row['uc_hier_id']);

        if ((int)$sql_row['uc_obj_id'] > 0) { // create new default fields for new products
            mysql_query("INSERT INTO cms3_object_content (obj_id, field_id)
				SELECT " . $sql_row['uc_obj_id'] . " as uc_obj_id, cms3_fields_controller.field_id
				FROM cms3_fields_controller 
				INNER JOIN cms3_object_field_groups ON cms3_fields_controller.group_id=cms3_object_field_groups.id 
				WHERE cms3_fields_controller.group_id IN (SELECT id FROM cms3_object_field_groups WHERE type_id =(SELECT id FROM cms3_object_types WHERE guid='catalog-object' GROUP BY guid)) AND 
				" . $sql_row['uc_obj_id'] . " NOT IN (SELECT cms3_object_content.obj_id FROM cms3_object_content WHERE cms3_object_content.field_id=cms3_fields_controller.field_id)
				GROUP BY cms3_fields_controller.field_id", $link) or die(SendAnswer("Error: " . mysql_error()));
        }
    }


}

function umicms_makeHierarchyRelationsTable($id)
{
    global $link;

    $parents = umicms_getAllParents($id);

    $level = sizeof($parents);

    //First-level for every element required
    mysql_query("INSERT INTO cms3_hierarchy_relations (rel_id, child_id, level) VALUES (NULL, " . $id . ", " . $level . ")", $link) or die(SendAnswer("Error: " . mysql_error()));

    foreach ($parents as $parent_id) {
        mysql_query("INSERT INTO cms3_hierarchy_relations (rel_id, child_id, level) VALUES (" . $parent_id . ", " . $id . ", " . $level . ")", $link) or die(SendAnswer("Error: " . mysql_error()));
    }
}

function umicms_getAllParents($id)
{
    global $link;

    $parents = Array();

    while ($id) {
        $result = mysql_query("SELECT rel FROM cms3_hierarchy WHERE id = " . $id, $link) or die(SendAnswer("Error: " . mysql_error()));

        if (mysql_num_rows($result)) {
            list($id) = mysql_fetch_row($result);

            if (!$id) continue;
            if (in_array($id, $parents)) break;    //Infinity recursion

            $parents[] = $id;
        } else {
            return false;
        }
    }

    return array_reverse($parents);
}


function opencart_import_pics($DB_TablePrefix, $TableSource)
{
// for del
}

/* function opencart_import_pics($DB_TablePrefix, $TableSource) {

	global $link;

	//  проверка индексов в таблицах
	$index_query=mysql_query("SHOW INDEX FROM ".$DB_TablePrefix."product_image WHERE key_name = 'product_id'") or die("Invalid query: " . mysql_error());
	if (mysql_num_rows($index_query)==0) {
		mysql_query("ALTER TABLE ".$DB_TablePrefix."product_image ADD INDEX (product_id)") or die("Invalid query: " . mysql_error());
	}
	
	$index_query=mysql_query("SHOW INDEX FROM ".$DB_TablePrefix."product_image WHERE key_name = 'image'") or die("Invalid query: " . mysql_error());
	if (mysql_num_rows($index_query)==0) {
		mysql_query("ALTER TABLE ".$DB_TablePrefix."product_image ADD INDEX (image)") or die("Invalid query: " . mysql_error());
	}
	
	mysql_query("CREATE TEMPORARY TABLE addon_pics_temp ( `product_id` int(11) NOT NULL, `pic_file_name` varchar(240) NOT NULL, KEY `product_id` (`product_id`),  KEY `pic_file_name` (`pic_file_name`)) ENGINE=MyISAM DEFAULT CHARSET=utf8", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	if ($TableSource=='etrade_cc_desc' or $TableSource=='') {
		$sql_result = mysql_query("SELECT product_id, pic_file1, pic_file2, product_addon_pics FROM etrade_cc_desc", $link) or die(SendAnswer("Error: ". mysql_error()));
	} else {
		$sql_result = mysql_query("SELECT field_value1 as product_id, field_value2 as pic_file1, field_value3 as pic_file2, field_value4 as product_addon_pics FROM etrade_cc_filters WHERE row_type='pics'", $link) or die(SendAnswer("Error: ". mysql_error()));
	}

	// формируем список фотографий
	while ($sql_row = mysql_fetch_array($sql_result)) {
		//if (!empty($sql_row['pic_file1'])) {
			//mysql_query("INSERT INTO addon_pics_temp (product_id, pic_file_name) VALUES (".$sql_row['product_id'].", '".strtolower(trim($sql_row['pic_file1']))."')", $link) or die(SendAnswer("Error: ". mysql_error()));
		//}
		
		if (!empty($sql_row['pic_file2'])) {  // фото №2
			if (strtolower(trim($sql_row['pic_file1']))<>strtolower(trim($sql_row['pic_file2']))) { // если разные фото №1 и фото №2
				mysql_query("INSERT INTO addon_pics_temp (product_id, pic_file_name) VALUES (".$sql_row['product_id'].", 'data/products/".strtolower(trim($sql_row['pic_file2']))."')", $link) or die(SendAnswer("Error: ". mysql_error()));
			}
		}
		
		if (empty($sql_row['product_addon_pics'])) continue;
		
		$product_addon_pics=explode(',', $sql_row['product_addon_pics']);
		
		foreach ($product_addon_pics as $product_addon_pic) {
			mysql_query("INSERT INTO addon_pics_temp (product_id, pic_file_name) VALUES (".$sql_row['product_id'].", 'data/products/".strtolower(trim($product_addon_pic))."')", $link) or die(SendAnswer("Error: ". mysql_error()));
		}
	}

	// удаляем старые фотографии
	mysql_query("DELETE ".$DB_TablePrefix."product_image FROM addon_pics_temp JOIN ".$DB_TablePrefix."product_image ON ".$DB_TablePrefix."product_image.product_id = addon_pics_temp.product_id", $link) or die(SendAnswer("Error: ". mysql_error()));
	
	// добавляем новые 
	mysql_query("INSERT INTO ".$DB_TablePrefix."product_image (product_id, image) SELECT product_id, addon_pics_temp.pic_file_name FROM addon_pics_temp WHERE pic_file_name NOT IN (SELECT image FROM ".$DB_TablePrefix."product_image)", $link) or die(SendAnswer("Error: ". mysql_error()));
				
	mysql_query("DROP TEMPORARY TABLE IF EXISTS addon_pics_temp", $link) or die(SendAnswer("Error: ". mysql_error()));
} */

// delete cache
function opencart_delete_cache()
{
    remove_files('../system/cache/', $delete_root = false, $pattern = '');
}

?>