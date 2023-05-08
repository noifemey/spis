<style type="text/css">
.tg  {border-collapse:collapse;border-spacing:0;}
.tg td{font-family:Arial, sans-serif;font-size:10px;padding:5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
.tg th{font-family:Arial, sans-serif;font-size:10px;font-weight:normal;padding:5px;border-style:solid;border-width:1px;overflow:hidden;word-break:normal;border-color:black;}
/* .tg .tg-xldj{border-color:inherit;text-align:left}
.tg .tg-0pky{border-color:inherit;text-align:left;vertical-align:top}
.tg .tg-0lax{text-align:left;vertical-align:top} */
.noborder{
    border-style: none;
    border-width: 0px !important; 
}
.textbold{
  font-weight:bold;
}
.textboldcenter{
  font-weight:bold;
  text-align:center;
}

@media print, screen {
  
  .lightbluebold{
    background-image: url("<?=base_url("assets/img/lightblue.png")?>");
    font-weight:bold;-webkit-print-color-adjust: exact;
  }
  .darkbluebold{
    background-image: url("<?=base_url("assets/img/darkblue.png")?>");
    font-weight:bold;
    color:white;-webkit-print-color-adjust: exact;
  }
  .lightblueitalic{
    background-image: url("<?=base_url("assets/img/lightblue.png")?>");
    font-style:italic;
    text-align:center;-webkit-print-color-adjust: exact;
  }
}

</style>
<table class="tg" style="width: 100%; table-layout: fixed;" cellpadding="0">
  <tr>
    <th class="noborder" colspan="7"><img src="<?=base_url("assets/img/logo.png")?>" height="45px" alt="" style="float:left;"> </th>
    <th class="noborder" colspan="23"><font face="Oswald Regular" size="6">Social Pension Beneficiary Update Form</font></th>
  </tr>
  <tr>
  <!-- light blue = #DBEEF4
  dark blue = #254061 -->
    <td width="20px" class="noborder" colspan="7" style="font-size:8px;">PDPB-SPBUF v.2 October 31, 2018</td>
    <td width="20px" class="noborder" colspan="17" style="text-align:right;">Reference Code:</td>
    <td width="20px"></td>
    <td width="20px"></td>
    <td width="20px"></td>
    <td width="20px"></td>
    <td width="20px"></td>
    <td width="20px"></td>
  </tr>
  <tr>
    <td colspan="5" class="lightbluebold">SENIOR CITIZEN ID NO.</td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td></td>
    <td colspan="4" class="lightbluebold"><input type="checkbox"/> Encoded</td>
    <td colspan="3" class="lightbluebold">Time Started: </td>
    <td colspan="4"></td>
    <td colspan="4" class="lightbluebold">Time Ended:</td>
    <td colspan="4"></td>
  </tr>
  <tr>
    <td colspan="5" class="textbold"><input type="radio"/>Grantee (GO TO 1)</td>
    <td colspan="6" class="textbold"><input type="radio"/>Not Grantee (CONTINUE)</td>
    <td colspan="4" class="lightbluebold">Name of Respondent:</td>
    <td colspan="15"></td>
  </tr>
  <tr>
    <td colspan="30" class="darkbluebold">I. IDENTIFICATION</td>
  </tr>
  <tr>
    <td colspan="30"></td>
  </tr>
  <tr>
    <td colspan="5" rowspan=2 class="lightbluebold">1. Name of Pensioner/ Senior Citizen</td>
    <td colspan="6" height="25" class="textboldcenter"><?=strtoupper($pensiondata->lastname)?></td>
    <td colspan="6" height="25" class="textboldcenter"><?=strtoupper($pensiondata->firstname)?></td>
    <td colspan="6" height="25" class="textboldcenter"><?=strtoupper($pensiondata->middlename)?></td>
    <td colspan="7" height="25" class="textboldcenter"><?=strtoupper($pensiondata->extensionname)?></td>
  </tr>
  <tr>
    <td colspan="6" class="lightblueitalic">Last Name</td>
    <td colspan="6" class="lightblueitalic">First Name</td>
    <td colspan="6" class="lightblueitalic">Middle Name</td>
    <td colspan="7" class="lightblueitalic">Name Extension (Jr,Sr)</td>
  </tr>
  <tr>
    <td colspan="30"></td>
  </tr>
  <tr>
    <td colspan="5" rowspan=4>2. Address</td>
    <td colspan="6" height="25" class="textboldcenter">CAR</td>
    <td colspan="6" height="25" class="textboldcenter"><?=$locationdata->prov_name?></td>
    <td colspan="6" height="25" class="textboldcenter"><?=$locationdata->mun_name?></td>
    <td colspan="7" height="25" class="textboldcenter"><?=$locationdata->bar_name?></td>
  </tr>
  <tr>
    <td colspan="6" class="lightblueitalic">Region</td>
    <td colspan="6" class="lightblueitalic">Province</td>
    <td colspan="6" class="lightblueitalic">City / Municipality</td>
    <td colspan="7" class="lightblueitalic">Barangay</td>
  </tr>
  <tr>
    <td colspan="18" height="25" class="textboldcenter"><?=$pensiondata->address?></td>
    <td colspan="7" height="25"></td>
  </tr>
  <tr>
    <td colspan="18" class="lightblueitalic">House No./Zone/Purok/Sitio</td>
    <td colspan="7" class="lightblueitalic">Street</td>
  </tr>
  <tr>
    <td colspan="30"></td>
  </tr>
  <tr>
    <td colspan="5" rowspan=2 class="lightbluebold">3. Date of Birth</td>
    <?php $bdatearr = str_split($pensiondata->birthdate); ?>
    <td class="textboldcenter"><?=$bdatearr[5]?></td>
    <td class="textboldcenter"><?=$bdatearr[6]?></td>
    <td class="textboldcenter"><?=$bdatearr[8]?></td>
    <td class="textboldcenter"><?=$bdatearr[9]?></td>
    <td class="textboldcenter"><?=$bdatearr[2]?></td>
    <td class="textboldcenter"><?=$bdatearr[3]?></td>
    <td colspan="6" class="lightbluebold">5. Name of Guardian/Care Giver</td>
    <td colspan="6"></td>
    <td colspan="2" rowspan=2>8. Marital Status</td>
    <td colspan="5" rowspan=2>
      <div style="float:left;">
        <input type="radio"/>1 Single<br/>
        <input type="radio"/>3 Widowed<br/>
        <input type="radio"/>5 Live-in
      </div>
      <div style="float:right;">
        <input type="radio"/>2 Married<br/>
        <input type="radio"/>4 Separated<br/>
        <input type="radio"/>6 Others
      </div>
    </td>
  </tr>
  <tr>
    <td height="10" style="text-align:center;">m</td>
    <td height="10" style="text-align:center;">m</td>
    <td height="10" style="text-align:center;">d</td>
    <td height="10" style="text-align:center;">d</td>
    <td height="10" style="text-align:center;">y</td>
    <td height="10" style="text-align:center;">y</td>
    <td colspan="6" height="25" class="lightbluebold">6. Relationship of (5) to the Senior Citizen</td>
    <td colspan="6" height="25"></td>
  </tr>
  <tr>
    <td colspan="30"></td>
  </tr>
  <tr>
    <td colspan="5" class="lightbluebold">4. Sex</td>
    <?php
      if($pensiondata->gender=="Female"){
        $checkedfemale="checked=true";
        $checkedmale="";
      } else {
        $checkedmale="checked=true";
        $checkedfemale="";
      }
    ?>
    <td colspan="3"><input type="radio" <?=$checkedmale?>/>1 Male</td>
    <td colspan="3"><input type="radio" <?=$checkedfemale?>/>2 Female</td>
    <td colspan="6" class="lightbluebold">7. Contact Number</td>
    <td colspan="6" class="textboldcenter"><?=$pensiondata->contactno?></td>
    <td colspan="3" class="lightbluebold">9. Household Size</td>
    <td colspan="4"></td>
  </tr>
  <tr>
    <td colspan="30"></td>
  </tr>
  <tr>
    <td colspan="30" class="darkbluebold">II. SOCIOECONOMIC INFORMATION</td>
  </tr>
  <tr>
    <td colspan="15" class="textbold">A. Income Sources and Financial Support</td>
    <td colspan="15" class="textbold">B. Health and Social Condition</td>
  </tr>
  <tr>
    <td colspan="15" class="lightbluebold">10. Do you receive any form of pension?</td>
    <td colspan="15" class="lightbluebold">13. Who are you living with?</td>
  </tr>
  <tr>
    <td colspan="15">
      <input type="radio"/> 1 Yes (GO TO 11)<br/>
      <input type="radio"/> 2 No (GO TO 12)<br/>
      <input type="radio"/> 3 Don't know (GO TO 12)
    </td>
    <td colspan="15">
      <input type="radio"/> 1 Living alone<br/>
      <input type="radio"/> 2 Living with spouse only<br/>
      <input type="radio"/> 3 Living with a child (including adopted children), child-in-law or grandchild<br/>
      <input type="radio"/> 4 Living with another relative (other than a spouse or child/grandchild)<br/>
      <input type="radio"/> 5 Living with unrelated people only, apart from the older person’s spouse
    </td>
  </tr>
  <tr>
    <td colspan="15" class="lightbluebold">11. What pension/s did you receive in the past 6 months? You may read the options</td>
    <td colspan="15" class="lightbluebold">14. Frailty Questions</td>
  </tr>
  <tr>
    <td colspan="15" rowspan=2>
      <input type="checkbox"/> 1 DSWD Social Pension<br/>
      <input type="checkbox"/> 2 GSIS<br/>
      <input type="checkbox"/> 3 SSS<br/>
      <input type="checkbox"/> 4 AFPSLAI<br/>
      <input type="checkbox"/> 5 Others________________________
    </td>
    <td colspan="10">14.1 Are you older than 85 years?</td>
    <td colspan="5"> <input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
  </tr>
  <tr>
    <td colspan="10">14.2 In general, do you have any health problems that require you to limit your activities?</td>
    <td colspan="5"> <input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
  </tr>
  <tr>
    <td colspan="15" class="lightbluebold">12. What are your sources of income and financial support in the past 6 months (other than your pension/s)? You may read the options. For each source, indicate if it is regular then record the estimated amount of income and divide by the household size, if applicable. </td>
    <td colspan="10">14.3 Do you need someone to help you on a regular basis?</td>
    <td colspan="5"> <input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
  </tr>
  <tr>
    <td colspan="6" class="textboldcenter">A. Source</td>
    <td colspan="4" class="textboldcenter">B. Is it regular?</td>
    <td colspan="5" class="textboldcenter">C. Amount of Income</td>
    <td colspan="10">14.4 In general, do you have any health problems that require you to stay at home?</td>
    <td colspan="5"> <input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
  </tr>
  <tr>
    <td colspan="6"><input type="checkbox"/> 1 Wages/ Salaries</td>
    <td colspan="4"><input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
    <td colspan="5">PhP__________/___=</td>
    <td colspan="10">14.5 If you need help, can you count on someone close to you?</td>
    <td colspan="5"> <input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
  </tr>
  <tr>
    <td colspan="6"><input type="checkbox"/> 2 Profits from Entrepreneurial Activities</td>
    <td colspan="4"><input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
    <td colspan="5">PhP__________/___=</td>
    <td colspan="10">14.6. Do you regularly use a stick/ walker/ wheelchair to move about?</td>
    <td colspan="5"> <input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
  </tr>
  <tr>
    <td colspan="6"><input type="checkbox"/> 3 Household Family Members/ Relatives</td>
    <td colspan="4"><input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
    <td colspan="5">PhP__________/___=</td>
    <td colspan="15">15. Do you have any disability?</td>
  </tr>
  <tr>
    <td colspan="6"><input type="checkbox"/> 4 Domestic Family Members / Relatives</td>
    <td colspan="4"><input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
    <td colspan="5">PhP__________/___=</td>
    <td colspan="15" rowspan="2"><input type="radio"/> 0 Yes - Disability:____________________ <input type="radio"/> 2 None</td>
  </tr>
  <tr>
    <td colspan="6"><input type="checkbox"/> 5 International Family Members/ Relatives</td>
    <td colspan="4"><input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
    <td colspan="5">PhP__________/___=</td>
  </tr>
  <tr>
    <td colspan="6"><input type="checkbox"/> 6 Friends/ Neighbors</td>
    <td colspan="4"><input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
    <td colspan="5">PhP__________/___=</td>
    <td colspan="15">15. Do you have any critical illness or disease?</td>
  </tr>
  <tr>
    <td colspan="6"><input type="checkbox"/> 7 Transfers from the Government</td>
    <td colspan="4"><input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
    <td colspan="5">PhP__________/___=</td>
    <td colspan="15" rowspan="3"><input type="radio"/> 0 Yes - Illness:____________________ <input type="radio"/> 2 None</td>
  </tr>
  <tr>
    <td colspan="6"><input type="checkbox"/> 8 Others________________________</td>
    <td colspan="4"><input type="radio"/> 0 No <input type="radio"/> 1 Yes</td>
    <td colspan="5">PhP__________/___=</td>
  </tr>
  <tr>
    <td colspan="10" style="text-align:right;">TOTAL</td>
    <td colspan="5">PhP</td>
  </tr>
  <tr>
    <td colspan="15" class="darkbluebold">III. UTILIZATION OF SOCIAL PENSION</td>
    <td colspan="15" class="darkbluebold">IV. INITIAL ASSESSMENT</td>
  </tr>
  <tr>
    <td colspan="15" class="lightbluebold">17. Where do you spend your Social Pension? Do not read the options.</td>
    <td colspan="15" class="lightbluebold">18. Initital Impression</td>
  </tr>
  <tr>
    <td colspan="15" rowspan=3>
      <input type="checkbox"/> 1 Food <br/>
      <input type="checkbox"/> 2 Medicines and Vitamins <br/>
      <input type="checkbox"/> 3 Health check-up and other hospital/ medical services <br/>
      <input type="checkbox"/> 4 Clothing <br/>
      <input type="checkbox"/> 5 Utilities (e.g. electric and water bills) <br/>
      <input type="checkbox"/> 6 Debt payment <br/>
      <input type="checkbox"/> 7 Livelihood / Entrepreneurial Activities <br/>
      <input type="checkbox"/> 8 Others________________________ <br/>
    </td>
    <td colspan="15"><input type="radio"/> 1 Eligible <input type="radio"/> 2 Not Eligible</td>
  </tr>
  <tr>
    <td colspan="15" class="darkbluebold">Accomplished by:</td>
  </tr>
  <tr>
    <td colspan="15">
      Name and Signature of Worker:_________________<br/><br/><br/>
      Date Accomplished:____________________________<br/><br/>
    </td>
  </tr>
</table>