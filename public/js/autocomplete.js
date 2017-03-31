/* Fixed Header */
$(window).scroll(function() { 
if ($(this).scrollTop()>25)  { $('#Top').fadeIn("Slow"); }
else { $('#Top').fadeOut(); }
});

// Back to Top
$('#Top').ready(function() {
var away = false;
$('#Top').click(function() {
$("html, body").animate({scrollTop: 0}, 500);
return false;
});
});

function login(showhide){
if(showhide == "show"){
document.getElementById('login-box-modal').style.display = 'block'; /* If the function is called with the variable 'show', show the login box */
}else if(showhide == "hide"){
document.getElementById('login-box-modal').style.display = 'none'; /* If the function is called with the variable 'hide', hide the login box */
} }

$(function(){

/* Video Player */
$(".lightSwitcher").click(function(){
$("#shadow").toggle();
if ($("#shadow").is(":hidden"))
$(".lightSwitcher").html("Turn off the lights").removeClass("turnedOff");
else
$(".lightSwitcher").html("Turn on the lights").addClass("turnedOff");
});


/* Menu */
//$(".show-menu").click(function (e) { e.preventDefault(); $('#off-canvas-menu').toggle(); });

// Search
$(".show-search").click(function (e) { e.preventDefault(); $(".show-search .icon-search").toggleClass("icon-cross","icon-search"); $('#menu-search').toggle(); }); 

function return_window_with(display){
if (display) {
var width = window.innerWidth;
$(window).resize(function() { width = window.innerWidth; if( width > 700 ) { $('#menu-search').show(); } else { $('#menu-search').hide(); } });
}
}

return_window_with(true);


// Notification
$(".show-menu").click(function(){
$(".popup").fadeOut(300);
$("#off-canvas-menu").fadeToggle(300);
$("#off-canvas-menu").addClass('popup');
return false;
});

$(document).click(function(e) {
if (!$(e.target).closest('.popup').length){
$(".popup").hide();
}	
});

// Ajax Search
$('#searchbox').typeahead({
minLength:1,
dynamic: true,
searchOnFocus: true,
emptyTemplate: 'No result for "{{query}}"',
display: ["title","alt_name"],
template: '<span class="result">' +
'<span>{{title}}</span>'  +
"</span>",
source: {
href: function (item) {
return item.url;
},
url: [{
type: "get",
dataType: "json",
url: base_url+'/ajax/search',
data: {
q: "{{query}}"
}
}]
},
callback: {
onSendRequest: function(node, query) {
$("header .search-input").addClass('search-loading');
},
onReceiveRequest: function(node, query) {
$("header .search-input").removeClass('search-loading');
}
}
});

if($('#rating').length > 0){

// Rating
$('#rating').raty({halfShow:true,size:24,starHalf:'star-half.png',
starOff:'star-off.png',
starOn:'star-on.png',
path:img_url,start:$('#rating').attr('rel'),score:$('#rating').attr('rel'),click:function(b,c){
var sid = $(this).attr("sid");	
var rate_url = base_url+'/ajax/rate';
$.post(rate_url,{'rating':b,'sid':sid},function(a){
if(a)alert(a.msg);else alert("Sorry, something wrong with our server, please try it again later.")},"json")}});
}

// Bookmark Add
$(".bookmark").click(function(e) {

var id = $(this).attr("rel");
var bmk_add = base_url + "/ajax/bookmark/add";
$.post(bmk_add,{series_id : id}, function( data ) {
alert(data.msg);
$(".bookmark").replaceWith("Bookmarked");
});

});

// Bookmark Remove
$(".bookmark_remove").click(function(e) {
var id = $(this).attr("rel");
});


if(typeof Croppic !== 'undefined'){
// Upload Avatar
var croppic_avatar_options = {
processInline:true,
cropUrl: base_url + '/ajax/avatar',
customUploadButtonId: 'upload-avatar',
modal: true,
loaderHtml: '<div class="loader bubblingG"><span id="bubblingG_1"></span><span id="bubblingG_2"></span><span id="bubblingG_3"></span></div> ',
onError: function(errormessage){ console.log('onError:'+errormessage) }
}
var croppic_avatar = new Croppic('profile_img', croppic_avatar_options);

}

// Change Episode
$('.change_epi').on('change', function () {
var url = $(this).val(); // get selected value
if (url) { // require a URL
window.location = url; // redirect
}
return false;
});

});