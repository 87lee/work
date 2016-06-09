
// 直播系统_添加直播授权
//     	 * post /Live/addLiveAuth
//     	 * {
//     	 * 	"model":"型号",
//     	 * 	"num":"数量"
//     	 * }
// 直播系统_修改直播授权设置
//     	 * post /Live/modifyLiveAuth
//     	 * {
//     	 * 	"id":"直播授权ID",
//     	 * 	"model":"厂商",
//     	 * 	"num":"数量"
//     	 * }
// 直播系统_删除直播授权设置
//     	 * post /Live/deleteLiveAuth
//     	 * ["id1","id2"]
// 直播系统_直播授权列表
//     	 * get /Live/liveAuthLists?page=x&pageSize=x&name=x