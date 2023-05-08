<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/

$route['default_controller'] = 'Dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


//HOME/DASHBOARD
    $route['dashboard'] = 'Dashboard/index';
    $route['dash-get-data'] = 'Dashboard/get_benes_data';
    $route['search-served-data'] = 'Dashboard/search_served_data';
//HOME/DASHBOARD

//REPLACEMENT
    $route['replacement'] = 'Replacement/index';
    $route['get-replacement'] = 'Replacement/getAllPayroll';

    $route['replace-unpaid'] = 'Replacement/ReplaceUnpaidSubmit';
    $route['transfer-unpaid'] = 'Replacement/TransferUnpaidSubmit';
    $route['bulk-replace-unpaid'] = 'Replacement/BulkReplaceUnpaid';
    $route['bulk-transfer-unpaid'] = 'Replacement/BulkTransferUnpaid';
    $route['undo-replace'] = 'Replacement/ReplaceMemberUndo';
//REPLACEMENT

//REPORTS
    $route['report-served'] = 'Reports/served';
    $route['get-total-served'] = 'Reports/get_total_served_beneficiaries';

    $route['report-unclaimed'] = 'Reports/unclaimed';
    $route['get-total-unclaimed'] = 'Reports/get_total_unclaimed';

    $route['report-waitlist'] = 'Reports/waitlist';
    $route['get-total-waitlist'] = 'Reports/get_total_waitlist';

    $route['report-active'] = 'Reports/active';
    $route['get-total-active'] = 'Reports/get_total_active';

    $route['report-target'] = 'Reports/target';
    $route['get-total-target'] = 'Reports/get_total_target';

    $route['send-served-data'] = 'Reports/sendServedData';

    $route['report-repMonitoring'] = 'Reports/repwaitlistMonitoring';
    $route['get-repwaitlistMonitoring'] = 'Reports/get_repwaitlistMonitoring';
    $route['get-monthly-total-served'] = 'Reports/get_monthly_served';
    // $route['get-monthly-total-served'] = 'Reports/get_actual_month_served';

    $route['report-inactive'] = 'Reports/inactive_report';
    $route['get-total-inactive'] = 'Reports/get_inactive_report';
//REPORTS

//CROSS MATCHING
    $route['import-sp'] = 'SP_Crossmatching/uploadSP';
    $route['import-sp-uct'] = 'SP_Crossmatching/crossmatchUCT';
    $route['export-sp'] = 'SP_Crossmatching/exportDuplicate';
    $route['search-name'] = 'SP_Crossmatching/checkProbableDuplicate';
//CROSS MATCHING

//LOGIN
    $route['login'] = 'Login/index';
    $route['activate'] = 'Login/activate';
    $route['logout'] = 'Login/logout';
    $route['checklogin'] = 'Login/chklogin';
//LOGIN

//LIBRARIES
    $route['libraries/target-pensioners'] = 'Libraries/Pensioners';
    $route['save-pensioners'] = 'Libraries/Pensioners/save';
    $route['get-pensioners'] = 'Libraries/Pensioners/get_pensioners';
    $route['update-pensioners'] = 'Libraries/Pensioners/update_pensioners';
    $route['delete-pensioners'] = 'Libraries/Pensioners/delete_pensioners';
    $route['clone-pensioners'] = 'Libraries/Pensioners/clone_pensioners';

    $route['libraries/signatories'] = 'Libraries/Signatories';
    $route['get-signatories'] = 'Libraries/Signatories/get_signatories';
    $route['update-signatories'] = 'Libraries/Signatories/update_signatories';

    $route['libraries/reasons'] = 'Libraries/Reasons';
    $route['save-reasons'] = 'Libraries/Reasons/save_reasons';
    $route['get-reasons'] = 'Libraries/Reasons/get_reasons';
    $route['update-reasons'] = 'Libraries/Reasons/update_reasons';
    $route['delete-reasons'] = 'Libraries/Reasons/delete_reasons';

    $route['libraries/marital-status'] = 'Libraries/Marital_status';
    $route['save-marital-status'] = 'Libraries/Marital_status/save_marital_status';
    $route['get-marital-status'] = 'Libraries/Marital_status/get_marital_status';
    $route['update-marital-status'] = 'Libraries/Marital_status/update_marital_status';
    $route['delete-marital-status'] = 'Libraries/Marital_status/delete_marital_status';

    $route['libraries/house-type'] = 'Libraries/House_type';
    $route['save-house-type'] = 'Libraries/House_type/save_house_type';
    $route['get-house-type'] = 'Libraries/House_type/get_house_type';
    $route['update-house-type'] = 'Libraries/House_type/update_house_type';

    $route['libraries/living-arrangement'] = 'Libraries/Living_arrangement';
    $route['get-living-arrangement'] = 'Libraries/Living_arrangement/get_living_arrangement';
    $route['save-living-arrangement'] = 'Libraries/Living_arrangement/save_living_arrangement';
    $route['update-living-arrangement'] = 'Libraries/Living_arrangement/update_living_arrangement';

    $route['get-all-provinces'] = 'Libraries/Pensioners/get_provinces';
    $route['get-all-municipalities'] = 'Libraries/Pensioners/get_municipalities';
    $route['get-target-years'] = 'Libraries/Pensioners/get_target_years';
//LIBRARIES

//API / LIBRARIES
    $route['get-all-location'] = 'API/getallLocation';
    $route['get-all-libraries'] = 'API/getallLibrary';
    $route['get-all-ReplacementReason'] = 'API/getallReplacementReason';
    $route['get-all-MaritalStatus'] = 'API/getallMaritalStatus';
    $route['get-all-LivingArrangement'] = 'API/getallLivingArrangement';
    $route['get-all-Relationships'] = 'API/getallRelationships';
    $route['get-all-Disabilities'] = 'API/getallDisabilities';
    $route['get-login-user'] = 'API/getLoginData';
    $route['get-current-period'] = 'API/getCurrentPeriod';
//API / LIBRARIES

//MEMBER MODULE
    $route['get-all-Members'] = 'Member/getAllMembers';
    $route['set-ActiveIndividual'] = 'Member/setActiveIndividual';
    $route['set-ForReplacementIndividual'] = 'Member/setForReplacementIndividual';
    $route['get-Eligible-Waitlist'] = 'Member/getEligibleList';
    $route['replace-Member'] = 'Member/ReplaceMemberSubmit';
    $route['member-Edit'] = 'Member/edit';
    $route['download-member'] = 'Member/exportMasterlist';

    //Payment History
    $route['get-member-payment'] = 'Member/getMemPayment';
    $route['add-member-payment'] = 'Member/addMemPayment';
    $route['update-member-payment'] = 'Member/updateMemPayment';
    $route['delete-member-payment'] = 'Member/deleteMemPayment';
    $route['transfer-payment-location'] = 'Member/transferPaymentLocation';

    //VIEW EDIT MEMBER DETAILS
    $route['view-member/(:any)'] = 'Member/viewMember/$1';
    $route['edit-member/(:any)'] = 'Member/editMember/$1';
    $route['print-Lbp/(:any)'] = 'Member/printlbp/$1';
    $route['print-BUF/(:any)'] = 'Member/printbuf/$1';
    $route['get-member-detail'] = 'Member/getMemberDetail';
    $route['update-member-detail'] = 'Member/updateMemDetail';

//MEMBER MODULE

//WAITLIST MODULE
    //Import Export waitlist (New and Eligibility Updates)
    $route['update-waitlist-eligibility'] = 'Waitlist/updateEligibilityStatus';
    $route['download-waitlist'] = 'Waitlist/downloadWaitlist';
    $route['export-buf'] = 'Waitlist/exportWaitlist';
//WAITLIST MODULE

//LIQUIDATION MODULE
    $route['get-all-liquidation'] = 'Liquidation/getAllPayroll';
    $route['set-bene-status'] = 'Liquidation/setBeneStatus';
    $route['update-bene-payment'] = 'Liquidation/updatePaymentDetails';
    $route['batch-payment'] = 'Liquidation/batchPayment';
    $route['dl-payroll-list'] = 'Liquidation/dlPayrollList';
    $route['dl-paid-registry'] = 'Liquidation/dlPaidRegistry';

    $route['liquidation-summary'] = 'Liquidation/payrollreports';
    $route['liq-summary-export'] = 'Liquidation/generatePayrollReports';
//LIQUIDATION MODULE

//GENERATE PAYROLL MODULE
    $route['get-generated-payroll'] = 'Payroll/getAllPayroll';
    $route['export-ce-active'] = 'Payroll/exportMasterlist';
    $route['export-ce-unpaid'] = 'Payroll/exportUnpaidMasterlist';
    $route['generate-cap-active'] = 'Payroll/capGenerate';
    $route['generate-cap-unpaid'] = 'Payroll/UnpaidCapGenerate';
//GENERATE PAYROLL MODULE

//LBP ENROLLMENT
    $route['lbp-enrollment'] = 'GenerateLbp/index';
    $route['export-blank-lbp'] = 'GenerateLbp/blankLBP';
    $route['export-lbp-form'] = 'GenerateLbp/generatelbpform';
//LBP ENROLLMENT

//PROFILE
    $route['profile'] = 'User/profile';
    $route['get-user-profile'] = 'User/getUserProfile';
    $route['update-user-profile'] = 'User/updateUserProfile';
    $route['register'] = 'User/register';
    $route['save-register'] = 'User/saveRegister';
    $route['user-list'] = 'User/index';
    $route['get-user-list'] = 'User/getUserList';
    $route['update-user'] = 'User/updateUser';
    $route['delete-user'] = 'User/deleteUser';
    $route['activate-user'] = 'User/activateUser';

    $route['myLogs']    = 'User/myLogs';
    $route['getmyLogs'] = 'User/getmyLogs';
    
    $route['user-logs']    = 'User/userLogs';
    $route['get-user-Logs'] = 'User/getuserLogs';
    $route['get-all-user-for-logs'] = 'User/getUserListLogs';
    $route['reset-user-password'] = 'User/resetUserPassword';
//PROFILE




// REQUEST FORM
$route['get-list-users'] = 'RequestForm/getListOfUsers';
$route['add-new-request'] = 'RequestForm/addNewRequest';
$route['get-all-request'] = 'RequestForm/getAllRequest';
$route['get-list-users-by-status'] = 'RequestForm/getAllRequestByStatus';
$route['get-list-users-by-date'] = 'RequestForm/getAllRequestByDate';
$route['update-request-form'] = 'RequestForm/updateRequestForm';


