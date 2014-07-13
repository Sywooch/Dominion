/**
 * Created by Константин on 12.07.14.
 */
var servicesSelection = {
        brands: function (brandsId) {
            var brandsSelector = "";
            $.each(brandsId, function (index, value) {
                brandsSelector += "input[rel=attr_brand_id][value=" + value["key"] + "], ";
            });

            return brandsSelector.substring(0, brandsSelector.length - 2);
        },
        attributes: function (attributesId) {
            var attributesCheck = "";
            var attributesRange = [];
            $.each(attributesId, function (index, value) {

//                    if (objectValueSelection.attributesIdChecked == value["key"]) return;

                    if (value["int_value"]["buckets"].length > 0) {
                        $.each(value.int_value.buckets, function (key, valAttr) {
                            attributesCheck += "input[rel=attr_value][atid=" + valAttr["key"] + "][atg=" + value["key"] + "], ";
                        });
                    } else {
//                        var objectRange = {
//                            selectorFrom: "input[name^=attr_range_min][xid=" + value["key"] + "]",
//                            selectorTo: "input[name^=attr_range_max][xid=" + value["key"] + "]",
//                            valueFrom: value.range_value.max_value.value,
//                            valueTo: value.range_value.min_value.value
//                        };
                        var objectRange = {
                            selectorRange: "div.fieldgroup[xid=" + value["key"] + "] div.attr_range_view",
                            valueFrom: value.range_value.max_value.value,
                            valueTo: value.range_value.min_value.value
                        };

                        attributesRange.push(objectRange);
                    }
                }
            );


            return {check: attributesCheck.substring(0, attributesCheck.length - 2), range: attributesRange};
        }
    }
    ;