<style type="text/css">
.tg  {padding:0; margin:0; border-collapse:collapse;border-spacing:0;}
.tg td{padding:0; margin:0; font-family:Arial, sans-serif;font-size:10px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg th{padding:0; margin:0; font-family:Arial, sans-serif;font-size:14px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg .tg-c3ow{padding:0; margin:0; border-color:inherit;text-align:center;vertical-align:top;font-size:10px;}
.tg .tg-4vfs{padding:0 0 0 3%; margin:0; border-color:inherit;text-align:left;vertical-align:top;font-size:10px;}
.tg .tg-uys7{padding:0; margin:0; border-color:inherit;text-align:center;font-size:10px;font-weight:bold;}
.tg .tg-xldj{padding:0 0 0 3%; margin:0; border-color:inherit;text-align:left;}
.tg .tg-0pky{padding:0 0 0 3%; margin:0; border-color:inherit;text-align:left;}
.tg .tg-6yth{padding:0; margin:0; text-align:center;border-color:inherit;}
.tg .tg-0lax{padding:0; margin:0; text-align:left; vertical-align:top;}
.noborder{
    padding:0; margin:0;
    border-style: none;
    border-width: 0px !important; 
}
h1, h3{
  text-align:left;
}
</style>

<!-- <img src="<?=base_url("assets/img/capture.png")?>" alt=""> -->
<?php if(!empty($beneficiary)){
        foreach($beneficiary as $bene){ ?>

<table class="tg" style="width: 100%; table-layout: fixed;" cellpadding="0">
<tr>
    <th class=" noborder " colspan ="3" rowspan="2"><img src="<?=base_url("assets/img/lbi.png")?>" height="80px" alt=""> </th>
    <th class=" noborder " colspan="19"><h3>LAND BANK OF THE PHILIPPINES</h3></th>
  </tr>
  <tr>
    <td class=" noborder tg-c3ow" colspan="19"><h1>LAND BANK CASH CARD/PREPAID CARD ENROLLMENT FORM</h1></td>
  </tr>
  <tr>
    <td class=" noborder " colspan="4">ID No.:</td>
    <td class=" noborder " colspan="12">Acct.No:</td>
    <td class=" noborder " colspan="6">Date:____________________________, 2018</td>
  </tr>
  <tr>
    <th class="tg-uys7" colspan="22">Please check the type of card being enrolled:</th>
  </tr>
  <tr>
    <td class="tg-xldj" style="font-size:12pt;" colspan="6">☒ Cash Card<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Individual<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☒ Institution</td>
    <td class="tg-0pky" style="font-size:12pt;" colspan="16">☐ Prepaid Card &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"/> Individual &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"/> Institution<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Regular (CUP/JCB/Master Card/Others, pls specify ___________)<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Gift Card (CUP/JCB/Master Card/Others, pls specify ___________)</td>
  </tr>
  <tr>
    <td class="tg-uys7" colspan="22"><span style="font-weight:bold">Purchaser's Information:</span></td>
  </tr>
  <tr>
    <td class="tg-0pky" height=40 colspan="14">Purchaser's Name:<br><span style="font-weight:bold; font-size:12pt;">DEPARTMENT OF SOCIAL WELFARE AND DEVELOPMENT</span></td>
    <td class="tg-0pky" height=40 colspan="8">With existing account with LBP   Yes ☐ No ☐<br>If yes, please specify Acct. No/s.: __________<br>Cash Card Number/s: ____________________</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">ADDITIONAL INFORMATION FOR WALK-IN INVIDUAL PURCHASER, AND CARDHOLDER OF INSTITUTIONAL PURCHASER:</td>
  </tr>
  <tr>
    <td class="tg-4vfs" height="40" colspan="18">Cardholder's Name (Applicable only for Gift Card purchased by an Institutional Customer):<br><br><div style="text-align:left; font-weight:bold; font-size:12pt;"><?=strtoupper($bene->lastname).", ".strtoupper($bene->firstname)." ".strtoupper($bene->middlename)?></td>
    <td class="tg-4vfs" height="40" colspan="4" rowspan="4"></td>
  </tr>
  <?php
    //get location $bene->barangay
    $address = $bene->address.",";
  ?>
  <tr>
    <td class="tg-4vfs" height="40" colspan="15">Permanent Address:<br><br><div style="text-align:left; font-weight:bold; font-size:12pt;"><?=strtoupper($address)?></div></td>
    <td class="tg-4vfs" height="40" colspan="3">Zip Code:<br><br/><div style="text-align:left; font-weight:bold; font-size:12pt;">3609</div></td>
  </tr>
  <tr>
    <td class="tg-4vfs" height="40" colspan="15">Present Address:<br><br><div style="text-align:left; font-weight:bold; font-size:12pt;"><?=strtoupper($address)?></div></td>
    <td class="tg-4vfs" height="40" colspan="3">Zip Code:<br/><br/><div style="text-align:left; font-weight:bold; font-size:12pt;">3609</div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="40" colspan="4">Place of Birth:<br><br></td>
    <td class="tg-c3ow" height="40" colspan="4">Date of Birth:<br><br><div style="font-weight:bold; font-size:12pt;"><?= date_format(date_create(date($bene->birthdate)),"F d, Y"); ?></div></td>
    <td class="tg-c3ow" height="40" colspan="4">Nationality: <br/><br/><div style="font-weight:bold; font-size:12pt;">	FILIPINO / 608 </td>
    <td class="tg-c3ow" height="40" colspan="6">Mother's Maiden Name:</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="40" colspan="5">Type of ID Presented:<br><br></td>
    <td class="tg-c3ow" height="40" colspan="6">ID Number Presented:<br><br></td>
    <td class="tg-c3ow" height="40" colspan="5">TIN Number:<br><br></td>
    <td class="tg-c3ow" height="40" colspan="6">Source of Fund:</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="40" colspan="11">Name of Employer/ Company/ Business/ School:<br><br/><span style="font-weight:bold; font-size:12pt;">DSWD CAR</span></td>
    <td class="tg-c3ow" height="40" colspan="11">Contact Number/s: (home/office/mobile)<br><br/><span style="font-weight:bold; font-size:12pt;">(074)-446-5961</span></td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">Cardholder's Information</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="40" colspan="11">Cardholder's Name:<br><br><div style="font-weight:bold; font-size:12pt;"><?=strtoupper($bene->lastname).", ".strtoupper($bene->firstname)." ".strtoupper($bene->middlename)?></td>
    <td class="tg-c3ow" height="40" colspan="4">Contact Number/s:<br><br/><div style="font-weight:bold; font-size:12pt;"><?=strtoupper($bene->contactno)?></td>
    <td class="tg-c3ow" height="40" colspan="7">Date of Birth:<br><br/><div style="font-weight:bold; font-size:12pt;"><?= date_format(date_create(date($bene->birthdate)),"F d, Y"); ?></div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="40" colspan="11">Permanent Address:<br><br><div style="font-weight:bold; font-size:12pt;"><?=strtoupper($address)?></div></td>
    <td class="tg-c3ow" height="40" colspan="4">Zip Code:<br/><br/><div style="font-weight:bold; font-size:12pt;">3609</div></td>
    <td class="tg-c3ow" height="40" colspan="7" rowspan="2">Relationship with the Purchaser:</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="40" colspan="11">Present Address:<br><br><div style="font-weight:bold; font-size:12pt;"><?=strtoupper($address)?></div></td>
    <td class="tg-c3ow" height="40" colspan="4">Zip Code:<br/><br/><div style="font-weight:bold; font-size:12pt;">3609</div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">Name to Appear on the Card (with maximum of 22 characters)</td>
  </tr>
  <?php
    $fnamecount  = strlen($bene->firstname);
    $lnamecount  = strlen($bene->lastname);
    
    if($lnamecount+$fnamecount<=22){
      $nameprint = strtoupper($bene->firstname)." ".strtoupper($bene->lastname);
      $nameprint = str_split($nameprint);
    }
    else{
      $fnamecount = 22-$lnamecount;
      $nameprint = strtoupper( rtrim($bene->firstname , substr($bene->firstname, strpos($bene->firstname, " "))) . " " . $bene->lastname );
      $nameprint = str_split($nameprint);
    }
  ?>
  <tr>
    <?php for($i = 0; $i <= 21; $i++){
      echo '<td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;">';
       if(!empty($nameprint[$i])){ echo $nameprint[$i]; };
      echo '</div></td>';
    } ?>
    <!-- <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php/* if(!empty($nameprint[0])){ echo $nameprint[0]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[1])){ echo $nameprint[1]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[2])){ echo $nameprint[2]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[3])){ echo $nameprint[3]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[4])){ echo $nameprint[4]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[5])){ echo $nameprint[5]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[6])){ echo $nameprint[6]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[7])){ echo $nameprint[7]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[8])){ echo $nameprint[8]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[9])){ echo $nameprint[9]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[10])){ echo $nameprint[10]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[11])){ echo $nameprint[11]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[12])){ echo $nameprint[12]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[13])){ echo $nameprint[13]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[14])){ echo $nameprint[14]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[15])){ echo $nameprint[15]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[16])){ echo $nameprint[16]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[17])){ echo $nameprint[17]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[18])){ echo $nameprint[18]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[19])){ echo $nameprint[19]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[20])){ echo $nameprint[20]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:12pt;"><?php if(!empty($nameprint[21])){ echo $nameprint[21]; }*/?></div></td> -->
  </tr>
  <tr>
    <td class="tg-0pky" colspan="11">Amount of Fees/ Charges to be paid:<br><div style="text-align:center; font-size:12pt;">&nbsp;&nbsp;&nbsp;P________________<br>(Initial Cost of the Card)</div></td>
    <td class="tg-0pky" colspan="11">Mode of Payment:<br><div style="font-size:12pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Cash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;☒ Debit from Account<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acct. No. ________________</div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="100" style="text-align:left !important;" colspan="22">I/We hereby that the above information is true and correct:<br>
    <br><br><br><br>
    <div style = "float:left;margin-left:100px;text-align:center">
    <br>____________________________________<br>Signature Over Printed Name<br>of Purchaser/ Applicant / Authorized Signatory<br>
    </div>
    <div style = "float:right;margin-right:100px;text-align:center">
    <br><u style="font-size:12pt;">ENRIQUE H. GASCON JR.</u><br>Signature Over Printed Name<br>of Purchaser/ Applicant / Authorized Signatory<br></td>
    </div>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">FOR BANK'S USE ONLY<br></td>
  </tr>
  <tr>
    <td  style="text-align:center;" class="tg-0pky" colspan="7">Processed by:<br><br><br>  ___________________________ <br> Customer Associate/NAC<br>  Date: ____________________</td>
    <td  style="text-align:center;" class="tg-0pky" colspan="8">Checked by:  <br><br><br>  ___________________________ <br> BOO/BSO<br>  Date: ____________________<br></td>
    <td  style="text-align:center;" class="tg-0pky" colspan="7">Approved by: <br><br><br>  ___________________________ <br> Branch Head<br>  Date: ____________________<br></td>
  </tr>
  <tr>
    <td class="tg-0pky noborder" colspan=22 style="font-size:6px">for Branches without BOO <br/> Validation Print</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22"><span style="font-weight:bold">CASH CARD/PREPAID CARD/PIN MAILER CLAIM STUB</span></td>
  </tr>
  <tr>
    <td  height="40" style="text-align:center;" class="tg-c3ow" colspan="5">Card Number:<br></td>
    <td  height="40" style="text-align:center;" class="tg-c3ow" colspan="6">Card Holder's Name:</td>
    <td  height="40" style="text-align:center;" class="tg-c3ow" colspan="5">Purchaser's Name:<br><br/><span style="font-weight:bold">DEPARTMENT OF SOCIAL WELFARE AND DEVELOPMENT</span></td>
    <td  height="40" style="text-align:center;" class="tg-c3ow" colspan="6">Date:<br><br/><span style="font-weight:bold; font-size:12pt;">______________, 2018</span></td>
  </tr>
  <tr>
    <td  style="text-align:center;" class="tg-0lax" colspan="5">Prepaid Card Released by:   <br><br><br>_______________________<br>Card Custodian<br>Date:______________________________</td>
    <td  style="text-align:center;" class="tg-0lax" colspan="6">PIN Mailer Released by:     <br><br><br>_______________________<br>PIN Mailer Custodian<br>  Date: ____________________<br></td>
    <td  style="text-align:center;" class="tg-0lax" colspan="5">Approved for Release:       <br><br><br>_______________________<br>Branch Head/BOO/BSO<br> Date: ____________________<br></td>
    <td  style="text-align:center;" class="tg-0lax" colspan="6">Card/PIN Mailer Received by:<br><br><br>_______________________<br>Signature Over Printed Name<br>of Purchaser/Cardholder<br></td>
  </tr>
  <tr>
    <td class="tg-0lax" colspan="22" style="font-size:9px">Reminders:<br>-You may claim your Prepaid Card after 7 banking days for Metro Manila Branches, and 15 banking days for Provincial Branches, and a replacement fee shall be collected <br>-Unclaimed Prepaid Card/PIN Mailer shall be perforated after 120 calendar days (for CCT)/30 calendar days (regular) from issuance/re-issuance <br>-Please sign your Prepaid Card immediately<br></td>
  </tr>
  <tr>
    <td class="tg-0pky noborder" colspan=22 style="font-size:6px">Validation Print (if debited from deposit account)</td>
  </tr>
</table>
<br/><br/><br/><br/>.<br/>
<?php
        }
      } ?>