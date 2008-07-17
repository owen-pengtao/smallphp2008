jQuery.validator.addMethod('maxWords', function(value, element, params) { 
    return !$(element).val() || $(element).val().match(/bw+b/g).length < params; 
}, 'Please enter {0} words or less.'); 
 
jQuery.validator.addMethod('minWords', function(value, element, params) { 
    return !$(element).val() || $(element).val().match(/bw+b/g).length >= params; 
}, 'Please enter at least {0} words.'); 
 
jQuery.validator.addMethod('rangeWords', function(value, element, params) { 
    return !$(element).val() || ($(element).val().match(/bw+b/g).length >= params[0] && $(element).val().match(/bw+b/g).length < params[1]); 
}, 'Please enter between {0} and {1} words.');


jQuery.validator.addMethod("password", function(value, element) {
	return value.length>=6 && value.length<=20;
}, "密码须在6-20个字符之间。");



jQuery.validator.addMethod("letterswithbasicpunc", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^[a-z-.,()'\"s]+$/i.test(value);
}, "Letters or punctuation only please");  

jQuery.validator.addMethod("alphanumeric", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^w+$/i.test(value);
}, "Letters, numbers, spaces or underscores only please");  

jQuery.validator.addMethod("lettersonly", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^[a-z]+$/i.test(value);
}, "Letters only please"); 

jQuery.validator.addMethod("nowhitespace", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^S+$/i.test(value);
}, "No white space please"); 

jQuery.validator.addMethod("anything", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^.+$/i.test(value);
}, "May contain any characters."); 

jQuery.validator.addMethod("integer", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^d+$/i.test(value);
}, "Numbers only please");

jQuery.validator.addMethod("phone", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^\d{3}-\d{8}|\d{4}-\d{7}$/.test(value);
}, "Must be 010-88880000");

jQuery.validator.addMethod("ziprange", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^[1-9]\d{5}(?!\d)$/.test(value);
}, "Your ZIP-code must be in the range 100001");

jQuery.validator.addMethod("qq", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^[1-9][0-9]{4,}$/.test(value);
}, "Must be 11737077");

jQuery.validator.addMethod("url", function(value, element) {
	return !jQuery.validator.methods.required(value, element) || /^[a-zA-z]+:\/\/[^\s]*$/.test(value);
}, "Must be http://www.d5s.cn");
