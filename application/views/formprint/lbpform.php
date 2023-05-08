<style type="text/css">
.tg  {padding:0; margin:0; border-collapse:collapse;border-spacing:0;}
.tg td{padding:0; margin:0; font-family:Arial, sans-serif;font-size:11px;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg th{padding:0; margin:0; font-family:Arial, sans-serif;font-size:11px;font-weight:normal;padding:10px 5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
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
</style>

<!-- <img src="<?=base_url("assets/img/capture.png")?>" alt=""> -->

<table class="tg" style="width: 100%; table-layout: fixed;" cellpadding="0">
<tr>
    <th class=" noborder " colspan ="3" rowspan="2"><img src="<?=base_url("assets/img/lbi.png")?>" height="70px" alt=""> </th>
    <th class=" noborder " colspan="19" style="text-align:left;"><b style="font-size:10pt;">LAND BANK OF THE PHILIPPINES</b><br/>Branch: <u> 0022 - BAGUIO </u>
  </tr>
  <tr>
    <td class=" noborder tg-c3ow" colspan="19"><h1>LAND BANK CASH CARD ENROLLMENT FORM</h1></td>
  </tr>
  <tr>
    <td class=" noborder " colspan="6">HH ID No.<u><?=$pensiondata->hh_id?></u></td>
    <td class=" noborder " colspan="10">Acct.No:<u></u></td>
    <td class=" noborder " colspan="6">Date:__________________, 2018</td>
  </tr>
  <tr>
    <th class="tg-uys7" colspan="22">Please check the type of card being enrolled:</th>
  </tr>
  <tr>
    <td class="tg-xldj" style="font-size:10pt;" colspan="6">☒ Cash Card<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Individual<br> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☒ Institution</td>
    <td class="tg-0pky" style="font-size:10pt;" colspan="16">☐ Prepaid Card &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"/> Individual &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <input type="radio"/> Institution<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Regular (CUP/JCB/Master Card/Others, pls specify ___________)<br>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Gift Card (CUP/JCB/Master Card/Others, pls specify ___________)</td>
  </tr>
  <tr>
    <td class="tg-uys7" colspan="22"><span style="font-weight:bold">Purchaser's Information:</span></td>
  </tr>
  <tr>
    <td class="tg-0pky" height=20 colspan="14">Purchaser's Name:<br><span style="font-weight:bold; font-size:10pt;">DEPARTMENT OF SOCIAL WELFARE AND DEVELOPMENT</span></td>
    <td class="tg-0pky" height=20 colspan="8">With existing account with LBP   Yes ☐ No ☐<br>If yes, please specify Acct. No/s.: __________<br>Cash Card Number/s: ____________________</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">ADDITIONAL INFORMATION FOR WALK-IN INVIDUAL PURCHASER, AND CARDHOLDER OF INSTITUTIONAL PURCHASER:</td>
  </tr>
  <tr>
    <td class="tg-4vfs" height="20" colspan="18">Cardholder's Name (Applicable only for Gift Card purchased by an Institutional Customer):<br><div style="text-align:left; font-weight:bold; font-size:14pt;"><?=strtoupper($pensiondata->lastname)?>, <?=strtoupper($pensiondata->firstname)?> <?=strtoupper($pensiondata->middlename)?></div></td>
    <td class="" height="20" colspan="4" rowspan="4"><img src="<?=base_url("assets/img/lbppicture.jpg")?>" height="120" alt=""></td>
  </tr>
  <tr>
    <td class="tg-4vfs" height="20" colspan="15">Permanent Address:<br><div style="text-align:left; font-weight:bold; font-size:10pt;"><?=strtoupper($pensiondata->address)?> <?=strtoupper($permanentlocdata->bar_name)?> <?=strtoupper($permanentlocdata->mun_name)?>, <?=strtoupper($permanentlocdata->prov_name)?></div></td>
    <td class="tg-4vfs" height="20" colspan="3">Zip Code:<br/><div style="text-align:left; font-weight:bold; font-size:10pt;">3609</div></td>
  </tr>
  <tr>
    <td class="tg-4vfs" height="20" colspan="15">Present Address:<br><div style="text-align:left; font-weight:bold; font-size:10pt;"><?=strtoupper($pensiondata->address)?> <?=strtoupper($locationdata->bar_name)?> <?=strtoupper($locationdata->mun_name)?>, <?=strtoupper($locationdata->prov_name)?></div></td>
    <td class="tg-4vfs" height="20" colspan="3">Zip Code:<br/><div style="text-align:left; font-weight:bold; font-size:10pt;">3609</div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="4">Place of Birth:<br><div style="font-weight:bold; font-size:10pt;"><?=strtoupper($pensiondata->birthplace)?></div></td>
    <td class="tg-c3ow" height="20" colspan="4">Date of Birth:<br><div style="font-weight:bold; font-size:10pt;"><?=$pensiondata->birthdate?></div></td>
    <td class="tg-c3ow" height="20" colspan="4">Nationality: <br/><div style="font-weight:bold; font-size:10pt;">	FILIPINO / 608 </td>
    <td class="tg-c3ow" height="20" colspan="6">Mother's Maiden Name:</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="5">Type of ID Presented:<br><div style="font-size:10pt;">01 DSWD-4Ps ID</div></td>
    <td class="tg-c3ow" height="20" colspan="6">ID Number Presented:<br></td>
    <td class="tg-c3ow" height="20" colspan="3">Profession:<br></td>
    <td class="tg-c3ow" height="20" colspan="4">Source of Fund:<br/><div style="font-size:10pt;">99</div></td>
    <td class="tg-c3ow" height="20" colspan="4">TIN Number:<br></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="11">Name of Employer/ Company/ Business/ School:<br/><span style="font-weight:bold; font-size:10pt;">DSWD CAR</span></td>
    <td class="tg-c3ow" height="20" colspan="11">Contact Number/s: (home/office/mobile)<br/><span style="font-weight:bold; font-size:10pt;">(074)-446-5961</span></td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">Cardholder's Information</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="11">Cardholder's Name:<br><div style="font-weight:bold; font-size:10pt;"><?=strtoupper($pensiondata->lastname)?>, <?=strtoupper($pensiondata->firstname)?> <?=strtoupper($pensiondata->middlename)?></div></td>
    <td class="tg-c3ow" height="20" colspan="4">Contact Number/s:<br/><div style="font-weight:bold; font-size:10pt;"><?=$pensiondata->contactno?></td>
    <td class="tg-c3ow" height="20" colspan="7">Date of Birth:<br/><div style="font-weight:bold; font-size:10pt;"><?=$pensiondata->birthdate?></div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="11">Permanent Address:<br><div style="font-weight:bold; font-size:10pt;"><?=strtoupper($pensiondata->address)?> <?=strtoupper($permanentlocdata->bar_name)?> <?=strtoupper($permanentlocdata->mun_name)?>, <?=strtoupper($permanentlocdata->prov_name)?></div></td>
    <td class="tg-c3ow" height="20" colspan="4">Zip Code:<br/><div style="font-weight:bold; font-size:10pt;">3609</div></td>
    <td class="tg-c3ow" height="20" colspan="7" rowspan="2">Relationship with the Purchaser:</td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="20" colspan="11">Present Address:<br><div style="font-weight:bold; font-size:10pt;"><?=strtoupper($pensiondata->address)?> <?=strtoupper($locationdata->bar_name)?> <?=strtoupper($locationdata->mun_name)?>, <?=strtoupper($locationdata->prov_name)?></div></td>
    <td class="tg-c3ow" height="20" colspan="4">Zip Code:<br/><div style="font-weight:bold; font-size:10pt;">3609</div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22">Name to Appear on the Card (with maximum of 22 characters)</td>
  </tr>
  <?php
    $fnamecount  = strlen($pensiondata->firstname);
    $lnamecount  = strlen($pensiondata->lastname);
    $middlename  = str_split(strtoupper($pensiondata->middlename));

    if($lnamecount+$fnamecount<=22){
      $nameprint1 = strtoupper($pensiondata->firstname)." ".$middlename[0]." ".strtoupper($pensiondata->lastname);
      $nameprint = str_split($nameprint1);
    }
    else{
      $fnamecount = 22-$lnamecount;
      $nameprint1 = strtoupper( rtrim($pensiondata->firstname , substr($pensiondata->firstname, strpos($pensiondata->firstname, " "))) . " " . $middlename[0] . " " . $pensiondata->lastname );
      $nameprint = str_split($nameprint1);
    }
  ?>
  <tr>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[0])){ echo $nameprint[0]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[1])){ echo $nameprint[1]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[2])){ echo $nameprint[2]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[3])){ echo $nameprint[3]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[4])){ echo $nameprint[4]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[5])){ echo $nameprint[5]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[6])){ echo $nameprint[6]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[7])){ echo $nameprint[7]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[8])){ echo $nameprint[8]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[9])){ echo $nameprint[9]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[10])){ echo $nameprint[10]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[11])){ echo $nameprint[11]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[12])){ echo $nameprint[12]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[13])){ echo $nameprint[13]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[14])){ echo $nameprint[14]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[15])){ echo $nameprint[15]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[16])){ echo $nameprint[16]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[17])){ echo $nameprint[17]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[18])){ echo $nameprint[18]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[19])){ echo $nameprint[19]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[20])){ echo $nameprint[20]; }?></div></td>
    <td class="tg-6yth" height="30"><div style="font-weight:bold; font-size:10pt;"><?php if(!empty($nameprint[21])){ echo $nameprint[21]; }?></div></td>
  </tr>
  <tr>
    <td class="tg-0pky" colspan="11">Amount of Fees/ Charges to be paid:<br><div style="text-align:center; font-size:10pt;">&nbsp;&nbsp;&nbsp;P________________<br>(Initial Cost of the Card)</div></td>
    <td class="tg-0pky" colspan="11">Mode of Payment:<br><div style="font-size:10pt;">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ☐ Cash &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;☒ Debit from Account<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Acct. No. ________________</div></td>
  </tr>
  <tr>
    <td class="tg-c3ow" height="100" style="text-align:left !important;" colspan="22">I/We hereby that the above information is true and correct:<br>
    <br><br><br>
    <div style = "float:left;margin-left:100px;text-align:center">
    <br><u style="font-size:10pt;"><?=$nameprint1?></u><br>Signature Over Printed Name<br>of Purchaser/ Applicant / Authorized Signatory<br>
    </div>
    <div style = "float:right;margin-right:100px;text-align:center">
    <br><u style="font-size:10pt;">ENRIQUE H. GASCON JR.</u><br>Signature Over Printed Name<br>of Purchaser/ Applicant / Authorized Signatory<br></td>
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
    <td class="tg-0pky noborder" colspan=22 style="font-size:4px">for Branches without BOO <br/> Validation Print</td>
  </tr>
  <tr>
    <td class="tg-c3ow" colspan="22"><span style="font-weight:bold">CASH CARD/PREPAID CARD/PIN MAILER CLAIM STUB</span></td>
  </tr>
  <tr>
    <td  height="20" style="text-align:center;" class="tg-c3ow" colspan="5">Card Number:<br></td>
    <td  height="20" style="text-align:center;" class="tg-c3ow" colspan="6">Card Holder's Name:<br><br/><span style="font-size:10pt; font-weight:bold"><?=$nameprint1?></span></td>
    <td  height="20" style="text-align:center;" class="tg-c3ow" colspan="5">Purchaser's Name:<br><br/><span style="font-weight:bold; font-size:4pt;">DEPARTMENT OF SOCIAL WELFARE AND DEVELOPMENT</span></td>
    <td  height="20" style="text-align:center;" class="tg-c3ow" colspan="6">Date:<br><br/><span style="font-weight:bold; font-size:10pt;">______________, 2018</span></td>
  </tr>
  <tr>
    <td  style="text-align:center;" class="tg-0lax" colspan="5">Prepaid Card Released by:   <br><br><br>_______________________<br>Card Custodian<br>Date:______________________________</td>
    <td  style="text-align:center;" class="tg-0lax" colspan="6">PIN Mailer Released by:     <br><br><br>_______________________<br>PIN Mailer Custodian<br>  Date: ____________________<br></td>
    <td  style="text-align:center;" class="tg-0lax" colspan="5">Approved for Release:       <br><br><br>_______________________<br>Branch Head/BOO/BSO<br> Date: ____________________<br></td>
    <td  style="text-align:center;" class="tg-0lax" colspan="6">Card/PIN Mailer Received by:<br><br><br><u style="font-size:10pt;"><?=$nameprint1?></u><br>Signature Over Printed Name<br>of Purchaser/Cardholder<br></td>
  </tr>
  <tr>
    <td class="tg-0lax" colspan="22" style="font-size:4px">Reminders:<br>-You may claim your Prepaid Card after 7 banking days for Metro Manila Branches, and 15 banking days for Provincial Branches, and a replacement fee shall be collected <br>-Unclaimed Prepaid Card/PIN Mailer shall be perforated after 120 calendar days (for CCT)/30 calendar days (regular) from issuance/re-issuance <br>-Please sign your Prepaid Card immediately<br></td>
  </tr>
  <tr>
    <td class="tg-0pky noborder" colspan=22 style="font-size:4px">Validation Print (if debited from deposit account)</td>
  </tr>
</table>

<br/><br/><br/><br/>

<div class=WordSection1>

<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 align=left
 width=0 style='width:535.5pt;border-collapse:collapse;border:none;mso-border-alt:
 solid windowtext .5pt;mso-yfti-tbllook:1184;mso-table-lspace:9.0pt;mso-table-rspace:9.0pt;margin-right:6.75pt;mso-table-anchor-vertical:
 page;mso-table-anchor-horizontal:margin;mso-table-left:left;mso-table-top:
 38.3pt;mso-padding-alt:0in 5.4pt 0in 5.4pt; font-size:9.5pt;font-family:"Arial",sans-serif'>
 <tr style='mso-yfti-irow:0;mso-yfti-firstrow:yes;height:4.1pt'>
  <td width=714 colspan=4 valign=top style='width:535.5pt;border:solid windowtext 1.0pt;
  mso-border-alt:solid windowtext .5pt;background:black;mso-background-themecolor:
  text1;padding:0in 5.4pt 0in 5.4pt;height:4.1pt'>
  <p class=MsoNoSpacing align=center style='text-align:center;background:black;
  mso-background-themecolor:text1;mso-element:frame;mso-element-frame-hspace:
  9.0pt;mso-element-wrap:around;mso-element-anchor-vertical:page;mso-element-anchor-horizontal:
  margin;mso-element-top:38.3pt;mso-height-rule:exactly'><span lang=EN-PH
  style='font-size:9.5pt;font-family:"Arial",sans-serif'>TERMS AND CONDITIONS<o:p></o:p></span></p>
  </td>
 </tr>
 <tr style='mso-yfti-irow:1;height:600pt'>
  <td width=714 colspan=4 valign=top style='width:535.5pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  background:black;mso-background-themecolor:text1;padding:0in 5.4pt 0in 5.4pt;
  height:600pt'>
  <div style='mso-element:para-border-div;border:solid windowtext 1.0pt;
  mso-border-alt:solid windowtext .5pt;padding:1.0pt 4.0pt 1.0pt 4.0pt;
  background:white;mso-background-themecolor:background1'>
  1. <b style='mso-bidi-font-weight:normal'>The Card.</b> LANDBANK Prepaid/Cash Card
  is a preloaded card and debit card, respectively, which functions just like
  an ATM Card but is not linked to any bank account.
  <br/>
  2. <b style='mso-bidi-font-weight:normal'>Card Value.</b> The stored value in the
  Card is expressed in Philippine currency. The Card shall only be redeemed at
  face value and that it shall earn interest or rewards and other similar
  incentives convertible to cash, or be purchased at a discount.
  <br/>
  3. <b style='mso-bidi-font-weight:normal'>Validity and Renewal.</b> Unless earlier
  terminated by LANDBANK or returned by the cardholder, the Card is valid from
  date of issuance and shall end on the fifth year. The cardholder may request
  for a new card by visiting his/her branch account subject to existing
  policies on client identification, if no request for renewal/issuance of new
  card upon expiration and the card value becomes zero, the card shall be
  closed. Renewal/Issuance request at the branch of account/card purchase shall
  be subject to banking policies. Approval thereof shall, in all cases, be at
  the sole discretion of LBP.
  <br/>
  4. <b style='mso-bidi-font-weight:normal'>Cash Loading.</b> Prepaid card for
  individual clients can be loaded for a maximum of P100<span class=GramE>,000.00</span>
  per month to any accredited Cash In/Cash Out (CICO) Agents subject to
  corresponding fees/charges. LBP shall not be held liable for any
  discrepancy/error during cash loading.
  <br/>
  5. <b style='mso-bidi-font-weight:normal'>Point of Sale</b>. The card is <span
  class=SpellE>honoured</span> to any establishment where card bank is
  accepted. LANDBANK shall not be liable to the cardholder if, for any reason,
  the card is not <span class=SpellE>honoured</span>. Likewise, LBP shall not
  be liable for any unauthorized or fraudulent utilization of the Card.
  <br/>
  6. <b style='mso-bidi-font-weight:normal'>Withdrawals.</b> The cardholder can
  withdraw from any LBP or <span class=SpellE>Megalink</span>, <span
  class=SpellE>BancNet</span> member bank’s ATM, with corresponding
  fees/charges. For Prepaid Cardholders, they can also withdraw from any
  accredited CICO Agents with corresponding fees/charges. LBP shall not be
  liable to the cardholder if, for any reason, the card is not <span
  class=SpellE>honoured</span>. Cash withdrawals/purchase transactions outside
  the Philippines using the Card shall be in the original currency which is
  subject to maximum amount imposed by the LBP and institution which own the
  ATM/POS/web-based application. Each successful transaction is subject to
  transaction fees as determined by the LBP and card network indicated on the
  card. The transaction amount and the applicable transaction fee are subject
  to foreign exchange rate prevailing at the time of the transaction.
  <br/>
  7. <b style='mso-bidi-font-weight:normal'>Loss of the Card.</b> The cardholder is
  responsible for the card PINS’s confidentiality. In case of theft/loss, the
  cardholder shall immediately call LBP (phone banking or branch of account) to
  report the loss/theft and immediately request for a new card at the Branch of
  Account with corresponding fee. LBP will <span class=SpellE>endeavour</span>
  to block transactions after the report. However, any loss due to
  withdrawal/purchase/transfer of funds using any lost/stolen LANDBANK Card
  prior to receipt of request for blocking lost/stolen card by the Bank shall
  be for the cardholder’s account.
  <br/>
  8. Replacement of
  Card. LBP will replace a card with inherent defect in the magnetic stripe at
  no cost if reported within thirty (30) days upon receipt of the card.
  Replacement due to loss/theft, wear and tear shall be subject to replacement
  fee for a new card. The cardholder must surrender the damaged card or submit
  an affidavit of loss. The replacement card may be claimed after five (5)
  banking days from receipt of the request and compliance requirements.
  <br/>
  9. <b
  style='mso-bidi-font-weight:normal'>Service Charges and Other Fees</b>. LBP
  may increase or impose additional charges/fees in providing this service. The
  cardholder agrees to pay the increase and/or additional charges/fees that may
  be imposed in the future.
  <br/>
  10. <b
  style='mso-bidi-font-weight:normal'>Perforation of Unclaimed Card</b>. A card
  that remains unclaimed thirty (30) calendar days from date of receipt by the
  issuing branch shall be perforated for security reasons. Purchase of a new
  card shall be required.
  <br/>
  11.<b
  style='mso-bidi-font-weight:normal'> Limitations on Liability. </b>LBP is not
  liable for any loss or damage of whatever nature in connection with the use
  of the card such as, but not limited to, the following instances:
  <br/>
  a. disruption, failure or delay relating to
  or in connection with the ATM and Point-of-Sale (POS) functions of the card
  due to circumstances beyond the control of LBP;
  <br/>
  b. fortuitous events and force majeure such
  as, but not limited to, prolonged power outages, breakdown of computers and
  communication facilities, typhoons, floods, public disturbances and other
  similar or related cases;
  <br/>
  c. loss or damaged which the cardholder may
  suffer arising out of any unauthorized utilization of the card due to theft
  or disclosure of PIN or violation of other measures with or without the
  cardholder’s participation;
  <br/>
  d. inaccurate, incomplete or delayed
  information received due to disruption or failure of any communication
  facilities used for the card; and
  <br/>
  e. indirect, incidental or consequential
  loss, loss of profit or damage that the cardholder may suffer or has suffered
  by reason of the use or failure/inability to use the card under the terms
  hereof.
  <br/>
  12. <b style='mso-bidi-font-weight:normal'>Insurance.</b> THE CARD FUND IS NOT
  INSURED WITH PDIC.
  <br/>
  13. <b style='mso-bidi-font-weight:normal'>Escheat.</b> Laws on unclaimed balances
  apply.
  <br/>
  14. <b style='mso-bidi-font-weight:normal'>Rules and Regulations.</b> The cardholder
  agrees to be bound by the rules, regulations and official issuances
  applicable to this service now existing or which may hereinafter be issued,
  as well as, such other terms and conditions governing the use of this
  service.
  <br/>
  15. <b style='mso-bidi-font-weight:normal'>Agreement to the Terms and Conditions.</b>
  The cardholder’s signature herein or the cardholder’s receipt of the card
  from the purchaser constitutes the cardholder’s to the above terms and
  conditions.
  <br/><br/><br/>

  <div style="float:left;">Cardholder’s/Purchaser’s Signature:__________________</div>
  <div style="float:right;">Date:__________________</div>
  <br/>
  ID No: <b style='mso-bidi-font-weight:normal'>
  </div>
  </td>
 </tr>
 <tr>
    <td style="text-align:center;">FOR BANK USE</td>
 </tr>
 <tr style='mso-yfti-irow:2;height:23.35pt'>
  <td width=352 colspan=2 valign=top style='width:264.25pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0in 5.4pt 0in 5.4pt;height:23.35pt'>
  <div style='mso-element:para-border-div;border:solid windowtext 1.0pt;
  mso-border-alt:solid windowtext .5pt;padding:0in 4.0pt 1.0pt 4.0pt'>
  <p class=MsoNoSpacing style='line-height:250%;border:none;mso-border-alt:
  solid windowtext .5pt;padding:0in;mso-padding-alt:0in 4.0pt 1.0pt 4.0pt;
  mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:around;
  mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly;mso-border-between:.5pt solid windowtext;
  mso-padding-between:1.0pt;padding-bottom:1.0pt;mso-padding-bottom-alt:1.0pt;
  border-bottom:.5pt solid windowtext;mso-border-bottom-alt:.5pt solid windowtext'><span
  lang=EN-PH>Name:<b style='mso-bidi-font-weight:normal'> </b></span><!--[if supportFields]><b
  style='mso-bidi-font-weight:normal'><span lang=EN-PH><span style='mso-element:
  field-begin'></span><span style='mso-spacerun:yes'> </span>MERGEFIELD
  &quot;FULL_NAME&quot; <span style='mso-element:field-separator'></span></span></b><![endif]--><b
  style='mso-bidi-font-weight:normal'><span lang=EN-PH><span style='mso-no-proof:
  yes'>CHRISTINA _ PULIDO</span></span></b><!--[if supportFields]><b
  style='mso-bidi-font-weight:normal'><span lang=EN-PH><span style='mso-element:
  field-end'></span></span></b><![endif]--><b style='mso-bidi-font-weight:normal'><span
  lang=EN-PH><o:p></o:p></span></b></p>
  </div>
  </td>
  <td width=362 colspan=2 valign=top style='width:271.25pt;border-top:none;
  border-left:none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0in 5.4pt 0in 5.4pt;height:23.35pt'>
  <div style='mso-element:para-border-div;border:solid windowtext 1.0pt;
  mso-border-alt:solid windowtext .5pt;padding:0in 4.0pt 1.0pt 4.0pt'>
  <p class=MsoNoSpacing style='line-height:250%;border:none;mso-border-alt:
  solid windowtext .5pt;padding:0in;mso-padding-alt:0in 4.0pt 1.0pt 4.0pt;
  mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:around;
  mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly;mso-border-between:.5pt solid windowtext;
  mso-padding-between:1.0pt'><span lang=EN-PH>Card Number: </span><!--[if supportFields]><b
  style='mso-bidi-font-weight:normal'><span lang=EN-PH><span style='mso-element:
  field-begin'></span><span style='mso-spacerun:yes'> </span>MERGEFIELD
  &quot;account_no&quot; <span style='mso-element:field-separator'></span></span></b><![endif]--><b
  style='mso-bidi-font-weight:normal'><span lang=EN-PH><span style='mso-no-proof:
  yes'>603131-225-002744-6</span></span></b><!--[if supportFields]><b
  style='mso-bidi-font-weight:normal'><span lang=EN-PH><span style='mso-element:
  field-end'></span></span></b><![endif]--><b style='mso-bidi-font-weight:normal'><span
  lang=EN-PH><o:p></o:p></span></b></p>
  </div>
  </td>
 </tr>
 <tr style='mso-yfti-irow:3;mso-yfti-lastrow:yes;height:36.85pt'>
  <td width=182 valign=top style='width:136.15pt;border:solid windowtext 1.0pt;
  border-top:none;mso-border-top-alt:solid windowtext .5pt;mso-border-alt:solid windowtext .5pt;
  padding:0in 5.4pt 0in 5.4pt;height:36.85pt'>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal;mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:
  around;mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly'>Reviewed By:</p>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal;mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:
  around;mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly'><o:p>&nbsp;</o:p></p>
  </td>
  <td width=171 valign=top style='width:128.1pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0in 5.4pt 0in 5.4pt;height:36.85pt'>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal;mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:
  around;mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly'>Date:</p>
  </td>
  <td width=171 valign=top style='width:128.05pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0in 5.4pt 0in 5.4pt;height:36.85pt'>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal;mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:
  around;mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly'>Approved by:</p>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal;mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:
  around;mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly'><o:p>&nbsp;</o:p></p>
  </td>
  <td width=191 valign=top style='width:143.2pt;border-top:none;border-left:
  none;border-bottom:solid windowtext 1.0pt;border-right:solid windowtext 1.0pt;
  mso-border-top-alt:solid windowtext .5pt;mso-border-left-alt:solid windowtext .5pt;
  mso-border-alt:solid windowtext .5pt;padding:0in 5.4pt 0in 5.4pt;height:36.85pt'>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal;mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:
  around;mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly'>Date:</p>
  <p class=MsoNormal style='margin-bottom:0in;margin-bottom:.0001pt;line-height:
  normal;mso-element:frame;mso-element-frame-hspace:9.0pt;mso-element-wrap:
  around;mso-element-anchor-vertical:page;mso-element-anchor-horizontal:margin;
  mso-element-top:38.3pt;mso-height-rule:exactly'><o:p>&nbsp;</o:p></p>
  </td>
 </tr>
 <![if !supportMisalignedColumns]>
 <tr height=0>
  <td width=182 style='border:none'></td>
  <td width=171 style='border:none'></td>
  <td width=171 style='border:none'></td>
  <td width=191 style='border:none'></td>
 </tr>
 <![endif]>
</table>

</div>