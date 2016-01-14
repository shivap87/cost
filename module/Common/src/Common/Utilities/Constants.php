<?php
namespace Common\Utilities;
class Constants{
	
	// Constants - Role
	const ROLE_STUDENT=2;
	const ROLE_GUEST=5;
	
	
	const REPORT_ACCESS="vgupta@uceazy.com:vkolady@uceazy.com";
	
	const CHANNEL_IDS = '1#Facebook:2#Email:3#PTA:4:SchoolCounsellors:5#WEB:6#SingTao:7#Youtube:8#FremontHighSchool:9#FriendsFamily';
	const SERVICES_IDS="1#/user/signup:2#/user/signin:3#/user/register";
	
	const SEARCH_RESULT_SAFETY = "safety";
	const SEARCH_RESULT_REACH="reach";
	const SEARCH_RESULT_TARGET="target";
	const COUNTY ="Alameda,Alpine,Amador,Butte,Calaveras,Colusa,Contra Costa,Del Norte,El Dorado,Fresno,Glenn,Humboldt,Imperial,Inyo,Kern,Kings,Lake,Lassen,Los Angeles,Madera,Marin,Mariposa,Mendocino,Merced,Modoc,Mono,Monterey,Napa,Nevada,Orange,Placer,Plumas,Riverside,Sacramento,San Benito,San Bernardino,San Diego,San Francisco,San Joaquin,San Luis Obispo,San Mateo,Santa Barbara,Santa Clara,Santa Cruz,Shasta,Sierra,Siskiyou,Solano,Sonoma,Stanislaus,Sutter,Tehama,Trinity,Tulare,Tuolumne,Ventura,Yolo,Yuba";
	
	//Development
	//const SITE_BASE_URL="http://69.64.88.204:8090";
	
	//production
	//const SITE_BASE_URL="https://app.uceazy.com";
	
	// Staging 
	const SITE_BASE_URL="https://stage.uceazy.com";
	
	//Local
	//const RTS_BASE_URL="http://localhost:8080";	
	
	//Test
	
	//Local
	const LOG_FILE="D:\\error.log";
	
	// Development
	//const LOG_FILE="/root/myapp/code/UCEazy/logs.txt";
	
	// Stage
	//const LOG_FILE="/usr/local/panther/UCEazy/logs.txt";



//local
	//const LOG_FILE=
	const IS_LOG=true;
	const MAJORS="Undeclared,Actuarial Science,Agricultural Studies ,Animal Science,Anthropology,Apparel  Design & Merchandising,Architecture,Architecture - Landscape,Art,Art History,Astrophysics,Atmospheric Science,Bioinformatics,Biology,Biotechnology,Business,Chemistry/Biochemistry,Child Development,Civilization,Clothing/Textiles,Cognitive Science,Communication,Community Development,Computer Science,Conservation/Resource Studies,Criminal Justice/Administration,Dance/Performance,Development Studies,Earth Science,Ecology,Economics,Engineering,English,Entomology,Environmental Biology,Environmental Design,Environmental Policy,Environmental Studies,Environmental Toxicology,Ethnic and Area Studies,Family & Consumer Sciences,Fiber Science,Film & Electronic Arts,Food & Nutrition/Dietetics,Foreign Language and Literature,Forestry,Genetics,Geography,Geology,Global Disease Biology,Graphic Design,Health Science,History,Hospitality & Tourism Management,Hydrology,Information Systems,Interior Design,International Business/Relations,Journalism,Kinesiology/Physical  Education,Liberal Studies/Education,Linguistics,Marine Science,Mathematics,Media Studies,Medical Technology,Microbiology,Music,Natural Science,Neuroscience,Nursing ADN to BSN,Nursing Basic,Occupational Therapy,Pharmaceutical Science,Philosophy,Physics,Physiology,Plant Biology,Political Science,Psychology,Public Administration and Health,Radio-Television & Film,Religious Studies,Rhetoric,Social Science,Social Work,Sociology,Spanish,Theatre,Urban Forestry,Urban Studies,Viticulture,Women/Gender Studies,Zoology";
	const SIM_FEED_SUCCESS="Thank you for your valuable feedback";
	const ADV_FEED_SUCCESS="Thank you for your valuable feedback";
	
	const MONTHS="January,Febrary,March,April,May,June,July,August,September,October,November,December";
	
	const PLAN_DREAM_SCHOOLS = "UC Berkeley,UCLA,UC San Diego,UC Davis,UC Santa Barbara,UC Irvine,Cal Poly SLO,UC Santa Cruz,San Diego State,UC Riverside,Cal Poly Pomona,UC Merced,CSU Fullerton,San Jose State,CSU Long Beach,CSU Chico,Sonoma State,CSU Fresno,CSU Stanislaus,San Francisco State,Sacramento State,Humboldt State,CSU San Bernardino,CSU Monterey Bay,CSU Northridge,CSU Channel Islands,CSU Bakersfield,CSU San Marcos,CSU Los Angeles,CSU Dominguez Hills,CSU East Bay";
	
	const PLAN_TESTING_SATTESTS="Biology E/M,Chemistry,Chinese with Listening,French,French with Listening,German,German with Listening,Italian,Japanese with Listening,Korean with Listening,Latin,Literature,Math Level 1,Math Level 2,Modern Hebrew,Physics,Spanish,Spanish with Listening,U.S. History,World History";
	const PALN_TESTING_APTESTS="Art History,Biology,Calculus AB,Calculus BC,Chemistry,Chinese Language and Culture,Comparative Government and Politics,Computer Science A,English Language and Composition,English Literature and Composition,Environmental Science,European History,French Language and Culture,German Language and Culture,Human Geography,Italian Language and Culture,Japanese Language and Culture,Latin,Macroeconomics,Microeconomics,Music Theory,Physics 1: Algebra-Based,Physics 2: Algebra-Based,Physics C: Electricity and Magnetism,Physics C: Mechanics,Psychology,Spanish Language and Culture,Spanish Literature and Culture,Statistics,Studio Art,United States Government and Politics,United States History,World History";
	const PALN_TESTING_IBTESTS="Studies in language and literature,Language acquisition,Individuals and societies,Sciences,Mathematics,The arts";
	
	const PLAN_MYPROFILE_ETHNICITY="-1:Select,1:Argentinean,2:Bolivian,3:Chilean,4:Colombian,5:Costa Rican,6:Other Central American,7:Other South American,999:Other Hispanic or Latino (please specify)";
	
	
	const PLAN_GPA_CALC_GENERAL="http://69.64.88.204:8070/uceazy/plan/gpa/weighted/<UID>";
	const PLAN_GPA_CALC_CSUC="http://69.64.88.204:8070/uceazy/plan/gpa/capweighted/<UID>";
	const PLAN_GPA_BASE_URL="http://69.64.88.204:8070/uceazy/plan/gpa/<UID>";
	const PLAN_TESTING_BASE_URL = "http://69.64.88.204:8070/uceazy/plan/stdtests/<UID>";
	const PLAN_RESUME_BASE_URL="http://69.64.88.204:8070/uceazy/plan/resume/<UID>";
	const PLAN_PROFILE_BASE_URL="http://69.64.88.204:8070/uceazy/plan/profile/<UID>";
	
	const PLAN_TESTING_SATTOACT = "http://69.64.88.204:8070/uceazy/plan/stdtests/sat2act/<UID>";
	const PLAN_TESTING_ACTTOSAT="http://69.64.88.204:8070/uceazy/plan/stdtests/act2sat/<UID>";
	
	// get major by id
	const PLAN_GET_MAJOR="http://69.64.88.204:8070/uceazy/plan/major/<UID>";
	
	//get all majors
	const PLAN_GET_ALL_MAJORS="http://69.64.88.204:8070/uceazy/plan/major/majors";
	
	// Get Colleges by major
	const PLAN_GET_COLLEGE_BY_MAJOR="http://69.64.88.204:8070/uceazy/plan/major/colleges/<MAJOR>";
	
	const PLAN_COSTS_BASE_URL = "http://69.64.88.204:8070/uceazy/plan/costs/<UID>";
	const PLAN_COSTS_MASTERDATA_URL="http://69.64.88.204:8070/uceazy/plan/costs/costbreakdown";
	
	
	// added for mycounsellor
	const PLAN_MYUCEAZY_BASE_URL="http://69.64.88.204:8070/uceazy/plan/counselor/<UID>";
	
	const PLAN_SCHOOL_INFO_URL="http://69.64.88.204:8070/uceazy/plan/counselor/schoolinfo/<SCHOOL>";
	
	const PLAN_MYUCEAZY_STUINFO="http://69.64.88.204:8070/uceazy/plan/gpa/capweighted/<UID>";
	
	const PLAN_MYUCEAZY_ELIGIBILITY="http://69.64.88.204:8070/uceazy/plan/counselor/eligibility/<UID>";
	
	const PLAN_UCEAZY_ELIGIBLE_COURSE = "http://69.64.88.204:8070/uceazy/plan/counselor/a2grequirementmet/<UID>";
	
	const PLAN_UCEAZY_COMPETITIVE_COURSES ="http://69.64.88.204:8070/uceazy/plan/counselor/a2grecommendationmet/<UID>";
	
	const PLAN_UCEAZY_BEST_STD_TESTS ="http://69.64.88.204:8070/uceazy/plan/stdtests/findbeststdtest/<UID>";
	//const PLAN_UCEAZY_BEST_STD_TESTS ="http://69.64.88.204:8070/uceazy/plan/counselor/findbeststdtest/<UID>";
	
	const PLAN_PERSONAL_GET_ALL_QUESTIONS="http://69.64.88.204:8070/uceazy/plan/personalstmtquestionnaire";
	
	const PLAN_GET_ANSWERED_QUESTIONS = "http://69.64.88.204:8070/uceazy/plan/personalstmt/<UID>";
	
	
	const PLAN_GET_STARS = "http://69.64.88.204:8070/uceazy/plan/starcount/<UID>";
	
	const ALL_SCHOOLS ="http://69.64.88.204:8070/uceazy/highschools/ca";
	
	const SCHOOLS_BY_COUNTY = "http://69.64.88.204:8070/uceazy/highschools/ca/<COUNTY>";
	
	
	/* const SAT_TESTING_DATE = "October 11, 2014:November 8, 2014:December 6, 2014:January 24, 2015:May 2, 2015:June 6, 2015";
	
	const SAT_SUB_TESTING_DATE = "October 11, 2014:November 8, 2014:December 6, 2014:January 24, 2015:May 2, 2015:June 6, 2015";
	
	const AP_TESTING_DATE = "October 11, 2014:November 8, 2014:December 6, 2014:January 24, 2015:May 2, 2015:June 6, 2015";
	
	const IB_TESTING_DATE = "October 11, 2014:November 8, 2014:December 6, 2014:January 24, 2015:May 2, 2015:June 6, 2015";
	
	const ACT_TESTING_DATE = "September 12, 2015:October 24, 2015:December 12, 2015:February 6, 2016:April 9, 2016:June 11, 2016"; */
	
	
	
	const SAT_TESTING_DATE = "10/06/2012:11/03/2012:12/01/2012:01/26/2013:03/09/2013:05/04/2013:06/01/2013:10/05/2013:11/02/2013:12/07/2013:01/24/2014:03/08/2014:05/03/2014:06/07/2014:10/11/2014:11/08/2014:12/06/2014:01/24/2015:03/14/2015:05/02/2015:06/06/2015:10/03/2015:11/07/2015:12/05/2015:01/23/2016:03/05/2016:05/07/2016:06/04/2016";
	
	const SAT_SUB_TESTING_DATE = "10/06/2012:11/03/2012:12/01/2012:01/26/2013:03/09/2013:05/04/2013:06/01/2013:10/05/2013:11/02/2013:12/07/2013:01/24/2014:03/08/2014:05/03/2014:06/07/2014:10/11/2014:11/08/2014:12/06/2014:10/03/2015:11/07/2015:12/05/2015:01/23/2016:03/05/2016:05/07/2016:06/04/2016";
	
	const AP_TESTING_DATE = "5/7/2012:5/8/2012:5/9/2012:5/10/2012:5/11/2012:5/14/2012:5/15/2012:5/16/2012:5/17/2012:5/18/2012:5/6/2013:5/7/2013:5/8/2013:5/9/2013:5/10/2013:5/13/2013:5/14/2013:5/15/2013:5/16/2013:5/17/2013:5/5/2014:5/6/2014:5/7/2014:5/8/2014:5/9/2014:5/12/2014:5/13/2014:5/14/2014:5/15/2014:5/16/2014:5/4/2015:5/5/2015:5/6/2015:5/7/2015:5/8/2015:5/11/2015:5/12/2015:5/13/2015:5/14/2015:5/15/2015:5/2/2016:5/3/2016:5/4/2016:5/5/2016:5/6/2016:5/9/2016:5/10/2016:5/11/2016:5/12/2016:5/13/2016";
	
	const IB_TESTING_DATE = "10/30/2015:11/03/2015:11/04/2015:11/05/2015:11/06/2015:11/09/2015:11/10/2015:11/11/2015:11/12/2015:11/13/2015:11/16/2015:11/17/2015:11/18/2015:11/19/2015:11/20/2015:11/23/2015:11/24/2015:04/30/2015:05/04/2015:05/05/2015:05/06/2015:05/07/2015:05/08/2015:05/11/2015:05/12/2015:05/13/2015:05/14/2015:05/15/2015:05/18/2015:05/19/2015:05/20/2015:05/21/2015:05/22/2015";
	
	const ACT_TESTING_DATE = "09/08/2012:10/27/2012:12/08/2012:02/09/2013:04/13/2013:06/08/2013:09/21/2013:10/26/2013:12/14/2013:02/08/2014:04/12/2014:06/14/2014:09/13/2014:10/25/2014:12/13/2014:02/07/2015:04/18/2015:06/13/2015:09/12/2015:10/24/2015:12/12/2015:02/06/2016:04/09/2016:06/11/2016";
	
	const UC_URI_SAT="http://69.64.88.204:8070/uceazy/rts/adv/uc/sat/";
	const UC_URI_ACT="http://69.64.88.204:8070/uceazy/rts/adv/uc/act/";
	const UC_URI_PSAT="http://69.64.88.204:8070/uceazy/rts/adv/uc/psat/";
	const URI_SAT="http://69.64.88.204:8070/uceazy/rts/sat/";
	const URI_ACT="http://69.64.88.204:8070/uceazy/rts/act/";
	const URI_PSAT="http://69.64.88.204:8070/uceazy/rts/psat/";
	const BOTH_URI_SAT="http://69.64.88.204:8070/uceazy/rts/adv/sat/";
	const BOTH_URI_ACT="http://69.64.88.204:8070/uceazy/rts/adv/act/";
	const BOTH_URI_PSAT="http://69.64.88.204:8070/uceazy/rts/adv/psat/";
	const CSU_URI_PSAT="http://69.64.88.204:8070/uceazy/rts/adv/csu/psat/";
	const CSU_URI_SAT="http://69.64.88.204:8070/uceazy/rts/adv/csu/sat/";
	const CSU_URI_ACT="http://69.64.88.204:8070/uceazy/rts/adv/csu/act/";
	const ESSAY_ALERT_EMAIL="essay@uceazy.com";
	const ESSAY_GROUP_EMAIL="uceazy_essayreviewers@uceazy.com";

	// staging Paypal
	const ESSAY_PAYPAL_RETURN_URL="https://stage.uceazy.com/buyservice/paymentComplete";
	const ESSAY_PAYPAL_CANCEL_URL="https://stage.uceazy.com/buyservice/cancel";
	const ESSAY_PAYPAL_URL="https://www.sandbox.paypal.com/cgi-bin/webscr";
	const ESSAY_PAYPAL_ID="shivakumarparamasivam-facilitator@gmail.com";
	
	
	//production Paypal
	//const ESSAY_PAYPAL_RETURN_URL="https://app.uceazy.com/buyservice/paymentComplete";
	//const ESSAY_PAYPAL_CANCEL_URL="https://app.uceazy.com/buyservice/cancel";
	//const ESSAY_PAYPAL_URL="https://www.paypal.com/cgi-bin/webscr";
	//const ESSAY_PAYPAL_ID="vgupta@uceazy.com";
	
	
	//local Paypal
	//const ESSAY_PAYPAL_RETURN_URL="http://uc.dev/paymentComplete";
	//const ESSAY_PAYPAL_CANCEL_URL="http://uc.dev/Buyservice/cancel";
	


}
