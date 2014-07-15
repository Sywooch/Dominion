/**
 * Created by kostya on 7/15/14.
 */
var buildUrl = {
    mainUrl: "",
    mergeUrl: function (brands) {
        if (brands.length > 0) this.mainUrl += "br/" + "b" + brands.join("b");

        return this.mainUrl;
    }
};