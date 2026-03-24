 function changeMultiCategory(catid){
 
    $.getJSON(site_url + "m/listing/ajax_multicategory_options",{id: catid, ajax: 'true'}, function(j){
      var options = '';
      for (var i = 0; i < j.length; i++) {
        options += '<option value="' + j[i].optionValue + '">' + j[i].optionDisplay + '</option>';
      }
 
      $('select[name="category_id[]"]').html(options); 

    });
 

 } 
