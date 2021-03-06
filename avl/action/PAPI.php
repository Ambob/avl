<?php
define("XH_PARAM_INT",0);
define("XH_PARAM_TXT",1);
function PAPI_GetSafeParam($pi_strName, $pi_Def = "", $pi_iType = XH_PARAM_TXT)
{
  if ( isset($_GET[$pi_strName]) )
    $t_Val = trim($_GET[$pi_strName]);
  else if ( isset($_POST[$pi_strName]))
    $t_Val = trim($_POST[$pi_strName]);
  else
    return $pi_Def;
 
  // INT
  if ( XH_PARAM_INT == $pi_iType)
  {
    if (is_numeric($t_Val))
      return $t_Val;
    else
      return $pi_Def;
  }
  
  // String
  $t_Val = str_replace("&", "&amp;",$t_Val);
  $t_Val = str_replace("<", "&lt;",$t_Val);
  $t_Val = str_replace(">", "&gt;",$t_Val);
  if ( get_magic_quotes_gpc() )
  {
    $t_Val = str_replace("\\\"", "&quot;",$t_Val);
    $t_Val = str_replace("\\''", "&#039;",$t_Val);
  }
  else
  {
    $t_Val = str_replace("\"", "&quot;",$t_Val);
    $t_Val = str_replace("'", "&#039;",$t_Val);
  }
  return $t_Val;
}