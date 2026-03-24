<?php
 //EVENTI DEGLI AMICI
 $queryeventi = "SELECT * FROM `bx_events_main` WHERE (((`ResponsibleID`=";
 for ($sfoglia=0;$sfoglia<$contaamici;$sfoglia++)
 {
    if ($sfoglia<($contaamici-1))
	{
	 $queryeventi=$queryeventi . $amico[$sfoglia] . " OR `ResponsibleID`=";
	}
	else
	{
	 $queryeventi=$queryeventi . $amico[$sfoglia] . " OR `ResponsibleID`=".$userid;
	}
 } 

 $queryeventi=$queryeventi . ") AND `Status`='approved') 
 AND 
 (
  (EventStart-".(time()+$deltatime).")>0
 OR 
  (
  (EventEnd-".(time()+$deltatime).")>0 and (EventStart-".(time()+$deltatime).")<0
  )
 )
 ) 
 AND 
 (
   (EventStart-".(time()+$deltatime).")<". $maxdaysevent*86400 ."
 )
 ORDER BY EventStart ASC LIMIT " . $numeventmax;
 $resulteventi = mysql_query($queryeventi);
 $contaeventi = mysql_num_rows($resulteventi);

 if ($contaeventi>0)
 {
  echo '<div class="rhegionmenuelement3">';
 // echo '<div class="rigamenumodificadx"><div class="titleeventirec"><i class="sys-icon calendar"></i>'._t('_ibdw_thirdcolumn_event').'</div></div>';
   echo '<div class="rigamenumodificadx"><div class="titleeventirec">'._t('_ibdw_thirdcolumn_event').'</div></div>';

  for($acca=0;$acca<$contaeventi;$acca++)
  {
   $evento=mysql_fetch_array($resulteventi);
   echo '<div class="rigamenuev"><a href="m/events/view/' . $evento[2] . '">' . $evento[1] . '</a>';
   echo '<div class="eventino">(' . convertiDataTime($evento['EventStart'],$dateFormatC) . ')</div></div>';
  }
  echo '</div>';
 }
?>