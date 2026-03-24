function addQuestion(){
	$aParent = $('input.addbutton').parent().parent().parent();
	
	$('input.question').parent().parent().parent().clone().find('*').removeClass('question').end().find('input').attr({value:''}).end().insertBefore($aParent) ; 

	$('input.answer').parent().parent().parent().clone().find('*').removeClass('answer').end().find('input').attr({value:''}).end().insertBefore($aParent) ; 
 
}