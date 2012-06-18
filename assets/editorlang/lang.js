
$(function() {
$(".lang").change(function(){

//Save the link in a variable called element
var element = $(this);

//Find the id of the link that was clicked
var id = element.attr("value");
if(id == 'english')
 {
$.ajax({
   type: "GET",
   url: "googledata/english.js",
   dataType: "script"
 }); 
 
 }
 
if(id == 'hindi')
 {
$.ajax({
   type: "GET",
   url: "googledata/hindi.js",
   dataType: "script"
 }); 
 
 }

 if(id == 'telugu')
 {
$.ajax({
   type: "GET",
   url: "googledata/telugu.js",
   dataType: "script"
 }); 
 
 }
 
  if(id == 'tamil')
 {
$.ajax({
   type: "GET",
   url: "googledata/tamil.js",
   dataType: "script"
 }); 
 
 }
 
 if(id == 'malayalam')
 {
$.ajax({
   type: "GET",
   url: "googledata/malayalam.js",
   dataType: "script"
 }); 
 
 }
 
 if(id == 'kannada')
 {
$.ajax({
   type: "GET",
   url: "googledata/kannada.js",
   dataType: "script"
 }); 
 
 }
 
return false;

});

});

