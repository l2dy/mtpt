// JavaScript Document
function dropNav(obj){
	$(obj).each(function(){
						 var theSpan=$(this);
						 var theUl=theSpan.find("ul");
						 var theHeight=theUl.height();
						 theUl.css({height:0,opacity:0});
						 theSpan.hover(function(){
												$(this).addClass("nav-list-1");
												theUl.stop().show().animate({height:theHeight,opacity:1},400);

												},function(){
													$(this).removeClass("nav-list-1");
													theUl.stop().show().animate({height:0,opacity:0},400,
																				function(){
																					$(this).css({display:"none"});
																					})});                                                 
						 });
	}
	$(document).ready(function(){
	
	dropNav(".nav-list");

});


var def="0";
function changetop(object){
	var topnav=document.getElementById("top-nav"+object);
	topnav.className="cur";
	if(def!=0){
		var mdef=document.getElementById("top-nav"+def);
		mdef.className="topnav";
	}
	var ss=document.getElementById("top-sub"+object);
	ss.style.display="block";
	
	//初始子菜单先隐藏效果
	if(def!=0){
		var sdef=document.getElementById("top-sub"+def);
		sdef.style.display="none";
	}
	}
function changetop2(object){

	//主菜单
	var mm=document.getElementById("top-nav"+object);
	mm.className="topnav";
	
	//初始主菜单还原效果
	if(def!=0){
		var mdef=document.getElementById("top-nav"+def);
		mdef.className="cur";
	}
	
	//子菜单
	var ss=document.getElementById("top-sub"+object);
	ss.style.display="none";
	
	//初始子菜单还原效果
	if(def!=0){
		var sdef=document.getElementById("top-sub"+def);
		sdef.style.display="block";
	}
	
}
