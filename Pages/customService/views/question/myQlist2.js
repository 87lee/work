//@ sourceURL=question.myQlist2.js
var myData = {};
var pageSize = 10; //自定义分页，每页显示的数据量
var currentPage = 1; //应用列表当前的页面
var appPage = 1; //应用当前的页面
var ID = window.localStorage.getItem("CUSTOM_PERMISSION_ID");
var POWER = window.localStorage.getItem("CUSTOM_PERMISSION_USEPOWER");
var NAME = window.localStorage.getItem("CUSTOM_PERMISSION_USERNAME");
var where = '';
var interface = '';

$(function () {
        initTopMenu();
	myData.checkedLists = [];   //应用列表存储check选中项
	myData.checkedItems = [];	//应用存储check选中项
	//客户单问题分类
	interface = 'getCategory';
	where = '/is_common/0/';
	AjaxGet('/Customer/Home/Question/'+interface+where, function(data){
	createSelectType(data);
        	btnClick();
    });
	//未解决问题
	interface = 'getQuestionList';
	where = '/status/0-1/asker_id/'+ID;
	AjaxGet('/Customer/Home/Question/'+interface+'/page/'+currentPage+'/pageSize/'+pageSize+where, function(data){
        	createNotSlove(data, 1);
        	btnClick();
    });
    	//监听分页
    	listenMyPage('all', currentPage, updateAll);
    	listenMyPage('sloved', currentPage, updateSloved/*, {'venid':''}, orderTableVenid*/);
    	listenMyPage('notSlove', currentPage, updateNotSlove);

	listenTab(function(str){
		//防止回调函数&方法重名导致的重复请求，临时解决
		if(window.location.hash.split('#')[1] !== 'question/myQlist2'){
			return false;
		}
		$('.dataTables_filter input').val('');
	currentPage = 1;
    	if(str === '未解决'){
    		interface = 'getQuestionList';
    		where = '/status/0-1/asker_id/'+ID;
		AjaxGet('/Customer/Home/Question/'+interface+'/page/'+currentPage+'/pageSize/'+pageSize+where, function(data){
			createNotSlove(data, 1);
			btnClick();
		});
		myData.checkedLists = [];
    		$('.tab-list').hide();
    		$('.tab-list:eq(0)').show();
    	}else if(str === '已解决'){
			myData.checkedLists = [];
			interface = 'getQuestionList';
    			where = '/status/2/asker_id/'+ID;
			AjaxGet('/Customer/Home/Question/'+interface+'/page/'+currentPage+'/pageSize/'+pageSize+where, function(data){
				createSloved(data, 1);
				btnClick();
			});
    		$('.tab-list').hide();
    		$('.tab-list:eq(1)').show();
    	}else if(str === '全部问题单'){
			myData.checkedLists = [];
			interface = 'getQuestionList';
    			where = '/asker_id/'+ID;

			AjaxGet('/Customer/Home/Question/'+interface+'/page/'+currentPage+'/pageSize/'+pageSize+where, function(data){
				createAll(data, 1);
				btnClick();
		});
    		$('.tab-list').hide();
    		$('.tab-list:eq(2)').show();
    	}
    });

function btnClick(){
    //查看问题单btn-more
	$(".btn-more").on('click',function(){
		if ($(this).text() === '查看更多') {
			$(this).parents('li').find('.jh').removeClass('ellipsis');
		}else{
			$(this).parents('li').find('.jh').addClass('ellipsis');
		}
		var $parents=$(this).parents('li');
		var id=$parents.find('.jh').attr('href');
		var $that=$(this);
		var text=$(this).text();
		$parents.find('.cont').html('')
		if(text == ' 查看更多'){
			AjaxGet('/Customer/Home/Question/getAppend/id/'+id, function(data){
				if(data.retval[id] && data.retval[id].length>0){
					var arr=data.retval[id];
					var len=arr.length;
					var html='';
					for(var i=0;i<len;i++){
						if(data.retval[id][i].type==1){
						   html+='<p>追加提问：'+arr[i].content+'</p>'
						   //alert(arr.append_attach);
						   if(arr[i].append_attach!=false){
							   var l=arr[i].append_attach.length;
							   html+='<p>查看附件：';
							   for(var j=0;j<l;j++){
							   html+='<a href="'+hostUrl+'url='+arr[i].append_attach[j].replace('Upload/Question/','')+'">查看附件</a>&nbsp;&nbsp;';
							   }
							   html+='</p>';
						   }
						}else{
						   html+='<p>'+arr[i].reply_name+' 回复：'+arr[i].content+'</p>'
						   if(arr[i].append_attach!=false){
							   var l=arr[i].append_attach.length;
							   html+='<p>查看附件：';
							   for(var j=0;j<l;j++){
							   html+='<a href="'+hostUrl+'url='+arr[i].append_attach[j].replace('Upload/Question/','')+'" >查看附件</a>&nbsp;&nbsp;';
							   }
							   html+='</p>';
						   }
						}
					}
				}
				$parents.find('.cont').append(html);
				$parents.find('.h-cont').show();
			});
			  $that.html('<i class="fa fa-chevron-up"></i> 收起');
		}else{
			$parents.find('.h-cont').hide();
			$that.html('<i class="fa fa-bars"></i> 查看更多');
		}
		return false;
	});
	//评论
	$('.btn-comment').on('click',function(){
		var $parents=$(this).parents('li');
		var $comment=$(this).parent().siblings('.comment')
		//$parents.find('input').trigger('click')
		if($comment.is(':hidden')){
			$comment.slideDown();
		}else{
			$comment.slideUp();
		}
		return false;


	})
	//选择评分分数
	$('.comment .glyphicon-star').click(function(){
	    $(this).addClass('selected').siblings().addClass('selected').end().nextAll().removeClass('selected');
		var len=$('.glyphicon-star').hasClass('selected').length
		return false;
	});
	//发表评论
	$('.subCom').on('click',function(){
		var $parents=$(this).parents('li');
		var qid=$parents.find('.jh').attr('href');
		var con=$parents.find('textarea.experience').val();
		var score=$parents.find('span.selected').length;
		if(con=="" || !con){
			alert('请输入评论');
			return false;
		}
		var dataObj={
			"question_id":qid,
			"content":con,
			"score":score
		};
		$.ajax({
			   type:"POST",
			   url:"/Customer/Home/Question/questionComment",
			   data:dataObj,
			   success:function(){
				   updateOne();
			   }
	     });
	 		return false;
	})
	//追加提问
	$('.btn-add').on('click',function(){
		var $parents=$(this).parents('li');
		var $question=$(this).parent().siblings('.add-question')
		//$parents.find('input').trigger('click')
		if($question.is(':hidden')){
			$question.slideDown();
		}else{
			$question.slideUp();
		}
		return false;
	})
	//提交追加提问
    $('.subAddQ').on('click',function(){
	    var $parents=$(this).parents('li');
		var qid=$parents.find('.jh').attr('href');
		var con=$parents.find('textarea.add-con').val();

		var attach=$parents.find('input.ask-attach')[0].files;

	    var data = new FormData();
		var len=attach.length;

		if(con=="" || !con){
			alert('请输入追加提问内容');
			return false;
		}

		data.append("q_id",qid);
		data.append("content", con);
		for(var i=0;i<len;i++){
		   data.append("append_attach[]", attach[i]);
		}
		AjaxFile('/Customer/Home/Question/askAppendQuestion', data, function(){
			 updateOne()
		});

		return false;
       });
}

function updateOne(){
   var str=$('#breadcrumb span.active').text();
   if(str=="未解决"){
	interface = 'getQuestionList';
    	where = '/status/0-1/asker_id/'+ID;
	   updateNotSlove(1);
   }else if(str=="已解决"){
   	interface = 'getQuestionList';
    	where = '/status/2/asker_id/'+ID;
	   updateSloved(1);
   }else if(str=="全部问题单"){
   	interface = 'getQuestionList';
    	where = '/asker_id/'+ID;
	   updateAll(1);
   }

};
//更新未解决问题单
function updateNotSlove(page,object){
	if (typeof object ==='object') {
		var filter = '';
		for (var i in object) {
			filter += '/'+i + '/'+object[i];
		}
		AjaxGet('/Customer/Home/Question/'+interface+'/page/'+page+'/pageSize/'+pageSize+where+filter, function(data){
			createNotSlove(data, page);
			btnClick();
		});
	}else{
		AjaxGet('/Customer/Home/Question/'+interface+'/page/'+page+'/pageSize/'+pageSize+where, function(data){
			createNotSlove(data, page);
			btnClick();
		});
	}
}
//更新已解决问题单
function updateSloved(page,object){
	if (typeof object ==='object') {
		var filter = '';
		for (var i in object) {
			filter += '/'+i + '/'+object[i];
		}
		AjaxGet('/Customer/Home/Question/'+interface+'/page/'+page+'/pageSize/'+pageSize+where+filter, function(data){
			createSloved(data, page);
			btnClick();
		});
	}else{
		AjaxGet('/Customer/Home/Question/'+interface+'/page/'+page+'/pageSize/'+pageSize+where, function(data){
			createSloved(data, page);
			btnClick();
		});
	}
}
//更新全部问题单
function updateAll(page,object){
	if (typeof object ==='object') {
		var filter = '';
		for (var i in object) {
			filter += '/'+i + '/'+object[i];
		}

		AjaxGet('/Customer/Home/Question/'+interface+'/page/'+page+'/pageSize/'+pageSize+where+filter, function(data){
			createAll(data, page);
			btnClick();
		});
	}else{
		AjaxGet('/Customer/Home/Question/'+interface+'/page/'+page+'/pageSize/'+pageSize+where, function(data){
			createAll(data, page);
			btnClick();
		});
	}

}

//生成选择选择分类下拉列表
function createSelectType(data){
	var arr = data.retval.list
    var con = '<option value="">请选择分类</option>';
    var $select = $('.type');
    var len = arr.length;
    for (var i = 0; i < len; i++) {
        con += '<option value="' + arr[i].cate_id + '">' + arr[i].cate_name + '</option>';
    }
    $select.html(con).trigger("chosen:updated.chosen").chosen({
        allow_single_deselect: true,
        width: "220px"
    });
}

//未解决问题单分类改变
$('#notSloveType').on('change', function(e, name) {
    var cid = $(this).val();
    	interface = 'getQuestionList';
    	where = '/status/0-1/cate_id/1-'+cid+'/asker_id/'+ID;
	AjaxGet('/Customer/Home/Question/'+interface+'/page/1/pageSize/'+pageSize+where, function(data){
		createNotSlove(data,1);
		btnClick();
	});

});
//解决问题单分类改变
$('#slovedType').on('change', function(e, name) {
    var cid = $(this).val();
    	interface = 'getQuestionList';
    	where = '/status/2/cate_id/1-'+cid+'/asker_id/'+ID;
	AjaxGet('/Customer/Home/Question/'+interface+'/page/1/pageSize/'+pageSize+where, function(data){
		createSloved(data,1);
		btnClick();
	});

});
//全部问题单分类改变
$('#allType').on('change', function(e, name) {
    var cid = $(this).val();
    	interface = 'getQuestionList';
    	where = '/cate_id/1-'+cid+'/asker_id/'+ID;
	AjaxGet('/Customer/Home/Question/'+interface+'/page/1/pageSize/'+pageSize+where, function(data){
		createAll(data,1);
		btnClick();
	});
});


//查看问题单
function seeMoreQuestion(){
	$('#seeMoreQuestionDesc').val('问题描述问题描述');
	$('#seeMoreQuestionModal').modal('show');
}
//生成未解决问题单列表
function createNotSlove(data, page){

	var arr = data.retval.list;
    	var len = arr.length;
		var con='<div class="pull-right">'
	      +'  <a class="btn btn-default btn-more" href="#"><i class="fa fa-bars"></i> 查看更多</a>'
		  +'  <a class="btn btn-default btn-add" href="javascript:" role="button"><i class="fa fa-share"></i> 追加提问</a>'
		  +'</div>'
          +'<div class="add-question">'
          +' <form>'
          +'   <div class="form-group">'
          +'     <label class="add-con" for="question">问题描述:</label>'
          +'     <textarea   class="add-con" placeholder=""></textarea>'
          +'   </div>'
          +'   <div class="form-group">'
          +'     <label  for="ask-attach" class="ask-attach">添加附件:</label>'
          +'     <input  type="file" class="ask-attach" name="ask_attach[]"  multiple="multiple"  style="display: inline-block;width: 180px;">'
          +'   </div>'
          +'   <button type="botton" class="btn btn-default subAddQ">提交</button>'
          +'  </form>'
          +'</div>';


	if(len==0){
		$('#notSlove').html('<ul><li>没有检测到数据</li></ul>')
	}else{

        var html='<ul id="wrapNotSlove">';
		for( var i=0; i<len; i++ ) {
           			arr[i].ask_time=formatDate(arr[i].ask_time);
           			// arr[i].ask_time=d.format("yyyy-MM-dd HH:mm:ss");
					html+='<li><div style="float:left;"><label class="position-relative">'
						+'<input type="checkbox" class="ace">'
						+'<span class="lbl"></span></label></div>'
						+'<div >'
				        +'    <p>'+/*arr[i].asker_name+*/'&nbsp;&nbsp;'+arr[i].ask_time+'</p>'
						+'    <p style="margin-left: 31px;"><a href="'+arr[i].id+'" class="jh ellipsis" onclick="javascript:return false;" style = "word-wrap:break-word; word-break:normal;">'+ arr[i].content +'</a></p>'
						+'    <div class="h-cont" style="margin-left: 31px;">'
			  if(arr[i].ask_attach!=false){
				  html+='  <p>附件：';
				  var l=arr[i].ask_attach.length;
				  for(var j=0;j<l;j++){
					  html+='<a href="'+hostUrl+'url='+arr[i].ask_attach[j].replace('Upload/Question/','')+'" >查看附件</a>&nbsp;&nbsp;';
				  }
				  html+='</p>';
			  }
			  if(arr[i].reply!==""){
				  html+='   <p>'+arr[i].reply_name+' 回复：'+arr[i].reply+'</p>';
			  }
			  if(arr[i].reply_attach!=false){
				  var l=arr[i].reply_attach.length;
				  html+='  <p>附件：';
				  for(var j=0;j<l;j++){
					  html+='   <a href="'+hostUrl+'url='+arr[i].reply_attach[j].replace('Upload/Question/','')+'" >查看附件</a>&nbsp;&nbsp;';
				  }
				  html+='</p>'
			  }

					html+='        <div class="cont"></div>'
						+'    </div>'
						+'</div>'
						+con
						+'</li>';

		 }
		html+='</ul>';
		$('#notSlove').html(html);

		updatePagination(len, page, data.retval.count, 'notSlove');
		listenCheckBox('#notSlove');
	    	updateChecked('#notSlove');

	    	var keyList = {
			"venid": '1',
	        		"pform": '2',
	        		"firmv": '3',
	        		"cust": '5',
	        		"puber": '6',
	        		"time": '7'
	   	};
	    	orderTab('#notSlove', data, keyList);

		/*$('.page1').prepend('<div class="info">每页10条，共'+len+'条</div>');
		$("div.holder1").jPages({
		  containerID  : "wrapNotSlove",
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
//已经解决问题单列表
function createSloved(data,page){
	// $(".page2").html('<div class="holder holder2"></div>');
	var arr = data.retval.list;
    var len = arr.length;
		var con='<div class="pull-right">'
	      +'  <a class="btn btn-default btn-more" href="#"><i class="fa fa-bars"></i> 查看更多</a>'
		  +'  <a class="btn btn-default btn-comment" href="javascript:" role="button"><i class="fa fa-tree"></i> 评价</a>'
		  +'  <a class="btn btn-default btn-add" href="javascript:" role="button"><i class="fa fa-share"></i> 追加提问</a>'
		  +'</div>'
          +'<div class="comment">'
          +' <form>'
          +'   <div class="form-group">评分： '
		  +'      <span class="glyphicon glyphicon-star selected"></span> <span class="glyphicon glyphicon-star selected"></span> <span class="glyphicon glyphicon-star"></span> <span class="glyphicon glyphicon-star"></span> <span class="glyphicon glyphicon-star"></span> '
		  +'   </div>'
          +'   <div class="form-group">'
          +'     <label class="experience" for="experience">心得:</label>'
          +'     <textarea   class="experience" placeholder=""></textarea>'
          +'   </div>'
          +'   <button type="submit" class="btn btn-default subCom">发表评论</button>'
          +'  </form>'
          +'</div>'
          +'<div class="add-question">'
          +' <form>'
          +'   <div class="form-group">'
          +'     <label class="add-con" for="question">问题描述:</label>'
          +'     <textarea   class="add-con" placeholder=""></textarea>'
          +'   </div>'
          +'   <div class="form-group">'
          +'     <label  for="ask-attach" class="ask-attach">添加附件:</label>'
          +'     <input  type="file" class="ask-attach" name="ask_attach[]"  multiple="multiple"  style="display: inline-block;width: 180px;">'
          +'   </div>'
          +'   <button type="botton" class="btn btn-default subAddQ">提交</button>'
          +'  </form>'
          +'</div>';
	if(len==0){
		$('#sloved').html('<ul><li>没有检测到数据</li></ul>')
	}else{
        var html='<ul id="wrapSloved">';
		for( var i=0; i<len; i++ ) {
			arr[i].ask_time=formatDate(arr[i].ask_time);
		  html+='<li><div style="float:left;"><label class="position-relative">'
			  +'<input type="checkbox" class="ace">'
			  +'<span class="lbl"></span></label></div>'
			  +'<div >'
					  +'    <p>'+/*arr[i].asker_name+*/'&nbsp;&nbsp;'+arr[i].ask_time+'</p>'
			  +'    <p style="margin-left: 31px;"><a href="'+arr[i].id+'" class="jh ellipsis" onclick="javascript:return false;" style = "word-wrap:break-word; word-break:normal;">'+arr[i].content+'</a></p>'
			  +'    <div class="h-cont" style="margin-left: 31px;">'
			if(arr[i].ask_attach!=false){
				html+='  <p>附件：';
				var l=arr[i].ask_attach.length;
				for(var j=0;j<l;j++){
					html+='<a href="'+hostUrl+'url='+arr[i].ask_attach[j].replace('Upload/Question/','')+'" >查看附件</a>&nbsp;&nbsp;';
				}
				html+='</p>';
			}
			if(arr[i].reply!==""){
				html+='   <p>'+arr[i].reply_name+' 回复：'+arr[i].reply+'</p>';
			}
			if(arr[i].reply_attach!=false){
				var l=arr[i].reply_attach.length;
				html+='  <p>附件：';
				for(var j=0;j<l;j++){
					html+='   <a href="'+hostUrl+'url='+arr[i].reply_attach[j].replace('Upload/Question/','')+'" >查看附件</a>&nbsp;&nbsp;';
				}
				html+='</p>';
			}
			html+='        <div class="cont"></div>'
				+'    </div>'
				+'</div>'
				+con
				+'</li>';

		}
		html+='</ul>';
		$('#sloved').html(html);
		updatePagination(len, page, data.retval.count, 'sloved');
		listenCheckBox('#sloved');
	    	updateChecked('#sloved');

	    	var keyList = {
			"venid": '1',
	        		"pform": '2',
	        		"firmv": '3',
	        		"cust": '5',
	        		"puber": '6',
	        		"time": '7'
	   	};
	    	orderTab('#sloved', data, keyList);

		/*$('.page2').prepend('<div class="info">每页10条，共'+len+'条</div>');
	    $("div.holder2").jPages({
		      containerID  : "wrapSloved",
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
//全部问题单列表
function createAll(data,page){
	// $(".page3").html('<div class="holder holder3"></div>');
	var arr = data.retval.list;
    var len = arr.length;
	var con ='<div class="pull-right">'
	  		+'  <a class="btn btn-default btn-more" href="#"><i class="fa fa-bars"></i> 查看更多</a>';
	var con1 =   	'  <a class="btn btn-default btn-comment" href="javascript:" role="button"><i class="fa fa-tree"></i> 评价</a>';
	var con2 = '  <a class="btn btn-default btn-add" href="javascript:" role="button"><i class="fa fa-share"></i> 追加提问</a>'
	  	+'</div>'
	  			+'<div class="comment">'
	  			+' <form>'
	  			+'   <div class="form-group">评分：'
	  	+'      <span class="glyphicon glyphicon-star selected"></span> <span class="glyphicon glyphicon-star selected"></span> <span class="glyphicon glyphicon-star"></span> <span class="glyphicon glyphicon-star"></span> <span class="glyphicon glyphicon-star"></span> '
	  	+'   </div>'
	  			+'   <div class="form-group">'
	  			+'     <label class="experience" for="experience">心得:</label>'
	  			+'     <textarea   class="experience" placeholder=""></textarea>'
	  			+'   </div>'
	  			+'   <button type="submit" class="btn btn-default subCom">发表评论</button>'
	  			+'  </form>'
	  			+'</div>'
	  			+'<div class="add-question">'
	  			+' <form>'
	  			+'   <div class="form-group">'
	  			+'     <label class="add-con" for="question">问题描述:</label>'
	  			+'     <textarea   class="add-con" placeholder=""></textarea>'
	  			+'   </div>'
	  			+'   <div class="form-group">'
	  			+'     <label  for="ask-attach" class="ask-attach">添加附件:</label>'
	  			+'     <input  type="file" class="ask-attach" name="ask_attach[]"  multiple="multiple"  style="display: inline-block;width: 180px;">'
	  			+'   </div>'
	  			+'   <button type="botton" class="btn btn-default subAddQ">提交</button>'
	  			+'  </form>'
	  			+'</div>';

	if(len==0){
		$('#all').html('<ul><li>没有检测到数据</li></ul>')
	}else{
        var html='<ul id="wrapAll">';
		for( var i=0; i<len; i++ ) {
		arr[i].ask_time=formatDate(arr[i].ask_time);
			html+='<li><div style="float:left;"><label class="position-relative">'
                +'<input type="checkbox" class="ace">'
                +'<span class="lbl"></span></label></div>'
				+'<div >'
				+'    <p>'+/*arr[i].asker_name+*/'&nbsp;&nbsp;'+arr[i].ask_time+'</p>'
			    +'    <p style="margin-left: 31px;"><a href="'+arr[i].id+'" class="jh ellipsis" onclick="javascript:return false;" style = "word-wrap:break-word; word-break:normal;">'+arr[i].content+'</a></p>'
				+'    <div class="h-cont" style="margin-left: 31px;">';
		if(arr[i].ask_attach!=false){
			html+='  <p>附件：';
			var l=arr[i].ask_attach.length;
			for(var j=0;j<l;j++){
				html+='<a href="'+hostUrl+'url='+arr[i].ask_attach[j].replace('Upload/Question/','')+'" >查看附件</a>&nbsp;&nbsp;';
			}
			html+='</p>';
		}
		if(arr[i].reply!==""){
			html+='   <p>'+arr[i].reply_name+' 回复：'+arr[i].reply+'</p>';
		}
		if(arr[i].reply_attach!=false){
			var l=arr[i].reply_attach.length;
			html+='  <p>附件：';
			for(var j=0;j<l;j++){
				html+='   <a href="'+hostUrl+'url='+arr[i].reply_attach[j].replace('Upload/Question/','')+'" >查看附件</a>&nbsp;&nbsp;';
			}
			html+='</p>';
		}
			html+='        <div class="cont"></div>'
				+'    </div>'
				+'</div>'
				+con+(arr[i].status  == '2'?con1:'') +con2
				+'</li>';
		}
		html+='</ul>';
		$('#all').html(html);
		updatePagination(len, page, data.retval.count, 'all');
		listenCheckBox('#all');
	    	updateChecked('#all');

	    	var keyList = {
			"venid": '1',
	        		"pform": '2',
	        		"firmv": '3',
	        		"cust": '5',
	        		"puber": '6',
	        		"time": '7'
	   	};
	    	orderTab('#all', data, keyList);

		/*$('.page3').prepend('<div class="info">每页10条，共'+len+'条</div>');
		$("div.holder3").jPages({
		  containerID  : "wrapAll",
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

/*function formatDate(now) {
	var year=now.getFullYear();
	var month=now.getMonth()+1;
	var date=now.getDate();
	var hour=now.getHours();
	var minute=now.getMinutes();
	var second=now.getSeconds();
	return year+"-"+( month > 9 ? month : '0'+month )+"-"+( date > 9 ? date : '0'+date )+" "+ ( hour > 9 ? hour : '0'+hour ) +":"+ ( minute > 9 ? minute : '0'+minute ) +':'+  ( second > 9 ? second : '0'+second ) ;
}*/



});


