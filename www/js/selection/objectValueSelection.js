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
    attributesArr: {
        value: {},
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

            this.attributesArr[attributeId].value.push(attributeValue);
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

            this.attributesArr[keyAttributes].value.splice(this.attributesArr[keyAttributes].value.indexOf(valKey), 1);
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