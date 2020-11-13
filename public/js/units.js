function checkPhoneTel(value) {
    var isPhone = /^([0-9]{3,4}-)?[0-9]{7,8}$/;
    var isMob = /^((\+?86)|(\(\+86\)))?(1[0-9]{10})$/;
    value = value.trim();

    if (isMob.test(value) || isPhone.test(value)) {
        return true;
    } else {
        return false;
    }
}

function keyupMobile(value) {
    value = value.trim();
    value = value.replace(/[^0-9|\-]/g, '');
    if (value.length > 11) {
        value = value.substr(0, 11);
    }
    var isMob = /^((\+?86)|(\(\+86\)))?(1[0-9]{10})$/;
    if (isMob.test(value)) {
        return value;
    } else {
        if(value.length == 11){
            return ''
        }
        return value;
    }
}

function keyupPhoneTel(value, maxLength) {
    value = value.trim();
    value = value.replace(/[^0-9|\-]/g, '');
    if (maxLength === undefined) {
        maxLength = 13;//
    }
    if (value.length > maxLength) {
        value = value.substr(0, maxLength);
    }

    return value;
}

function keyupNumber(value, digit, max) {
    value = value.trim();
    if(max === undefined){
        max = 99999999;
    }
    value = value.replace(/[^0-9]{\.}[0-9{2}]/g, '');
    console.log(value);
    if (max !== undefined && value > max) {
        value = max
    }
    if (isNaN(value)) {
        value = ''
    }
    if (digit !== undefined && value && typeof value =="string") {
        console.log(value);
        var ret = value.split('.')
        if (ret[1] !== undefined && ret[1].length > digit) {
            var num = new Number(value);
            value = num.toFixed(digit);
        }
    }

    return value
}
