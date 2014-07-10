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
    attributesObj: {
        value: {},
        writable: true,
        configurable: false
    },
    indexAttribute: {
        value: null,
        writable: true,
        configurable: false
    },
    attributesValues: {
        value: [],
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
    unsetBrand: {
        value: function (brand_id) {
            this.brandsArr.splice(this.brandsArr.indexOf(brand_id), 1);
        },
        enumerable: false
    },
    unsetAttribute: {
        value: function (keyAttributes, valKey) {
            if (!(this.attributesObj[keyAttributes].value.length > 1)) {
                delete this.attributesObj[keyAttributes];
                this.attributesValues = [];

                return;
            }

            this.attributesObj[keyAttributes].value.splice(this.attributesObj[keyAttributes].value.indexOf(valKey), 1);
        },
        enumerable: false
    }
});

/**
 * Object value for getter setter attributes
 */
Object.defineProperty(objectValueSelection, "attributes_id", {
    set: function (attribute) {
        this.attributesObj[this.indexAttribute] = attribute;
        this.attributesObj[this.indexAttribute].value = this.attributesValues;
    },
    get: function () {
        return this.attributesObj;
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
 * Define attributes value
 */
Object.defineProperty(objectValueSelection, "attrValues", {
    set: function (value) {
        this.attributesValues.push(value);
    },
    get: function () {
        return this.attributesValues;
    }
});