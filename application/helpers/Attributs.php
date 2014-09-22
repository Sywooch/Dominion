<?php

class Helpers_Attributs extends App_Controller_Helper_HelperAbstract
{

    public function getMinMaxPrice($catalogue_id)
    {
        $_catalogue_id = $this->params['Catalogue']->getChildren($catalogue_id);
        $_catalogue_id[count($_catalogue_id)] = $catalogue_id;

        $_item_id = $this->params['Item']->getMinMaxPrice($_catalogue_id);
    }

    public function getAttributs($catalogue_id, $at, $active_attrib)
    {
        $attr = $this->work_model->getAttributes($catalogue_id, 'ATTR_CATALOG_VIS');
        $formatRangeAttributes = array();
        if (!empty($attr)) {
            foreach ($attr as $view) {
                $this->domXml->create_element('attr_cat', '', 2);
                $this->domXml->set_attribute(array('id' => $view['ATTRIBUT_ID']
                , 'is_range_view' => $view['IS_RANGE_VIEW'],
                    "expand" => $view["EXPAND"]
                ));

                $this->domXml->create_element('name', $view['NAME']);
                if (!empty($view['U_NAME'])) $this->domXml->create_element('uname', $view['U_NAME']);

                if ($view['IS_RANGE_VIEW'] == 1) {
                    $formatRangeAttributes[$view['ATTRIBUT_ID']]["min"] = $active_attrib[$view['ATTRIBUT_ID']]["min"];
                    $formatRangeAttributes[$view['ATTRIBUT_ID']]["max"] = $active_attrib[$view['ATTRIBUT_ID']]["max"];

                    $this->getAttributValuesRange($catalogue_id, $view['ATTRIBUT_ID'], $at, $active_attrib);
                } else {
                    $this->getAttributValues($catalogue_id, $view['ATTRIBUT_ID'], $at, $active_attrib);
                }

                $this->domXml->go_to_parent();
            }
        }

        $this->domXml->create_element("attr_range_value_json", json_encode(Format_ConvertDataElasticSelection::getAttributesLine($formatRangeAttributes)));
        $this->domXml->create_element("attr_active_value_json", json_encode($at));
    }

    private function getAttributValues($catalogue_id, $attribut_id, $at, $active_attrib)
    {
        $_catalogue_id = $this->params['Catalogue']->getChildren($catalogue_id);
        $_catalogue_id[count($_catalogue_id)] = $catalogue_id;

        $_item_id = $this->params['Item']->getCatalogItemsID($_catalogue_id);

        $attr = $this->work_model->getDopparam($attribut_id, $_item_id);

        if (!empty($attr)) {
            foreach ($attr as $val) {
                $selected = 0;
                $is_disabled = 0;

                foreach ($at as $attrValue) {
                    if ($attrValue["is_range"] || !in_array($val["id"], $attrValue["value"])) continue;

                    $selected = 1;
                }

                if (!empty($active_attrib[$attribut_id])) {
                    if (!in_array($val['id'], $active_attrib[$attribut_id])) $is_disabled = 1;
                }

                $this->domXml->create_element('attr_value', '', 2);
                $this->domXml->set_attribute(array('id' => $val['id']
                , 'parent_id' => $attribut_id
                , 'selected' => $selected
                , 'is_disabled' => $is_disabled
                ));

                $this->domXml->create_element('name', $val['val']);
                $this->domXml->go_to_parent();
            }
        }
    }

    private function getAttributValuesRange($catalogue_id, $attribut_id, $at, $active_attrib)
    {
        $_catalogue_id = $this->params['Catalogue']->getChildren($catalogue_id);
        $_catalogue_id[count($_catalogue_id)] = $catalogue_id;

        $_item_id = $this->params['Item']->getCatalogItemsID($_catalogue_id);

        $attr = $this->work_model->getDopparam($attribut_id, $_item_id);

        $min_val = 1000000;
        $max_val = 0;

        $min_key = null;
        $max_key = null;
        $result_attr = array();
        $unitName = null;
        foreach ($attr as $key => $val) {
            if (preg_match('/\d{1,}/', $val['val'], $out)) {
                if (empty($unitName)) {
                    preg_match_all("/(?:\d+\s)(.+)?/", $val["val"], $resultUnit);
                    $unitName = $resultUnit[1][0];
                }
                $_val = (double)$val['val'];
                if ($_val > $max_val) {
                    $max_val = $_val;
                    $max_key = $key;
                }

                if ($_val < $min_val) {
                    $min_val = $_val;
                    $min_key = $key;
                }
            }
        }

        if (!is_null($min_key)) {
            $result_attr[] = $attr[$min_key];
        }

        if (!is_null($max_key)) {
            $result_attr[] = $attr[$max_key];
        }

        $selected = 0;
        $is_disabled = 0;

        if (!empty($at)) {
            foreach ($at as $value) {
                if ($attribut_id != $value["id"]) continue;

                $selected = 1;

                break;
            }
        }

        $this->domXml->create_element('attr_value', '', 2);
        $this->domXml->set_attribute(array(
                'parent_id' => $attribut_id
            , 'selected' => $selected
            , 'is_disabled' => $is_disabled
            , "unit_name" => $unitName
            )
        );

        $this->domXml->go_to_parent();
    }

}