function toggleCompanyBlock(sValue){
	if(sValue=='-99'){ 
		$('#company_block').parent().show(); 
		$('#company_name').parent().parent().parent().parent().show(); 
	}else{
		$('#company_block').parent().hide(); 
		$('#company_name').parent().parent().parent().parent().hide();  
	}
}

function jobFormInit(){

	if ((document.getElementById("company_id").value == null) || (document.getElementById("company_id").value == '-99')){
		$('#company_block').parent().show(); 
		$('#company_name').parent().parent().parent().parent().show(); 
	}else{
		$('#company_block').parent().hide(); 
		$('#company_name').parent().parent().parent().parent().hide(); 
	}
}

window.onload = jobFormInit;


