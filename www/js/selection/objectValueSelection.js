/**
 * Created by Константин on 09.07.14.
 */
var objectValueSelection = {};

/**
 * Init object value selection
 */
Object.defineProperties(objectValueSelection, {
    catalogue_id: {
        value: null,
        writable: true,
        configurable: false
    },
    attributesIdChecked: {
        value: null,
        writable: true,
        configurable: false
    },
    attributesRangeIdActive: {
        value: null,
        writable: true,
        configurable: false
    },
    attributesArr: {
        value: {},
        writable: true,
        configurable: false
    },
    priceRangeChecked: {
        value: 0,
        writable: true,
        configurable: false
    },
    attributesObj: {
        value: {},
        writable: true,
        configurable: false
    },
    brandsArr: {
        value: [],
        writable: true,
        configurable: false
    },
    price_min: {
        value: null,
        writable: true,
        configurable: false
    },
    price_max: {
        value: null,
        writable: true,
        configurable: false
    },
    setAttributeArr: {
        value: function (attributeId, is_range, attributeValue) {
            if (!(attributeId in this.attributesArr)) {
                this.attributesArr[attributeId] = {
                    id: attributeId,
                    is_range: is_range
                };
                this.attributesArr[attributeId].value = [];
            }

            if (typeof attributeValue == "Object") {
                this.attributesArr[attributeId].value = attributeValue;
            }
            else if (Array.isArray(attributeValue)) {
                this.attributesArr[attributeId].value = attributeValue;
            }
            else {
                this.attributesArr[attributeId].value.push(attributeValue);
            }
        }
    },
    checkBrands: {
        value: 0,
        writable: true,
        configurable: false
    },
    setAttributeObj: {
        value: function (attributeId, is_range, from, to) {
            if (!(attributeId in this.attributesObj)) {
                this.attributesObj[attributeId] = {
                    id: attributeId,
                    is_range: is_range
                };
            }

            this.attributesObj[attributeId].value = {
                from: from,
                to: to
            };
        }
    },
    unsetBrand: {
        value: function (brand_id) {
            this.brandsArr.splice(this.brandsArr.indexOf(brand_id), 1);
        },
        enumerable: false
    },
    unsetAttributeArr: {
        value: function (keyAttributes, valKey) {
            if (!(this.attributesArr[keyAttributes].value.length > 1)) {
                delete this.attributesArr[keyAttributes];

                return;
            }

            this.attributesArr[keyAttributes].value.splice(this.attributesArr[keyAttributes].value.indexOf(parseInt(valKey)), 1);
        },
        enumerable: false
    },
    isEmpty: {
        value: function () {

            return Object.keys(this.attributes_id).length == 0 && this.price_min == null && this.price_max == null;
        },
        enumerable: false
    }
});

/**
 * Object value for getter setter brands
 */
Object.defineProperty(objectValueSelection, "brands_id", {
    set: function (brand_id) {
        this.brandsArr.push(brand_id);
    },
    get: function () {
        return this.brandsArr;
    }
});

/**
 * Define property of attributes main
 */
Object.defineProperty(objectValueSelection, "attributes_id", {
    get: function () {
        return $.extend({}, this.attributesArr, this.attributesObj);
    }
});

/**
 * Define check brands
 */
Object.defineProperty(objectValueSelection, "check_brands", {
    get: function () {
        var check = this.checkBrands;
        this.checkBrands = 0;

        return check;
    }
});

Object.defineProperty(objectValueSelection, "attribute_id_checked", {
    get: function () {
        var check = this.attributesIdChecked;
        this.attributesIdChecked = null;

        return check;
    }
});

/**
 * Define attribute id range active
 */
Object.defineProperty(objectValueSelection, "attribute_id_range_active", {
    get: function () {
        var check = this.attributesRangeIdActive;
        this.attributesRangeIdActive = null;

        return check;
    },
    set: function (attributeId) {
        this.attributesRangeIdActive = attributeId;
    }
});

/**
 * Define price range check
 */
Object.defineProperty(objectValueSelection, "price_range_check", {
    get: function () {
        var check = this.priceRangeChecked;
        this.priceRangeChecked = 0;

        return check;
    },
    set: function (priceRangeCheck) {
        this.priceRangeChecked = priceRangeCheck;
    }
});