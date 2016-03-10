var request;
var dir;
var size = 0;
var cccc = ['b','w']

function varReq(){
	if(window.XMLHttpRequest){
		request = new XMLHttpRequest();
	}else{
		request = new ActiveXObject("Microsoft.XMLHTTP");
	}
}
function loadTxt(file_name){
	request.open("GET", file_name, false);
	request.setRequestHeader("Content-Type", "application/x-www-form-urlencoded; Charset=utf-8");
	request.send(null);
	if(request.readyState==4 && request.status==200)
		return request.responseText;
	else
		return "";
}

function init(d){
	dir = d;
	document.getElementById("listing").innerHTML = "";
	document.getElementById("SearchedContainer").style.display = "none";
	varReq();
	loadList(0);
}
function loadList(p){
	var json = loadTxt("../json.php?dir="+dir+"&p="+p);
	var item = JSON.parse(json);
	var list = document.getElementById("listing");
	var i = 0;
	while(item[i]){
		temp = "<div><a href=\"" + item[i].a + "\" class=\"" + cccc[i%2] + "\"  id=\"a" + (size++) + "\">" + item[i++].t + "</a></div>";
		list.insertAdjacentHTML('beforeEnd', temp);
	}
	if(i<30){
		// 버튼 숨기기
		document.getElementById("more").parentNode.removeChild(document.getElementById("more"));
	}else{
		// 다음 페이지 버튼
		document.getElementById("more").href = "javascript:loadList(" + (size/30) + ")";
	}
	search();
}

function search(){
	// 검색어가 없으면 기본 출력
	if(document.getElementById("search").value==""){
		document.getElementById("ListingContainer").style.display = "";
		document.getElementById("SearchedContainer").style.display = "none";
		document.getElementById("rssa").href = "../rss.php?dir="+dir;
		return;
	}
	
	// 검색결과 출력
	document.getElementById("ListingContainer").style.display = "none";

	var json = loadTxt("../json.php?dir="+dir+"&q="+document.getElementById("search").value);
	var item = JSON.parse(json);
	var list = document.getElementById("Searched");
	list.innerHTML = "";
	for(i=0; item[i]; i++){
		temp = "<div><a href=\"" + item[i].a + "\" class=\"" + cccc[i%2] + "\">" + item[i].t + "</a></div>";
		list.insertAdjacentHTML('beforeEnd', temp);
	}

	document.getElementById("rssa").href = "../rss.php?dir="+dir+"&q=" + document.getElementById("search").value;
	document.getElementById("SearchedContainer").style.display = "";
}
