var naver_id_login = new naver_id_login(naverClientID, naverCallBackURL);
var state = naver_id_login.getUniqState();
naver_id_login.setButton("green", 20,40);
naver_id_login.setDomain(naverDomain);
naver_id_login.setState(state);
naver_id_login.setPopup();
naver_id_login.init_naver_id_login();
$("document").ready(function(){
	$('#naver_id_login img').attr({'alt':'naver login button'});
});