function validateBruteforceLoginForm(eForm) {

    if (! eForm)
        return false;

    $(eForm).ajaxSubmit({
        success: function(sResponce) {
            if(sResponce == 'OK')
                eForm.submit();
            else if(sResponce == 'blocked')
                alert('Your account is locked');
            else
                alert(_t('_PROFILE_ERR'));
        }
    });
}

