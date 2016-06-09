//@ sourceURL=question.customer.js
var pageSize = 15; //自定义分页，每页显示的数据量
var currentPage = 1; //应用列表当前的页面
var where = '/is_common/0/typical/1' //获取全部常见问题条件
var addrFile = 'getComQuestionList';
$(function () {

	where = '/is_common/1';
	addrFile = 'getCategory';
    	//获取全部常见问题分类
	AjaxGet('/Customer/Home/Question/'+addrFile+'/parent_id/0/page/1/pageSize/'+pageSize+where, function(data){
        		createCategory(data,currentPage);
    	});
	where = '/is_common/0/typical/1' //获取全部常见问题条件
	addrFile = 'getComQuestionList';
	//获取全部常见问题
	AjaxGet('/Customer/Home/Question/'+addrFile+'/page/1/pageSize/'+pageSize+where, function(data){
        		createCommonQuestionTable(data,currentPage);
    	});
	//监听分页
	listenMyPage('wrap', currentPage, updateCommonQuestionTable);
	//搜索问题单
	$('#search').on('click',function(){
		currentPage = 1;
		var name = $('#searchVal').val();
		where = '/is_common/1';
		addrFile = 'getComQuestionList';
		AjaxGet('/Customer/Home/Question/'+addrFile+'/page/1/pageSize/'+pageSize+where+'/name/'+name, function(data){
			createCommonQuestionTable(data,currentPage);
		});
	});

	$('.pos').on('click','a',function(){
		var href=$(this).attr('href');
		$('.nav-type li a[href='+href+']').trigger('click');
		return false;
	})

	$('.nav-type').on('click','li dl dd a',function(){
		var $dd=$(this).parent('dd');
		var $li=$(this).parents('li');
		var $a=$li.children('a');
		var ahref=$a.attr('href');
		var atext=$a.text();
		var cid=$(this).attr('href');
		var text=$(this).text();

		$(this).addClass('active');
		$dd.siblings().find('a').removeClass('active');

		var html=$('.pos').html('<a href="all" class="all">全部</a>&nbsp;&gt;&nbsp;<a href="'+ahref+'">'+atext+'</a>&nbsp;&gt;&nbsp <a href="'+cid+'">'+ text+'</a>');
		where = '/is_common/1/cate_id/2-'+cid;
		addrFile = 'getComQuestionList';
		//$('.pos').append($a+'&nbsp;&gt;&nbsp;'+ text
		AjaxGet('/Customer/Home/Question/'+addrFile+'/page/1/pageSize/'+pageSize+where, function(data){
			createCommonQuestionTable(data,1);
			return false;
		});
		return false;
	})

	$('.nav-type').on('click','li > a',function(){
		var $parent=$(this).parent();
		var text=$parent.children('a').text();
		$(this).addClass('active');
		$parent.siblings().find('a').removeClass('active');
		var pid=$(this).attr('href');
		if(pid=="all"){
			$('.pos').html('<a href="all"  class="all">全部</a>');
			where = '/is_common/0/typical/1';
			addrFile = 'getComQuestionList';
			AjaxGet('/Customer/Home/Question/'+addrFile+'/page/1/pageSize/'+pageSize+where, function(data){
		        		createCommonQuestionTable(data,currentPage);
		    	});
		}else{
			$('.pos').html('<a href="all"  class="all">全部</a>&nbsp;&gt;&nbsp;<a href="'+pid+'">'+ text+'</a>');

			//二级菜单滑动
			where = '/is_common/1/parent_id/'+pid;
			addrFile = 'getCategory';

			AjaxGet('/Customer/Home/Question/'+addrFile/*+'/page/1/pageSize/'+pageSize*/+where, function(data){

				// createCommonQuestionTypeTable(data,1);
				//二级菜单滑动
				var len = data.retval.list.length;
				var arr = data.retval.list;
				var html='<dl style="display:none;">';
				for (var i = 0; i < len; i++) {
					html+='<dd><a href="'+arr[i].cate_id+'">'+arr[i].cate_name+'</a></dd>'
				}
				html+='</dl>';

			    if($parent.children('dl').length==0){
				  $parent.append(html);
				}
				if($parent.find('dl').is(":hidden")){

					$parent.find('dl').slideDown();
				}else{
					$parent.find('dl a').removeClass('active');
					$parent.find('dl').slideUp();
				}
				$parent.siblings('li').children('dl').slideUp();
			});
			//分类
			where = '/is_common/1/cate_id/1-'+pid;
			addrFile = 'getComQuestionList';
			AjaxGet('/Customer/Home/Question/'+addrFile+'/page/1/pageSize/'+pageSize+where, function(data){
				createCommonQuestionTable(data,1);
				//二级菜单滑动
			});
		}
		return false;
	})
//
function createCategory(data){
	var len = data.retval.list.length;
	var arr = data.retval.list;
	var html="";
	for (var i = 0; i < len; i++) {
        html+='<li class="list-group-item">'
			+'<a href="'+arr[i].cate_id+'">'+arr[i].cate_name+'</a>'
			+'</li>';
    }
	$(".nav-type .list-group").append(html);
}
//生成分类下拉框
function selectType(data, id){
    var arr = data.retval.list
    var con = '<option value="请选择分类">请选择分类</option>';
    var $select = $('#questionType');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].cate_id + '">' + arr[i].cate_name + '</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "793px"
    });
}
//创建问题单
$("#createAnswer").on('click',function(){
	addQuestion();
	return false;
});
//创建问题单
function addQuestion(){
	AjaxGet('/Customer/Home/Question/getCategory', function(data){
       selectType(data);
    });
	$('#questionType').val('请选择分类');
	$('#questionDesc').val('');
	$('#fileToUpload').val('');
	$('#questionModal').modal('show');
}

//提交问题单
$('#subQuestion').click(function subQuestion(){
	var type=$('#questionType').val();
	var desc=$('#questionDesc').text();
	var data = new FormData();
	var attach=$('#fileToUpload')[0].files;
    if(type=="请选择分类"|| !type){
		alert('请选择分类');
		return false;
	}
	if(desc=="" || !desc){
		alert('请输入问题描述');
		return false;
		}

	var len=attach.length;
	for(var i=0;i<len;i++){
       data.append("ask_attach[]", attach[i]);
	}
    	data.append("cate_id_1",type);
    	data.append("content", desc);
    	AjaxFile('/Customer/Home/Question/addQuestion', data, function(){
        	$('#questionDesc').text('')
	    	$('#questionModal').modal('hide');
    	});

	/*/alert('ddd');
	//alert(document.getElementById("fileToUpload").files);

	var type=$('#questionType').val();
	var desc=$('#questionDesc').text();
	var attach=$('#fileToUpload').files;
	//alert(attach.length)
	if(type=='请选择分类' || !type){
		alert('请选择分类');
		return false;
	}
	if(desc=='' || !desc){
		alert('请输入问题描述');
		return false;
	}
	dataObj={
		"cate_id_1":type,
		"content":desc,
		"ask_attach":attach
	};
	var data = new FormData();
	data.append("ask_attach", attach);
	data.append("cate_id_1", type)
	data.append("content", desc)
	console.log(data);
	AjaxFile('/Customer/Home/Question/addQuestion', data, function(){
        $('#questionDesc').text('')
	    $('#questionModal').modal('hide');
		//return false;
    });

	/*console.log(dataObj);
	 $.ajax({
		 type:"POST",
		 url:"/Customer/Home/Question/addQuestion",
		 data:dataObj,
		 success:function(){
			 $('#questionDesc').text('')
	         $('#questionModal').modal('hide');
		 }
	 });
	*/
})
//创建常见问题列表
function createCommonQuestionTable(data,page){
	/*$(".page").html('<div class="holder"></div>');*/
	var arr = data.retval.list;
    	var len = data.retval.list.length;

	if(len==0){
		$('#wrap').html('<ul><li>没有检测到数据</li></ul>');
	}else{
        		var html = '<ul id="item">';
		for( var i=0; i<len; i++ ) {
			html +='<li>'
			    +'<a href="'+arr[i].id+'" class="con">'+arr[i].content+'</a>'
				+'</li>';
		}
		html += '</ul>';
		$('#wrap').html(html);
		//添加分页
		updatePagination(len, page, data.retval.count, 'wrap');
		/*listenCheckBox('#wrap');
	    	updateChecked('#wrap');*/

	    	/*var keyList = {
			"venid": '1',
	        		"pform": '2',
	        		"firmv": '3',
	        		"cust": '5',
	        		"puber": '6',
	        		"time": '7'
	   	};
	    	orderTab('#wrap', data, keyList);*/

		/*$('.page').prepend('<div class="info">每页10条，共'+len+'条</div>');
    		$("div.holder").jPages({
      			containerID  : "item",
	  		previous: "上一页",
      			next: "下一页",
      			perPage      : 10,
      			startPage    : 1,
      			startRange   : 1,
      			midRange     : 5,
      			endRange     : 1
    		});*/
	}
}
//查看所有问题
function updateCommonQuestionTable(page,name){

	if (typeof name ==='string') {
		AjaxGet('/Customer/Home/Question/'+addrFile+'/page/'+page+'/pageSize/'+pageSize+where+'/name/'+name, function(data){
	        		createCommonQuestionTable(data,page);
	    	});
	}else{
		AjaxGet('/Customer/Home/Question/'+addrFile+'/page/'+page+'/pageSize/'+pageSize+where, function(data){
			createCommonQuestionTable(data, page);
		});
	}
}

//点击分类
$('#wrap').on('click','a.jh',function(){
	var cid=$(this).attr('href');
	var text=$(this).text();
	var html=$('.pos').html();
	$('.pos').append('&nbsp;&gt;&nbsp;<a href="'+cid+'">'+ text+'</a>');

	where = '/is_common/1/cate_id/2-'+cid;
	addrFile = 'getComQuestionList';
	AjaxGet('/Customer/Home/Question/'+addrFile+'/page/1/pageSize/'+pageSize+where, function(data){
		createCommonQuestionTable(data,1);
	});
	return false;
})

//点击问题
$('#wrap').on('click','a.con',function(){
	var id=$(this).attr('href');
	var html=$('.pos').html();
	var text=$(this).text();
	if(html == '<a href="all" class="all">全部</a>' ){
		$('.pos').html('<a href="back" class="back">返回</a>');
	}else{
		$('.pos').append('&nbsp;&gt;&nbsp;'+ text);
	}
	addrFile  = 'getComQuestionList';

	AjaxGet('/Customer/Home/Question/'+addrFile+'/id/'+id, function(data){
		createContent(data);
		$('.pos a.back').on('click',function(){
			$('.pos').html('<a href="all" class="all">全部</a>')
			where = '/is_common/0/typical/1';
			AjaxGet('/Customer/Home/Question/'+addrFile+'/page/1/pageSize/'+pageSize+where, function(data){
				createCommonQuestionTable(data,1);
			});
			return false;
	     })
	});
	return false;
})

function createContent(data){
	var arr=data.retval.list[0];
	var html='';
	html+='<ul id="con"><li>'
	    +'<p>问题描述：'+arr.content+'</p>'
	if(arr.ask_attach!==false){
		html+='<p>附件：<a href="'+arr.reply_attach+'">查看附件</a></p>'
	}
	html+='<p>问题回复：'+arr.reply+'</p>';
	if(arr.reply_attach!==false){
		html+='<p>附件：<a href="'+arr.reply_attach+'">查看附件</a></p>'
	}
	html+='</li></ul>';
	$('#wrap_paginate').remove();
	$('#wrap').html(html);

	// $('.page').html('');
}
	//创建常见问题单分类列表
	function createCommonQuestionTypeTable(data,page){
	   // $(".page").html('<div class="holder"></div>');
		var arr=data.retval.list;
		var len=arr.length;
		if(len==0){
			$('#wrap').html('<ul><li>没有检测到数据</li></ul>')
		}else{
			var html='<ul id="wp">';
				for( var i=0; i<len; i++ ) {
					html+='<li>'
						+'<a href="'+arr[i].cate_id+'" class="jh">'+arr[i].cate_name+'</a>'
						+'</li>'
				}
			html+='</ul>';
			$('#wrap').html(html);
			//添加分页
			updatePagination(len, page, data.retval.count, 'wrap');
			/*listenCheckBox('#wrap');
		    	updateChecked('#wrap');*/
			/*$('.page').prepend('<div class="info">每页10条，共'+len+'条</div>');
			$("div.holder").jPages({
			  containerID  : "wp",
			  previous: "上一页",
			  next: "下一页",
			  perPage      : 10,
			  startPage    : 1,
			  startRange   : 1,
			  midRange     : 5,
			  endRange     : 1
			});*/
		}
	}
});
