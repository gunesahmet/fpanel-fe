$(document).ready(function() {
var newDate = new Date();
newDate.setDate(newDate.getDate());
$('#date').html( newDate.getDate() + '.' + newDate.getMonth() + '.' + newDate.getFullYear());

setInterval( function() {
	var minutes = new Date().getMinutes();
	$("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
    },1000);
	
setInterval( function() {
	var hours = new Date().getHours();
	$("#hours").html(( hours < 10 ? "0" : "" ) + hours);
    }, 1000);	
});
