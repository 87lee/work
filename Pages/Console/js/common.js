//@ sourceURL=common.js
/*发布通用函数*/
function releaseOpt(options){
    options = $.extend({
        "model": true,
        "type": true,
        "group": '/group/nameLists',
    }, options || {});

    options.model && $('#chooseModel > input').on('change', function(){   //全型号和自定义
        $(this).prop('checked');
        var val = $(this).val();
        if(val == "ALL"){
            $('#model').val(val);
            $('#model').parent().hide();
        }else{
            $('#model').val('');
            $('#model').parent().show();
        }
    });

    options.type && $('#chooseType > input').on('change', function(){     //内测、灰度、公开
        $(this).prop('checked');
        var val = $(this).val();
        if(val == "group"){
            $('#group').parent().show();
            $('#countNum').parent().hide();
        }else if(val == "AB"){
            $('#countNum').parent().show();
            $('#group').parent().hide();
        }else if(val == "ALL"){
            $('#group').parent().hide();
            $('#countNum').parent().hide();
        }
    });

    options.group && AjaxGet(options.group, function(data){     //内测组
        var arr = data.groups;
        var con = '';
        var $select = $('#group');
        for( var i=0; i<arr.length; i++ ){
            con += '<option value="'+arr[i].group_id+'">'+arr[i].group_name+'</option>';
        }
        $select.html(con);
    });

    if(options.version){        //版本
        AjaxGet('/monitor/versionLists', function (data){
            var arr = data.content;
            var con = '';
            var $select = $('#version');

            arr = descSort(arr);

            for( var i=0; i<arr.length; i++ ){
                con += '<option value="'+arr[i].version+'">'+arr[i].version+'</option>';
                $select.data('ver'+arr[i].version, arr[i].time);
            }
            $select.html(con).trigger('change');
        });

        $('#version').on('change', function(){    //从版本更新时间
            var val = $(this).val();
            var time = $(this).data('ver'+val);
            var $verTime = $('#verTime');
            $verTime.val(time);
        });
    }
}

function releaseTool(del){      //监听发布和下架
    listenToolbar('release', releaseTableInfo);
    listenToolbar('under', underTableInfo);

    function releaseTableInfo(){
        if(myData.id){
            if(myData.model == 'ALL'){
                $('#chooseModel').find('input').eq(0).prop('checked', true).trigger('change');
            }else{
                $('#chooseModel').find('input').eq(1).prop('checked', true).trigger('change');
                $('#model').val(myData.model);
            }
            if(myData.type == 'ALL'){
                $('#chooseType').find('input').eq(0).prop('checked', true).trigger('change');
            }else if(myData.type == 'AB'){
                $('#chooseType').find('input').eq(2).prop('checked', true).trigger('change');
            }else if(myData.type == 'group'){
                $('#chooseType').find('input').eq(1).prop('checked', true).trigger('change');
            }
            $('#vendorID').val(myData.vendorID);
        }else{
            $('#model').val('');
            $('#vendorID').val('');
            $('#countNum').val('');
            $('#chooseType').find('input').eq(0).prop('checked', true).trigger('change');
            $('#chooseModel').find('input').eq(1).prop('checked', true).trigger('change');
        }
        $('#myModal').modal('show');

        if($("#version").get(0)){
            $("#version").get(0).selectedIndex = 0;
            $("#version").trigger('change');
        }
    }

    function underTableInfo(){
        if(myData.id){
            if( confirm('确定下架？') ){
                AjaxGet(del + myData.id, updateTable);
            }
        }else{
            alert('请选择版本！');
        }
    }
}

//查看SN号
function releaseSN(url){
    AjaxGet(url + myData.id, function(data){
        createElem2(data.extra.snList);
        trHover('#myTable2');
        $('#myModal2').modal('show');
    });

    function createElem2(data) {
        var dataArr = [];
        var len = data.length;
        for( var i=0; i<len; i++ ) {
            var arr = data[i];
            dataArr.push([arr]);
        }
        myDataTable('#myTable2', {
            "data": dataArr,
            "pageLength": 10,
            "columnDefs": [
                {'title':'Mac','width':'100%', 'targets':0},
            ]
        });
    }
}