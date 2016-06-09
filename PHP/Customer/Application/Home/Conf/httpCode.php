<?php
/**
 * http常用状态码
 * 参考百度：http://baike.baidu.com/link?url=pP91cfxsFuoObJQfxe33ipum-eKvHK1r0AwGCg5DTVZ7VyCUvhN-vpkVC9_LKQ0YAMKJMK4yo6sdjQTORPP2Rq
 */
return array(
    'SUCCESS' => 200, //表示成功请求并返回内容
    'NO_CONTENT' => 204, //表示成功请求，无内容返回

    'MOVED_PERMANENTLY' => 301, //永久重定向
    'MOVE_TEMPORARILY' => 302, //临时重定向
    'NOT_MODIFIED' => 304, 

    'BAD_REQUEST' => 400, //服务器无法解析请求。请求参数有误
    'UNAUTHORIZED' => 401, //未授权请求，无权限
    'FORBIDDEN' => 403, //服务器拒绝此请求
    'NOT_FOUND' => 404, //请求的资源不存在

    'INTERNAL_SERVER_ERROR' => 500, //服务器内部错误
    'NOT_IMPLEMENTED' => 501, //服务器不支持此功能
    'BAD_GATEWAY' => 502, //服务器网关错误，从上游获取到无效响应
    'SERVICE_UNAVAILABLE' => 503, //服务器维护或过载，无法处理请求
    'GATEWAY_TIMEOUT' => 504, //服务器网关超时，上游响应超时
    
    'UNKNOWN_ERROR'=>700, //未知错误
    
    'LOGIN_EXPIRED'=>800,//登录长时间无操作，重新登录
    'INVALID_ACCESS'=>801, //没有权限
    'FAIL'=>802
);