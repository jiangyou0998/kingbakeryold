function confInsertSubmit() {

    if (!validate(document.getElementById('txtNo'))) {
        alert('「起始編號」請輸入數字');
        return false;
    }
    if (!validate(document.getElementById('txtNumber'))) {
        alert('「張數」請輸入數字');
        return false;
    }
    if (document.getElementById('selShop').selectedIndex == 0) {
        alert('請提供「分店」選項');
        return false;
    }
    var bool_Reset = true;
    $.ajax({ async: false, type: "post", url: "card_main.php",data: "submit=ajax&no=" + document.getElementById('txtNo').value + "&number=" + document.getElementById('txtNumber').value + "",
        success: function (data) {
            if (data == 0) {

                bool_Reset = false;
            }
        },
        error: function () {
            bool_Reset = false;
        }
    });
    if (bool_Reset == false) {
        alert('編號有重複');
        return false;
    }


}
function validate(obj) {
    var reg = new RegExp("^[0-9]*$");
    if (!reg.test(obj.value)) {
        return false;
    } else {
        return true;
    }
}

function updateButtonClick(o) {
    if (document.getElementById('txtName' + o.id).value == '') {
        document.getElementById('updateCheck').value = 0;
        alert('請輸入姓名!');
    } else {
        document.getElementById('updateID').value = o.id;
        document.getElementById('updateName').value = document.getElementById('txtName' + o.id).value;
        document.getElementById('updateEmployeeId').value = document.getElementById('txtEmployeeId' + o.id).value;
        document.getElementById('updateCashier').value = document.getElementById('cbcashier' + o.id).checked ? 1 : 0;
        document.getElementById('updateTitle').value = document.getElementById('selTitle' + o.id).value;

        document.getElementById('updateCheck').value = 1;

    }
}

function confUpdateSubmit() {
    return document.getElementById('updateCheck').value == 1 ? true : false;
}
function sendUpdateSubmit() {
    return document.getElementById('updateIds').value == 0 ? false : true;
}
function checkbox() {
    var ids = "";
    $("input[type=checkbox]").each(function () {
        var id = $(this).attr("value");
        if ($(this).attr("checked") == true && $(this).attr("name") == 'card') {
            ids += id + ',';
        }
    });
    if (ids != '') {
        ids = ids.substring(0, ids.length - 1);
        $("#updateIds").val(ids);
    } else {
        $("#updateIds").val('0');
    }
}