 // Custom JS
var clone_limit=5;


 $('#aus').click(function(){
      	$("#county").prop("disabled", true);

      	
 });


 $('#ous').click(function(){
      	$("#county").prop("disabled", true);

      	
 });


 $('#cal').click(function(){
      	$("#county").prop("disabled", false);

      	
 });


//Form element Tooltip 
$(function(){
  
  $(".tip-top").tooltip({
        placement : 'top'
    });
    $(".tip-right").tooltip({
        placement : 'right'
    });
    $(".tip-bottom").tooltip({
        placement : 'bottom'
    });
    $(".tip-left").tooltip({
        placement : 'left'
    });
});

/* Add/Remove Current School Name */
$(function() {
        var schoolDiv = $('#schoolName');
        var i = $('#schoolName').size() + 1;
        $(document).on('click', '#addSchool', function(){    
                
                $('<div class="col-sm-6 col-md-offset-3"><div class="input-group inputBtn"><input type="text" class="p_school form-control timepicker" data-auto="yes" id="p_school_d" name="previousSchoolNames[]" value="" placeholder="Enter the school name" list="school_names" maxlength="100" ><div class="input-group-addon"><button id="remSchool" class="btn btn-default" ><i class="fa fa-minus"></i></button></div></div>').appendTo(schoolDiv);
                i++;
                if (i>4)
                {
                    $('#addSchool').attr('disabled', 'disabled');
                }
                return false;
        });
        
        $(document).on('click', '#remSchool', function() { 
            if( i > 2 ) {
                $(this).parent().parent().remove();
                $('#addSchool').removeAttr('disabled');
                i--;}
                return false;
        });
});




/* add / remove course details */
$(document).ready (function(){
	
	  /* var i = $('#satdatetaken').size();
	   
	   $(document).on('change', '#satdatetaken', function(){
			 $('#satconv').append($('<option>', {
			    value: $(this).val(),
			    text: $(this).val()
			}));
			 
			 
			 
			   var i = $('#actdatetaken').size();
			   $(document).on('change', '#actdatetaken', function(){
					 $('#actconv').append($('<option>', {
					    value: $(this).val(),
					    text: $(this).val()
					}));
		 
	 });
	  
	   });*/
	   
 var isInt = function(data){
	 if(data%1==0){
		 return true;
	 }
	 return false;
 }	
	
    $(document).on('click', '.plusbtn', function(e){    	
		 e.preventDefault();
		//validation agnist the clone limt
		var par_ele=$(this).parent().parent();
		var cnt=get_row_count(par_ele,this);
		
		if(cnt <= clone_limit){	
			
			
		val=$(this).val();
    	res = val.split("_")[1];
    	
    	 var cl=$(this).parent().parent().clone();    
    	 
    	 
    
    	// added this line for adding SAT and ACT dynamic population for Testing page
    	 if(val == 'T_SAT'){
    		var cr = cl.find('input[name="satcr[]"]').val(); 
    		var math = cl.find('input[name="satmath[]"]').val();
    		var wrting = cl.find('input[name="satwriting[]"]').val();
    		var flg = 0;
    		$('select[name="satdatetaken[]"]').each(function() {
    			if($(this).val()=='Select'){
    				flg =1;
    			}
    		});
    		
    		if(flg==1){
    			$('#saterror').show();
    			//alert("dateError");
    			setTimeout(function(){ $('#saterror').hide();}, 10000);
    			return false;
    		}
    		
    		
    		
    		if(isInt(cr) && cr%10 == 0 && math%10==0 && wrting%10 == 0 && cr <= 800 && cr >=200 && isInt(math) && math >= 200 && math <=800 && isInt(wrting) && wrting >= 200 && wrting <=800){
    			$('#satconv').empty();
    		    cl.find("button").val("T_SAT");
    		    $('select[name="satdatetaken[]"]').each(function() {
    			var dt = $(this).val();
        		if(dt!='Select'){
        		$('#satconv').append($('<option>', {
     			    value: dt,
     			    text: dt
     			}));
        		}
    			 
    			});
    		}else{
    			
    			$('#saterror').show();
    			setTimeout(function(){ $('#saterror').hide();}, 10000);
    			return false;
    		}
    		 
    		 
    	 }
    	
    	 
    	 // ACT Exam
    	 
    	 if(val == 'T_ACT'){
    		 
    		 
     		
     		var acteng = cl.find('input[name="actenglish[]"]').val();
     		var actmath = cl.find('input[name="actmath[]"]').val();
     		var actread = cl.find('input[name="actreading[]"]').val();
     		var actscience = cl.find('input[name="actscience[]"]').val();
     		var actwriting = cl.find('input[name="actwriting[]"]').val();
     		
     		//alert ("act:"+act+"eng:"+acteng+"math:"+actmath+"read:"+actread+"science:"+actscience+"writing:"+actwriting);
     	
     		var flg = 0;
     		$('select[name="actdatetaken[]"]').each(function() {
     			if($(this).val()=='Select'){
     				flg =1;
     			}
     		});
     		
     		if(flg==1){
     			
     			$('#acterror').show();
     			setTimeout(function(){ $('#acterror').hide();}, 10000);
     			return false;
     		}
     		
     		
     		
     		if(isInt(acteng) && acteng >= 1 && acteng <=36 && isInt(actmath) && actmath >= 1 && actmath <=36	&& isInt(actread) && actread >= 1 && actread <=36 && isInt(actscience) && actscience >= 1 && actscience <=36 && isInt(actwriting) && actwriting >= 1 && actwriting <=36
     	     		){
     			$('#actconv').empty();
     		    cl.find("button").val("T_ACT");
     		    $('select[name="actdatetaken[]"]').each(function() {
     			var dt = $(this).val();
         		if(dt!='Select'){
         		$('#actconv').append($('<option>', {
      			    value: dt,
      			    text: dt
      			}));
         		}
     			 
     			});
     		}else{
     		//	alert("val check");
     			$('#acterror').show();
     			setTimeout(function(){ $('#acterror').hide();}, 10000);
     			return false;
     		}
     		 
     		 
     	 }
    	 
    	 // End
    	 
    	  // PSAT
    	 //psatgrade[]
   
    	 
    	 if(val == 'T_PSAT'){
    		
     		var cr = cl.find('input[name="psatcr[]"]').val(); 
     		var math = cl.find('input[name="psatmath[]"]').val();
     		var wrting = cl.find('input[name="psatwriting[]"]').val();
     		var flg = 0;
     		$('select[name="psatgrade[]"]').each(function() {
     			if($(this).val()=='-1'){
     				flg =1;
     			}
     		});
     		
     		if(flg==1){
     			$('#psaterror').show();
     			setTimeout(function(){ $('#psaterror').hide();}, 10000);
     			return false;
     		}
     		
     		if(isInt(cr) && cr <= 80 && cr >=20 && isInt(math) && math >= 20 && math <=80 && isInt(wrting) && wrting >= 20 && wrting <=80){
     		    cl.find("button").val("T_PSAT");
     		}else{
     			$('#psaterror').show();
     			setTimeout(function(){ $('#psaterror').hide();}, 10000);
     			return false;
     		}
     		 
     		 
     	 }
     	
    	 
    	 //END
    	 //SAT SUBS
    	 
    	 
    	
    	 if(val == 'T_SAT_SUB'){
     		
      		var cr = cl.find('input[name="satsubscore[]"]').val(); 
      		var flg = 0;
      		$('select[name="satsubtest[]"]').each(function() {
      			if($(this).val()=='-1'){
      				flg =1;
      			}
      		});
      		$('select[name="satsubtestdate[]"]').each(function() {
      			if($(this).val()=='-1'){
      				flg =1;
      			}
      		});
      		
      		if(flg==1){
      			$('#satsuberror').show();
      			setTimeout(function(){ $('#satsuberror').hide();}, 10000);
      			return false;
      		}
      		
      		if(isInt(cr) && cr <= 800 && cr >=200 && cr%10 == 0 ){
      		    cl.find("button").val("T_SAT_SUB");
      		}else{
      			$('#satsuberror').show();
      			setTimeout(function(){ $('#satsuberror').hide();}, 10000);
      			return false;
      		}
      		 
      		 
      	 }
    	 //END
    	 
    	 
    	
    	
    	 if(val == 'T_AP'){
      		
       		var cr = cl.find('input[name="apexamscore[]"]').val(); 
       		var flg = 0;
       		$('select[name="apexam[]"]').each(function() {
       			if($(this).val()=='-1'){
       				flg =1;
       			}
       		});
       		$('select[name="apexamdate[]"]').each(function() {
       			if($(this).val()=='-1'){
       				flg =1;
       			}
       		});
       		
       		if(flg==1){
       			$('#aperror').show();
       			setTimeout(function(){ $('#aperror').hide();}, 10000);
       			return false;
       		}
       		
       		if(isInt(cr) && cr <= 5 && cr >=1 ){
       		    cl.find("button").val("T_AP");
       		}else{
       			$('#aperror').show();
       			setTimeout(function(){ $('#aperror').hide();}, 10000);
       			return false;
       		}
       		 
       		 
       	 }
    	 
    	 //
    	 
    	 
    	
    	 
    	 
    	 if(val == 'T_IB'){
       		
        		
        		var flg = 0;
        		$('select[name="ibscore[]"]').each(function() {
        			if($(this).val()=='-1'){
        				flg =1;
        			}
        		});
        		$('select[name="ibdate[]"]').each(function() {
        			if($(this).val()=='-1'){
        				flg =1;
        			}
        		});
        		$('select[name="iblevel[]"]').each(function() {
        			if($(this).val()=='-1'){
        				flg =1;
        			}
        		});
        		$('select[name="ibexam[]"]').each(function() {
        			if($(this).val()=='-1'){
        				flg =1;
        			}
        		});
        		
        		if(flg==1){
        			$('#iberror').show();
        			setTimeout(function(){ $('#iberror').hide();}, 10000);
        			return false;
        		}else{
        			  cl.find("button").val("T_IB");
        		}
        		
        		
        		 
        		 
        	 }
    	 //
    	 
    	 cl.find("i").last().attr("class", "fa fa-minus");
    	 cl.find("button").attr("class", "btn btn-default minusbtn");
    	 cl.find('input').val('');
    	 cl.find('select').val('Select');		
		 var next_plus_btn=$(this).closest('tr').nextAll(':has(.plusbtn):first').find('.plusbtn');		 
		 if(next_plus_btn.length>0){
			 $(next_plus_btn).parent().parent().before(cl);			 
		 }else{
			$(this).closest('table').find("tr:last").after(cl);
			
		 }
		 return false; 	
    	
		}else{		
			
			par_ele.find("button.plusbtn").prop('disabled','true');
			return false;
		}
    	 
      // $(this).closest('tr').after('<tr><td>'+res +'<sup>th</sup> Grade <input type="hidden" name="'+val+'grade[]" value="'+res+'" /></td><td><input type="text" name="'+val+'cname[]" class="form-control" placeholder="Enter Course Name"/></td><td><select name="'+val+'gradesem1[]" id="year" class="form-control"><option>Select</option><option>A+/A/A-</option><option>B+/B/B-</option></select></td><td><select name="'+val+'gradesem2[]" id="year" class="form-control"><option>Select</option><option>A+/A/A-</option><option>B+/B/B-</option></select></td>            <td><select name="'+val+'gradesem3[]" id="year" class="form-control"><option>Select</option><option>A+/A/A-</option><option>B+/B/B-</option></select></td><td><select name="'+val+'level[]" id="year" class="form-control"><option>Select</option><option>College Prep</option><option>AP</option><option>IB</option><option>Unsure</option></select></td><td><input type="text" name="'+val+'credit[]" class="form-control" placeholder="2-99"/></td><td><button value="" class="btn btn-default minusbtn"><i class="fa fa-minus"></i></button></td></tr>');
        
    });
   $(document).on('click', '.minusbtn', function(e){
	   e.preventDefault();
	   var self=this;
	   var par_ele=$(self).parent().parent();
	   var cnt=get_row_count(par_ele,this);
	 
	   cnt-=1;
	   if(cnt==2){
		$(par_ele).prev().prev().find("button.plusbtn").removeAttr("disabled");   
		$(par_ele).prev().find("button.plusbtn").removeAttr("disabled"); 
	   }
	   $(self).parent().parent().remove();
	   
	   if(val == 'T_SAT'){
	   		
	  		
	  		 $('#satconv').empty();
	  		 $('select[name="satdatetaken[]"]').each(function() {
	  			 
	  			 var dt = $(this).val();
	  			
	      		 if(dt!='Select'){
	      		 $('#satconv').append($('<option>', {
	   			    value: dt,
	   			    text: dt
	   			}));
	      		}
	  			 
	  			});
	  		
	  		 
	  		 
	  	 }
	   
	   if(val == 'T_ACT'){
	   		
	  		
	  		 $('#actconv').empty();
	  		 $('select[name="actdatetaken[]"]').each(function() {
	  			 
	  			 var dt = $(this).val();
	      		 if(dt!='Select'){
	      		 $('#actconv').append($('<option>', {
	   			    value: dt,
	   			    text: dt
	   			}));
	      		}
	  			 
	  			});
	  		
	  		 
	  		 
	  	 }
	   
    });
});


function get_row_count(par_ele,self){
	   var hidden_var_name=par_ele.find( 'input:hidden').attr('name').slice(0,-2);
		var hidden_var_arr = $("[name='"+hidden_var_name+"\\[\\]']")
			  .map(function(){return $(self).val();}).get();		
		var cnt=hidden_var_arr.length;
	return cnt;
	   }

/* Data Mask*/

      $(function () {
        //Datemask dd/mm/yyyy
        $("#datemask").inputmask("dd/mm/yyyy", {"placeholder": "dd/mm/yyyy"});
        //Datemask2 mm/dd/yyyy
        $("#datemask2").inputmask("mm/dd/yyyy", {"placeholder": "mm/dd/yyyy"});
        //Money Euro
        $("[data-mask]").inputmask();

      });
      
      
      /* carousel 
      $(document).ready(function() {
         $('#myCarousel').carousel({
             interval: 10000
         });
      });    */


      /* Plan - Hover slide */
      $( document ).ready(function() {
          $("[rel='tooltip']").tooltip();    
       
          $('.thumbnail').hover(
              function(){
                  $(this).find('.caption').slideDown(250); //.fadeIn(250)
                  
              },
              function(){
                  $(this).find('.caption').slideUp(250); //.fadeOut(205)
                  
              }
          ); 
      });

      /* Ethnicity */
      $('.trigger').click(function() {
          $('.displayNone').hide();
          $('.' + $(this).data('rel')).show();
      });
      
      
      $('#ethnicityname').change(function() {
    	 if($(this).val()=='999'){
    		  $('#ethnicitynameothers').show();
    	 }else{
    		 $('#ethnicitynameothers').hide();
    	 }
      });
      
      /* Select Race */

      
      $('#selectRace').change(function() {
    	  $("." + $(this).val()).show();
      });
      
      
      
      
      $('#racewhite').change(function() {
          //$('.displayNone').hide();
    	  if($(this).val()=="others"){
          $(".others").show();
    	  }else{
    		  $(".others").hide();
    	  }

      });

      
      $('#raceblack').change(function() {
          //$('.displayNone').hide();
    	  if($(this).val()=="others"){
          $(".others").show();
    	  }else{
    		  $(".others").hide();
    	  }

      });

      

      $('#raceamericanindian').change(function() {
          //$('.displayNone').hide();
    	  if($(this).val()=="others"){
          $(".others").show();
    	  }else{
    		  $(".others").hide();
    	  }

      });

      $('#raceasian').change(function() {
          //$('.displayNone').hide();
    	  if($(this).val()=="others"){
          $(".others").show();
    	  }else{
    		  $(".others").hide();
    	  }

      });

      $('#racenativehawaiian').change(function() {
          //$('.displayNone').hide();
    	  if($(this).val()=="others"){
          $(".others").show();
    	  }else{
    		  $(".others").hide();
    	  }

      });
      
      //send game score to user's mail
      $('#summary').click(function(){
    	  var btn = $(this)
	   	  btn.button('loading');
		  var data = {    			     
		      "mode" : 'update'
		      
		    };
	 	    data = $.param(data);

		    $.ajax({
		      type: "POST",
		      dataType: "json",
		      url: "/plan/sendsummary", 
		      data: data,
		      
		      success: function(data) {
	  		  if(data['flag']==true){
	  		    	$('#success').modal();
				  }else{
					  $('#failure').modal();
   		      }
		      },
		      error: function(xhr, textStatus, errorThrown){
		          alert('Request failed..Please retry after sometime.');
		         
		       },
			   complete: function(xhr, textStatus){
 		  		  btn.button('reset');
 		  		}
		    });
		    return false;
    	      

    		

    	 });



//notification count updater
$("ul[data-note='list']").on("click","li", function(e){	
		e.preventDefault();
		var self=this;
		var note_id=$(self).attr('data-note_id');
		 $.ajax({       
			type: "POST",
			dataType: "json",
			url: "/user/readNotification", 
			data: {"note_id":note_id},
			success: function(data) {					
				//console.log(data['result']);
				if(data['result']===1 || data['result']===0){
					$("a[data-showNote='show']").click();					
				}
						
			},
		   error: function(e) {
			//called when there is an error
			console.log(e.message);
		  }
		 
		});
		
	});		 
    //replay the game
	$("#replay-game").on("click", function(e){			
		var btn = $(this)
	   	  btn.button('loading');
		    $.ajax({
		      type: "POST",
		      dataType: "json",
		      url: "/plan/replayGame",
			  success: function(data) {				 
	  		  if(data['flag']==true){
				 var getUrl = window.location;
				 var baseUrl = getUrl .protocol + "//" + getUrl.host + "/" + getUrl.pathname.split('/')[0];	 
				 window.location.replace(baseUrl+"plan/personalhome");
				  }else{
					 alert('Request failed..Please retry after sometime.');
   		      }
		      },
		      error: function(xhr, textStatus, errorThrown){
		          alert('Request failed..Please retry after sometime.');
		         
		       },
			   complete: function(xhr, textStatus){
 		  		  btn.button('reset');
 		  		}
		    });
		    return false;
		
	});

      

$(function(){
	
	$(document).keypress(
    function(event){
     if (event.which == '13') {
        event.preventDefault();
      }
	});
});


/* Video pagination */
   $(function() {
      // Cycles the carousel to a particular frame 
      $(".slide-one").click(function() {
         $("#myCarousel").carousel(0);      
            $( this ).parent().find( '.active' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
      });
      
      $(".slide-two").click(function() {
         $("#myCarousel").carousel(1);
            $( this ).parent().find( '.active' ).removeClass( 'active' );
            $( this ).addClass( 'active' );
      });      
     
   });
    
   //customizing the bootstrap Accordion
   $(function(){
		$(document).on('click', "[data-collapse='custom']", function(e){						
			var self=this;
			var classList = $(self).find("h3.box-title").attr('class').split(/\s+/);
			var fa_class=classList[1];			
			$(self).find("h3.box-title").removeClass(classList[1]);			
			$(self).find("i.fa").attr('class', "fa "+ fa_class);
		});
   });
		
   
