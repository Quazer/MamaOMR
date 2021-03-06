<?
/**
 * @Controller ISBN 코드로 책정보를 가져오기
 *
 * @subpackage   	Core/DBmanager/DBmanager
 * @package      	Mangong/Book
 * @package      	Mangong/Test
 * @package      	Mangong/MQuestion
 */
require_once("Model/Core/DBmanager/DBmanager.php");
require_once('Model/ManGong/Book.php');
require_once('Model/ManGong/Test.php');
require_once('Model/ManGong/MQuestion.php');

/**
 * Variable 세팅
 * @var 	$strISBNCode	ISBN 코드
 * @var 	$resultType		ISBN 결과 데이터 형식 (XML)		
 */ 
$strISBNCode = $_REQUEST['ISBN'];
$resultType = $_REQUEST['result_type'];

/**
 * Object 생성
 * @property	resource 		$resMangongDB 	: DB 커넥션 리소스
 * @property	object			Book  				: Book 객체
 * @property	object 		Test 					: Test 객체
 * @property	object 		MQuestion 			: MQuestion 객체
 */
$resMangongDB = new DB_manager('MAIN_SERVER');
$objBook = new Book($resMangongDB);
$objTest = new Test($resMangongDB);
$objQuestion = new MQuestion($resMangongDB);

 /**
 * Main Process
 */
	
$arrSearch = array();
$arrSearch['ISBN_CODE'] = $strISBNCode;
$arrBook = $objBook->getBook($arrSearch);
$arrBookOutput = array(
		'isbn_code'=>$arrBook[0]['isbn_code'],
		'title'=>$arrBook[0]['title'],
		'pub_name'=>$arrBook[0]['pub_name'],
		'pub_year'=>$arrBook[0]['pub_year'],
		'cover_url'=>$arrBook[0]['cover_url'],
		'create_date'=>$arrBook[0]['create_date'],
		'pub_date'=>$arrBook[0]['pub_date'],
		'test_info'=>array()
);
//get all test info 
$arrTestListByBook = $objBook->getTestListByBook(md5($arrBook[0]['seq']));

//set arr survey seq
foreach ($arrTestListByBook as $key => $arrResult) {
	//set test count
	$arrTestListByBookOutput = array(
		'subject'=>$arrResult['subject'],
		'example_numbering_style'=>$arrResult['example_numbering_style'],
		'test_question_cnt'=>$objQuestion->getQuestionCountInTest($arrResult['test_seq']),
		'questions'=>array()
	);
	$arrQuestion = $objTest->getTestQuestionListWithExample($arrResult['test_seq'],false,array(1,2,3,4,5,6,7,8,9,11),$arrTestResult[0]['example_numbering_style']);
	foreach($arrQuestion as $intQuestionKey=>$arrQuestionResult){
		$arrQuestionOutput = array(
				'question_number'=>$arrQuestionResult['question_number'],
				'question_type'=>$arrQuestionResult['question_type'],
				'example_type'=>$arrQuestionResult['example_type'],
				'tags'=>$arrQuestionResult['tags'],
				'example'=>array()
		);
		foreach($arrQuestionResult['example']['type_'.$arrQuestionResult['example_type']] as $intExampleKey=>$arrExampleResult){
			array_push($arrQuestionOutput['example'],array('example_type'=>$arrExampleResult['example_type'],'answer_flg'=>$arrExampleResult['answer_flg']));
		}
		array_push($arrTestListByBookOutput['questions'],$arrQuestionOutput);
	}
	array_push($arrBookOutput['test_info'],$arrTestListByBookOutput);
}


/**
 * View OutPut Data 세팅 
 * OutPut Type array 또는 json : $resultType 이 xml 이면 array로 출력
 * 
 * @property	array 		$arr_output['book_info'] 			: Book 정보
 */
$arr_output['book_info'] = $arrBookOutput;

if($resultType=="xml"){
	function array_to_xml($array, &$xml_user_info) {
		foreach($array as $key => $value) {
			if(is_array($value)) {
				if(!is_numeric($key)){
					$subnode = $xml_user_info->addChild("$key");
					array_to_xml($value, $subnode);
				}else{
					$subnode = $xml_user_info->addChild("item$key");
					array_to_xml($value, $subnode);
				}
			}else {
				$xml_user_info->addChild("$key",htmlspecialchars("$value"));
			}
		}
	}
	
	
	$xml_test_book_info = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\" ?><omr></omr>");
	array_to_xml($arr_output,$xml_test_book_info);
	header("Content-type: text/xml;charset=utf-8");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	print $xml_test_book_info->asXML();
}else{
	header('Content-Type: application/json;charset=utf-8');
	echo json_encode($arr_output,JSON_UNESCAPED_UNICODE);
}
exit;
?>