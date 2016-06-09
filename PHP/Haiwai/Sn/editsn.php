<?php 
/**
 * 修改sn
 * 需要 get 需要删除的id  需要添加的id   /editsn.php?del_id=XXXX&add_id=xxx
 */
include './ini/ini.php';
  if(isset($_GET['delete_id'])&&!empty($_GET['delete_id'])&&isset($_GET['add_id'])&&!empty($_GET['add_id'])){
    
    $admin = new Admin();
    $del_id = $_GET['delete_id'];
    $add_id = $_GET['add_id'];
    $i = $admin->editSn($del_id,$add_id);

    switch ($i) {
        case 1:
            result("修改失败!新账户:" . $add_id . " 已存在");
            break;
        case 2:
            result("修改失败!原账户:" . $del_id . "不存在");
            break;
        case 3:
            result();
            break;
    }
    
   }else{
    result('param');
   }

 ?>