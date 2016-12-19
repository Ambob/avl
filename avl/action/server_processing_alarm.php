<?php
/**
 * Created by PhpStorm.
 * User: dt.thxopen.com
 * Date: 2014/12/7
 * Time: 11:13
 */
header("Content-Type: text/html;charset=utf-8");
include_once('./step.php');
 
//获取Datatables发送的参数 必要
$draw = $_GET['draw'];//这个值作者会直接返回给前台
$start_time = $_GET['start_time'];
$end_time = $_GET['end_time'];
$excel_is = $_GET['excel'];
$only = $_GET['only'];
//排序
$order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
$order_dir = $_GET['order']['0']['dir'];//ase desc 升序或者降序
 
//拼接排序sql
$orderSql = "";
if(isset($order_column)){
    $i = intval($order_column);
    switch($i){
        case 0;$orderSql = " order by D_ID ".$order_dir;break;
        case 1;$orderSql = " order by Reg ".$order_dir;break;
        case 2;$orderSql = " order by data ".$order_dir;break;
        case 3;$orderSql = " order by sms ".$order_dir;break;
        case 4;$orderSql = " order by phone ".$order_dir;break;
        case 5;$orderSql = " order by sound ".$order_dir;break;
        case 6;$orderSql = " order by Start_Time ".$order_dir;break;
        case 7;$orderSql = " order by End_Time ".$order_dir;break;
        default;$orderSql = '';
    }
}
//搜索
$search = $_GET['search']['value'];//获取前台传过来的过滤条件
 
//分页
$start = $_GET['start'];//从多少开始
$length = $_GET['length'];//数据长度
$limitSql = '';
$limitFlag = isset($_GET['start']) && $length != -1 ;
if ($limitFlag ) {
    $limitSql = " LIMIT ".intval($start).", ".intval($length);
}
if($start_time){
    if($end_time){
        $sumSqlWhere =" where Start_Time>'".$start_time."' and Start_Time<'".$end_time."'";
    }
}else{
    if($only=='only'){
        $sumSqlWhere=' where End_Time=""';
    }else{
        $sumSqlWhere='';
    }
}
//定义查询数据总记录数sql
$sumSql = "SELECT count(ID) as sum FROM alarm ".$sumSqlWhere;
//条件过滤后记录数 必要
$recordsFiltered = 0;
//表的总记录数 必要
$recordsTotal = 0;
$recordsTotalResult = $db->get_row($sumSql);
if($recordsTotalResult){
    foreach ($recordsTotalResult as $key=>$value) {
        $recordsTotal =  $value;
    }
}
//定义过滤条件查询过滤后的记录数sql


if(strlen($search)>0){
    $recordsFilteredResult = $db->query($sumSql.$sumSqlWhere);
    while ($row = $recordsFilteredResult->fetchArray(SQLITE3_ASSOC)) {
        $recordsFiltered =  $row['sum'];
    }
}else{
    $recordsFiltered = $recordsTotal;
}
// (case sms when 1 then '已发送' else '未发送' end),(case phone when 1 then '已拨打' else '未拨打' end),(case sound when 1 then '已播放' else '未播放' end),
$totalResultSql = "SELECT D_ID,Reg,data,Start_Time,End_Time,contents FROM alarm";
$infos = array();

if(strlen($search)>0){
    //如果有搜索条件，按条件过滤找出记录
    $dataResult = $db->get_results($totalResultSql.$sumSqlWhere.$limitSql);
    if($dataResult){
        foreach ($dataResult as $data) {
            $obj = array();
            foreach ($data as $key => $value) {
                array_push($obj,$value);
            }
            array_push($infos,$obj);
        }
    }
}else{
    //直接查询所有记录
    $dataResult = $db->get_results($totalResultSql.$sumSqlWhere.' order by Start_Time desc'.$limitSql);
    if($dataResult){
        foreach ($dataResult as $data) {
            $obj = array();
            foreach ($data as $key => $value) {
                array_push($obj,$value);
            }
            array_push($infos,$obj);
        }
    }
}
/*
 * Output 包含的是必要的
 */
if($excel_is=='excel'){
    $url="../excel/excel_alarm.php?device_id=".$device_id."&sql=".$totalResultSql.$sumSqlWhere.$orderSql.$limitSql; 
    echo "<script LANGUAGE='Javascript'>"; 
    echo 'window.open("'.$url.'","_self")'; 
    echo "</script>"; 
}else{
    echo json_encode(array(
        "draw" => intval($draw),
        "recordsTotal" => intval($recordsTotal),
        "recordsFiltered" => intval($recordsFiltered),
        "data" => $infos
    ));
}
function fatal($msg)
{
    echo json_encode(array(
        "error" => $msg
    ));
    exit(0);
}
?>