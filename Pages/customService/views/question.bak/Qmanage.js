//@ sourceURL=question.Qmanage.js
var myData = {};
var pageSize = 10; //自定义分页，每页显示的数据量
var currentPage = 1; //用户当前的页面

$(function () {
	var power = window.localStorage.getItem("CUSTOM_PERMISSION_USEPOWER");
    	if (power != '客服管理员') {
        		$('#breadcrumb').html('<span class="active">常见问题</span>');
    	}

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

    		}else if(str === '问题管理'){
			myData.checkedLists = []
    			AjaxGet('/Customer/Home/Question/getQuestionList/status/0/page/1/pageSize/' + pageSize, function(data){
			   	createQuestionTypeTable(data,1);
		   	});
    		$('.tab-list').hide();
    		$('.tab-list:eq(1)').show();
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
		$('.common-question-type').show();
	}
//更新常见问题列表
function updateCommonQuestionTable(page,name){
	  myData.checkedLists = [];


	  /*if (typeof object === 'object') {
	  	for (var key in object) {
	  		console.log(key);
	  	}
	  }*/

	  if (typeof name === 'string') {
	  	AjaxGet('/Customer/Home/Question/getComQuestionList/page/'+page+'/pageSize/' + pageSize+'/name/'+name, function(data){
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
		$(this).text('取消全选');
	}else{
		$(this).text('全选');
		$parents.find('input:checkbox').attr("checked", true).trigger('click');
	}
}
$('#questionTypeTable').on('click','input:checkbox',function(){
	var len=$('#questionTypeTable input:checkbox').length;
	var cl=$('#questionTypeTable input:checked').length;

	if(len==cl){
		$(this).parents('#questionTypeTable_wrapper').find('.delBtn').text('取消全选');
	}else{
		$(this).parents('#questionTypeTable_wrapper').find('.delBtn').text('全选')
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

}

//删除常见问题分类
function delQuestionType(){
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
		AjaxGet('/Customer/Home/User/customerList/type/admin/type-admin-online-normal', function(data){
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
		 success:function(){
			 $('#firstTypeName').val('');
			 $('#firstTypeDesc').val('')
	         $('#firstTypeModal').modal('hide');
		 }
	 });

	 updateCommonQuestionTypeTable(1);
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
		 success:function(){
			 $('#sSecondTypeName').val('');
			 $('#sSecondTypeDesc').val('')
	         $('#secondTypeModal').modal('hide');
		 }
	 });
	 updateSecondTable(1);
})
function updateSecondTable(page,name){

	var pid=myData.pId;

	myData.checkedLists = [];
	if (typeof name  === "string" ) {
		AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/1/pageSize/'+pageSize+'/parent_id/'+pid+'/name/'+name,function(data){
			createElem(data,1);
		})
	}else{
	 	AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/1/pageSize/'+pageSize+'/parent_id/'+pid,function(data){
			createElem(data,1);
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
		 success:function(){
			 $('#questionTypeName').val('');
			 $('#questionTypeDesc').val('')
	         $('#questionTypeModal').modal('hide');
		 }
	 });
	 updateWenTiDanTable(1);
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
//创建常见问题
function createCommonQuestionTable(data,page){
	var dataArr = [];
    var len = data.retval.list.length;
    for( var i=0; i<len; i++ ) {
		var arr = data.retval.list[i];
		var d=new Date(arr.add_time*1000);
		arr.add_time=formatDate(d);

		arr.content=' <span class="time">'+arr.admin_name+'&nbsp;'+arr.add_time+'</span>'
		            +'<p><a class="cont-q" href="javascript:">'+arr.content+'</a></p>'
		            +'<p class="look-reply">回复：'+arr.reply+'</p>';
        dataArr.push([arr.id, arr.content]);
    }
    myDataTable('#commonQuestionTable', {
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
		}
	});
	initToolBar('#commonQuestionTable', [
	  '<a class="btn question-btn addBtn" href="javascript:">发布</a>',
	  '<a class="btn question-btn delBtn" href="javascript:">删除</a>',
	]);
	updatePagination(len, page, data.retval.count, 'commonQuestionTable');
	listenCheckBox('#commonQuestionTable');
    updateChecked('#commonQuestionTable');
}
//更新问题管理列表
function updateQuestionTypeTable(page,name){
	myData.checkedLists = [];
	if (typeof name  === "string" ) {

		AjaxGet('/Customer/Home/Question/getQuestionList/status/0/page/'+page+'/pageSize/' + pageSize+'/name/'+name, function(data){
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
		var d=new Date(arr.ask_time*1000);
		arr.ask_time=formatDate(d);

		arr.content=' <span class="time">'+arr.asker_name+arr.ask_time+'</span><p>'+arr.content+'</p>';
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
		}
	});
	initToolBar('#questionTypeTable', [
	  '<a class="btn question-btn assignBtn" href="javascript:">指派</a>',
	  '<a class="btn question-btn delBtn" href="javascript:">全选</a>',
	]);
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
            {'title':'一级分类','width':'32%', 'targets':1},
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
	  '<a class="btn question-btn addBtn" href="javascript:">新增</a>',
	  '<a class="btn question-btn delBtn" href="javascript:">删除</a>',
	]);
	updatePagination(len, page, data.retval.count, 'commonQuestionTypeTable');
    listenCheckBox('#commonQuestionTypeTable');
    updateChecked('#commonQuestionTypeTable');
}
//创建常见问题二级分类列表
function createElem(data,page){
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
	  '<a class="btn question-btn backBtn" href="javascript:">返回</a>',
	  '<a class="btn question-btn addBtn" href="javascript:">新增</a>',
	  //'<a class="btn question-btn addSecondBtn" href="javascript:">编辑</a>',
	  '<a class="btn question-btn delBtn" href="javascript:">删除</a>',
	]);
	updatePagination(len, page, data.retval.count, 'secondTable');
    listenCheckBox('#secondTable');
    updateChecked('#secondTable');
}


	//更新常见问题分类列表
	function updateCommonQuestionTypeTable(page,name){
		myData.checkedLists = [] ;
		if (typeof name  === "string" ) {
			AjaxGet('/Customer/Home/Question/getCategory/is_common/1/page/'+page+'/pageSize/'+pageSize+'/level/1/name/'+name, function(data){
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
				{'title':'一级分类','width':'44%', 'targets':1},
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
		  '<a class="btn question-btn addBtn" href="javascript:">新增</a>',
		  '<a class="btn question-btn delBtn" href="javascript:">删除</a>',
		]);
		updatePagination(len, page, data.retval.count, 'wenTiDanTable');
		listenCheckBox('#wenTiDanTable');
		updateChecked('#wenTiDanTable');
	}

	//更新问题单分类列表
	function updateWenTiDanTable(page,name){
		myData.checkedLists = []


		if (typeof name  === "string" ) {
			AjaxGet('/Customer/Home/Question/getCategory/ is_common/0/page/'+page+'/pageSize/'+pageSize+'/name/'+name, function(data){
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

	  var qFile=$('#questionFile').val();
	  var rFile=$('#replyFile').val();


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
	  var dataObj = {
		  "cate_id_1": firstType,
		  "content": desc,
		  "cate_id_2": secondType,
		  'typical':typical,
		  "ask_attach": qFile,
		  "reply":reply,
		  "reply_attach":rFile
	  };
	  $.ajax({
		 type:"POST",
		 url:"/Customer/Home/Question/addCommonQuestion",
		 data:dataObj,
		 success:function(){
		 }
	 });
      $('#publicFirstType').val('');
      $('#publicSecondType').val('');
	  $('#questionDesc').val('');
	  $('#questionReply').val('');
	  $('#publicModal').modal('hide');
	  updateCommonQuestionTable(1);
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
function formatDate(now) {
var year=now.getFullYear();
var month=now.getMonth()+1;
var date=now.getDate();
var hour=now.getHours();
var minute=now.getMinutes();
var second=now.getSeconds();
return year+"-"+month+"-"+date+" "+hour+":"+minute;
}


});