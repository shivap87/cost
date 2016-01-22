$(function(){
	$(document).on('keyup change', "[data-validate='yes']", function(e){     
	var self=$(this);
	var cur_val=$(this).val();		
	var alp_num_regex=/^[a-zA-Z0-9]+$/;
	//var numericReg = /^[0-9]*(?:\d{1,2})?$/;
	var numericReg = /^[0-9]{1,10}$/;
	var dateReg=!/^(((0[1-9]|[12]\d|3[01])\/(0[13578]|1[02])\/((1[6-9]|[2-9]\d)\d{2}))|((0[1-9]|[12]\d|30)\/(0[13456789]|1[012])\/((1[6-9]|[2-9]\d)\d{2}))|((0[1-9]|1\d|2[0-8])\/02\/((1[6-9]|[2-9]\d)\d{2}))|(29\/02\/((1[6-9]|[2-9]\d)(0[48]|[2468][048]|[13579][26])|((16|[2468][048]|[3579][26])00))))$/;
	var alphabet=/^[a-zA-Z ]+$/;
	var whiteSpace=/^.*\s{3,}.*$/;
	var email= /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;
	var alp_num_spl_char=/^[ A-Za-z0-9_@./#'!&+-]*$/
	var mob_num=/^([0|\+[0-9]{1,5})?([7-9][0-9]{9})$/;
		
   
	var rule_set=($(this).attr("data-rules"));
	var rule_list=get_rules_list(rule_set);	
		
	  /*$(this).closest('tr').find("input:text,select").each(function() {
        console.log(this.value)
    });	//console.log(tr);*/
		
	//white space validation 
	if (whiteSpace.test(cur_val)){
		add_class(self, "Please enter valid input");
		return false;
	}
    else{           
       remove_class(self);
	}	
	//email validation
	if(cur_val!="" && rule_list['type']=="email"){
		//console.log(email.test(cur_val));
		if (email.test(cur_val)){
			remove_class(self);
		}
    else{ 
			add_class(self,"Please enter valid email id");
			return false;      
		}
	}
	//Date validation	
	if(cur_val!="" && rule_list['type']=="date"){
		if (dateReg.test(cur_val)){
			add_class(self,"Please enter a valid date");
			return false;
		}
    else{           
       remove_class(self);
		}
	}
	//alphabets validation
	if(cur_val!="" && rule_list['type']=="alphabet"){
		if (cur_val!='' && !alphabet.test(cur_val)){			
			add_class(self,"Please enter only alphabets");
			return false;
		}
    else{
       remove_class(self);
		}
	}
	//alphanumeric validation
	if(cur_val!="" && rule_list['type']=="alphanumeric"){
		if(alp_num_regex.test(cur_val)){
			remove_class(self);
		}else{
			add_class(self,"Please enter only alphanumeric");
			return false;
		}
		
	}
	 
	//numeric validation
	if(cur_val!=""  && rule_list['type']=="numeric"){	
		if(numericReg.test(cur_val)){			
			remove_class(self);
			
		}else{
			add_class(self,"Please enter only numeric value");
			return false;
			
		}
		
	}
	
	// minimum value validation
	if(cur_val!="" &&  rule_list['min']>0){
		
		if( parseInt(cur_val) < rule_list['min']){			
			add_class(self,"Please enter a value greater than " +rule_list['min']);
			return false;
		}else{
			remove_class(self);
		}	
	}
	// maximum value validation
	if(cur_val!="" && rule_list['max']>0){
		if(parseInt(cur_val)>rule_list['max']){
			//console.log('if max');
			add_class(self,"Please enter a value less than " +rule_list['max']);
			return false;
		}else{
			remove_class(self);
		}	
	}
	
	if(cur_val!="" &&  rule_list['divide_by']>0){
		if(cur_val % rule_list['divide_by'] ==0){
			remove_class(self);
		}else{
			add_class(self,"Please enter a value, multiple of 10");
			return false;
		}
		
	}
	
	if(rule_list['type']=="alp_num_char"){
		
		if(alp_num_spl_char.test(cur_val)){
			remove_class(self);
		}else{
			add_class(self,"Please enter the alphanumeric with special charecters( @./#'!&+-)");
			return false;
		}	
	}
	//Max length validation	
	if(cur_val!="" && rule_list['max_length']>0){
		if((cur_val.length)>rule_list['max_length']){
			//console.log('if len');
			add_class(self,"Please enter valid input");		
			return false;
		}else{
			remove_class(self);
		}
	}
	
	
	
	
	
	});
	
	
	
	function get_rules_list(rule_set){
		var rules=rule_set.split(',');
		var max=rules.length;
		var rule_list={};
		
		for(var i=0;i<max;i++){
			var temp=rules[i].split(":");
			rule_list[temp[0]]=temp[1];
		}
		return rule_list;
	}
	
	
	function add_class(ele,err_msg){
		$(ele).closest("tr").find(":button").addClass("disabled");
		$(ele).closest("tr").find(":button").attr('disabled','true');				
			
		ele.addClass("has_error");
		ele.attr('data-haserror', 'yes');
		ele.attr('data-toggle', 'popover');
		ele.attr('data-content', err_msg);		
		ele.attr('data-placement', 'top');		
		setTimeout(function () {
			ele.popover('show');
			if(!$(ele).hasClass("has_error")){
				remove_class(ele);
			}
		}, 1000);
		
		
		
	}
	
	function remove_class(ele){
		$(ele).closest("tr").find(":button").removeClass("disabled");
		$(ele).closest("tr").find(":button").removeAttr('disabled');		
		
		ele.removeClass("has_error");
		ele.removeAttr('data-haserror');
		ele.removeAttr('data-toggle');
		ele.removeAttr('data-content');		
		ele.removeAttr('data-placement');
		ele.popover('destroy');		
		
	}
	
});