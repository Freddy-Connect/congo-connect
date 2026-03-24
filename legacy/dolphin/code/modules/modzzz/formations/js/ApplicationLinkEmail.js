function activer() {
document.form.businessemail.disabled=false;
document.form.application_link.disabled=true;
document.form.application_link.value="";
document.getElementById("application_link").checked = false;
}

function desactiver(){
document.form.businessemail.disabled=true;
document.form.application_link.disabled=false;
document.getElementById("businessemail").checked = false;
}

