//@ sourceURL=question.Qmanage.js
var myData = {};
var pageSize = 10; //自定义分页，每页显示的数据量
var currentPage = 1; //用户当前的页面
var questionRequestUrl=null; //区分查看指派、查看全部、未指派问题

$(function () {
	initTopMenu();

	myData.checkedLists = [];   //存储check选中项
	AjaxGet('/Customer/Home/Question/getComQuestionList?page=1&pageSize=' + pageSize, function(data){
       		createCommonQuestionTable(data,1);
    	});

	listenTab(function(str){
		$('.dataTables_filter input').val('');
    		if(str === '常见问题'){
			myData.checkedLists = []
    			$('.tab-list').hide();
    			$('.tab-list:eq(0)').show();

    		}else if(str === '问题单管理'){
			myData.checkedLists = []
    			AjaxGet('/Customer/Home/Question/getQuestionList/status/0/page/1/pageSize/' + pageSize, function(data){
			   	createQuestionTypeTable(data,1);
		   	});
    		$('.tab-list').hide();
    		$('.tab-list:eq(1)').show();
    		questionRequestUrl=null;
    	}else if(str === '分类管理'){
			myData.checkedLists = []
			AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/1/pageSize/'+pageSize+'/level/1',function(data){
				 createCommonQuestionTypeTable(data,1);
			})

			AjaxGet('/Customer/Home/Question/getCategory/is_common/0/page/1/pageSize/'+pageSize,function(data){
				 createWenTiDanTable(data,1);
			})
			$('.tab-list').hide();
			$('.tab-list:eq(2)').show();
			$('.common-question-type').show();
            		$('.second-question-type').hide();
    	}
    });

   //进入二级分类
	$('#commonQuestionTypeTable').on('click','.glyphicon-list',function(){
		var id=$(this).parents('tr').children('td:eq(0)').data('id');
		var name=$(this).parents('tr').children('td:eq(0)').data('cate_name');
		myData.pId = id;
		AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/1/pageSize/'+pageSize+'/parent_id/'+id,function(data){
				$('.second-question-type h3').html(name);
					 createElem(data,1);
					$('.common-question-type').hide();
					$('.second-question-type').show();
				});
	});


    listenToolbar('assign', assignQuestion, '#questionTypeTable');
    listenToolbar('del', selectAll, '#questionTypeTable');
    listenToolbar('myAssign', myAssign, '#questionTypeTable');
    listenToolbar('allQues', allQues, '#questionTypeTable');

	listenMyPage('questionTypeTable', currentPage, updateQuestionTypeTable);


    listenToolbar('del', delQuestion, '#commonQuestionTable');
    listenToolbar('add', addQuestion, '#commonQuestionTable');
    listenMyPage('commonQuestionTable', currentPage, updateCommonQuestionTable);

    listenToolbar('add', addFirstTypeQuestion, '#commonQuestionTypeTable');
    listenToolbar('del', delQuestionType, '#commonQuestionTypeTable');
	listenMyPage('commonQuestionTypeTable', currentPage, updateCommonQuestionTypeTable);


    listenToolbar('back', backTable, '#secondTable');
    listenToolbar('add', addSecondTypeQuestion, '#secondTable');
    listenToolbar('del', delSecondTypeQuestion, '#secondTable');
    	//监听子分类搜索
	listenMyPage('secondTable', currentPage, updateSecondTable);

    listenToolbar('add', addQuestionType, '#wenTiDanTable');
    listenToolbar('del', delWenTiDan, '#wenTiDanTable');
	listenMyPage('wenTiDanTable', currentPage, updateWenTiDanTable);

	//listenSingleCheckBox('#commonQuestionTable');
	//返回
	function backTable(){
		$('.second-question-type').hide();
		updateCommonQuestionTypeTable(1)
		$('.common-question-type').show();
	}
//更新常见问题列表
function updateCommonQuestionTable(page,object){
	  myData.checkedLists = [];

	  /*if (typeof object === 'object') {
	  	for (var key in object) {
	  		console.log(key);
	  	}
	  }*/

	  if (typeof object === 'object') {
	  	var filter = '';
		for (var i in object) {
			filter += '/'+i + '/'+object[i];
		}
	  	AjaxGet('/Customer/Home/Question/getComQuestionList/page/'+page+'/pageSize/' + pageSize+filter, function(data){
		  	createCommonQuestionTable(data, page);
	  	});
	  }else{
	  	AjaxGet('/Customer/Home/Question/getComQuestionList/page/'+page+'/pageSize/' + pageSize, function(data){
		  	createCommonQuestionTable(data, page);
	  	});
	  }


}
//全选
function selectAll(){
	var txt=$(this).text();
	var $parents=$(this).parents('.dataTables_wrapper');

	if(txt=="全选"){
		if($parents.find('input:checkbox').length==0){
			return false;
		}
	    $parents.find('input:checkbox').attr("checked", false).trigger('click');
		$(this).html('<i class="fa fa-times" style="padding-right:4px"></i>取消全选');
	}else{
		$(this).html('<i class="fa fa-check-square-o" style="padding-right:4px"></i>全选');
		$parents.find('input:checkbox').attr("checked", true).trigger('click');
	}
}
$('#questionTypeTable').on('click','input:checkbox',function(){
	var len=$('#questionTypeTable input:checkbox').length;
	var cl=$('#questionTypeTable input:checked').length;

	if(len==cl){
		$(this).parents('#questionTypeTable_wrapper').find('.delBtn').html('<i class="fa fa-times" style="padding-right:4px"></i>取消全选');
	}else{
		$(this).parents('#questionTypeTable_wrapper').find('.delBtn').html('<i class="fa fa-check-square-o" style="padding-right:4px"></i>全选')
	}

})
//点击常见问题
/*$('#commonQuestionTable').on('click','input',function(){
	var $parents=$(this).parents('tr');
	$parents.siblings().find('input:checked').trigger('click')

	if($parents.find('p.look-reply').is(':hidden')){
		$parents.find('p.look-reply').slideDown();
	}else{
		$parents.find('p.look-reply').slideUp();
	}

	$parents.siblings('tr').find('p.look-reply').slideUp();
});*/
$('#commonQuestionTable').on('click','.cont-q',function(){
	//$(this).parents('tr').find('input:checkbox').trigger('click');
	var $parents=$(this).parents('tr');
	//$parents.siblings().find('input:checked').trigger('click')
	if($parents.find('p.look-reply').is(':hidden')){
		$parents.find('p.look-reply').slideDown();
	}else{
		$parents.find('p.look-reply').slideUp();
	}
	$parents.siblings('tr').find('p.look-reply').slideUp();
})
//发布常见问题
function addQuestion(){
	AjaxGet('/Customer/Home/Question/getCategory/parent_id/0/is_common/1', function(data){
        createSelect1(data);
    });
	$('#publicModal').modal('show');
}
//删除常见问题
function delQuestion(){
	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			dataObj={
				"ids":myData.checkedLists,
				"is_common":1
			};
			 $.ajax({
				 type:"POST",
				 url:"/Customer/Home/Question/deleteComQuestion",
				 data:dataObj,
				 success:function(){
				 }
			 });
			updateCommonQuestionTable(1);
		}
	}else{
		alert('请选择一个常见问题分类！');
		return;
	}
}
//删除二级分类
function delSecondTypeQuestion(){
	console.log(myData.checkedLists)
    if(myData.checkedLists.length){
    	if(confirm('分类删除不能恢复，确认删除？')){
    		dataObj={
    				"ids":myData.checkedLists,
    				"is_common":1
    			};
    			 $.ajax({
    				 type:"POST",
    				 url:"/Customer/Home/Question/deleteCategory",
    				 data:dataObj,
    				 success:function(){
    				 }
    			 });
    			 updateSecondTable(1);
    	}
    }else{
    	alert('请选择常见问题二级分类');
    	return;
    }
}

//删除常见问题分类
function delQuestionType(){
	console.log(myData.checkedLists)
	if(myData.checkedLists.length){
		if (confirm('删除一级分类，其下的二级分类也将被删除！确定删除？')) {
			dataObj={
				"ids":myData.checkedLists,
				"is_common":1
			};
			 $.ajax({
				 type:"POST",
				 url:"/Customer/Home/Question/deleteCategory",
				 data:dataObj,
				 success:function(){
				 }
			 });
			updateCommonQuestionTypeTable(1);
		}
	}else{
		alert('请选择一个常见问题分类！');
		return;
	}
}
//指派问题单
function assignQuestion(){
	if(myData.checkedLists.length!=0){
		//alert(myData.checkedLists)
		//Customer/Home/User/customerList
		AjaxGet('/Customer/Home/User/customerList/type/notCustomer', function(data){
			createUser(data);
		});
		AjaxGet('/Customer/Home/Question/getCategory/is_common/0', function(data){
			createType(data);
		});
		$('#assignModal').modal('show');
	}else{
		alert('请选择一个问题单');
		return false;
	}
}
//生成选择用户下拉列表--指派
function createUser(data){
	var arr = data.retval
    var con = '<option value="请选择用户">请选择用户</option>';
    var $select = $('#assignUser');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].id + '">' + arr[i].name + '</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "420px"
    });
}

//生成选择分类下拉列表--指派
function createType(data){
	var arr = data.retval.list
    var con = '<option value="请选择分类">请选择分类</option>';
    var $select = $('#assignType');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].cate_id + '">' + arr[i].cate_name + '</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "420px"
    });
}
 //指派问题
  $('#assignQuestion').click(function(){
	  var uid=$('#assignUser').val();
	  var cid=$('#assignType').val();
	  if(uid=='请选择用户'){
		  alert('请选择用户')
		  return false;
	  }
	  if(cid=='请选择分类'){
		  alert('请选择分类')
		  return false;
	  }
	  dataObj={
		  "qids":myData.checkedLists,
		  "user_id":uid,
		  "cate_id":cid

	  };
	  console.log(dataObj);
	   $.ajax({
		   type:"POST",
		   url:"/Customer/Home/Question/assignQuestion",
		   data:dataObj,
		   success:function(){
		   }
	   });
	  updateQuestionTypeTable(1);
	 $('#assignModal').modal('hide');
  })
//新增常见问题一级分类
function addFirstTypeQuestion(){
	$('#firstTypeName').val('')
	$('#firstTypeDesc').val('');
	$('#firstTypeModal').modal('show');
}
//提交常见问题一级分类
$('#addFirstType').on('click',function(){
	var name=$('#firstTypeName').val();
	var desc=$('#firstTypeDesc').val();


	if(name==="" || !name){
		alert('请输入分类名');
		return false;
	}
	var dataObj={
		"cate_name":name,
		"remark":desc,
		"parent_id":0,
		"is_common":1
	};
	 $.ajax({
		 type:"POST",
		 url:"/Customer/Home/Question/addCategory",
		 data:dataObj,
		 dataType: 'json',
		 success:function(res){
			 if(res.code == 200){
				 $('#firstTypeName').val('');
				 $('#firstTypeDesc').val('')
		                 $('#firstTypeModal').modal('hide');
				 updateCommonQuestionTypeTable(1);
			 }else{
				 $('#firstTypeModal .error-info').text(res.msg);
				 return;
			 }
		 }
	 });
})
//新增常见问二级分类问题
function addSecondTypeQuestion(){
	$('#sSecondTypeName').val('');
	$('#sSecondTypeDesc').val('');
	/*
	AjaxGet('/Customer/Home/Question/getCategory/parent_id/0/is_common/1', function(data){
        createSelect(data);
    });*/

	$('#secondTypeModal').modal('show');
}
//创建一级分类下拉列表
function createSelect1(data){
	var arr = data.retval.list
    var con = '<option value="">请选择一级分类</option>';
    var $select = $('#publicFirstType');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].cate_id + '">' + arr[i].cate_name + '</option>';
    }
	$select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "793px"
    });

    //$select.html(con);
}
//选择一级分类时生成对应子分类
$('#publicFirstType').on('change', function(e, name) {

    var pid = $(this).val();
	if(pid!=="请选择一级分类"){
		var $select = $('#publicSecondType');
		AjaxGet('/Customer/Home/Question/getCategory/is_common/1/parent_id/'+pid, function(data){
			var arr = data.retval.list;
			var con = '<option value="请选择二级分类">请选择二级分类</option>';
			var len = arr.length;
			for (var i = 0; i < len; i++) {
				con += '<option value="' + arr[i].cate_id + '">' + arr[i].cate_name + '</option>';
			}
			$select.html(con);
			return;
		});

	}
});
//提交常见问题二级分类
$('#addSecondType').on('click',function(){
	var cate_name=$('#sSecondTypeName').val();
	var remark=$('#sSecondTypeDesc').val();

	if(cate_name==="" || !cate_name){
		alert('请输入分类名');
		return false;
	}
	var dataObj={
		"parent_id":myData.pId,
		"cate_name":cate_name,
		"remark":remark,
		"is_common":1
	};
	 $.ajax({
		 type:"POST",
		 url:"/Customer/Home/Question/addCategory",
		 data:dataObj,
		 dataType: 'json',
		 success:function(res){
			 if(res.code == 200){
				 $('#sSecondTypeName').val('');
				 $('#sSecondTypeDesc').val('')
		                 $('#secondTypeModal').modal('hide');
				 updateSecondTable(1);
			 }else{
				 $('#secondTypeModal .error-info').text(res.msg);
				 return;
			 }
		 }
	 });
	 
})

function updateSecondTable(page,object){

	var pid=myData.pId;

	myData.checkedLists = [];
	if (typeof object  === "object" ) {
		var filter = '';
		for (var i in object) {
			filter += '/'+i + '/'+object[i];
		}
		AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/'+page+'/pageSize/'+pageSize+'/parent_id/'+pid+filter,function(data){
			createElem(data,page);
		})
	}else{
	 	AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/'+page+'/pageSize/'+pageSize+'/parent_id/'+pid,function(data){
			createElem(data,page);
		})
	}
}

function addQuestionType(){
	$('#questionTypeName').val('');
	$('#questionTypeDesc').val('');
	$('#questionTypeModal').modal('show');
}
//新增问题单一级分类
$('#addQuestionType').on('click',function(){
	var cate_name=$('#questionTypeName').val();
	var remark=$('#questionTypeDesc').val();
	if(cate_name=="" || !cate_name){
		alert('请输入分类名称');
		return false;
	}
	var dataObj={
		"parent_id":0,
		"cate_name":cate_name,
		"remark":remark,
		"is_common":0
	};
	$.ajax({
		 type:"POST",
		 url:"/Customer/Home/Question/addCategory",
		 data:dataObj,
		 dataType: 'json',
		 success:function(res){
			 if(res.code == 200){
				 $('#questionTypeName').val('');
				 $('#questionTypeDesc').val('')
				 $('#questionTypeModal').modal('hide');
				 updateWenTiDanTable(1);
			 }else{
				 $('#questionTypeModal .error-info').text(res.msg);
				 return;
			 }
		 }
	 });
})
//删除问题单一级分类
function delWenTiDan(){

	if(myData.checkedLists.length){
		if (confirm('确定删除？')) {
			dataObj={
				"ids":myData.checkedLists
			};
			 $.ajax({
				 type:"POST",
				 url:"/Customer/Home/Question/deleteCategory",
				 data:dataObj,
				 success:function(){
					 $('#questionDesc').text('')
					 $('#questionModal').modal('hide');
				 }
			 });

			updateWenTiDanTable(1);
		}
	}else{
		alert('请选择一个分类！');
		return;
	}
}

//生成附件html代码
function createAttachHtml(data, type,className) {
	var html = '';
	// 1:提问附件 2：回复附件
	if (type == 1 || type == 2) {
		var obj = type == 1 ? data.ask_attach : data.reply_attach;
		if (obj != false && obj != 'undefined') {
			var className = className === undefined?'':' class="'+className+'"';
			html += '  <p '+className+'>附件：';
			var l = obj.length;
			for (var j = 0; j < l; j++) {
				html += '<a class="attach_link" href="' + hostUrl  +'url=' + obj[j].replace('Upload/Question/','') + '">查看附件</a>&nbsp;&nbsp;';
			}
			html += '</p>';
		}
	}
	return html;
}

//创建常见问题
function createCommonQuestionTable(data,page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
		var arr = data.retval.list[i];
		arr.add_time=formatDate(arr.add_time);
		var class_name='time';
		var id_name='';

		if(arr.typical == 1){
			class_name='typical_question';
			id_name='typical_content'
		}
		if (arr.cate_name_2) {
			arr.content=' <span style="color:#999">创建人：'+arr.admin_name+'　　创建时间：'+arr.add_time+'　　分类：'+arr.cate_name_1+' > '+arr.cate_name_2+'</span>'
		            +'<p><a id="'+id_name+'" class="cont-q ellipsis" onclick="$(this).toggleClass(\'ellipsis\')"  href="javascript:">'+arr.content+'</a></p>';
		}else{
			arr.content=' <span style="color:#999">创建人：'+arr.admin_name+'　　创建时间：'+arr.add_time+'　　分类：'+arr.cate_name_1+'</span>'
		            +'<p><a id="'+id_name+'" class="cont-q ellipsis" onclick="$(this).toggleClass(\'ellipsis\')"  href="javascript:">'+arr.content+'</a></p>'
		}
		
		arr.content+=createAttachHtml(arr,1,'look-reply')+'<p class="look-reply">回复：<br><a class="texta">'+arr.reply+'</a></p>'+createAttachHtml(arr,2,'look-reply');
        dataArr.push([arr.id, arr.content]);
    }
    myDataTable('#commonQuestionTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'','width':'3%', 'targets':0, "orderable": false},
            {'title':'','width':'97%', 'targets':1,"orderable": false}
        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"content": aData[1]
			});
			$('td:eq(1)', nRow).css('max-width','900px');
		}
	});
    
	toolbar = [];
	initToolBtn(myData.data.retval, '常见问题');
  	initToolBar('#commonQuestionTable', toolbar);
	/*initToolBar('#commonQuestionTable', [
	  '<a class="btn question-btn addBtn" href="javascript:">发布</a>',
	  '<a class="btn question-btn delBtn" href="javascript:">删除</a>',
	]);*/
	updatePagination(len, page, data.retval.count, 'commonQuestionTable');
	listenCheckBox('#commonQuestionTable');
    updateChecked('#commonQuestionTable');
}
//更新问题管理列表
function updateQuestionTypeTable(page,object){

	myData.checkedLists = [];
	if(questionRequestUrl !==null){
		AjaxGet(questionRequestUrl+'/page/'+page+'/name/'+name, function(data){
			createQuestionTypeTable(data,page);
		});
	}else if (typeof object  === "object" ) {
		var filter = '';
		for (var i in object) {
			filter += '/'+i + '/'+object[i];
		}
		AjaxGet('/Customer/Home/Question/getQuestionList/status/0/page/'+page+'/pageSize/' + pageSize+filter, function(data){
			createQuestionTypeTable(data,page);
		});
	}else{
	 	AjaxGet('/Customer/Home/Question/getQuestionList/status/0/page/'+page+'/pageSize/' + pageSize, function(data){
			createQuestionTypeTable(data,page);
		});
	}
}
//创建问题管理
function createQuestionTypeTable(data,page){
	var dataArr = [];
    var len = data.retval.list.length;

    for( var i=0; i<len; i++ ) {
		var arr = data.retval.list[i];
		arr.ask_time=formatDate(arr.ask_time);

		//判断是否有指派人
		var assign_user=arr.reply_id > 0?'　　指派：'+arr.reply_name:'';
		arr.content=' <span class="time" style="color:#999">创建人：'+arr.asker_name+'　　创建时间：'+arr.ask_time+assign_user+'　　分类：'+(arr.cate_name === null?'未分类':arr.cate_name)+'</span><p><a class="cont-q ellipsis" onclick="$(this).toggleClass(\'ellipsis\')">'+arr.content+'</a></p>'+createAttachHtml(arr,1);
        dataArr.push([arr.id, arr.content]);
    }
    myDataTable('#questionTypeTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'','width':'1%', 'targets':0, "orderable": false},
            {'title':'','width':'99%', 'targets':1,"orderable": false}

        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"content": aData[1]
			});
			$('td:eq(1)', nRow).css('max-width','900px');
		}
	});
     var judgeAssign=(questionRequestUrl === null || questionRequestUrl.indexOf('status/1') === -1)?false:true;
     var judgeCheckAllQues= (questionRequestUrl === null || questionRequestUrl.indexOf('assign_id') !== -1 || questionRequestUrl.indexOf('status') !== -1)?false:true;

     if(judgeAssign === true || judgeCheckAllQues === true){
    	 var btn=[
    	    	  '<a class="btn question-btn myAssignBtn" href="javascript:">'+'返回未指派'+'</a>'
    	    	];
    	 initToolBar('#questionTypeTable',btn);
     }else{
    	toolbar = [];
    	initToolBtn(myData.data.retval, '问题单管理');
    	initToolBar('#questionTypeTable', toolbar);
     }
	updatePagination(len, page, data.retval.count, 'questionTypeTable');
	listenCheckBox('#questionTypeTable');
    updateChecked('#questionTypeTable');

}
//创建常见问题分类列表
function createCommonQuestionTypeTable(data,page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        dataArr.push([arr.cate_id,arr.cate_name,arr.remark,null]);
    }
    myDataTable('#commonQuestionTypeTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
           {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'2%', 'targets':0, "orderable": false},
		   {'title':'ID','width':'4%', 'targets':0, "orderable": false},
            {'title':'分类名称','width':'32%', 'targets':1},
           {'title':'备注','width':'32%', 'targets':2},
            {'title':'子分类列表','width':'32%', 'targets':3}

        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			tableTdIcon(3, nRow, 'list');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"cate_name": aData[1],
				"remark": aData[2]

			});
		}
	});
	initToolBar('#commonQuestionTypeTable', [
	  '<a class="btn question-btn addBtn" href="javascript:"><i class="fa fa-plus-square" style="padding-right:4px"></i>新增</a>',
	  '<a class="btn question-btn delBtn" href="javascript:"><i class="fa fa-minus-square" style="padding-right:4px"></i>删除</a>',
	]);
	updatePagination(len, page, data.retval.count, 'commonQuestionTypeTable');
    listenCheckBox('#commonQuestionTypeTable');
    updateChecked('#commonQuestionTypeTable');
}
//创建常见问题二级分类列表
function createElem(data,page){
	myData.checkedLists = [];
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
        var arr = data.retval.list[i];
        dataArr.push([arr.cate_id,arr.cate_name,arr.remark]);
    }
    myDataTable('#secondTable', {
        "data": dataArr,
        "order": [[1, "desc"]],
		"columnDefs": [
            {'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'2%', 'targets':0, "orderable": false},
            {'title':'分类名称','width':'33%', 'targets':1},
           {'title':'备注','width':'33%', 'targets':2},

        ],
		"createdRow": function(nRow, aData, iDataIndex) {
			$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');
			$('td:eq(0)', nRow).data({
				"id": aData[0],
				"cate_name": aData[1],
				"remark": aData[2]

			});
		}
	});
	initToolBar('#secondTable', [
	  '<a class="btn question-btn backBtn" href="javascript:"><i class="fa fa-arrow-left" style="padding-right:4px"></i>返回</a>',
	  '<a class="btn question-btn addBtn" href="javascript:"><i class="fa fa-plus-square" style="padding-right:4px"></i>新增</a>',
	  //'<a class="btn question-btn addSecondBtn" href="javascript:">编辑</a>',
	  '<a class="btn question-btn delBtn" href="javascript:"><i class="fa fa-minus-square" style="padding-right:4px"></i>删除</a>',
	]);
	updatePagination(len, page, data.retval.count, 'secondTable');
    listenCheckBox('#secondTable');
    updateChecked('#secondTable');
}


	//更新常见问题分类列表
	function updateCommonQuestionTypeTable(page,object){
		myData.checkedLists = [] ;
		if (typeof object  === "object" ) {
			var filter = '';
			for (var i in object) {
				filter += '/'+i + '/'+object[i];
			}
			AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/'+page+'/pageSize/'+pageSize+'/level/1'+filter, function(data){
				createCommonQuestionTypeTable(data, page);
			});
		}else{
			AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/'+page+'/pageSize/'+pageSize+'/level/1', function(data){
				createCommonQuestionTypeTable(data, page);
			});
		}
	}

	//创建问题单分类
	function createWenTiDanTable(data,page){
		var dataArr = [];
		var len = data.retval.list.length;
		for( var i=0; i<len; i++ ) {
			var arr = data.retval.list[i];
			dataArr.push([arr.cate_id, arr.cate_name,arr.remark]);
		}
		myDataTable('#wenTiDanTable', {
			"data": dataArr,
			"order": [[1, "desc"]],
			"columnDefs": [
				{'title':'<label class="position-relative"><input type="checkbox" class="ace" /><span class="lbl"></span></label>','width':'2%', 'targets':0, "orderable": false},
				{'title':'分类名称','width':'44%', 'targets':1},
				{'title':'备注','width':'44%', 'targets':2}

			],
			"createdRow": function(nRow, aData, iDataIndex) {
				$('td:eq(0)', nRow).html('<label class="position-relative"><input type="checkbox" class="ace"><span class="lbl"></span></label>');

				$('td:eq(0)', nRow).data({
					"id": aData[0],
					"cate_name": aData[1],
					"remark": aData[2]

				});
			}
		});
		initToolBar('#wenTiDanTable', [
		  '<a class="btn question-btn addBtn" href="javascript:"><i class="fa fa-plus-square" style="padding-right:4px"></i>新增</a>',
		  '<a class="btn question-btn delBtn" href="javascript:"><i class="fa fa-minus-square" style="padding-right:4px"></i>删除</a>',
		]);
		updatePagination(len, page, data.retval.count, 'wenTiDanTable');
		listenCheckBox('#wenTiDanTable');
		updateChecked('#wenTiDanTable');
	}

	//更新问题单分类列表
	function updateWenTiDanTable(page,object){
		myData.checkedLists = []


		if (typeof object  === "object" ) {
			var filter = '';
			for (var i in object) {
				filter += '/' + i + '/'+object[i];
			}
			AjaxGet('/Customer/Home/Question/getCategory/ is_common/0/page/'+page+'/pageSize/'+pageSize+filter, function(data){
				createWenTiDanTable(data, page);
			});
		}else{
			AjaxGet('/Customer/Home/Question/getCategory/ is_common/0/page/'+page+'/pageSize/'+pageSize, function(data){
				createWenTiDanTable(data, page);
			});
		}
	}



  //发布问题
  $('#publicQuestion').click(function(){
	  var firstType=$('#publicFirstType').val();
	  var secondType=$('#publicSecondType').val();
	  var typical=$('#typical').val();
	  var desc=$('#questionDesc').val();

	  var reply=$('#questionReply').val();
	  if(firstType=='请选择一级分类'){
		  alert('请选择一级分类')
		  return false;
	  }
	  if(secondType=='请选择二级分类'){
		  alert('请选择二级分类')
		  return false;
	  }
	  if(desc==""||!desc){
		  alert('请输入问题描述')
		  return false;
	  }
	  if(reply==""||!reply){
		  alert('请输入回复内容')
		  return false;
	  }


	  var data = new FormData();
	  //处理上传附件
      var qFile=$('#questionFile')[0].files;
      var rFile=$('#replyFile')[0].files;
      var qlen=qFile.length;
      var rlen=rFile.length;
      for(var i=0;i<qlen;i++){
    	  data.append("ask_attach[]", qFile[i]);
      }
      for(var i=0;i<rlen;i++){
    	  data.append("reply_attach[]", rFile[i]);
      }
      data.append("cate_id_1", firstType);
      data.append("content", desc);
      data.append("cate_id_2", secondType);
      data.append("typical", typical);
      data.append("reply", reply);

	  AjaxFile('/Customer/Home/Question/addCommonQuestion', data, function(){
	      $('#publicFirstType').val('');
	      $('#publicSecondType').val('');
		  $('#questionDesc').val('');
		  $('#questionReply').val('');
		  $('#questionFile').val('');
		  $('#replyFile').val('');
		  $('#publicModal').modal('hide');
		  updateCommonQuestionTable(1);
  	});
  })

   //新增一级分类
  $('#addFirstType22').click(function(){
	  var firstTypeName=$('#firstTypeName').val();
	  var firstTypeDesc=$('#firstTypeDesc').val();
	  var filter = $('#CommonQuestionTypeTable_filter input').val() || '';
	  var data = {};
	  if(firstTypeName==''){
		  alert('请输入分类名称');
		  return false;
	  }
	  data = {
		  "cate_name": firstTypeName,
		  "parent_id": "",
		  "sort": "",
		  "if_show": "",
		  "is_common":1
	  };
	  AjaxPost('/Customer/Home/Question/addCategory', data, function (){
		  //createCommonQuestionTypeTable(data, 1);
		  //return;
	  });
	  alert('新增一级分类成功');
	  $('#firstTypeName').val('');
	  $('#firstTypeDesc').val('');
	  $('#firstTypeModal').modal('hide');

  });
   //新增二级分类
  $('#eeweaddSecondType').click(function(){
	  var val=$('#sFirstTypeName').val();
	  var val2=$('#sSecondTypeName').val();
	  if(val=='请选择一级分类'){
		  alert('请选择一级分类');
		  return false;
	  }
	  if(val2==''|| !val2){
		  alert('请输入二级分类名称')
		  return false;
	  }
	  alert('新增二级分类成功');
	  $('#secondTypeModal').modal('hide');

  })
/*function formatDate(now) {
var year=now.getFullYear();
var month=now.getMonth()+1;
var date=now.getDate();
var hour=now.getHours();
var minute=now.getMinutes();
var second=now.getSeconds();
return year+"-"+month+"-"+date+" "+hour+":"+minute;
}*/

//查看指派
function myAssign() {
	var text=$.trim($(this).text());
	var uid = window.localStorage.getItem("CUSTOM_PERMISSION_ID");
	var url = tag = '';
	if(text == '查看指派'){
		url='/Customer/Home/Question/getQuestionList/page/1/pageSize/'+ pageSize+'/status/1';
		questionRequestUrl='/Customer/Home/Question/getQuestionList/pageSize/'+ pageSize+'/status/1';
	}else if(text == '返回未指派'){
		url='/Customer/Home/Question/getQuestionList/status/0/page/1/pageSize/' + pageSize;
		questionRequestUrl=null;
	}else{
        alert('网络错误');
        return false;
	}
	myData.checkedLists = []
    AjaxGet(url, function(data) {
		createQuestionTypeTable(data, 1);
	});
}

//查看全部
function allQues(){
	myData.checkedLists = []
	questionRequestUrl='/Customer/Home/Question/getQuestionList/pageSize/' + pageSize;
    AjaxGet('/Customer/Home/Question/getQuestionList/page/1/pageSize/' + pageSize, function(data) {
		createQuestionTypeTable(data, 1);
	});
}

});
