function checkID(id) {
    tab = "ABCDEFGHJKLMNPQRSTUVXYWZIO";
    A1 = new Array(1,1,1,1,1,1,1,1,1,1,2,2,2,2,2,2,2,2,2,2,3,3,3,3,3,3);
    A2 = new Array(0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5,6,7,8,9,0,1,2,3,4,5);
    Mx = new Array(9,8,7,6,5,4,3,2,1,1);
    if (id.length != 10) {
        return false
    }
    i = tab.indexOf(id.charAt(0));
    if (i == -1) {
        return false
    }
    sum = A1[i] + A2[i] * 9;
    for (i = 1; i < 10; i++) {
        v = parseInt(id.charAt(i));
        if (isNaN(v)) {
            return false
        }
        sum = sum + v * Mx[i]
    }
    if (sum % 10 != 0) {
        return false
    }
    return true
}
function checkEmpty(str, re, ne) {
    if (str == "") {
        return ne + re + "\n"
    } else {
        return ne + ""
    }
}
function checkPhone(str, re, ne) {
    var cellphone = /^09[0-9]{8}$/;
	if ( !cellphone.test( str ) ) {
		return ne + re + "\n"
	}
    return ne + ""
}
function checkBirthday(str, re, ne) {
    if (str.length != 7)
        return ne + re + '\n';
    for (i = 0; i < 7; i++) {
        v = parseInt(str.charAt(i));
        if (isNaN(v))
            return ne + re + '\n';
    }
    return ne + '';
}
