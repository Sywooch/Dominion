<?php
/**
* Импортер атрибутов
* 
* Syomik Dmitriy <dsemik@gmail.com>
*/
class AttributImport
{
    /**
    * Модель БД
    * 
    * @var AttributImportModel
    */
    private $_db;

    /**
    * Массив ошибок
    * 
    * @var array
    */
    private $_errors = array();

    /**
    * Результирующий HTML код
    * 
    * @var mixed
    */
    private $_html='';

    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $_itemId = null;

    /**
    * put your comment there...
    * 
    * @var mixed
    */
    private $_catalogueId = null;

    /**
    * Конструктор
    * 
    * @param AttributImportModel $db
    * 
    * @return AttributImport
    */
    public function __construct(AttributImportModel $db)
    {
        $this->_db = $db;
    }

    /**
    * Запуск всего сущего
    * 
    */
    public function run($post) 
    {
        $catalogId = !empty($post['catalog_id']) ? $post['catalog_id']:0;
        $items = $this->_db->getItems($catalogId);

        if (!empty($items)) {
            foreach ($items as $view) {
                $this->processItem($view);

                $this->_db->updateItem($view['ITEM_ID']);
            }
        }

        $this->getAttTableRow($catalogId);

        $result = '';
        if (strlen($this->_html) > 0) {
            $result.='<tr bgcolor="#F0F0F0"><td colspan="7"><a href="#" class="applay_all" style="color: black; font-weight: bold; font-size: 14px;">Применить к выделенным</a></td></tr>';
            $result.='<tr bgcolor="#FFFFFF">'
            .'<td><input type="checkbox" onclick="return SelectAll(this.form,checked,\'id[]\');"></td>'
            .'<td><b>Имя атрибута</b></td>'
            .'<td><b>Группа атрибутов</b></td>'
            .'<td><b>Группировка атрибутов</b></td>'
            .'<td><b>Тип атрибута</b></td>'
            .'<td><b>Ед. изм.</b></td>'
            .'<td>Действия</td></tr>';
            $result.= $this->_html;
            $result.='<tr bgcolor="#F0F0F0"><td colspan="7"><a href="#" class="applay_all" style="color: black; font-weight: bold; font-size: 14px;">Применить к выделенным</a></td></tr>';
        } else {
            $result.='<tr bgcolor="#FFFFFF">'
            .'<td>Не найдено записей по выбранному условию</td></tr>';
        }

        $jsonResult['html'] = $result;
        $jsonResult['errors'] = '';
        if (count($this->_errors) > 0) {
            $jsonResult['errors'] = '<p>Произошла ошибка. Проверте следующее:</p><ul><li>'.implode('</li><li>', $this->_errors).'</li></ul>';
        }

        echo json_encode($jsonResult);
    }

    /**
    * Старт обновления атрибутов и перенос данных по 
    * соотвествующим таблицам
    * 
    * @param mixed $post
    */
    public function create($post)
    {
        $data = json_decode(stripslashes($post['data']));
        $attrId = array();
        if (!empty($data)) {
            foreach ($data as $val) {
                $_attrId = (int) $val->attrId;
                $attrId[] = $_attrId;
                $this->finishCreate($val);
                $this->_db->deleteTempAttrValue($_attrId);
            }
        } else {
            $this->_errors[] = 'Данные не указаны';
        }
        $jsonResult['status'] = 0;
        $jsonResult['errors'] = '';
        $jsonResult['id'] = $attrId;
        if (count($this->_errors) > 0) {
            $jsonResult['status'] = 1;
            $jsonResult['errors'] = '<p>Произошла ошибка. Проверте следующее:</p><ul><li>'.implode('</li><li>', $this->_errors).'</li></ul>';
        }

        echo json_encode($jsonResult);
    }
    
    public function lookAttrVal($post)
    {
        $html = '<p>Произошла ошибка или данные для данного тарибута не найдены.</p>';
        $attrId = !empty($post['attrId']) ? $post['attrId']:0;
        if (!empty($attrId)) {
            $attrName = $this->_db->getAttributeName($attrId);
            $html = 'Таблица возможных данных для атрибуа <h3>'.$attrName.'</h3>';
            $html.= '<table cellspacing="1" cellpadding="5" border="0" bgcolor="#CCCCCC" class="l">'
                    .'<tr bgcolor="#FFFFFF">'
                    .'<td><b>Товар</b></td>'
                    .'<td><b>Значение</b></td></tr>';
            $itemData = $this->_db->getTempItemsAttrValues($attrId);
            if (!empty($itemData)) {
                foreach ($itemData as $view) {
                    $html.= '<tr bgcolor="#FFFFFF">'
                            .'<td>'.$view['NAME'].'</td>'
                            .'<td>'.$view['VALUE'].'</td></tr>';
                }
            }
            $html.= '</table>';
        }

        echo $html;
    }

    /**
    * Обработка товара
    * 
    * @param array $item
    */
    private function processItem($item)
    {
        $item['CC_XML'] = strip_tags($item['CC_XML'],'<block><param><name><value>');
        $xml_text = explode('</param><block>', $item['CC_XML']);
        $xml_text = implode('</param></block><block>', $xml_text).'</block>';

        $config = array('output-xml' => true, 'input-xml' => true);
        $xml_text = tidy_repair_string($xml_text, $config, 'utf8');
        $xml_text = '<?xml version="1.0" encoding="utf-8"?><body>'.$xml_text.'</body>';
//        file_put_contents('_test.xml', $xml_text);

        $xml = simplexml_load_string($xml_text);
        $attrGroupId = $this->getAttrGroupId($item['CATALOGUE_ID']);

        $this->_itemId = $item['ITEM_ID'];
        $this->_catalogueId = $item['CATALOGUE_ID'];
        if (!empty($xml)) {
            foreach ($xml as $value) {
                $vAttGrpName =  trim((string) $value->name);
                $viewAttrGroupId = $this->_db->getViewAttrGroupId($vAttGrpName);
                $this->setAttributs($value->param, $viewAttrGroupId, $attrGroupId);
            }
        }
    }

    /**
    * put your comment there...
    * 
    * @param mixed $params          атрибуты
    * @param mixed $viewAttrGroupId ID группы атрибутов VIEW 
    * @param mixed $attrGroupId     ID группы атрибутов
    * 
    * @return nyll
    */
    private function setAttributs($params, $viewAttrGroupId, $attrGroupId)
    {
        if (!empty($params)) {
            foreach ($params as $attr) {
                $name = trim((string) $attr->name);
                $value = trim((string) $attr->value);

                $_attrData = $this->_db->getAttrId($name, $attrGroupId);
                $attrId = !empty($_attrData[0]) ? $_attrData[0]:0;
                $attrType = !empty($_attrData[1]) ? $_attrData[1]:null;
//                var_dump($_attrData, $attrId, $attrType);

                $attrData['name'] = $name; 
                $attrData['value'] = $value;
                $attrData['type'] = $attrType;
                $attrData['attrId'] = $attrId;
                if (empty($attrId) || !$attrType) {
                    $attrData['viewAttrGroupId'] =$viewAttrGroupId; 
                    $attrData['attrGroupId'] =$attrGroupId;

                    $this->addAttribut($attrData);
                } else {
                    $attrData['attrId'] = $attrId;
                    $attrData['itemId'] = $this->_itemId;
                    $this->editAttribut($attrData);
                }
            }
        }
    }

    /**
    * Получить ID группы атрибутов
    * 
    * @param mixed $catalogueId
    */
    private function getAttrGroupId($catalogueId)
    {
        $catName = $this->_db->getCatalogueName($catalogueId);
        if (!empty($catName)) {
            return $this->_db->getAttrGroupId($catName);
        }
        $this->_errors[] = 'Ошибка инициализации группы атрибутов '.$catName;

        return null;
    }

    /**
    * Добавить новый атрибут
    * 
    * @param array $attrData
    */
    private function addAttribut($attrData)
    {
        $attrInsertData['ATTRIBUT_GROUP_ID'] = $attrData['attrGroupId'];
        $attrInsertData['NAME'] = $attrData['name'];
        $attrInsertData['VIEW_ATTRIBUT_GROUP_ID'] = $attrData['viewAttrGroupId'];
        if (!empty($attrData['attrId']) && !$attrData['type']) {
            $attrId = $attrData['attrId'];
        } else {
            $attrId = $this->_db->insertAttr($attrInsertData);
        }

        if (!empty($attrId)) {
            $attrItemTemp['ITEM_ID'] = $this->_itemId;
            $attrItemTemp['ATTRIBUT_ID'] = $attrId;
            $attrItemTemp['VALUE'] = $attrData['value'];

            $this->_db->insertItemTemp($attrItemTemp);

            $attrCatLink['CATALOGUE_ID'] = $this->_catalogueId;
            $attrCatLink['ATTRIBUT_ID'] = $attrId;

            $this->_db->insertAttrCatalogLink($attrCatLink);
        } else {
            $this->_errors[] = 'Ошибка добавления атрибута '.$attrData['name'];
        }
    }

    private function getAttTableRow($catalogId)
    {
        $attributs = $this->_db->getNotTypeAttr($catalogId);
        if (!empty($attributs)) {
            $V_STR_TYPE = $this->getTypeSelect();
            $V_STR_UNIT_ID = $this->_db->Spravotchnik(0,'select UNIT_ID,NAME from UNIT  order by NAME');

            foreach ($attributs as $view) {
                $V_STR_ATTRIBUT_GROUP_ID = $this->_db->Spravotchnik($view['ATTRIBUT_GROUP_ID'],'select ATTRIBUT_GROUP_ID,NAME from ATTRIBUT_GROUP  order by NAME');
                $V_STR_VIEW_ATTRIBUT_GROUP_ID = $this->_db->Spravotchnik($view['VIEW_ATTRIBUT_GROUP_ID'],'select VIEW_ATTRIBUT_GROUP_ID,NAME from VIEW_ATTRIBUT_GROUP  order by NAME');

                $this->_html.='<tr bgcolor="#FFFFFF" id="tr'.$view['ATTRIBUT_ID'].'">'
                .'<td><input type="checkbox" value="'.$view['ATTRIBUT_ID'].'" name="id[]" class="id-checkbox"></td>'
                .'<td>'.$view['NAME'].'</td>'
                .'<td><select name="ATTRIBUT_GROUP_ID">'.$V_STR_ATTRIBUT_GROUP_ID.'</select></td>'
                .'<td><select name="VIEW_ATTRIBUT_GROUP_ID">'.$V_STR_VIEW_ATTRIBUT_GROUP_ID.'</select></td>'
                .'<td><select name="TYPE">'.$V_STR_TYPE.'</select></td>'
                .'<td><select name="UNIT_ID">'.$V_STR_UNIT_ID.'</select></td>'
                .'<td><a href="#" class="look_attr_val" title="Значения атрибута" xid="'.$view['ATTRIBUT_ID'].'"><img src="img/i2_3.gif" alt="Значения атрибута" border="0"/></a>
                    <a href="#" class="applay_attr" title="Применить"><img src="img/main_i_plus.gif" alt="Применить" border="0"/></a></td></tr>';
            }
        }
    }

    /**
    * Редактирование существующего атрибута
    * 
    * @param mixed $attrData
    */
    private function editAttribut($attrData)
    {
        $attrInfo = $this->_db->getAttributInfo($attrData['attrId']);
        if (!empty($attrInfo)) {
            if ($attrInfo[0]['TYPE'] ==3 || $attrInfo[0]['TYPE'] == 4) {
                $attrData['value'] = $this->_db->getAttrListId($attrData['attrId'], $attrData['value']);
            }
            $itemAtTable = $this->getItemAttrTable($attrInfo[0]['TYPE']);

            if (!empty($itemAtTable)) {
                $this->_db->editAttr($itemAtTable, $attrData);
            }
        }
    }

    /**
    * Получить имя таблицы по типу атрибута
    * 
    * @param int $type
    * 
    * @return string
    */
    private function getItemAttrTable($type)
    {
        $itemAtTable = '';
        switch ($type) {
            case 0:
            case 3:
            case 4:
            case 5:
            case 6:
                $itemAtTable = 'ITEM0';
                break;
            case 1:
                $itemAtTable = 'ITEM1';
                break;
            case 2:
                $itemAtTable = 'ITEM2';
                break;
            case 7:
                $itemAtTable = 'ITEM7';
                break;
        }

        return $itemAtTable;
    }

    /**
    * put your comment there...
    * 
    * @return string
    */
    private function getTypeSelect()
    {
        return '<option value="0">Целое число</option>
                <option value="1">Дробь</option>
                <option value="2">Строка</option>
                <option value="3">Список</option>
                <option value="4">Список. с карт.</option>
                <option value="5">чекбокс</option>
                <option value="6">чекбокс с тремя состояниями (да,нет,не знаю)</option>
                <option value="7">краткое описание(64 к)</option>';
    }
    
    /**
    * put your comment there...
    * 
    * @param mixed $attr
    */
    private function finishCreate($attr)
    {
        $updateAttr['attrId'] = (int) $attr->attrId;
        $updateAttr['attrGrpId'] = (int) $attr->attrGrpId;
        $updateAttr['viewAttrGrpId'] = (int) $attr->viewAttrGrpId;
        $updateAttr['type'] = (int) $attr->type;
        $updateAttr['unitId'] = (int) $attr->unitId;

        $this->_db->updateAttribut($updateAttr);

        $itemValues = $this->_db->getItemTempData($updateAttr['attrId']);
        if (!empty($itemValues)) {
            $itemAtTable = $this->getItemAttrTable($updateAttr['type']);
            foreach ($itemValues as $value) {
                switch ($updateAttr['type']) {
                    case 3:
                    case 4:
                        $value['VALUE'] = $this->_db->getAttrListId($updateAttr['attrId'], $value['VALUE']);
                        break;
                }

                $this->_db->insertItemAttr($itemAtTable, $value);
            }
        } 
    }
}