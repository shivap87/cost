function get_essay_history(tbl_id,modal_id){
	var table_data='';
	$.ajax({       
		type: "POST",
		dataType: "json",
		url: "/buyservice/getHistoryAjax", 
		data: {"action":"from_ajax"},
		success: function(data) {
			table_data=data['result'];			
			$('#'+tbl_id+ ' tbody').empty().append(table_data);
			$("#"+modal_id).modal('show');
		},
	   error: function(e) {
		//called when there is an error
		console.log(e.message);
	  }
	 
	});
	return;
	
}