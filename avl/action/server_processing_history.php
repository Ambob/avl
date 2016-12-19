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
$device_id = $_GET['device_id'];
$excel_is = $_GET['excel'];
$sql="select Read_register,Read_Reg_Name from device_para where Reg_display='1' and Name_ID=".$device_id;
$data=$db->get_results($sql);
$i=0;
foreach ($data as $key) {
    $sum=$sum."max(case modbus_id when '".$key->Read_register."' then D_Data else 0 end) '".$key->Read_Reg_Name."',";
    array_push($tableheader, $key->Read_Reg_Name);
    $i=$i+1;
}
$sum=rtrim($sum,",");
//排序
$order_column = $_GET['order']['0']['column'];//那一列排序，从0开始
$order_dir = $_GET['order']['0']['dir'];//ase desc 升序或者降序
 
//拼接排序sql
$orderSql = " order by Real_Time desc";
// if(isset($order_column)){
//     $i = intval($order_column);
//     switch($i){
//         case 0;$orderSql = " order by D_ID ".$order_dir;break;
//         case 1;$orderSql = " order by Reg ".$order_dir;break;
//         case 2;$orderSql = " order by data ".$order_dir;break;
//         case 3;$orderSql = " order by sms ".$order_dir;break;
//         case 4;$orderSql = " order by phone ".$order_dir;break;
//         case 5;$orderSql = " order by sound ".$order_dir;break;
//         case 6;$orderSql = " order by Start_Time ".$order_dir;break;
//         case 7;$orderSql = " order by End_Time ".$order_dir;break;
//         default;$orderSql = '';
//     }
// }
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
        $sumSqlWhere =" where Real_Time>'".$start_time."' and Real_Time<'".$end_time."'";
    }
}else{
    $sumSqlWhere='';
}
//定义查询数据总记录数sql
$sumSql = "SELECT count(ID)/".$i." as sum FROM data_min_".$device_id." ".$sumSqlWhere;
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
$totalResultSql = "SELECT Real_Time,".$sum." FROM data_min_".$device_id."";
$infos = array();
$obj = array();
if(strlen($search)>0){
    //如果有搜索条件，按条件过滤找出记录
    $dataResult = $db->get_results($totalResultSql.$sumSqlWhere." GROUP by Real_Time".$orderSql.$limitSql);
    if($dataResult){
        $obj = array();
        foreach ($dataResult as $data) {
            $obj = array($data->Real_Time,$data->ID,$data->Device_ID,$data->R_range,$data->U_unit);
            array_push($infos,$obj);
        }
    }
}else{
    //直接查询所有记录
    $dataResult = $db->get_results($totalResultSql.$sumSqlWhere." GROUP by Real_Time".$orderSql.$limitSql);
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
    $url="../excel/excel.php?device_id=".$device_id."&sql=".$totalResultSql.$sumSqlWhere." GROUP by Real_Time".$orderSql.$limitSql; 
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