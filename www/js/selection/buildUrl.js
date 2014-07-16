/**
 * Created by kostya on 7/15/14.
 */
var buildUrl = {
    brands: "",
    attributes: "",
    attributes_range: "",
    prices: "",
    mergeUrl: function (brands, attributes, price_min, price_max, status_brand) {
        if (brands.length > 0) buildUrl.brands = "br/" + "b" + brands.join("b") + "/";

        if ($.makeArray(attributes).length > 0) {
            $.each(attributes, function (index, value) {
                if (value["is_range"] == 1) {
                    buildUrl.attributes_range += "a" + index + "v" + value["value"]["from"] + "-" + value["value"]["to"];
                } else {
                    buildUrl.attributes += "a" + index + "v" + value["value"].join("a" + index + "v");
                }
            });

            if (buildUrl.attributes.length > 0) buildUrl.attributes = "at/" + this.attributes + "/";
            if (buildUrl.attributes_range.length > 0) buildUrl.attributes_range = "ar/" + this.attributes_range + "/";
        }

        if (price_min.toString().length > 0 && price_max.toString().length > 0) {
            buildUrl.prices = "pmin/" + price_min + "/pmax/" + price_max + "/";
        }

        return buildUrl.brands + buildUrl.attributes + buildUrl.attributes_range + buildUrl.prices + "stb/" + status_brand;
    },
    clearUrl: function () {
        buildUrl.brands = "";
        buildUrl.attributes = "";
        buildUrl.attributes_range = "";
        buildUrl.prices = "";
    }
};