<?php
/**
 *
 * @version $Id:connection.php 431 2006-10-17 21:55:46 +0200 (Di, 17 Okt 2006) soeren_nb $
 * @package VirtueMart
 * @subpackage classes
 * @copyright Copyright (C) 2004-2007 soeren, 2009-2011 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See /administrator/components/com_virtuemart/COPYRIGHT.php for copyright notices and details.
 *
 * http://virtuemart.org
 */

if( !defined( '_JEXEC' ) ) die( 'Direct Access to '.basename(__FILE__).' is not allowed.' );

if(!class_exists('JModel'))
require(JPATH_ROOT . DS . 'libraries' . DS . 'joomla' . DS . 'application' . DS . 'component' . DS . 'model.php');
if(!class_exists('VmModel'))
require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmmodel.php');


class Migrator extends VmModel{

	private $_stop = false;

	public function __construct(){

// 		JTable::addIncludePath(JPATH_VM_ADMINISTRATOR . DS . 'tables');

		$this->_app = JFactory::getApplication();
		$this->_db = JFactory::getDBO();
		$this->_oldToNew = new stdClass();
		$this->starttime = microtime(true);

		$max_execution_time = (int)ini_get('max_execution_time');
		$jrmax_execution_time= JRequest::getInt('max_execution_time');

		if(!empty($jrmax_execution_time)){
			// 			vmdebug('$jrmax_execution_time',$jrmax_execution_time);
			if($max_execution_time!=$jrmax_execution_time) @ini_set( 'max_execution_time', $jrmax_execution_time );
		} else if($max_execution_time<60) {
			@ini_set( 'max_execution_time', 60 );
		}

		$this->maxScriptTime = ini_get('max_execution_time')*0.80-1;	//Lets use 30% of the execution time as reserve to store the progress

		$jrmemory_limit= JRequest::getInt('memory_limit');
		if(!empty($jrmemory_limit)){
			@ini_set( 'memory_limit', $jrmemory_limit.'M' );
		} else {
			$memory_limit = (int) substr(ini_get('memory_limit'),0,-1);
			if($memory_limit<128)  @ini_set( 'memory_limit', '128M' );
		}

		$this->maxMemoryLimit = $this->return_bytes(ini_get('memory_limit')) - (14 * 1024 * 1024)  ;		//Lets use 11MB for joomla
		// 		vmdebug('$this->maxMemoryLimit',$this->maxMemoryLimit); //134217728
		//$this->maxMemoryLimit = $this -> return_bytes('20M');

		// 		ini_set('memory_limit','35M');
		$q = 'SELECT `id` FROM `#__virtuemart_migration_oldtonew_ids` ';
		$this->_db->setQuery($q);
		$res = $this->_db->loadResult();
		if(empty($res)){
			$q = 'INSERT INTO `#__virtuemart_migration_oldtonew_ids` (`id`) VALUES ("1")';
			$this->_db->setQuery($q);
			$this->_db->query();
			$this->_app->enqueueMessage('Start with a new migration process and setup log maxScriptTime '.$this->maxScriptTime.' maxMemoryLimit '.$this->maxMemoryLimit/(1024*1024));
		} else {
			$this->_app->enqueueMessage('Found prior migration process, resume migration maxScriptTime '.$this->maxScriptTime.' maxMemoryLimit '.$this->maxMemoryLimit/(1024*1024));
		}

		$this->_keepOldProductIds = VmConfig::get('keepOldProductIds',FALSE);
	}

	private function return_bytes($val) {
		$val = trim($val);
		$last = strtolower($val[strlen($val)-1]);
		switch($last) {
			// The 'G' modifier is available since PHP 5.1.0
			case 'g':
				$val *= 1024;
			case 'm':
				$val *= 1024;
			case 'k':
				$val *= 1024;
		}

		return $val;
	}

	function getMigrationProgress($group){

		$q = 'SELECT `'.$group.'` FROM `#__virtuemart_migration_oldtonew_ids` WHERE `id` = "1" ';

		$this->_db->setQuery($q);
		$result = $this->_db->loadResult();
		if(empty($result)){
			$result = array();
		} else {
			// 			vmdebug('getMigrationProgress '.$group,$result);
			$uresult = unserialize(trim($result));
			if(!$uresult){
				vmdebug('getMigrationProgress unserialize failed '.$group,$result);
				// 				vmWarn('getMigrationProgress '.$group.' array is created new and therefore empty $q '.$q.' '.print_r($uresult,1).' <pre>'.print_r($result,1).'</pre>');
				$result = array();
			} else {
				$result = $uresult;
			}
		}

		// 		$test = 'a:740:{i:2954;i:18;i:2955;i:45;i:2956;i:143;i:2957;i:1710;i:2958;i:51;i:2959;i:154;i:2960;i:46;i:2961;i:30;i:2962;i:40;i:2963;i:41;i:2964;i:42;i:2965;i:43;i:2966;i:44;i:2967;i:31;i:2968;i:32;i:2969;i:33;i:2970;i:34;i:2971;i:35;i:2972;i:36;i:2973;i:37;i:2974;i:38;i:2975;i:39;i:2976;i:56;i:2977;i:61;i:2978;i:66;i:2979;i:86;i:2980;i:91;i:2981;i:96;i:2982;i:101;i:2983;i:106;i:2984;i:111;i:2985;i:116;i:2986;i:121;i:2987;i:131;i:2988;i:137;i:2989;i:142;i:2990;i:148;i:2991;i:153;i:2992;i:159;i:2993;i:164;i:2994;i:169;i:2995;i:174;i:2996;i:179;i:2997;i:184;i:2998;i:189;i:2999;i:193;i:3000;i:197;i:3001;i:201;i:3002;i:206;i:3003;i:210;i:3004;i:214;i:3005;i:218;i:3006;i:222;i:3007;i:226;i:3008;i:230;i:3009;i:234;i:3010;i:238;i:3011;i:242;i:3012;i:246;i:3013;i:250;i:3014;i:254;i:3015;i:258;i:3016;i:262;i:3017;i:266;i:3018;i:798;i:3019;i:270;i:3020;i:274;i:3021;i:803;i:3022;i:278;i:3023;i:282;i:3024;i:786;i:3025;i:286;i:3026;i:787;i:3027;i:290;i:3028;i:788;i:3029;i:294;i:3030;i:298;i:3031;i:302;i:3032;i:306;i:3033;i:781;i:3034;i:310;i:3035;i:314;i:3036;i:318;i:3037;i:322;i:3038;i:326;i:3039;i:330;i:3040;i:334;i:3041;i:338;i:3042;i:342;i:3043;i:346;i:3044;i:350;i:3045;i:354;i:3046;i:358;i:3047;i:362;i:3048;i:366;i:3049;i:370;i:3050;i:382;i:3051;i:602;i:3052;i:377;i:3053;i:522;i:3054;i:706;i:3055;i:710;i:3056;i:398;i:3057;i:702;i:3058;i:402;i:3059;i:770;i:3060;i:406;i:3061;i:766;i:3062;i:410;i:3063;i:762;i:3064;i:414;i:3065;i:418;i:3066;i:422;i:3067;i:775;i:3068;i:426;i:3069;i:430;i:3070;i:714;i:3071;i:434;i:3072;i:698;i:3073;i:438;i:3074;i:442;i:3075;i:722;i:3076;i:446;i:3077;i:450;i:3078;i:718;i:3079;i:454;i:3080;i:458;i:3081;i:462;i:3082;i:474;i:3083;i:466;i:3084;i:470;i:3085;i:478;i:3086;i:482;i:3087;i:486;i:3088;i:490;i:3089;i:494;i:3090;i:498;i:3091;i:502;i:3092;i:506;i:3093;i:510;i:3094;i:514;i:3095;i:518;i:3096;i:526;i:3097;i:531;i:3098;i:536;i:3099;i:541;i:3100;i:553;i:3101;i:548;i:3102;i:554;i:3103;i:559;i:3104;i:730;i:3105;i:563;i:3106;i:726;i:3107;i:567;i:3108;i:571;i:3109;i:575;i:3110;i:734;i:3111;i:579;i:3112;i:585;i:3113;i:590;i:3114;i:591;i:3115;i:597;i:3116;i:603;i:3117;i:608;i:3118;i:613;i:3119;i:618;i:3120;i:623;i:3121;i:628;i:3122;i:633;i:3123;i:638;i:3124;i:643;i:3125;i:648;i:3126;i:750;i:3127;i:653;i:3128;i:746;i:3129;i:658;i:3130;i:663;i:3131;i:742;i:3132;i:668;i:3133;i:673;i:3134;i:678;i:3135;i:793;i:3136;i:682;i:3137;i:738;i:3138;i:686;i:3139;i:690;i:3140;i:694;i:3141;i:808;i:3142;i:813;i:3143;i:818;i:3144;i:822;i:3145;i:826;i:3146;i:830;i:3147;i:834;i:3148;i:838;i:3149;i:842;i:3150;i:846;i:3151;i:850;i:3152;i:854;i:3153;i:858;i:3154;i:862;i:3155;i:866;i:3156;i:870;i:3157;i:1709;i:3158;i:1313;i:3159;i:1050;i:3160;i:981;i:3161;i:1030;i:3162;i:885;i:3163;i:876;i:3164;i:880;i:3165;i:889;i:3166;i:893;i:3167;i:899;i:3168;i:898;i:3169;i:904;i:3170;i:908;i:3171;i:912;i:3172;i:916;i:3173;i:920;i:3174;i:924;i:3175;i:925;i:3176;i:1024;i:3177;i:930;i:3178;i:935;i:3179;i:940;i:3180;i:945;i:3181;i:946;i:3182;i:951;i:3183;i:956;i:3184;i:961;i:3185;i:965;i:3186;i:1078;i:3187;i:969;i:3188;i:973;i:3189;i:977;i:3190;i:1090;i:3191;i:1698;i:3192;i:986;i:3193;i:991;i:3194;i:996;i:3195;i:1001;i:3196;i:1006;i:3197;i:1010;i:3198;i:1015;i:3199;i:1019;i:3200;i:1023;i:3201;i:1025;i:3202;i:1856;i:3203;i:1035;i:3204;i:1039;i:3205;i:1044;i:3206;i:1045;i:3207;i:1055;i:3208;i:1060;i:3209;i:1065;i:3210;i:1070;i:3211;i:1074;i:3212;i:1082;i:3213;i:1086;i:3214;i:3033;i:3215;i:1095;i:3216;i:1688;i:3217;i:1100;i:3218;i:1105;i:3219;i:1279;i:3220;i:1110;i:3221;i:1114;i:3222;i:1119;i:3223;i:1693;i:3224;i:1124;i:3225;i:1129;i:3226;i:1134;i:3227;i:1139;i:3228;i:1144;i:3229;i:1149;i:3230;i:1154;i:3231;i:1159;i:3232;i:1164;i:3233;i:1711;i:3234;i:1169;i:3235;i:1174;i:3236;i:1179;i:3237;i:1184;i:3238;i:1189;i:3239;i:1194;i:3240;i:1708;i:3241;i:1199;i:3242;i:1204;i:3243;i:1209;i:3244;i:1214;i:3245;i:1219;i:3246;i:1224;i:3247;i:1229;i:3248;i:1234;i:3249;i:1238;i:3250;i:1242;i:3251;i:1246;i:3252;i:1250;i:3253;i:1254;i:3254;i:1259;i:3255;i:1263;i:3256;i:1267;i:3257;i:1271;i:3258;i:1275;i:3259;i:1284;i:3260;i:1289;i:3261;i:1294;i:3262;i:1299;i:3263;i:1304;i:3264;i:1326;i:3265;i:1309;i:3266;i:1314;i:3267;i:1315;i:3268;i:1316;i:3269;i:1321;i:3270;i:1340;i:3271;i:1331;i:3272;i:1336;i:3273;i:1673;i:3274;i:1345;i:3275;i:1350;i:3276;i:1355;i:3277;i:1367;i:3278;i:1363;i:3279;i:1371;i:3280;i:1375;i:3281;i:1379;i:3282;i:1384;i:3283;i:1388;i:3284;i:1392;i:3285;i:1397;i:3286;i:1402;i:3287;i:1406;i:3288;i:1953;i:3289;i:1410;i:3290;i:1414;i:3291;i:1633;i:3292;i:1930;i:3293;i:1418;i:3294;i:1423;i:3295;i:1428;i:3296;i:1433;i:3297;i:1437;i:3298;i:1441;i:3299;i:1442;i:3300;i:1443;i:3301;i:1444;i:3302;i:1445;i:3303;i:1446;i:3304;i:1447;i:3305;i:1448;i:3306;i:1449;i:3307;i:1450;i:3308;i:1451;i:3309;i:1452;i:3310;i:1453;i:3311;i:1454;i:3312;i:1455;i:3313;i:1456;i:3314;i:1457;i:3315;i:1458;i:3316;i:1459;i:3317;i:1463;i:3318;i:1467;i:3319;i:1471;i:3320;i:1476;i:3321;i:1481;i:3322;i:1493;i:3323;i:1497;i:3324;i:1501;i:3325;i:1505;i:3326;i:1510;i:3327;i:1515;i:3328;i:1519;i:3329;i:1523;i:3330;i:1527;i:3331;i:1528;i:3332;i:1537;i:3333;i:1542;i:3334;i:1547;i:3335;i:1552;i:3336;i:1557;i:3337;i:1558;i:3338;i:1563;i:3339;i:1568;i:3340;i:1573;i:3341;i:1575;i:3342;i:1580;i:3343;i:1585;i:3344;i:1590;i:3345;i:1595;i:3346;i:1600;i:3347;i:1605;i:3348;i:1610;i:3349;i:1792;i:3350;i:1615;i:3351;i:1620;i:3352;i:1625;i:3353;i:1629;i:3354;i:1637;i:3355;i:1641;i:3356;i:1645;i:3357;i:1649;i:3358;i:1654;i:3359;i:1658;i:3360;i:1663;i:3361;i:1668;i:3362;i:1678;i:3363;i:1683;i:3364;i:1703;i:3365;i:1718;i:3366;i:1723;i:3367;i:1728;i:3368;i:1865;i:3369;i:1748;i:3370;i:1733;i:3371;i:1958;i:3372;i:1738;i:3373;i:1743;i:3374;i:1753;i:3375;i:1758;i:3376;i:1762;i:3377;i:1767;i:3378;i:1772;i:3379;i:1777;i:3380;i:1782;i:3381;i:1787;i:3382;i:1797;i:3383;i:1802;i:3384;i:1807;i:3385;i:1811;i:3386;i:1815;i:3387;i:1819;i:3388;i:1823;i:3389;i:1828;i:3390;i:1832;i:3391;i:1836;i:3392;i:1841;i:3393;i:1846;i:3394;i:1851;i:3395;i:1860;i:3396;i:1870;i:3397;i:1875;i:3398;i:1880;i:3399;i:1884;i:3400;i:1889;i:3401;i:1894;i:3402;i:1898;i:3403;i:1902;i:3404;i:1907;i:3405;i:1912;i:3406;i:1917;i:3407;i:1921;i:3408;i:1926;i:3409;i:2846;i:3410;i:1935;i:3411;i:1939;i:3412;i:1943;i:3413;i:1948;i:3414;i:1963;i:3415;i:2011;i:3416;i:1967;i:3417;i:1971;i:3418;i:1975;i:3419;i:1980;i:3420;i:1985;i:3421;i:1989;i:3422;i:1993;i:3423;i:1997;i:3424;i:2002;i:3425;i:2007;i:3426;i:2015;i:3427;i:2020;i:3428;i:2025;i:3429;i:2926;i:3430;i:2029;i:3431;i:2034;i:3432;i:2038;i:3433;i:2042;i:3434;i:2046;i:3435;i:2051;i:3436;i:2056;i:3437;i:2060;i:3438;i:2065;i:3439;i:2070;i:3440;i:2074;i:3441;i:2079;i:3442;i:2084;i:3443;i:2089;i:3444;i:2093;i:3445;i:2097;i:3446;i:2101;i:3447;i:2106;i:3448;i:2111;i:3449;i:2115;i:3450;i:2120;i:3451;i:2125;i:3452;i:2130;i:3453;i:2134;i:3454;i:2228;i:3455;i:2138;i:3456;i:2142;i:3457;i:2147;i:3458;i:2151;i:3459;i:2156;i:3460;i:2204;i:3461;i:2404;i:3462;i:2721;i:3463;i:2160;i:3464;i:2667;i:3465;i:2164;i:3466;i:2168;i:3467;i:2575;i:3468;i:2173;i:3469;i:2177;i:3470;i:2181;i:3471;i:2185;i:3472;i:2189;i:3473;i:2194;i:3474;i:2199;i:3475;i:2208;i:3476;i:2212;i:3477;i:2216;i:3478;i:2220;i:3479;i:2224;i:3480;i:2233;i:3481;i:2238;i:3482;i:2243;i:3483;i:2247;i:3484;i:2251;i:3485;i:2256;i:3486;i:2260;i:3487;i:2279;i:3488;i:2265;i:3489;i:2269;i:3490;i:2274;i:3491;i:2284;i:3492;i:3008;i:3493;i:2289;i:3494;i:2294;i:3495;i:2299;i:3496;i:2304;i:3497;i:2308;i:3498;i:2312;i:3499;i:2316;i:3500;i:2320;i:3501;i:2324;i:3502;i:2328;i:3503;i:2332;i:3504;i:2336;i:3505;i:2340;i:3506;i:2345;i:3507;i:2349;i:3508;i:2353;i:3509;i:2357;i:3510;i:2358;i:3511;i:2362;i:3512;i:2368;i:3513;i:2372;i:3514;i:2831;i:3515;i:2376;i:3516;i:2380;i:3517;i:2384;i:3518;i:2389;i:3519;i:2394;i:3520;i:2399;i:3521;i:2409;i:3522;i:2413;i:3523;i:2417;i:3524;i:2422;i:3525;i:2426;i:3526;i:2431;i:3527;i:2436;i:3528;i:2437;i:3529;i:2438;i:3530;i:2439;i:3531;i:2440;i:3532;i:2441;i:3533;i:2442;i:3534;i:2443;i:3535;i:2444;i:3536;i:2445;i:3537;i:2447;i:3538;i:2462;i:3539;i:2483;i:3540;i:2477;i:3541;i:2484;i:3542;i:2485;i:3543;i:2486;i:3544;i:2487;i:3545;i:2491;i:3546;i:2509;i:3547;i:2522;i:3548;i:2517;i:3549;i:2506;i:3550;i:2507;i:3551;i:2508;i:3552;i:2510;i:3553;i:2511;i:3554;i:2512;i:3555;i:2513;i:3556;i:2514;i:3557;i:2515;i:3558;i:2516;i:3559;i:2523;i:3560;i:2536;i:3561;i:2540;i:3562;i:2544;i:3563;i:2548;i:3564;i:2561;i:3565;i:2571;i:3566;i:2567;i:3567;i:2588;i:3568;i:2593;i:3569;i:2598;i:3570;i:2611;i:3571;i:2616;i:3572;i:2658;i:3573;i:2629;i:3574;i:2630;i:3575;i:2631;i:3576;i:2644;i:3577;i:2632;i:3578;i:2636;i:3579;i:2654;i:3580;i:2648;i:3581;i:2662;i:3582;i:2666;i:3583;i:2789;i:3584;i:2672;i:3585;i:2676;i:3586;i:2680;i:3587;i:2685;i:3588;i:2686;i:3589;i:2687;i:3590;i:2688;i:3591;i:2689;i:3592;i:2690;i:3593;i:2691;i:3594;i:2692;i:3595;i:2693;i:3596;i:2771;i:3597;i:2772;i:3598;i:2703;i:3599;i:2716;i:3600;i:2725;i:3601;i:2729;i:3602;i:2733;i:3603;i:2738;i:3604;i:2742;i:3605;i:2747;i:3606;i:2751;i:3607;i:2756;i:3608;i:2760;i:3609;i:2761;i:3610;i:2766;i:3611;i:2775;i:3612;i:2776;i:3613;i:2777;i:3614;i:2778;i:3615;i:2770;i:3616;i:2773;i:3617;i:2774;i:3618;i:2779;i:3619;i:2794;i:3620;i:2799;i:3621;i:2800;i:3622;i:2804;i:3623;i:2805;i:3624;i:2809;i:3625;i:2810;i:3626;i:2814;i:3627;i:2818;i:3628;i:2823;i:3629;i:2827;i:3630;i:2836;i:3631;i:2841;i:3632;i:2856;i:3633;i:2857;i:3634;i:2858;i:3635;i:2859;i:3636;i:2860;i:3637;i:2864;i:3638;i:2868;i:3639;i:2873;i:3640;i:2877;i:3641;i:2883;i:3642;i:2882;i:3643;i:2884;i:3644;i:2889;i:3645;i:2894;i:3646;i:2898;i:3647;i:2902;i:3648;i:2907;i:3649;i:2912;i:3650;i:2916;i:3651;i:2922;i:3652;i:2931;i:3653;i:2945;i:3654;i:2949;i:3655;i:2944;i:3656;i:2950;i:3657;i:2951;i:3658;i:2952;i:3659;i:2953;i:3660;i:2954;i:3661;i:2955;i:3662;i:2961;i:3663;i:2965;i:3664;i:2970;i:3665;i:2975;i:3666;i:2980;i:3667;i:2985;i:3668;i:2999;i:3669;i:2990;i:3670;i:2991;i:3671;i:2995;i:3672;i:3003;i:3673;i:3007;i:3674;i:3013;i:3675;i:3018;i:3676;i:3023;i:3677;i:3028;i:3678;i:3037;i:3679;i:3041;i:3680;i:3046;i:3681;i:3050;i:3682;i:3054;i:3683;i:3059;i:3684;i:3064;i:3685;i:3067;i:3686;i:3070;i:3687;i:3075;i:3688;i:3078;i:3689;i:3081;i:3690;i:3084;i:3691;i:3088;i:3692;i:3092;i:3693;i:3096;i:3694;}';
		// 		$utest = unserialize(trim($test));
		// 		vmdebug('$utest',$utest);
		return $result;

	}

	function storeMigrationProgress($group,$array, $limit = ''){

		// 		vmdebug('storeMigrationProgress',$array);
		//$q = 'UPDATE `#__virtuemart_migration_oldtonew_ids` SET `'.$group.'`="'.implode(',',$array).'" WHERE `id` = "1"';

		$q = 'UPDATE `#__virtuemart_migration_oldtonew_ids` SET `'.$group.'`="'.serialize($array).'" '.$limit.' WHERE `id` = "1"';

		$this->_db->setQuery($q);
		if(!$this->_db->query()){
			$this->_app->enqueueMessage('storeMigrationProgress failed to update query '.$this->_db->getQuery());
			$this->_app->enqueueMessage('and ErrrorMsg '.$this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	function migrateGeneral(){

		$result = $this->portMedia();
		$result = $this->portShoppergroups();
		$result = $this->portCategories();
		$result = $this->portManufacturerCategories();
		$result = $this->portManufacturers();
		// 		$result = $this->portOrderStatus();

		$time = microtime(true) - $this->starttime;
		vmInfo('Worked on general migration for '.$time.' seconds');
		vmRamPeak('Migrate general vm1 info ended ');
		return $result;
	}

	function migrateUsers(){

		// 		$result = $this->portShoppergroups();
		$result = $this->portUsers();

		$time = microtime(true) - $this->starttime;
		vmInfo('Worked on user migration for '.$time.' seconds');
		vmRamPeak('Migrate shoppers ended ');
		return $result;
	}

	function migrateProducts(){

		// 		$result = $this->portMedia();

		// 		$result = $this->portCategories();
		// 		$result = $this->portManufacturerCategories();
		// 		$result = $this->portManufacturers();
		$result = $this->portProducts();

		$time = microtime(true) - $this->starttime;
		$this->_app->enqueueMessage('Worked on general migration for '.$time.' seconds');

		return $result;
	}

	function migrateOrders(){

		// 		$result = $this->portMedia();
		// 		$result = $this->portCategories();
		// 		$result = $this->portManufacturerCategories();
		// 		$result = $this->portManufacturers();
// 		$result = $this->portProducts();

		// 		$result = $this->portOrderStatus();
		$result = $this->portOrders();
		$time = microtime(true) - $this->starttime;
		vmInfo('Worked on migration for '.$time.' seconds');

		return $result;
	}

	function migrateAllInOne(){

		$result = $this->portMedia();

		$result = $this->portShoppergroups();
		$result = $this->portUsers();
		$result = $this->portVendor();

		$result = $this->portCategories();
		$result = $this->portManufacturerCategories();
		$result = $this->portManufacturers();
		$result = $this->portProducts();

		//$result = $this->portOrderStatus();
		$result = $this->portOrders();
		$time = microtime(true) - $this->starttime;
		$this->_app->enqueueMessage('Worked on migration for '.$time.' seconds');

		vmRamPeak('Migrate all ended ');
		return $result;
	}

	public function portMedia(){

		$ok = true;
		JRequest::setVar('synchronise',true);
		//Prevents search field from interfering with syncronization
		JRequest::setVar('searchMedia', '');

		//$imageExtensions = array('jpg','jpeg','gif','png');

		if(!class_exists('VirtueMartModelMedia'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'media.php');
		$this->mediaModel = VmModel::getModel('Media');
		//First lets read which files are already stored
		$this->storedMedias = $this->mediaModel->getFiles(false, true);

		//check for entries without file
		foreach($this->storedMedias as $media){

			if($media->file_is_forSale!=1){
				$media_path = JPATH_ROOT.DS.str_replace('/',DS,$media->file_url);
			} else {
				$media_path = $media->file_url;
			}

			if(!file_exists($media_path)){
				vmInfo('File for '.$media_path.' is missing');

				//The idea is here to test if the media with missing data is used somewhere and to display it
				//When it not used, the entry should be deleted then.
				/*				$q = 'SELECT * FROM `#__virtuemart_category_medias` as cm,
				`#__virtuemart_product_medias` as pm,
				`#__virtuemart_manufacturer_medias` as mm,
				`#__virtuemart_vendor_medias` as vm
				WHERE cm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
				OR pm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
				OR mm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'"
				OR vm.`virtuemart_media_id` = "'.$media->virtuemart_media_id.'" ';

				$this->_db->setQuery($q);
				$res = $this->_db->loadResultArray();
				vmdebug('so',$res);
				if(count($res)>0){
				vmInfo('File for '.$media->file_url.' is missing, but used ');
				}
				*/
			}
		}


		$countTotal = 0;
		//We do it per type
		$url = VmConfig::get('media_product_path');
		$type = 'product';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
		}

		$url = VmConfig::get('media_category_path');
		$type = 'category';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
		}

		$url = VmConfig::get('media_manufacturer_path');
		$type = 'manufacturer';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_NOT_FINISH', $countTotal);
		}

		$url = VmConfig::get('media_vendor_path');
		$type = 'vendor';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));

		$url = VmConfig::get('forSale_path');
		$type = 'forSale';
		$count = $this->_portMediaByType($url, $type);
		$countTotal += $count;
		$this->_app->enqueueMessage(JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT', $count, $type, $url));


		return $msg = JText::sprintf('COM_VIRTUEMART_UPDATE_PORT_MEDIA_RESULT_FINISH', $countTotal);
	}

	private function _portMediaByType($url, $type){

		$knownNames = array();
		//create array of filenames for easier handling
		foreach($this->storedMedias as $media){
			if($media->file_type == $type){
				//Somehow we must use here the right char encoding, so that it works below
				// in line 320
				$knownNames[] = $media->file_url;
			}
		}

		$filesInDir = array();
		$foldersInDir = array();

		if($type!='forSale'){

			$path = str_replace('/', DS, $url);
			$foldersInDir = array(JPATH_ROOT . DS . $path);
		} else {
			$foldersInDir = array($url);
		}

		if (!is_dir($foldersInDir[0])) {
			vmError($type.' Path/Url is not set correct :'.$foldersInDir[0]);
			return 0;
		}

		while(!empty($foldersInDir)){
			foreach($foldersInDir as $dir){
				$subfoldersInDir = null;
				$subfoldersInDir = array();
				if($type!='forSale'){
					$relUrl = str_replace(DS, '/', substr($dir, strlen(JPATH_ROOT . DS)));
				} else {
// 					vmdebug('$dir',$dir);
					$relUrl = $dir;
				}
				if($handle = opendir($dir)){
					while(false !== ($file = readdir($handle))){

						//$file != "." && $file != ".." replaced by strpos
						if(!empty($file) && strpos($file,'.')!==0  && $file != 'index.html'){

							$filetype = filetype($dir . DS . $file);
							$relUrlName = '';
							$relUrlName = $relUrl.$file;
							// vmdebug('my relative url ',$relUrlName);

							//We port all type of media, regardless the extension
							if($filetype == 'file'){
								if(!in_array($relUrlName, $knownNames)){
									$filesInDir[] = array('filename' => $file, 'url' => $relUrl);
								}
							}else {
								if($filetype == 'dir' && $file != 'resized' && $file != 'invoices'){
									$subfoldersInDir[] = $dir.$file.DS;
									// 									vmdebug('my sub folder ',$dir.$file);
								}
							}
						}

						if((microtime(true)-$this->starttime) >= ($this->maxScriptTime*0.4)){
							break;
						}
					}
				}
				$foldersInDir = $subfoldersInDir;
				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime*0.4)){
					break;
				}
			}
			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime*0.4)){
				break;
			}
		}

		$i = 0;
		foreach($filesInDir as $file){

			$data = null;
			$data = array('file_title' => $file['filename'],
		    'virtuemart_vendor_id' => 1,
		    'file_description' => $file['filename'],
		    'file_meta' => $file['filename'],
		    'file_url' => $file['url'] . $file['filename'],
	    	 'media_published' => 1
			);

			if($type == 'product') $data['file_is_product_image'] = 1;
			if($type == 'forSale') $data['file_is_forSale'] = 1;

			$this->mediaModel->setId(0);
			$success = $this->mediaModel->store($data, $type);
			$errors = $this->mediaModel->getErrors();
			foreach($errors as $error){
				$this->_app->enqueueMessage('Migrator ' . $error);
			}
			$this->mediaModel->resetErrors();
			if($success) $i++;
			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				vmError('Attention script time too short, no time left to store the media, please rise script execution time');
				break;
			}
		}

		return $i;
	}

	private function portShoppergroups(){

		if($this->_stop || (microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		$query = 'SHOW TABLES LIKE "%vm_shopper_group%"';
		$this->_db->setQuery($query);
		if(!$this->_db->loadResult()){
			vmInfo('No Shoppergroup table found for migration');
			$this->_stop = true;
			return false;
		}

		$ok = true;

		$q = 'SELECT * FROM #__vm_shopper_group';
		$this->_db->setQuery($q);
		$oldShopperGroups = $this->_db->loadAssocList();
		if(empty($oldShopperGroups)) $oldShopperGroups = array();

		$oldtoNewShoppergroups = array();
		$alreadyKnownIds = $this->getMigrationProgress('shoppergroups');

		$starttime = microtime(true);
		$i = 0;
		foreach($oldShopperGroups as $oldgroup){

			if(!array_key_exists($oldgroup['shopper_group_id'],$alreadyKnownIds)){
				$sGroups = null;
				$sGroups = array();
				//$category['virtuemart_category_id'] = $oldcategory['category_id'];
				$sGroups['virtuemart_vendor_id'] = $oldgroup['vendor_id'];
				$sGroups['shopper_group_name'] = $oldgroup['shopper_group_name'];

				$sGroups['shopper_group_desc'] = $oldgroup['shopper_group_desc'];
				$sGroups['published'] = 1;
				$sGroups['default'] = $oldgroup['default'];

				$table = $this->getTable('shoppergroups');

				$table->bindChecknStore($sGroups);
				$errors = $table->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						vmError('Migrator portShoppergroups '.$error);
					}
					break;
				}

				// 				$oldtoNewShoppergroups[$oldgroup['shopper_group_id']] = $sGroups['virtuemart_shoppergroup_id'];
				$alreadyKnownIds[$oldgroup['shopper_group_id']] = $sGroups['virtuemart_shoppergroup_id'];
				unset($sGroups['virtuemart_shoppergroup_id']);
				$i++;
			}
			// 			else {
			// 				$oldtoNewShoppergroups[$oldgroup['shopper_group_id']] = $alreadyKnownIds[$oldgroup['shopper_group_id']];
			// 			}

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}
		}

		$time = microtime(true) - $starttime;
		$this->_app->enqueueMessage('Processed '.$i.' vm1 shoppergroups time: '.$time);

		$this->storeMigrationProgress('shoppergroups',$alreadyKnownIds);

	}

	private function portUsers(){

		if($this->_stop || (microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		$query = 'SHOW TABLES LIKE "%vm_user_info%"';
		$this->_db->setQuery($query);
		if(!$this->_db->loadResult()){
			vmInfo('No vm_user_info table found for migration');
			$this->_stop = true;
			return false;
		}
		//declaration _vm_userfield >> _virtuemart_userfields`

		// vendor_id >> virtuemart_vendor_id
		$this->_db->setQuery('select `name` FROM `#__virtuemart_userfields`');
		$vm2Fields = $this->_db->loadResultArray ();
		$this->_db->setQuery('select * FROM `#__vm_userfield`');
		$oldfields = $this->_db->loadObjectList();
		$migratedfields ='';
		$userfields      = $this->getTable('userfields');
		$userinfo   = $this->getTable('userinfos');
		$orderinfo  = $this->getTable('order_userinfos');
		foreach ($oldfields as $field ) {
			if ($field->name =='country' or $field->name =='state') continue;
			if (!isset($field->shipment)) $field->shipment = 0 ;
			if ( !in_array( $field->name, $vm2Fields ) ) {
				$q = 'INSERT INTO `#__virtuemart_userfields` ( `name`, `title`, `description`, `type`, `maxlength`, `size`, `required`, `ordering`, `cols`, `rows`, `value`, `default`, `published`, `registration`, `shipment`, `account`, `readonly`, `calculated`, `sys`, `virtuemart_vendor_id`, `params`)
					VALUES ( "'.$field->name.'"," '.$field->title .'"," '.$field->description .'"," '.$field->type .'"," '.$field->maxlength .'"," '.$field->size .'"," '.$field->required .'"," '.$field->ordering .'"," '.$field->cols .'"," '.$field->rows .'"," '.$field->value .'"," '.$field->default .'"," '.$field->published .'"," '.$field->registration .'"," '.$field->shipment .'"," '.$field->account .'"," '.$field->readonly .'"," '.$field->calculated .'"," '.$field->sys .'"," '.$field->vendor_id .'"," '.$field->params .'" )';
				$this->_db->setQuery($q);
				$this->_db->query();
				if ($this->_db->getErrorNum()) {
					vmError ($this->_db->getErrorMsg() );
				}
				$userfields->type = $field->type;
				$type = $userfields->formatFieldType($field);
				if (!$userinfo->_modifyColumn ('ADD', $field->name, $type)) {
					vmError($userinfo->getError());
					return false;
				}

				// Alter the order_userinfo table
				if (!$orderinfo->_modifyColumn ('ADD',$field->name, $type)) {
					vmError($orderinfo->getError());
					return false;
				}
				$migratedfields .= '['.$field->name.'] ';

			}
		}
		if ($migratedfields) vminfo('Userfield declaration '.$migratedfields.' Migrated');
		$oldToNewShoppergroups = $this->getMigrationProgress('shoppergroups');
		if(empty($oldToNewShoppergroups)){
			vmInfo('portUsers getMigrationProgress shoppergroups ' . $this->_db->getErrorMsg());
			return false;
		}

		if(!class_exists('VirtueMartModelUser')) require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'user.php');
		$userModel = VmModel::getModel('user');

		$ok = true;
		$continue = true;

		//approximatly 110 users take a 1 MB
		$maxItems = $this->_getMaxItems('Users');


		// 		$maxItems = 10;
		$i=0;
		$startLimit = 0;
		$goForST = true;

		while($continue){

			//Lets load all users from the joomla hmm or vm? VM1 users does NOT exist
			$q = 'SELECT `p`.*,`ui`.*,`svx`.*,`aug`.*,`ag`.*,`vmu`.virtuemart_user_id FROM #__users AS `p`
								LEFT OUTER JOIN #__vm_user_info AS `ui` ON `ui`.user_id = `p`.id
								LEFT OUTER JOIN #__vm_shopper_vendor_xref AS `svx` ON `svx`.user_id = `p`.id
								LEFT OUTER JOIN #__vm_auth_user_group AS `aug` ON `aug`.user_id = `p`.id
								LEFT OUTER JOIN #__vm_auth_group AS `ag` ON `ag`.group_id = `aug`.group_id
								LEFT OUTER JOIN #__virtuemart_vmusers AS `vmu` ON `vmu`.virtuemart_user_id = `p`.id
								WHERE (`vmu`.virtuemart_user_id) IS NULL  LIMIT '.$startLimit.','.$maxItems ;

			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port shoppers');
			$oldUsers = $res[0];
			$startLimit = $res[1];
			$continue = $res[2];

			$starttime = microtime(true);

			foreach($oldUsers as $user){

				$user['virtuemart_country_id'] = $this->getCountryIDByName($user['country']);
				$user['virtuemart_state_id'] = $this->getStateIDByName($user['state']);

				if(!empty($user['shopper_group_id'])){
					$user['virtuemart_shoppergroups_id'] = $oldToNewShoppergroups[$user['shopper_group_id']];
				}

				$user['virtuemart_user_id'] = $user['id'];
				//$userModel->setUserId($user['id']);
				$userModel->setId($user['id']);		//Should work with setId, because only administrators are allowed todo the migration

				//Save the VM user stuff
				if(!$saveUserData=$userModel->saveUserData($user,false)){
					vmdebug('Error migration saveUserData ');
					// 					vmError(JText::_('COM_VIRTUEMART_NOT_ABLE_TO_SAVE_USER_DATA'));
				}


				$userfielddata = $userModel->_prepareUserFields($user, 'BT');

				$userinfo = $this->getTable('userinfos');
				if (!$userinfo->bindChecknStore($userfielddata)) {
					vmError('Migration storeAddress BT '.$userinfo->getError());
				}

				// 				$userinfo   = $this->getTable('userinfos');
				// 				if (!$userinfo->bindChecknStore($user)) {
				// 					vmError('Migrator portUsers '.$userinfo->getError());
				// 				}

				if(!empty($user['user_is_vendor']) && $user['user_is_vendor'] === 1){
					if (!$userModel->storeVendorData($user)){
						vmError('Migrator portUsers '.$userModel->getError());
					}
				}

				$i++;
				/*					if($i>1240){
				 $continue = false;
				break;
				}*/
				$errors = $userModel->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						vmError('Migrator portUsers '.$error);
					}
					$userModel->resetErrors();
					$continue = false;
					//break;
				}

				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					$goForST = false;
					break;
				}
			}
		}

		$time = microtime(true) - $starttime;
		vmInfo('Processed '.$i.' vm1 users time: '.$time);

		//adresses
		$starttime = microtime(true);
		$continue = $goForST;
		$startLimit = 0;
		$i = 0;
		while($continue){

			$q = 'SELECT `ui`.* FROM #__vm_user_info as `ui`
			LEFT OUTER JOIN #__virtuemart_userinfos as `vui` ON `vui`.`virtuemart_user_id` = `ui`.`user_id`
			WHERE `ui`.`address_type` = "ST" AND (`vui`.`virtuemart_user_id`) IS NULL LIMIT '.$startLimit.','.$maxItems;

			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port ST addresses');
			$oldUsersAddresses = $res[0];
			$startLimit = $res[1];
			$continue = $res[2];


			if(empty($oldUsersAddresses)) return $ok;

			//$alreadyKnownIds = $this->getMigrationProgress('staddress');
			$oldtonewST = array();

			foreach($oldUsersAddresses as $oldUsersAddi){

				// 			if(!array_key_exists($oldcategory['virtuemart_userinfo_id'],$alreadyKnownIds)){
				$oldUsersAddi['virtuemart_user_id'] = $oldUsersAddi['user_id'];

				$oldUsersAddi['virtuemart_country_id'] = $this->getCountryIDByName($oldUsersAddi['country']);
				$oldUsersAddi['virtuemart_state_id'] = $this->getStateIDByName($oldUsersAddi['state']);

				$userfielddata = $userModel->_prepareUserFields($oldUsersAddi, 'ST');

				$userinfo = $this->getTable('userinfos');
				if (!$userinfo->bindChecknStore($userfielddata)) {
					vmError('Migration storeAddress ST '.$userinfo->getError());
				}
				$i++;
				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					$continue = false;
					break;
				}

			}
		}

		$time = microtime(true) - $starttime;
		vmInfo('Processed '.$i.' vm1 users ST adresses time: '.$time);
		return $ok;
	}

	private function portVendor(){

		if($this->_stop || (microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		$query = 'SHOW TABLES LIKE "%_vm_vendor"';
		$this->_db->setQuery($query);
		if(!$this->_db->loadResult()){
			vmInfo('No vm_vendor table found for migration');
			$this->_stop = true;
			return false;
		}
		$this->_db->setQuery( 'SELECT *, vendor_id as virtuemart_vendor_id FROM `#__vm_vendor`' );
		$vendor = $this->_db->loadAssoc() ;
		$currency_code_3 = explode( ',', $vendor['vendor_accepted_currencies'] );//EUR,USD
		$this->_db->query( 'SELECT currency_id FROM `#__virtuemart_currencies` WHERE `currency_code_3` IN ( "'.implode('","',$currency_code_3).'" ) ' );
		$vendor['vendor_accepted_currencies'] = $this->_db->loadResultArray();

		$vendorModel = VmModel::getModel('vendor');
		$vendorId = $vendorModel->store($vendor);
		vmInfo('vendor '.$vendorId.' Stored');
		return true;
	}

	private function portCategories(){

		$query = 'SHOW TABLES LIKE "%vm_category%"';
		$this->_db->setQuery($query);
		if(!$this->_db->loadResult()){
			vmInfo('No vm_category table found for migration');
			$this->_stop = true;
			return false;
		}

		$catModel = VmModel::getModel('Category');

		$default_category_browse = JRequest::getString('migration_default_category_browse','');
		// 		vmdebug('migration_default_category_browse '.$default_category_browse);

		$default_category_fly = JRequest::getString('migration_default_category_fly','');

		$portFlypages = JRequest::getInt('migration_default_category_fly',0);

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}
		$ok = true;

		$q = 'SELECT * FROM #__vm_category';
		$this->_db->setQuery($q);
		$oldCategories = $this->_db->loadAssocList();

		$alreadyKnownIds = $this->getMigrationProgress('cats');
		// 		$oldtonewCats = array();

		$category = array();
		$i = 0;
		foreach($oldCategories as $oldcategory){

			if(!array_key_exists($oldcategory['category_id'],$alreadyKnownIds)){

				$category = array();
				//$category['virtuemart_category_id'] = $oldcategory['category_id'];
				$category['virtuemart_vendor_id'] = $oldcategory['vendor_id'];
				$category['category_name'] = stripslashes($oldcategory['category_name']);

				$category['category_description'] = $oldcategory['category_description'];
				$category['published'] = $oldcategory['category_publish'] == 'Y' ? 1 : 0;
// 				$category['created_on'] = $oldcategory['cdate'];
// 				$category['modified_on'] = $oldcategory['mdate'];
				$category['created_on'] = $this->_changeToStamp($oldcategory['cdate']);
				$category['modified_on'] = $this->_changeToStamp($oldcategory['mdate']);
				/*				if($default_category_browse!=$oldcategory['category_browsepage']){
				 $browsepage = $oldcategory['category_browsepage'];
				if (strcmp($browsepage, 'managed') ==0 ) {
				$browsepage="browse_".$oldcategory['products_per_row'];
				}
				$category['category_layout'] = $browsepage;
				}

				if($portFlypages && $default_category_fly!=$oldcategory['category_flypage']){
				$category['category_product_layout'] = $oldcategory['category_flypage'];
				}*/

				//idea was to do it by the layout, but we store this information additionally for enhanced pagination
				$category['products_per_row'] = $oldcategory['products_per_row'];
				$category['ordering'] = $oldcategory['list_order'];

				if(!empty($oldcategory['category_full_image'])){
					$category['virtuemart_media_id'] = $this->_getMediaIdByName($oldcategory['category_full_image'],'category');
				}

				$catModel->setId(0);
				$category_id = $catModel->store($category);
				$errors = $catModel->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						vmError('Migrator portCategories '.$error);
						$ok = false;
					}
					break;
				}


				$alreadyKnownIds[$oldcategory['category_id']] = $category_id;
				unset($category['virtuemart_category_id']);
				$i++;
			}

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}

		}

		// here all categories NEW/OLD are Know
		$this->storeMigrationProgress('cats',$alreadyKnownIds);
		if($ok)
		$msg = 'Looks everything worked correct, migrated ' . $i . ' categories ';
		else {
			$msg = 'Seems there was an error porting ' . $i . ' categories ';
			foreach($this->getErrors() as $error){
				$msg .= '<br />' . $error;
			}
		}
		$this->_app->enqueueMessage($msg);


		$q = 'SELECT * FROM #__vm_category_xref ';
		$this->_db->setQuery($q);
		$oldCategoriesX = $this->_db->loadAssocList();

		// $alreadyKnownIds = $this->getMigrationProgress('catsxref');

		$new_id = 0;
		$i = 0;
		$j = 0;
		$ok = true ;
		if(!empty($oldCategoriesX)){
			// 			vmdebug('$oldCategoriesX',$oldCategoriesX);
			foreach($oldCategoriesX as $oldcategoryX){
				$category = array();
				if(!empty($oldcategoryX['category_parent_id'])){
					if(array_key_exists($oldcategoryX['category_parent_id'],$alreadyKnownIds)){
						$category['category_parent_id'] = $alreadyKnownIds[$oldcategoryX['category_parent_id']];
					} else {
						vmError('Port Categories Xref unknow : ID '.$oldcategoryX['category_parent_id']);
						$ok = false ;
						$j++;
						continue ;
					}
				}

				if(array_key_exists($oldcategoryX['category_child_id'],$alreadyKnownIds)){
					$category['category_child_id'] = $alreadyKnownIds[$oldcategoryX['category_child_id']];
				} else {
					vmError('Port Categories Xref unknow : ID '.$oldcategoryX['category_child_id']);
					$ok = false ;
					$j++;
					continue ;
				}
				if ($ok == true) {
					$table = $this->getTable('category_categories');

					$table->bindChecknStore($category);
					$errors = $table->getErrors();
					if(!empty($errors)){
						foreach($errors as $error){
							vmError('Migrator portCategories ref '.$error);
							$ok = false;
						}
						break;
					}


					$i++;
				}

				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					break;
				}
			}

			//$this->storeMigrationProgress('catsxref',$oldtonewCatsXref);
			if($ok)
			$msg = 'Looks everything worked correct, migrated ' . $i . ' categories xref ';
			else {
				$msg = 'Seems there was an error porting ' . $j . ' of '. $i.' categories xref ';
				foreach($this->getErrors() as $error){
					$msg .= '<br />' . $error;
				}
			}
			$this->_app->enqueueMessage($msg);

			return $ok;
		} else {
			$this->_app->enqueueMessage('No categories to import');
			return $ok;
		}
	}

	private function portManufacturerCategories(){

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}
		$ok = true;

		$q = 'SELECT * FROM #__vm_manufacturer_category';
		$this->_db->setQuery($q);
		$oldMfCategories = $this->_db->loadAssocList();

		if(!class_exists('TableManufacturercategories')) require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'manufacturercategories.php');

		$alreadyKnownIds = $this->getMigrationProgress('mfcats');
		// 		$oldtonewMfCats = array();

		$mfcategory = array();
		$i=0;
		foreach($oldMfCategories as $oldmfcategory){

			if(!array_key_exists($oldmfcategory['mf_category_id'],$alreadyKnownIds)){

				$mfcategory = null;
				$mfcategory = array();
				$mfcategory['mf_category_name'] = $oldmfcategory['mf_category_name'];
				$mfcategory['mf_category_desc'] = $oldmfcategory['mf_category_desc'];
				$mfcategory['published'] = 1;
				$table = $this->getTable('manufacturercategories');

				$table->bindChecknStore($mfcategory);
				$errors = $table->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						vmError('Migrator portManufacturerCategories '.$error);
						$ok = false;
					}
					break;
				}

				$alreadyKnownIds[$oldmfcategory['mf_category_id']] = $mfcategory['virtuemart_manufacturercategories_id'];
				$i++;
			}

			unset($mfcategory['virtuemart_manufacturercategories_id']);

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}
		}
		$this->storeMigrationProgress('mfcats',$alreadyKnownIds);

		if($ok)
		$msg = 'Looks everything worked correct, migrated ' .$i . ' manufacturer categories ';
		else {
			$msg = 'Seems there was an error porting ' . $i . ' manufacturer categories ';
			$msg .= $this->getErrors();
		}

		$this->_app->enqueueMessage($msg);

		return $ok;
	}

	private function portManufacturers(){

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}
		$ok = true;

		$q = 'SELECT * FROM #__vm_manufacturer ';
		$this->_db->setQuery($q);
		$oldManus = $this->_db->loadAssocList();

		// 		vmdebug('my old manus',$oldManus);
		// 		$oldtonewManus = array();
		$oldtoNewMfcats = $this->getMigrationProgress('mfcats');
		$alreadyKnownIds = $this->getMigrationProgress('manus');

		$i =0 ;
		foreach($oldManus as $oldmanu){
			if(!array_key_exists($oldmanu['manufacturer_id'],$alreadyKnownIds)){
				$manu = null;
				$manu = array();
				$manu['mf_name'] = $oldmanu['mf_name'];
				$manu['mf_email'] = $oldmanu['mf_email'];
				$manu['mf_desc'] = $oldmanu['mf_desc'];
				$manu['virtuemart_manufacturercategories_id'] = $oldtoNewMfcats[$oldmanu['mf_category_id']];
				$manu['mf_url'] = $oldmanu['mf_url'];
				$manu['published'] = 1;

				if(!class_exists('TableManufacturers'))
				require(JPATH_VM_ADMINISTRATOR . DS . 'tables' . DS . 'manufacturers.php');
				$table = $this->getTable('manufacturers');

				$table->bindChecknStore($manu);
				$errors = $table->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){

						vmError('Migrator portManufacturers '.$error);
						$ok = false;
					}
					break;
				}
				$alreadyKnownIds[$oldmanu['manufacturer_id']] = $manu['virtuemart_manufacturer_id'];
				//unset($manu['virtuemart_manufacturer_id']);
				$i++;
			}
			// 			else {
			// 				$oldtonewManus[$oldmanu['manufacturer_id']] = $alreadyKnownIds[$oldmanu['manufacturer_id']];
			// 			}

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}
		}

		$this->storeMigrationProgress('manus',$alreadyKnownIds);

		if($ok)
		$msg = 'Looks everything worked correct, migrated ' .$i . ' manufacturers ';
		else {
			$msg = 'Seems there was an error porting ' . $i . ' manufacturers ';
			$msg .= $this->getErrors();
		}
		$this->_app->enqueueMessage($msg);
	}

	private function portProducts(){

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		$ok = true;
		$mediaIdFilename = array();

		//approximatly 100 products take a 1 MB
		$maxItems = $this->_getMaxItems('Products');
// 		$maxItems = 100;
		$startLimit = $this->_getStartLimit('products_start');;
		$i=0;
		$continue = true;

		$alreadyKnownIds = $this->getMigrationProgress('products');
		$oldToNewCats = $this->getMigrationProgress('cats');
		// 		$user = JFactory::getUser();

		//$oldtonewProducts = array();
		$oldtonewManus = $this->getMigrationProgress('manus');

		$productModel = VmModel::getModel('product');

		// 		vmdebug('$alreadyKnownIds',$alreadyKnownIds);
		while($continue){
$maxItems = 5;
			$q = 'SELECT *,`p`.product_id as product_id FROM `#__vm_product` AS `p`
			LEFT JOIN `#__vm_product_mf_xref` ON `#__vm_product_mf_xref`.`product_id` = `p`.`product_id`
			WHERE (`p`.product_id) IS NOT NULL
			GROUP BY `p`.product_id ORDER BY `p`.product_parent_id LIMIT '.$startLimit.','.$maxItems;

			$doneStart = $startLimit;
			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port Products');
			$oldProducts = $res[0];

			$startLimit = $res[1];
			$continue = $res[2];

 			//vmdebug('in product migrate $oldProducts ',$oldProducts);
//$continue = false;
			/* Not in VM1
			slug low_stock_notification intnotes metadesc metakey metarobot metaauthor layout published

			created_on created_by modified_on modified_by
			product_override_price override link

			Not in VM2
			product_thumb_image product_full_image attribute
			custom_attribute child_options quantity_options child_option_ids
			shopper_group_id    product_list
			*/

			//There are so many names the same, so we use the loaded array and manipulate it
// 					$oldProducts = array();
			foreach($oldProducts as $product){

				if(!empty($product['product_id']) and !array_key_exists($product['product_id'],$alreadyKnownIds)){

					$product['virtuemart_vendor_id'] = $product['vendor_id'];

					if(!empty($product['manufacturer_id'])){
						if(!empty($oldtonewManus[$product['manufacturer_id']])) {
							$product['virtuemart_manufacturer_id'] = $oldtonewManus[$product['manufacturer_id']];
						}
					}

					$q = 'SELECT `category_id` FROM #__vm_product_category_xref WHERE #__vm_product_category_xref.product_id = "'.$product['product_id'].'" ';
					$this->_db->setQuery($q);
					$productCats = $this->_db->loadResultArray();

					$productcategories = array();
					if(!empty($productCats)){
						foreach($productCats as $cat){
							//product has category_id and categories?
							if(!empty($oldToNewCats[$cat])){
								// 								$product['virtuemart_category_id'] = $oldToNewCats[$cat];
								//This should be an array, or is it not in vm1? not cleared, may need extra foreach
								$productcategories[] = $oldToNewCats[$cat];
							} else {
								vmInfo('Coulndt find category for product, maybe just not in a category');
							}
						}
					}
					// if(!empty($alreadyKnownIds[$product['product_id']])){
					// $product_parent_id = $alreadyKnownIds[$product['product_id']];
					// }
					// Converting Attributes from parent product to customfields Cart variant
					// $q = 'SELECT * FROM `#__vm_product_attribute` WHERE `#__vm_product_attribute`.`product_id` ="'.$product['product_id'].'" ';
					// $this->_db->setQuery($q);
					// if(!empty($productAttributes = $this->_db->loadAssocList()) {

					// foreach($productAttributes as $attrib){
					// //custom select or create it
					// $q = 'SELECT `virtuemart_custom_id` FROM `#__virtuemart_customs` as c WHERE c.field_type ="V" and c.`custom_title` ="'.$attrib['attribute_name'].'" ';
					// $this->_db->setQuery($q);
					// if (!$virtuemart_custom_id = $this->_db->loadResult()) {
					// $customModel = VmModel::getModel('Custom');
					// $attrib['custom_title'] = $attrib['attribute_name'];
					// $attrib['custom_value'] = $attrib['attribute_value'];
					// $attrib['is_cart_attribute'] = '1';

					// $customModel->store($attrib);
					// }
					// }
					// }

					// Attributes End
					$product['categories'] = $productcategories;

					$product['published'] = $product['product_publish'] == 'Y' ? 1 : 0;

					$q = 'SELECT * FROM `#__vm_product_price` WHERE `product_id` = "'.$product['product_id'].'" ';
					$this->_db->setQuery($q);
					$entries = $this->_db->loadAssocList();
					if($entries){
						foreach($entries as $i=>$price){
							$product['mprices']['product_price_id'][$i] = 0;
							$product['mprices']['product_id'][$i] = $price['product_id'];
							$product['mprices']['product_price'][$i] = $price['product_price'];
							$product['mprices']['virtuemart_shoppergroup_id'][$i] = $price['shopper_group_id'];
							$product['mprices']['product_currency'][$i] = $this->_ensureUsingCurrencyId($price['product_currency']);
							$product['mprices']['price_quantity_start'][$i] = $price['price_quantity_start'];
							$product['mprices']['price_quantity_end'][$i] = $price['price_quantity_end'];
							$product['mprices']['product_price_publish_up'][$i] = $price['product_price_vdate'];
							$product['mprices']['product_price_publish_down'][$i] = $price['product_price_edate'];
							$product['mprices']['created_on'][$i] = $this->_changeToStamp($price['cdate']);
							$product['mprices']['modified_on'][$i] = $this->_changeToStamp($price['mdate']);
						}
					}
				//	$product['price_quantity_start'] = $product['price_quantity_start'];
				//	$product['price_quantity_end'] = $product['price_quantity_end'];
		        //    $product['product_price_publish_up'] = $product['product_price_vdate'];
				//	$product['product_price_publish_down'] = $product['product_price_edate'];
					$product['created_on'] = $this->_changeToStamp($product['cdate']);
					$product['modified_on'] = $this->_changeToStamp($product['mdate']); //we could remove this to set modified_on today
					$product['product_available_date'] = $this->_changeToStamp($product['product_available_date']);

					if(!empty($product['product_weight_uom'])){
						$product['product_weight_uom'] = $this->parseWeightUom($product['product_weight_uom']);
					}

					if(!empty($product['product_lwh_uom'])){
						$product['product_lwh_uom'] = $this->parseDimensionUom($product['product_lwh_uom']);
					}
					//$product['created_by'] = $user->id;
					//$product['modified_by'] = $user->id;



					if(!empty($product['product_s_desc'])){
						$product['product_s_desc'] = stripslashes($product['product_s_desc']);
					}

					if(empty($product['product_name'] )){
						$product['product_name'] =  $product['product_sku'].':'.$product['product_id'].':'.$product['product_s_desc'];
					}

					// Here we  look for the url product_full_image and check which media has the same
					// full_image url
					if(!empty($product['product_full_image'])){
						$product['virtuemart_media_id'] = $this->_getMediaIdByName($product['product_full_image'],'product');
					}

					if(!empty($alreadyKnownIds[$product['product_parent_id']])){
						$product['product_parent_id'] = $alreadyKnownIds[$product['product_parent_id']];
						// 						vmInfo('new parent id : '. $product['product_parent_id']);
					} else {
						$product['product_parent_id'] = 0;
					}

					$product['virtuemart_product_id'] = $productModel->store($product);

					if($this->_keepOldProductIds){
						$product['virtuemart_product_id'] = $product['product_id'];
					}
					if(!empty($product['product_id']) and !empty($product['virtuemart_product_id'])){
						$alreadyKnownIds[$product['product_id']] = $product['virtuemart_product_id'];
					} else {
						vmdebug('$product["virtuemart_product_id"] or $product["product_id"] is EMPTY?',$product);
					}

					$errors = $productModel->getErrors();
					if(!empty($errors)){
						foreach($errors as $error){
							vmError('Migration: '.$i.' ' . $error);
						}
						vmdebug('Product add error',$product);
						$productModel->resetErrors();
						$continue = false;
						break;
					}
					$i++;
				}

				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					vmdebug('Product import breaked, you may rise the execution time, this is not an error, just a hint');
					$continue = false;
					break;
				}

			}

			$limitStartToStore = ', products_start = "'.($doneStart+$i).'" ';
			$this->storeMigrationProgress('products',$alreadyKnownIds);
			vmInfo('Migration: '.$i.' products processed ');
		}
		return $ok;
	}

	/**
	 * Finds the media id in the vm2 table for a given filename
	 *
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 *
	 */
	var $mediaIdFilename = array();

	function _getMediaIdByName($filename,$type){
		if(!empty($this->mediaIdFilename[$type][$filename])){

			return $this->mediaIdFilename[$type][$filename];
		} else {
			$q = 'SELECT `virtuemart_media_id` FROM `#__virtuemart_medias`
										WHERE `file_title`="' .  $this->_db->getEscaped($filename) . '"
										AND `file_type`="' . $this->_db->getEscaped($type) . '"';
			$this->_db->setQuery($q);
			$virtuemart_media_id = $this->_db->loadResult();
			if($this->_db->getErrors()){
				vmError('Error in _getMediaIdByName',$this->_db->getErrorMsg());
			}
			if(!empty($virtuemart_media_id)){
				$this->mediaIdFilename[$type][$filename] = $virtuemart_media_id;
				return $virtuemart_media_id;
			} else {

				// 				vmdebug('No media found for '.$type.' '.$filename);
			}
		}
	}

	function portOrders(){

		if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
			return;
		}

		if(!class_exists('VirtueMartModelOrderstatus'))
		require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'orderstatus.php');

		if (!class_exists('ShopFunctions')) require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
		$this->_db->setQuery('select `order_status_code` FROM `#__virtuemart_orderstates` `');
		$vm2Fields = $this->_db->loadResultArray ();
		$this->_db->setQuery('select * FROM `#__vm_order_status`');
		$oldfields = $this->_db->loadObjectList();
		$migratedfields ='';
		foreach ($oldfields as $field ) {
			if ( !in_array( $field->order_status_code, $vm2Fields ) ) {
				$q = 'INSERT INTO `#__virtuemart_orderstates` ( `virtuemart_vendor_id`, `order_status_code`, `order_status_name`, `order_status_description`, `order_stock_handle`, `ordering`, `published`)
					VALUES ( "'.$field->vendor_id.'","'.$field->order_status_code .'","'.$field->order_status_name .'","'.$field->order_status_description .'","A","'.$field->list_order .'", 1 )';
				$this->_db->setQuery($q);
				$this->_db->query();
				if ($this->_db->getErrorNum()) {
					vmError ($this->_db->getErrorMsg() );
				}
				$migratedfields .= '['.$field->order_status_code.'-'.$field->order_status_name.'] ';

			}
		}
		if ($migratedfields) vminfo('order states declaration '.$migratedfields.' Migrated');
		$oldtonewOrders = array();

		//Looks like there is a problem, when the data gets tooo big,
		//solved now with query directly ignoring already ported orders.
		$alreadyKnownIds = $this->getMigrationProgress('orders');
		$newproductIds = $this->getMigrationProgress('products');
		$orderCodeToId = $this->createOrderStatusAssoc();

		//approximatly 100 products take a 1 MB
		$maxItems = $this->_getMaxItems('Orders');


		$startLimit = $this->_getStartLimit('orders_start');
		vmdebug('portOrders $startLimit '.$startLimit);
		$i = 0;
		$continue=true;

		$reWriteOrderNumber = JRequest::getInt('reWriteOrderNumber',0);
		$userOrderId = JRequest::getInt('userOrderId',0);

		while($continue){

			$q = 'SELECT `o`.*, `op`.*, `o`.`order_number` as `vm1_order_number`, `o2`.`order_number` as `nr2`,`o`.order_id FROM `#__vm_orders` as `o`
				LEFT OUTER JOIN `#__vm_order_payment` as `op` ON `op`.`order_id` = `o`.`order_id`
				LEFT JOIN `#__virtuemart_orders` as `o2` ON `o2`.`order_number` = `o`.`order_number`
				WHERE (o2.order_number) IS NULL ORDER BY o.order_id LIMIT '.$startLimit.','.$maxItems;

			$doneStart = $startLimit;
			$res = self::loadCountListContinue($q,$startLimit,$maxItems,'port Orders');
			$oldOrders = $res[0];
			$startLimit = $res[1];
			$continue = $res[2];

			foreach($oldOrders as $order){

				if(!array_key_exists($order['order_id'],$alreadyKnownIds)){
					$orderData = new stdClass();

					$orderData->virtuemart_order_id = null;
					$orderData->virtuemart_user_id = $order['user_id'];
					$orderData->virtuemart_vendor_id = $order['vendor_id'];

					if($reWriteOrderNumber==0){
						if($userOrderId==1){
							$orderData->order_number = $order['order_id'];
						} else {
							$orderData->order_number = $order['vm1_order_number'];
						}
					}

					$orderData->order_pass = 'p' . substr(md5((string)time() . $order['order_number']), 0, 5);
					//Note as long we do not have an extra table only storing addresses, the virtuemart_userinfo_id is not needed.
					//The virtuemart_userinfo_id is just the id of a stored address and is only necessary in the user maintance view or for choosing addresses.
					//the saved order should be an snapshot with plain data written in it.
					//		$orderData->virtuemart_userinfo_id = 'TODO'; // $_cart['BT']['virtuemart_userinfo_id']; // TODO; Add it in the cart... but where is this used? Obsolete?
					$orderData->order_total = $order['order_total'];
					$orderData->order_subtotal = $order['order_subtotal'];
					$orderData->order_tax = empty($order['order_tax'])? 0:$order['order_tax'];
					$orderData->order_shipment = empty($order['order_shipment'])? 0:$order['order_shipment'];
					$orderData->order_shipment_tax = empty($order['order_shipment_tax'])? 0:$order['order_shipment_tax'];
					if(!empty($order['coupon_code'])){
						$orderData->coupon_code = $order['coupon_code'];
						$orderData->coupon_discount = $order['coupon_discount'];
					}
					$orderData->order_discount = $order['order_discount'];

					$orderData->order_status = $order['order_status'];

					if(isset($order['order_currency'])){
						$orderData->user_currency_id = $this->getCurrencyIdByCode($order['order_currency']);
						//$orderData->user_currency_rate = $order['order_status'];
					}
					$orderData->virtuemart_paymentmethod_id = $order['payment_method_id'];
					$orderData->virtuemart_shipmentmethod_id = $order['ship_method_id'];
					//$orderData->order_status_id = $oldToNewOrderstates[$order['order_status']]


					$_filter = JFilterInput::getInstance(array('br', 'i', 'em', 'b', 'strong'), array(), 0, 0, 1);
					$orderData->customer_note = $_filter->clean($order['customer_note']);
					$orderData->ip_address = $order['ip_address'];

					$orderData->created_on = $this->_changeToStamp($order['cdate']);
					$orderData->modified_on = $this->_changeToStamp($order['mdate']); //we could remove this to set modified_on today

					$orderTable = $this->getTable('orders');
					$orderTable->bindChecknStore($orderData);
					$errors = $orderTable->getErrors();
					if(!empty($errors)){
						foreach($errors as $error){
							$this->_app->enqueueMessage('Migration orders: ' . $error);
						}
						$continue = false;
						break;
					}
					$i++;
					$newId = $alreadyKnownIds[$order['order_id']] = $orderTable->virtuemart_order_id;

					$q = 'SELECT * FROM `#__vm_order_item` WHERE `order_id` = "'.$order['order_id'].'" ';
					$this->_db->setQuery($q);
					$oldItems = $this->_db->loadAssocList();
					//$this->_app->enqueueMessage('Migration orderhistories: ' . $newId);
					foreach($oldItems as $item){
						$item['virtuemart_order_id'] = $newId;
						if(!empty($newproductIds[$item['product_id']])){
							$item['virtuemart_product_id'] = $newproductIds[$item['product_id']];
						} else {
							vmWarn('Attention, order is pointing to deleted product (not found in the array of old products)');
						}

						//$item['order_status'] = $orderCodeToId[$item['order_status']];
						$item['created_on'] = $this->_changeToStamp($item['cdate']);
						$item['modified_on'] = $this->_changeToStamp($item['mdate']); //we could remove this to set modified_on today
						$item['product_attribute'] = $this->_attributesToJson($item['product_attribute']); //we could remove this to set modified_on today

						$orderItemsTable = $this->getTable('order_items');
						$orderItemsTable->bindChecknStore($item);
						$errors = $orderItemsTable->getErrors();
						if(!empty($errors)){
							foreach($errors as $error){
								$this->_app->enqueueMessage('Migration orderitems: ' . $error);
							}
							$continue = false;
							break;
						}
					}

					$q = 'SELECT * FROM `#__vm_order_history` WHERE `order_id` = "'.$order['order_id'].'" ';
					$this->_db->setQuery($q);
					$oldItems = $this->_db->loadAssocList();

					foreach($oldItems as $item){
						$item['virtuemart_order_id'] = $newId;
						//$item['order_status_code'] = $orderCodeToId[$item['order_status_code']];


						$orderHistoriesTable = $this->getTable('order_histories');
						$orderHistoriesTable->bindChecknStore($item);
						$errors = $orderHistoriesTable->getErrors();
						if(!empty($errors)){
							foreach($errors as $error){
								$this->_app->enqueueMessage('Migration orderhistories: ' . $error);
							}
							$continue = false;
							break;
						}
					}

					$q = 'SELECT * FROM `#__vm_order_user_info` WHERE `order_id` = "'.$order['order_id'].'" ';
					$this->_db->setQuery($q);
					$oldItems = $this->_db->loadAssocList();
					if($oldItems){
						foreach($oldItems as $item){
							$item['virtuemart_order_id'] = $newId;
							$item['virtuemart_user_id'] = $item['user_id'];
							$item['virtuemart_country_id'] = $this->getCountryIDByName($item['country']);
							$item['virtuemart_state_id'] = $this->getStateIDByName($item['state']);

							$item['email'] = $item['user_email'];
							$orderUserinfoTable = $this->getTable('order_userinfos');
							$orderUserinfoTable->bindChecknStore($item);
							$errors = $orderUserinfoTable->getErrors();
							if(!empty($errors)){
								foreach($errors as $error){
									$this->_app->enqueueMessage('Migration orderuserinfo: ' . $error);
								}
								$continue = false;
								break;
							}
						}
					}
					//$this->_app->enqueueMessage('Migration: '.$i.' order processed new id '.$newId);
				}
// 				$this->storeMigrationProgress('orders',$alreadyKnownIds);
				// 				 else {
				// 					$oldtonewOrders[$order['order_id']] = $alreadyKnownIds[$order['order_id']];
				// 				}

				if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
					$continue = false;

					break;
				}
			}
		}

		$limitStartToStore = ', orders_start = "'.($doneStart+$i).'" ';
		$this->storeMigrationProgress('orders',$alreadyKnownIds,$limitStartToStore);
		vmInfo('Migration: '.$i.' orders processed '.($doneStart+$i).' done.');
	}

	function portOrderStatus(){

		$q = 'SELECT * FROM `#__vm_order_status` ';

		$this->_db->setQuery($q);
		$oldOrderStatus = $this->_db->loadAssocList();

		$orderstatusModel = VmModel::getModel('Orderstatus');
		$oldtonewOrderstates = array();
		$alreadyKnownIds = $this->getMigrationProgress('orderstates');
		$i = 0;
		foreach($oldOrderStatus as $status){
			if(!array_key_exists($status['order_status_id'],$alreadyKnownIds)){
				$status['virtuemart_orderstate_id'] = 0;
				$status['virtuemart_vendor_id'] = $status['vendor_id'];
				$status['ordering'] = $status['list_order'];
				$status['published'] = 1;

				$newId = $orderstatusModel->store($status);
				$errors = $orderstatusModel->getErrors();
				if(!empty($errors)){
					foreach($errors as $error){
						$this->_app->enqueueMessage('Migration: ' . $error);
					}
					$orderstatusModel->resetErrors();
					//break;
				}
				$oldtonewOrderstates[$status['order_status_id']] = $newId;
				$i++;
			} else {
				//$oldtonewOrderstates[$status['order_status_id']] = $alreadyKnownIds[$status['order_status_id']];
			}

			if((microtime(true)-$this->starttime) >= ($this->maxScriptTime)){
				break;
			}
		}

		$oldtonewOrderstates = array_merge($oldtonewOrderstates,$alreadyKnownIds);
		$oldtonewOrderstates = array_unique($oldtonewOrderstates);

		vmInfo('Migration: '.$i.' orderstates processed ');
		return;
	}

	private function _changeToStamp($dateIn){

		$date = JFactory::getDate($dateIn);
		return $date->toMySQL();
	}

	private function _ensureUsingCurrencyId($curr){

		$currInt = '';
		if(!empty($curr)){
			$this->_db = JFactory::getDBO();
			$q = 'SELECT `virtuemart_currency_id` FROM `#__virtuemart_currencies` WHERE `currency_code_3`="' . $this->_db->getEscaped($curr) . '"';
			$this->_db->setQuery($q);
			$currInt = $this->_db->loadResult();
			if(empty($currInt)){
				JError::raiseWarning(E_WARNING, 'Attention, couldnt find currency id in the table for id = ' . $curr);
			}
		}

		return $currInt;
	}

	private function _getMaxItems($name){

		$maxItems = 50;
		$freeRam =  ($this->maxMemoryLimit - memory_get_usage(true))/(1024 * 1024) ;
		$maxItems = (int)$freeRam * 70;
		if($maxItems<=0){
			$maxItems = 50;
			vmWarn('Your system is low on RAM! Limit set: '.$this->maxMemoryLimit.' used '.memory_get_usage(true)/(1024 * 1024).' MB and php.ini '.ini_get('memory_limit'));
		} else if($maxItems>1000){
			$maxItems = 1000;
		}
		vmdebug('Migrating '.$name.', free ram left '.$freeRam.' so limit chunk to '.$maxItems);
		return $maxItems;
	}

	/**
	 *
	 * Enter description here ...
	 */
	private function _getStartLimit($name){

		$this->_db = JFactory::getDBO();

		$q = 'SELECT `'.$name.'` FROM `#__virtuemart_migration_oldtonew_ids` WHERE id="1" ';

		$this->_db->setQuery($q);

		$limit = $this->_db->loadResult();
		vmdebug('$limit',$limit,$q);
		if(!empty($limit)) return $limit; else return 0;
	}

	/**
	 * Gets the virtuemart_country_id by a country 2 or 3 code
	 *
	 * @author Max Milbers
	 * @param string $name Country 3 or Country 2 code (example US for United States)
	 * return int virtuemart_country_id
	 */
	private $_countries = array();
	private $_states = array();

	private function getCountryIdByName($name){

		if(empty($this->_countries[$name])){
			$this->_countries[$name] = Shopfunctions::getCountryIDByName($name);
		}

		return $this->_countries[$name];
	}

	private function getStateIdByName($name){

		if(empty($this->_states[$name])){
			$this->_states[$name] = Shopfunctions::getStateIDByName($name);
		}

		return $this->_states[$name];
	}

/*	private function getCountryIdByCode($name){
		if(empty($name)){
			return 0;
		}

		if(strlen($name) == 2){
			$countryCode = 'country_2_code';
		}else {
			$countryCode = 'country_3_code';
		}

		$q = 'SELECT `virtuemart_country_id` FROM `#__virtuemart_countries`
				WHERE `' . $countryCode . '` = "' . $this->_db->getEscaped($name) . '" ';
		$this->_db->setQuery($q);

		return $this->_db->loadResult();
	}

	/**
	 * Gets the virtuemart_country_id by a country 2 or 3 code
	 *
	 * @author Max Milbers
	 * @param string $name Country 3 or Country 2 code (example US for United States)
	 * return int virtuemart_country_id
	 */
/*	private function getStateIdByCode($name){
		if(empty($name)){
			return 0;
		}

		if(strlen($name) == 2){
			$code = 'country_2_code';
		}else {
			$code = 'country_3_code';
		}

		$q = 'SELECT `virtuemart_state_id` FROM `#__virtuemart_states`
				WHERE `' . $code . '` = "' . $this->_db->getEscaped($name) . '" ';
		$this->_db->setQuery($q);

		return $this->_db->loadResult();
	}
*/
	private function getCurrencyIdByCode($name){
		if(empty($name)){
			return 0;
		}

		if(strlen($name) == 2){
			$code = 'currency_code_2';
		}else {
			$code = 'currency_code_3';
		}

		$q = 'SELECT `virtuemart_currency_id` FROM `#__virtuemart_currencies`
					WHERE `' . $code . '` = "' . $this->_db->getEscaped($name) . '" ';
		$this->_db->setQuery($q);

		return $this->_db->loadResult();
	}

	/**
	 *
	 *
	 * @author Max Milbers
	 */
	private function createOrderStatusAssoc(){

		$q = 'SELECT * FROM `#__virtuemart_orderstates` ';
		$this->_db->setQuery($q);
		$orderstats = $this->_db->loadAssocList();
		$xref = array();
		foreach($orderstats as $status){

			$xref[$status['order_status_code']] = $status['virtuemart_orderstate_id'];
		}

		return $xref;
	}

	/**
	 * parse the entered string to a standard unit
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 *
	 */
	private function parseWeightUom($weightUnit){

		$weightUnit = strtolower($weightUnit);
		$weightUnitMigrateValues = self::getWeightUnitMigrateValues();
		return $this->parseUom($weightUnit,$weightUnitMigrateValues );

	}

	/**
	 *
	 * parse the entered string to a standard unit
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 *
	 */
	private function parseDimensionUom($dimensionUnit){

		$dimensionUnitMigrateValues = self::getDimensionUnitMigrateValues();
		$dimensionUnit = strtolower($dimensionUnit);
		return $this->parseUom($dimensionUnit,$dimensionUnitMigrateValues );

	}

	/**
	 *
	 * parse the entered string to a standard unit
	 * @author Max Milbers
	 * @author Valerie Isaksen
	 *
	 */
	private function parseUom($unit, $migrateValues){
		$new="";
		$unit = strtolower($unit);
		foreach ($migrateValues as $old => $new) {
			if (strpos($unit, $old) !== false) {
				return $new;
			}
		}
	}

	/**
	 *
	 * get new Length Standard Unit
	 * @author Valerie Isaksen
	 *
	 */
	function getDimensionUnitMigrateValues() {

		$dimensionUnitMigrate=array (
                  'mm' => 'MM'
		, 'cm' => 'CM'
		, 'm' => 'M'
		, 'yd' => 'YD'
		, 'foot' => 'FT'
		, 'ft' => 'FT'
		, 'inch' => 'IN'
		);
		return $dimensionUnitMigrate;
	}
	/**
	 *
	 * get new Weight Standard Unit
	 * @author Valerie Isaksen
	 *
	 */
	function getWeightUnitMigrateValues() {
		$weightUnitMigrate=array (
                  'kg' => 'KG'
		, 'kilos' => 'KG'
		, 'gr' => 'G'
		, 'pound' => 'LB'
		, 'livre' => 'LB'   //TODO ERROR HERE
		, 'once' => 'OZ'
		, 'ounce' => 'OZ'
		);
		return $weightUnitMigrate;
	}

	/**
	 * Helper function, was used to determine the difference of an loaded array (from vm19
	 * and a loaded object of vm2
	 */
	private function showVmDiff(){

		$productModel = VmModel::getModel('product');
		$product = $productModel->getProduct(0);

		$productK = array();
		$attribsImage = get_object_vars($product);

		foreach($attribsImage as $k => $v){
			$productK[] = $k;
		}

		$oldproductK = array();
		foreach($oldProducts[0] as $k => $v){
			$oldproductK[] = $k;
		}

		$notSame = array_diff($productK, $oldproductK);
		$names = '';
		foreach($notSame as $name){
			$names .= $name . ' ';
		}
		$this->_app->enqueueMessage('_productPorter  array_intersect ' . $names);

		$notSame = array_diff($oldproductK, $productK);
		$names = '';
		foreach($notSame as $name){
			$names .= $name . ' ';
		}
		$this->_app->enqueueMessage('_productPorter  ViceVERSA array_intersect ' . $names);
	}

	function loadCountListContinue($q,$startLimit,$maxItems,$msg){

		$continue = true;
		$this->_db->setQuery($q);
		if(!$this->_db->query()){
			vmError($msg.' db error '. $this->_db->getErrorMsg());
			vmError($msg.' db error '. $this->_db->getQuery());
			$entries = array();
			$continue = false;
		} else {
			$entries = $this->_db->loadAssocList();
			$count = count($entries);
			vmInfo($msg. ' take '.$count.' vm1 entries for migration ');
			$startLimit += $maxItems;
			if($count<$maxItems){
				$continue = false;
			}
		}

		return array($entries,$startLimit,$continue);
	}

	function portCurrency(){

		$this->setRedirect($this->redirectPath);
		$db = JFactory::getDBO();
		$q = 'SELECT `virtuemart_currency_id`,
		  `currency_name`,
		  `currency_code_2`,
		  `currency_code` AS currency_code_3,
		  `currency_numeric_code`,
		  `currency_exchange_rate`,
		  `currency_symbol`,
		`currency_display_style` AS `_display_style`
			FROM `#__virtuemart_currencia` ORDER BY virtuemart_currency_id';
		$db->setQuery($q);
		$result = $db->loadObjectList();

		foreach($result as $item){

			//			$item->virtuemart_currency_id = 0;
			$item->currency_exchange_rate = 0;
			$item->published = 1;
			$item->shared = 1;
			$item->virtuemart_vendor_id = 1;

			$style = explode('|', $item->_display_style);

			$item->currency_nbDecimal = $style[2];
			$item->currency_decimal_symbol = $style[3];
			$item->currency_thousands = $style[4];
			$item->currency_positive_style = $style[5];
			$item->currency_negative_style = $style[6];

			$db->insertObject('#__virtuemart_currencies', $item);
		}

		$this->setRedirect($this->redirectPath);
	}

	/**
	 * Method to restore all virtuemart tables in a database with a given prefix
	 *
	 * @access	public
	 * @param	string	Old table prefix
	 * @return	boolean	True on success.
	 */
	function restoreDatabase($prefix='bak_vm_') {
		// Initialise variables.
		$return = true;

		$this->_db = JFactory::getDBO();

		// Get the tables in the database.
		if ($tables = $this->_db->getTableList()) {
			foreach ($tables as $table) {
				// If the table uses the given prefix, back it up.
				if (strpos($table, $prefix) === 0) {
					// restore table name.
					$restoreTable = str_replace($prefix, '#__vm_', $table);

					// Drop the current active table.
					$this->_db->setQuery('DROP TABLE IF EXISTS '.$this->_db->nameQuote($restoreTable));
					$this->_db->query();

					// Check for errors.
					if ($this->_db->getErrorNum()) {
						vmError('Migrator restoreDatabase '.$this->_db->getErrorMsg());
						$return = false;
					}

					// Rename the current table to the backup table.
					$this->_db->setQuery('RENAME TABLE '.$this->_db->nameQuote($table).' TO '.$this->_db->nameQuote($restoreTable));
					$this->_db->query();

					// Check for errors.
					if ($this->_db->getErrorNum()) {
						vmError('Migrator restoreDatabase '.$this->_db->getErrorMsg());
						$return = false;
					}
				}
			}
		}

		return $return;
	}

	private function _attributesToJson($attributes){
		if ( !trim($attributes) ) return '';
		$attributesArray = explode(";", $attributes);
		foreach ($attributesArray as $valueKey) {
			// do the array
			$tmp = explode(":", $valueKey);
			if ( count($tmp) == 2 ) {
				if ($pos = strpos($tmp[1], '[')) $tmp[1] = substr($tmp[1], 0, $pos) ; // remove price
				$newAttributes['attributs'][$tmp[0]] = $tmp[1];
			}
		}
		return json_encode($newAttributes,JSON_FORCE_OBJECT);
	}
}

