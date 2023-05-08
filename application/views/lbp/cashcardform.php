<style type="text/css">
.tg  {padding:0; margin:0; border-collapse:collapse;border-spacing:0;}
.tg td{padding:0; margin:0; font-family:Arial, sans-serif;font-size:12px;padding:12px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg th{padding:0; margin:0; font-family:Arial, sans-serif;font-size:12px;font-weight:normal;padding:12px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg .tg-c3ow{padding:0; margin:0; border-color:inherit;text-align:center;vertical-align:top;font-size:12px;}
.tg .tg-4vfs{padding:0 0 0 1%; margin:0; border-color:inherit;text-align:left;vertical-align:top;font-size:12px;}
.tg .tg-uys7{padding:0; margin:0; border-color:inherit;text-align:center;font-size:12px;font-weight:bold;}
.tg .tg-xldj{padding:0 0 0 1%; margin:0; border-color:inherit;text-align:left;}
.tg .tg-0pky{padding:0 0 0 1%; margin:0; border-color:inherit;text-align:left;}
.tg .tg-6yth{padding:0; margin:0; text-align:center;border-color:inherit;}
.tg .tg-0lax{padding:0; margin:0; text-align:left; vertical-align:top;}
.noborder{
    padding:0; margin:0;
    border-style: none;
    border-width: 0px !important; 
}
</style>
<?php if(!empty($beneficiary)){
        foreach($beneficiary as $bene){ ?>
<table class="tg" style="width: 100%; table-layout: fixed;" cellpadding="0">
  <tr>
    <td class=" noborder tg-4vfs " colspan ="2" rowspan="2"><img src="<?=server_path?>/assets/img/lbi.png" height="60px" alt=""> </td>
    <!-- <td class=" noborder tg-4vfs " colspan ="3" rowspan="2"><img src="<?=base_url("assets/img/lbi.png")?>" height="90px" alt=""> </td> -->
    <td class=" noborder tg-4vfs " colspan="20"><div style="font-size:16px; font-weight:bold">LAND BANK OF THE PHILIPPINES<br/><span style="font-size:10px; color:red;">LBP BAGUIO</span></div><br/></td>
  </tr>
  <tr>
    <td class=" noborder tg-4vfs" colspan="20"><div style="font-size:20px; font-weight:bold">LAND BANK CASH CARD/PREPAID CARD ENROLLMENT FORM</div></td>
  </tr>
  <tr><td height=10 class="noborder tg-4vfs"></td></tr>
  <tr>
    <td class=" noborder tg-4vfs " colspan="4">ID No.:</td>
    <td class=" noborder tg-4vfs " colspan="10">Acct.No:</td>
    <td class=" noborder tg-4vfs " colspan="8">Date:____________________________, 2019</td>
  </tr>
  <tr>
    <th class="tg-uys7" colspan="22">Please check the type of card being enrolled:</th>
  </tr>
  <tr>
    <td class="tg-xldj" style="font-size:12px;" colspan="6"> Cash Card<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Individual<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Institution</td>
    <td class="tg-0pky" style="font-size:12px;" colspan="16">☐ Prepaid Card &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"/> Individual &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"/> Institution<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Regular (CUP/JCB/Master Card/Others, pls specify ___________)<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Gift Card (CUP/JCB/Master Card/Others, pls specify ___________)</td>
  </tr>
  <tr>
    <td class="tg-uys7" colspan="22"><span style="font-weight:bold">Purchaser's Information:</span></td>
  </tr>
  <tr>
    <td class="tg-0pky" height=30 colspan="13">Purchaser's Name:<br><span style="font-weight:bold; font-size:12px;">DEPARTMENT OF SOCIAL WELFARE AND DEVELOPMENT</span></td>
    <td class="tg-0pky" height=30 colspan="9">With existing account with LBP   Yes ☐ No ☐<br>If yes, please specify Acct. No/s.: __________<br>Cash Card Number/s: ____________________</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">ADDITIONAL INFORMATION FOR WALK-IN INVIDUAL PURCHASER, AND CARDHOLDER OF INSTITUTIONAL PURCHASER:</td>
  </tr>
  <tr>
    <td class="tg-4vfs" height="25" colspan="18">Cardholder's Name (Applicable only for Gift Card purchased by an Institutional Customer):<br><div style="text-align:left; font-weight:bold; font-size:16px;"><?=strtoupper($bene->lastname).", ".strtoupper($bene->firstname)." ".strtoupper($bene->middlename)?><br></td>
    <td class="tg-4vfs" height="25" colspan="4" rowspan="4"></td>
  </tr>
  <?php 
  $permanentloc = getLocation("b.bar_code = $bene->barangay","row"); 
  $presentloc = getLocation("b.bar_code = $bene->barangay","row"); 
  ?>
  <tr>
    <td class="tg-4vfs" height="20" colspan="15">Permanent Address:<br><div style="font-weight:bold; font-size:12px;"><?=$permanentloc->bar_name?>, <?=$permanentloc->mun_name?> <?=$permanentloc->prov_name?> CAR [CORDILLERA ADMINISTRATIVE REGION]</div></td>
    <td class="tg-4vfs" height="20" colspan="3">Zip Code:<br><div style="text-align:left; font-weight:bold; font-size:12px;">3609</div></td>
  </tr>
  <tr>
    <td class="tg-4vfs" height="20" colspan="15">Present Address:<br><div style="font-weight:bold; font-size:12px;"><?=$presentloc->bar_name?>, <?=$presentloc->mun_name?> <?=$presentloc->prov_name?> CAR [CORDILLERA ADMINISTRATIVE REGION]</div></td>
    <td class="tg-4vfs" height="20" colspan="3">Zip Code:<br/><div style="text-align:left; font-weight:bold; font-size:12px;">3609</div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="4">Place of Birth:<br><div style="font-weight:bold; font-size:12px;"><?=$bene->birthplace?></div></td>
    <td class="tg-c3ow" height="20" colspan="4">Date of Birth:<br><div style="font-weight:bold; font-size:12px;"><?=$bene->birthdate?></div></td>
    <td class="tg-c3ow" height="20" colspan="4">Nationality: <br/><div style="font-weight:bold; font-size:12px;">	FILIPINO / 608 </div></td>
    <td class="tg-c3ow" height="20" colspan="6">Mother's Maiden Name:<br/><div style="font-weight:bold; font-size:12px;"><?=$bene->mothersMaidenName?></div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="5">Type of ID Presented:<br><br></td>
    <td class="tg-c3ow" height="20" colspan="6">ID Number Presented:<br><br></td>
    <td class="tg-c3ow" height="20" colspan="5">TIN Number:<br><br></td>
    <td class="tg-c3ow" height="20" colspan="6">Source of Fund:</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="11">Name of Employer/ Company/ Business/ School:<br><span style="font-weight:bold; font-size:12px;">DSWD CAR</span></td>
    <td class="tg-c3ow" height="20" colspan="11">Contact Number/s: (home/office/mobile)<br><span style="font-weight:bold; font-size:12px;">(074)-446-5961</span></td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">Cardholder's Information</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="11">Cardholder's Name:<br><div style="font-weight:bold; font-size:14px;"><?=strtoupper($bene->lastname).", ".strtoupper($bene->firstname)." ".strtoupper($bene->middlename)?></div></td>
    <td class="tg-c3ow" height="20" colspan="4">Contact Number/s:<br><div style="font-weight:bold; font-size:12px;"><?=$bene->contactno?></div></td>
    <td class="tg-c3ow" height="20" colspan="7">Date of Birth:<br><div style="font-weight:bold; font-size:12px;"><?=$bene->birthdate?></div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="11">Permanent Address:<br><div style="font-weight:bold; font-size:9px;"><?=$permanentloc->bar_name?>, <?=$permanentloc->mun_name?> <?=$permanentloc->prov_name?> CAR [CORDILLERA ADMINISTRATIVE REGION]</div></td>
    <td class="tg-c3ow" height="20" colspan="4">Zip Code:<br/><div style="font-weight:bold; font-size:12px;">3609</div></td>
    <td class="tg-c3ow" height="20" colspan="7" rowspan="2">Relationship with the Purchaser:</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="11">Present Address:<br><div style="font-weight:bold; font-size:9px;"><?=$permanentloc->bar_name?>, <?=$permanentloc->mun_name?> <?=$permanentloc->prov_name?> CAR [CORDILLERA ADMINISTRATIVE REGION]</div></td>
    <td class="tg-c3ow" height="20" colspan="4">Zip Code:<br/><div style="font-weight:bold; font-size:12px;">3609</div></td>
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
      echo '<td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;">';
       if(!empty($nameprint[$i])){ echo $nameprint[$i]; };
      echo '</div></td>';
    } ?>
    <!-- <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php /*if(!empty($nameprint[0])){ echo $nameprint[0]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[1])){ echo $nameprint[1]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[2])){ echo $nameprint[2]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[3])){ echo $nameprint[3]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[4])){ echo $nameprint[4]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[5])){ echo $nameprint[5]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[6])){ echo $nameprint[6]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[7])){ echo $nameprint[7]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[8])){ echo $nameprint[8]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[9])){ echo $nameprint[9]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[10])){ echo $nameprint[10]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[11])){ echo $nameprint[11]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[12])){ echo $nameprint[12]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[13])){ echo $nameprint[13]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[14])){ echo $nameprint[14]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[15])){ echo $nameprint[15]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[16])){ echo $nameprint[16]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[17])){ echo $nameprint[17]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[18])){ echo $nameprint[18]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[19])){ echo $nameprint[19]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[20])){ echo $nameprint[20]; }?></div></td>
    <td class="tg-6yth" height="15"><div style="font-weight:bold; font-size:12px;"><?php if(!empty($nameprint[21])){ echo $nameprint[21]; }*/?></div></td> -->
  </tr>
  <tr>
    <td class="tg-0pky" colspan="11">Amount of Fees/ Charges to be paid:<br><div style="text-align:center; font-size:12px;">&nbsp;&nbsp;&nbsp;P________________<br>(Initial Cost of the Card)</div></td>
    <td class="tg-0pky" colspan="11">Mode of Payment:<br><div style="font-size:12px;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Cash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;☒ Debit from Account<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acct. No. ________________</div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="60" style="text-align:left !important;" colspan="22">I/We hereby that the above information is true and correct:<br>
    <br>
    <div style = "float:left;margin-left:100px;text-align:center;font-size:10px;">
    <br><u style="font-weight:bold; font-size:12px;"><?=strtoupper($bene->lastname).", ".strtoupper($bene->firstname)." ".strtoupper($bene->middlename)?></u><br/>Signature Over Printed Name<br>of Purchaser/ Applicant / Authorized Signatory<br>
    </div>
    <div style = "float:right;margin-right:100px;text-align:center;font-size:10px;">
    <br><u style="font-weight:bold; font-size:12px;">ENRIQUE H. GASCON JR.</u><br>Signature Over Printed Name<br>of Purchaser/ Applicant / Authorized Signatory</td>
    </div>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">FOR BANK'S USE ONLY<br></td>
  </tr>
  <tr>
    <td  style="text-align:center;" class="tg-0pky" colspan="7">Processed by:<br><br> ___________________________ <br> Customer Associate/NAC<br>  Date: ____________________</td>
    <td  style="text-align:center;" class="tg-0pky" colspan="8">Checked by:  <br><br> ___________________________ <br> BOO/BSO<br>  Date: ____________________<br></td>
    <td  style="text-align:center;" class="tg-0pky" colspan="7">Approved by: <br><br> ___________________________ <br> Branch Head<br>  Date: ____________________<br></td>
  </tr>
  <tr>
    <td class="tg-0pky noborder" colspan=22 style="font-size:4px">for Branches without BOO <br/> Validation Print</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22"><span style="font-weight:bold">CASH CARD/PREPAID CARD/PIN MAILER CLAIM STUB</span></td>
  </tr>
  <tr>
    <td  height="20" style="text-align:center;" class="tg-c3ow" colspan="5">Card Number:<br></td>
    <td  height="20" style="text-align:center;" class="tg-c3ow" colspan="6">Card Holder's Name:</td>
    <td  height="20" style="text-align:center;" class="tg-c3ow" colspan="5">Purchaser's Name:<br><span style="font-weight:bold; font-size:10px;">DEPARTMENT OF SOCIAL WELFARE AND DEVELOPMENT</span></td>
    <td  height="20" style="text-align:center;" class="tg-c3ow" colspan="6">Date:<br><span style="font-weight:bold">_________________, 2019</span></td>
  </tr>
  <tr>
    <td  height="20" style="text-align:center;" class="tg-0lax" colspan="5">Prepaid Card Released by:   <br><br>_______________________<br>Card Custodian<br>Date: _________________</td>
    <td  height="20" style="text-align:center;" class="tg-0lax" colspan="6">PIN Mailer Released by:     <br><br>_______________________<br>PIN Mailer Custodian<br>  Date: ____________________<br></td>
    <td  height="20" style="text-align:center;" class="tg-0lax" colspan="5">Approved for Release:       <br><br>_______________________<br>Branch Head/BOO/BSO<br> Date: _________________<br></td>
    <td  height="20" style="text-align:center;" class="tg-0lax" colspan="6">Card/PIN Mailer Received by:<br><br><u><div style="font-weight:bold; font-size:10px;"><?=strtoupper($bene->lastname).", ".strtoupper($bene->firstname)." ".strtoupper($bene->middlename)?></div></u>Signature Over Printed Name<br>of Purchaser/Cardholder<br></td>
  </tr>
  <tr>
    <td class="tg-0lax" colspan="22" style="font-size:8px">Reminders:<br>-You may claim your Prepaid Card after 7 banking days for Metro Manila Branches, and 15 banking days for Provincial Branches, and a replacement fee shall be collected <br>-Unclaimed Prepaid Card/PIN Mailer shall be perforated after 120 calendar days (for CCT)/30 calendar days (regular) from issuance/re-issuance <br>-Please sign your Prepaid Card immediately<br></td>
  </tr>
  <tr>
    <td class="tg-0pky noborder" colspan=22 style="font-size:6px">Validation Print (if debited from deposit account)</td>
  </tr>
</table>
<!-- set limit na lang -->

<div style="page-break-before: always"></div>

<table style="background-color: #000; width:100%; font-size:12px; font-family:Arial, sans-serif;" cellpadding="5">
    <tr>
      <td colspan=4 style="color:#FFF; text-align:center;">TERMS AND CONDITIONS</td>
    </tr>
    <tr style="background-color: #fff; width:90%;">
      <td colspan=4>
      1. The Card. LANDBANK Prepaid/Cash Card is a preloaded card and debit card, respectively, which functions just like an 
      ATM Card but is not linked to any bank account.<br/>
      2. Card Value. The stored value in the Card is expressed in Philippine currency. The Card shall only be redeemed at face
      value and that it shall earn interest or rewards and other similar incentives convertible to cash, or be purchased at a
      discount.<br/>
      3. Validity and Renewal. Unless earlier terminated by LANDBANK or returned by the cardholder, the Card is valid from date
      of issuance and shall end on the fifth year. The cardholder may request for a new card by visiting his/her branch account
      subject to existing policies on client identification, if no request for renewal/issuance of new card upon expiration and the
      card value becomes zero, the card shall be closed. Renewal/Issuance request at the branch of account/card purchase shall
      be subject to banking policies. Approval thereof shall, in all cases, be at the sole discretion of LBP.<br/>
      4. Cash Loading. Prepaid card for individual clients can be loaded for a maximum of P100,000.00 per month to any
      accredited Cash In/Cash Out (CICO) Agents subject to corresponding fees/charges. LBP shall not be held liable for any
      discrepancy/error during cash loading.<br/>
      5. Point of Sale. The card is honoured to any establishment where card bank is accepted. LANDBANK shall not be liable to
      the cardholder if, for any reason, the card is not honoured. Likewise, LBP shall not be liable for any unauthorized or
      fraudulent utilization of the Card.<br/>
      6. Withdrawals. The cardholder can withdraw from any LBP or Megalink, BancNet member bank’s ATM, with corresponding
      fees/charges. For Prepaid Cardholders, they can also withdraw from any accredited CICO Agents with corresponding
      fees/charges. LBP shall not be liable to the cardholder if, for any reason, the card is not honoured. Cash
      withdrawals/purchase transactions outside the Philippines using the Card shall be in the original currency which is subject to
      maximum amount imposed by the LBP and institution which own the ATM/POS/web-based application. Each successful
      transaction is subject to transaction fees as determined by the LBP and card network indicated on the card. The transaction
      amount and the applicable transaction fee are subject to foreign exchange rate prevailing at the time of the transaction.<br/>
      7. Loss of the Card. The cardholder is responsible for the card PINS’s confidentiality. In case of theft/loss, the cardholder
      shall immediately call LBP (phone banking or branch of account) to report the loss/theft and immediately request for a new
      card at the Branch of Account with corresponding fee. LBP will endeavour to block transactions after the report. However,
      any loss due to withdrawal/purchase/transfer of funds using any lost/stolen LANDBANK Card prior to receipt of request for
      blocking lost/stolen card by the Bank shall be for the cardholder’s account.<br/>
      8. Replacement of Card. LBP will replace a card with inherent defect in the magnetic stripe at no cost if reported within thirty
      (30) days upon receipt of the card. Replacement due to loss/theft, wear and tear shall be subject to replacement fee for a
      new card. The cardholder must surrender the damaged card or submit an affidavit of loss. The replacement card may be
      claimed after five (5) banking days from receipt of the request and compliance requirements.<br/>
      9. Service Charges and Other Fees. LBP may increase or impose additional charges/fees in providing this service. The
      cardholder agrees to pay the increase and/or additional charges/fees that may be imposed in the future.<br/>
      10. Perforation of Unclaimed Card. A card that remains unclaimed thirty (30) calendar days from date of receipt by the
      issuing branch shall be perforated for security reasons. Purchase of a new card shall be required.<br/>
      11. Limitations on Liability. LBP is not liable for any loss or damage of whatever nature in connection with the use of the
      card such as, but not limited to, the following instances:<br/>
      a. disruption, failure or delay relating to or in connection with the ATM and Point-of-Sale (POS) functions of the card due to
      circumstances beyond the control of LBP;<br/>
      b. fortuitous events and force majeure such as, but not limited to, prolonged power outages, breakdown of computers and
      communication facilities, typhoons, floods, public disturbances and other similar or related cases;<br/>
      c. loss or damaged which the cardholder may suffer arising out of any unauthorized utilization of the card due to theft or
      disclosure of PIN or violation of other measures with or without the cardholder’s participation;<br/>
      d. inaccurate, incomplete or delayed information received due to disruption or failure of any communication facilities used
      for the card; and<br/>
      e. indirect, incidental or consequential loss, loss of profit or damage that the cardholder may suffer or has suffered by
      reason of the use or failure/inability to use the card under the terms hereof.<br/>
      12. Insurance. THE CARD FUND IS NOT INSURED WITH PDIC.<br/>
      13. Escheat. Laws on unclaimed balances apply.<br/>
      14. Rules and Regulations. The cardholder agrees to be bound by the rules, regulations and official issuances applicable
      to this service now existing or which may hereinafter be issued, as well as, such other terms and conditions governing the
      use of this service.<br/>
      15. Agreement to the Terms and Conditions. The cardholder’s signature herein or the cardholder’s receipt of the card
      from the purchaser constitutes the cardholder’s to the above terms and conditions.<br/>
      <br/>
      <br/>
      Cardholder’s/Purchaser’s Signature:_______________________ &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Date:_______________________<br/>
      ID No: 
      </td>
    </tr>
    <tr> <td height=5; colspan=4></td> </tr>
    <tr style="background-color: #fff;">
      <td colspan=2> Name:<br/><b><?=strtoupper($bene->lastname).", ".strtoupper($bene->firstname)." ".strtoupper($bene->middlename)?></b><br/> </td>
      <td colspan=2> Card Number:<br/><br/> </td>
    </tr>
    <tr style="background-color: #fff;">
      <td> Reviewed by:<br/><br/> </td>
      <td> Date:<br/><br/> </td>
      <td> Approved by:<br/><br/> </td>
      <td> Date:<br/><br/> </td>
    </tr>
</table>

<div style="page-break-before: always"></div>

<!-- <br/><br/><br/><br/>.<br/> -->
<?php } } ?>